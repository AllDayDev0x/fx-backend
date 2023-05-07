<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor,Log;

use App\Models\Story, App\Models\StoryFile, App\Models\User;

class AdminStoryController extends Controller
{
    public function __construct(Request $request) {

        $this->middleware('auth:admin');

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

    }

    /**
     * @method stories_index()
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
    public function stories_index(Request $request) {

        $base_query = Story::orderBy('created_at','DESC');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query =  $base_query

            ->whereHas('user', function($q) use ($search_key) {

                return $q->Where('users.name','LIKE','%'.$search_key.'%');

            })->orWhere('stories.unique_id','LIKE','%'.$search_key.'%');

        }

        if(isset($request->status)) {

            $base_query = $base_query->where('stories.status', $request->status);

        }

        if($request->user_id) {

            $base_query = $base_query->where('user_id',$request->user_id);
        }

        $user = User::find($request->user_id)??'';

        $stories = $base_query->paginate(10);

        return view('admin.stories.index')
                ->with('page', 'stories')
                ->with('sub_page', 'stories-view')
                ->with('user', $user)
                ->with('stories', $stories);
    
    }

    /**
     * @method stories_create()
     *
     * @uses create new story
     *
     * @created Subham 
     *
     * @updated 
     *
     * 
     * @return View page
     *
    */
    public function stories_create() {

        $story = new Story;

        $users = User::Approved()->get();

        return view('admin.stories.create')
                ->with('page', 'stories')
                ->with('sub_page', 'stories-create')
                ->with('users', $users)
                ->with('story', $story);  

    }

    /**
     * @method stories_save()
     *
     * @uses save new story
     *
     * @created Subham 
     *
     * @updated 
     *
     * 
     * @return View page
     *
    */
    public function stories_save(Request $request) {
        
        try { 

            DB::begintransaction();

            $mimes = $request->file_type ? ($request->file_type == FILE_TYPE_IMAGE ? '|mimes:jpg,png,jpeg' : '|mimes:mp4,mov,webm,flv,avi') : '';

            $rules = [
                'user_id' => 'required',
                'content' => /*$request->has('story_files') ? */'nullable'/* : 'required'*/,
                'amount' => 'nullable|min:0',
                'story_files' => 'required'.$mimes,
            ];

            Helper::custom_validator($request->all(),$rules);

            $story = Story::find($request->story_id) ?? new Story;

            $story->user_id = $request->user_id;

            $story->content = $request->content;

            $story->is_published = $request->publish_type ?? PUBLISHED;

            $publish_time = $request->publish_time ?: date('Y-m-d H:i:s');
          
            $story->publish_time = date('Y-m-d H:i:s', strtotime($publish_time));

            $story->amount = $request->amount?? 0;

            $story->is_paid_story = $request->amount > 0 ? YES : NO;

            $message = $request->story_id ? tr('story_update_success') : tr('story_create_success');

            if($story->save()) {

                if($request->has('story_files')) {

                    $story_file = StoryFile::where('story_id',$story->id)->first() ?? new StoryFile();

                    $request->request->add(['file_type' => get_file_type($request->file('story_files'))]);

                    $filename = rand(1,1000000).'-story-'.$request->file_type ?? 'image';

                    $folder_path = POST_PATH.$story->user_id.'/';

                    $story_file_url = Helper::post_upload_file($request->story_files, $folder_path, $filename);

                    $ext = $request->file('story_files')->getClientOriginalExtension();

                    if($story_file_url) {

                        $story_file->story_id = $story->id;

                        $story_file->user_id = $story->user_id;
                        
                        $story_file->file = $story_file_url;

                        $story_file->file_type = $request->file_type;

                        $story_file->blur_file = $request->file_type == "image" ? Helper::generate_post_blur_file($story_file->file, $request->file('story_files'), $story->user_id) : Setting::get('post_video_placeholder');

                        if($request->file_type == FILE_TYPE_VIDEO) { 

                            if ($request->has('preview_file')) {

                                $preview_filename = rand(1,1000000).'-story-'.$request->file_type ?? 'image';

                                $preview_file = Helper::post_upload_file($request->preview_file, $folder_path, $preview_filename);
                            }
                            else{

                                $filename_img = "preview-".rand(1,1000000).'-story-image.jpg';

                                \VideoThumbnail::createThumbnail(storage_path('app/public/'.$folder_path.$filename.'.'.$ext),storage_path('app/public/'.$folder_path),$filename_img, 2);

                                $preview_file = asset('storage/'.$folder_path.$filename_img);

                               
                            }
                            
                            $story_file->preview_file = $preview_file ?? Setting::get('post_video_placeholder');

                        }


                        if(Setting::get('is_watermark_logo_enabled') && Setting::get('watermark_logo')){

                            if($request->file_type == FILE_TYPE_IMAGE){

                                $storage_file_path = public_path("storage/".$folder_path.get_video_end($story_file_url)); 
               
                                CommonRepo::add_watermark_to_image($storage_file_path);
                             }


                           if($request->file_type == FILE_TYPE_VIDEO){

                            $video_file = public_path("storage/".$folder_path.get_video_end($story_file_url)); 
                        
                            $new_video_path = public_path("storage/".$folder_path."water-".get_video_end($story_file_url)); 
    
                            $job_data['video'] = $video_file;
    
                            $job_data['watermark_video'] = $new_video_path;
            
                            $this->dispatch(new \App\Jobs\VideoWatermarkPositionJob($job_data));
    
                          }
                       }           

                        $story_file->save();

                    }

                }

                DB::commit(); 

                return redirect()->route('admin.stories.view',['story_id'=>$story->id])->with('flash_success', $message);

            } 

            throw new Exception(tr('post_save_failed'));

        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method stories_edit()
     *
     * @uses To display and update user details based on the user id
     *
     * @created sakthi
     *
     * @updated 
     *
     * @param object $request - User Id
     * 
     * @return redirect view page 
     *
    */
    public function stories_edit(Request $request) {

        try {

            $story = Story::find($request->story_id);

            $story_file = StoryFile::where('story_id', $request->story_id)->first();

            if(!$story) { 

                throw new Exception(tr('post_not_found'), 101);
            }
            
            $users = User::Approved()->get();

            return view('admin.stories.edit')
                        ->with('page', 'story')
                        ->with('sub_page', 'stories-view')
                        ->with('users', $users)
                        ->with('story_file', $story_file)
                        ->with('story', $story); 

        } catch(Exception $e) {

            return redirect()->route('admin.stories.index')->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method stories_view()
     *
     * @uses displays the specified stpries details based on story id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - story Id
     * 
     * @return View page
     *
     */
    public function stories_view(Request $request) {

        try {

            $story = Story::find($request->story_id);
            
            if(!$story) { 

                throw new Exception(tr('story_not_found'), 101);                
            }

            $story_files = StoryFile::where('story_id',$request->story_id)->get() ?? [];

            return view('admin.stories.view')
                    ->with('page', 'stories') 
                    ->with('sub_page','stories-view') 
                    ->with('story', $story)
                    ->with('story_files', $story_files);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method stories_delete()
     *
     * @uses delete the story details based on story id
     *
     * @created Subham 
     *
     * @updated  
     *
     * @param object $request - story Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function stories_delete(Request $request) {

        try {

            DB::begintransaction();

            $story = Story::find($request->story_id);

            if(!$story) {

                throw new Exception(tr('story_not_found'), 101);                
            }

            if($story->delete()) {

                DB::commit();

                if($request->page){
                    
                    return redirect()->route('admin.stories.index', ['page'=>$request->page])->with('flash_success', tr('post_deleted_success'));

                } else {

                    return redirect()->route('admin.stories.index')->with('flash_success', tr('story_deleted_success'));
                }

            } 

            throw new Exception(tr('story_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       

    }

    /**
     * @method stories_status
     *
     * @uses To update story status as DECLINED/APPROVED based on story id
     *
     * @created Subham
     *
     * @updated 
     *
     * @param object $request - story Id
     * 
     * @return response success/failure message
     *
     **/
    public function stories_status(Request $request) {

        try {

            DB::beginTransaction();

            $story = Story::find($request->story_id);

            if(!$story) {

                throw new Exception(tr('story_not_found'), 101);

            }

            $story->status = $story->status ? STORIES_DECLINED : STORIES_APPROVED ;

            if($story->save()) {

                DB::commit();

                $message = $story->status ? tr('story_approve_success') : tr('story_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }

            throw new Exception(tr('story_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.stories.index')->with('flash_error', $e->getMessage());

        }

    }
}
