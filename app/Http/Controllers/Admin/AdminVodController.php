<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor,Log;

use App\Models\User, App\Models\VodVideo, App\Models\VodCategory, App\Models\PostCategory;

class AdminVodController extends Controller
{
    public function __construct(Request $request) {

        $this->middleware('auth:admin');

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

    }

    /**
     * @method vod_videos_index()
     *
     * @uses Display the stories
     *
     * @created Subham
     *
     * @updated
     *
     * @param -
     *
     * @return view page 
     */
    public function vod_videos_index(Request $request) {

        $base_query = VodVideo::orderBy('created_at','DESC');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query =  $base_query

            ->whereHas('user', function($q) use ($search_key) {

                return $q->Where('users.name','LIKE','%'.$search_key.'%');

            });

        }

        if($request->status) {

            $base_query = $base_query->where('vod_videos.status', $request->status);

        }

        if($request->user_id) {

            $base_query = $base_query->where('user_id',$request->user_id);
        }

        if($request->post_category_id) {

            $vod_categories = VodCategory::select('vod_video_id')
                           ->where('post_category_id', $request->post_category_id)
                           ->pluck('vod_video_id')->toArray();

            
            $base_query = $base_query->whereIn('vod_videos.id',  $vod_categories);
           
        }

        $user = User::find($request->user_id)??'';

        $vod_videos = $base_query->paginate(10);

        return view('admin.vod_videos.index')
                ->with('page', 'vods')
                ->with('sub_page', 'vods-view')
                ->with('user', $user)
                ->with('vod_videos', $vod_videos);
    
    }

    /**
     * @method vod_videos_create()
     *
     * @uses create new vod
     *
     * @created Subham 
     *
     * @updated 
     *
     * 
     * @return View page
     *
    */
    public function vod_videos_create() {

        $vod_video = new VodVideo;

        $users = User::Approved()->get();

        $vod_category_details = VodCategory::where('vod_video_id',$vod_video->id)->pluck('post_category_id')->toArray();

        $post_categories = PostCategory::APPROVED()->get();

        return view('admin.vod_videos.create')
                ->with('page', 'vods')
                ->with('sub_page', 'vods-create')
                ->with('users', $users)
                ->with('vod_video', $vod_video)
                ->with('post_categories',$post_categories)
                ->with('vod_category_details',$vod_category_details);  

    }

    /**
     * @method vod_videos_save()
     *
     * @uses save new vod
     *
     * @created Subham 
     *
     * @updated 
     *
     * 
     * @return View page
     *
    */
    public function vod_videos_save(Request $request) {
        
        try {            

            DB::begintransaction();

            $rules = [
                'user_id' => 'required',
                'vod_files' => $request->has('vod_files') ? 'nullable' : 'required',
                'post_category_ids' => 'required',
            ];

            Helper::custom_validator($request->all(),$rules);

            $vod_video = VodVideo::find($request->vod_id) ?? new VodVideo;

            $vod_video->user_id = $request->user_id;

            $vod_video->description = $request->description;

            $vod_video->is_published = $request->publish_type ?? PUBLISHED;

            $publish_time = $request->publish_time ?: date('Y-m-d H:i:s');
          
            $vod_video->publish_time = date('Y-m-d H:i:s', strtotime($publish_time));

            $vod_video->amount = $request->amount?? 0;

            $vod_video->is_paid_vod = $request->amount > 0 ? YES : NO;

            $message = $vod_video->id ? tr('vod_update_success') : tr('vod_create_success');

            if($vod_video->save()) {

                if($request->has('vod_files')) {

                    $vod_file = VodVideo::where('id',$vod_video->id)->first() ?? new VodVideo();

                    if($vod_file->file != ''){

                        $folder_path_file = VOD_PATH.$request->user_id.'/';

                        Helper::storage_delete_file($vod_file->file, $folder_path_file);

                        Helper::storage_delete_file($vod_file->preview_file, $folder_path_file);
                    }

                    $request->request->add(['file_type' => get_file_type($request->file('vod_files'))]);

                    $filename = rand(1,1000000).'-vod-'.$request->file_type ?? 'video';

                    $folder_path = VOD_PATH.$vod_video->user_id.'/';

                    $vod_file_url = Helper::post_upload_file($request->vod_files, $folder_path, $filename);

                    $ext = $request->file('vod_files')->getClientOriginalExtension();

                    if($vod_file_url) {

                        $vod_file->user_id = $vod_video->user_id;
                        
                        $vod_file->file = $vod_file_url;

                        $vod_file->blur_file = $request->file_type == "image" ? Helper::generate_post_blur_file($vod_file->file, $request->file('vod_files'), $vod_video->user_id) : Setting::get('post_video_placeholder');

                        if($request->file_type == FILE_TYPE_VIDEO) { 

                            if ($request->has('preview_file')) {

                                $preview_filename = rand(1,1000000).'-vod-'.$request->file_type ?? 'image';

                                $preview_file = Helper::post_upload_file($request->preview_file, $folder_path, $preview_filename);
                            }
                            
                            $vod_file->preview_file = $preview_file ?? Setting::get('post_video_placeholder');

                        }       

                        $vod_file->save();

                    }

                }

                if($request->post_category_ids) {
                    
                    $post_category_ids = $request->post_category_ids;
                    
                    if(!is_array($post_category_ids)) {

                        $post_category_ids = explode(',', $post_category_ids);
                        
                    }

                    if($request->user_id) {
                    
                        VodCategory::where('vod_video_id', $request->vod_id)->whereNotIn('post_category_id', $post_category_ids)->delete();
                    }                    


                    foreach ($post_category_ids as $key => $value) {

                        $vod_category = new VodCategory;

                        $vod_category->vod_video_id = $vod_video->id;
                        
                        $vod_category->post_category_id = $value;

                        $vod_category->status = APPROVED;
                        
                        $vod_category->save();

                    } 
                }

                if($request->has('preview_file')){

                    $vod_file = VodVideo::where('id',$vod_video->id)->first() ?? new VodVideo();

                    if($vod_file->preview_file != ''){

                        $folder_path_file = VOD_PATH.$request->user_id.'/';

                        Helper::storage_delete_file($vod_file->preview_file, $folder_path_file);
                    }

                    $folder_path = VOD_PATH.$vod_video->user_id.'/';

                    $preview_filename = rand(1,1000000).'-vod-'.$request->file_type ?? 'image';

                    $preview_file = Helper::post_upload_file($request->preview_file, $folder_path, $preview_filename);

                    $vod_file->preview_file = $preview_file ?? Setting::get('post_video_placeholder');

                    $vod_file->save();

                }

                DB::commit(); 

                return redirect()->route('admin.vod_videos.view',['vod_id'=>$vod_video->id])->with('flash_success', $message);

            } 

            throw new Exception(tr('post_save_failed'));

        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method vod_videos_edit()
     *
     * @uses To display and update user details based on the user id
     *
     * @created Subham
     *
     * @updated 
     *
     * @param object $request - User Id
     * 
     * @return redirect view page 
     *
    */
    public function vod_videos_edit(Request $request) {

        try {

            $vod_video = VodVideo::find($request->vod_id);

            if(!$vod_video) { 

                throw new Exception(tr('vod_not_found'), 101);
            }
            
            $users = User::Approved()->get();

            $vod_category_details = VodCategory::where('vod_video_id',$vod_video->id)->pluck('post_category_id')->toArray();

            $post_categories = PostCategory::APPROVED()->get();

            return view('admin.vod_videos.edit')
                        ->with('page', 'vods')
                        ->with('sub_page', 'vods-view')
                        ->with('users', $users)
                        ->with('vod_video', $vod_video)
                        ->with('post_categories',$post_categories)
                        ->with('vod_category_details',$vod_category_details); 

        } catch(Exception $e) {

            return redirect()->route('admin.vod_videos.index')->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method vod_videos_view()
     *
     * @uses displays the specified stpries details based on vod id
     *
     * @created Subham 
     *
     * @updated 
     *
     * @param object $request - vod Id
     * 
     * @return View page
     *
     */
    public function vod_videos_view(Request $request) {

        try {

            $vod_video = VodVideo::find($request->vod_id);
            
            if(!$vod_video) { 

                throw new Exception(tr('vod_not_found'), 101);                
            }

            $vod_files = VodVideo::where('id',$request->vod_id)->get() ?? [];

            return view('admin.vod_videos.view')
                    ->with('page', 'vods') 
                    ->with('sub_page','vods-view') 
                    ->with('vod_video', $vod_video)
                    ->with('vod_files', $vod_files);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method vod_videos_delete()
     *
     * @uses delete the vod details based on vod id
     *
     * @created Subham 
     *
     * @updated  
     *
     * @param object $request - vod Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function vod_videos_delete(Request $request) {

        try {

            DB::begintransaction();

            $vod_video = VodVideo::find($request->vod_id);

            if(!$vod_video) {

                throw new Exception(tr('vod_not_found'), 101);                
            }

            if($vod_video->delete()) {

                DB::commit();

                if($request->page){
                    
                    return redirect()->route('admin.vod_videos.index', ['page'=>$request->page])->with('flash_success', tr('vod_deleted_success'));

                } else {

                    return redirect()->route('admin.vod_videos.index')->with('flash_success', tr('vod_deleted_success'));
                }

            } 

            throw new Exception(tr('vod_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       

    }

    /**
     * @method vod_videos_status
     *
     * @uses To update vod status as DECLINED/APPROVED based on vod id
     *
     * @created Subham
     *
     * @updated 
     *
     * @param object $request - vod Id
     * 
     * @return response success/failure message
     *
     **/
    public function vod_videos_status(Request $request) {

        try {

            DB::beginTransaction();

            $vod_video = VodVideo::find($request->vod_id);

            if(!$vod_video) {

                throw new Exception(tr('vod_not_found'), 101);

            }

            $vod_video->status = $vod_video->status ? VOD_DECLINED : VOD_APPROVED;

            if($vod_video->save()) {

                DB::commit();

                $message = $vod_video->status ? tr('vod_approve_success') : tr('vod_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }

            throw new Exception(tr('vod_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.vod_videos.index')->with('flash_error', $e->getMessage());

        }

    }
}
