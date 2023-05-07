<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor,Log;

use App\Jobs\SendEmailJob;

use App\Jobs\PublishPostJob;

use App\Models\User, App\Models\Hashtag, App\Models\Category, App\Models\CategoryDetail, App\Models\PostBookmark;

use App\Models\PostLike, App\Models\PostFile, App\Models\Post;

use App\Models\PostHashtag;

use Carbon\Carbon;

use App\Repositories\CommonRepository as CommonRepo;

use File;

use App\Jobs\VideoThumbnailJob;

class AdminPostController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {

        $this->middleware('auth:admin');

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

    }

    /**
     * @method posts_index()
     *
     * @uses Display the total posts
     *
     * @created Akshata
     *
     * @updated
     *
     * @param -
     *
     * @return view page 
     */
    public function posts_index(Request $request) {

        $base_query = \App\Models\Post::orderBy('created_at','DESC');

        if(isset($request->paid_status)) {

            $base_query = $base_query->where('posts.is_paid_post', $request->paid_status);

        }

        if(isset($request->status)) {

            $base_query = $base_query->where('posts.status', $request->status);

        }

        $amount = Setting::get('is_only_wallet_payment') ? 'token' : 'amount';

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query =  $base_query

            ->whereHas('user', function($q) use ($search_key) {

                return $q->Where('users.name','LIKE','%'.$search_key.'%');

            })->orWhere('posts.'.$amount,'LIKE','%'.$search_key.'%')
            ->orWhere('.unique_id','LIKE','%'.$search_key.'%');

        }

        if($request->scheduled) {

            $base_query = $base_query->where('is_published',NO);

            $posts = $base_query->paginate($this->take);

            return view('admin.posts.index')
                        ->with('page','scheduled-posts')
                        ->with('posts', $posts);
        }

        if($request->category_id) {

            $post_categories = CategoryDetail::select('post_id')
                           ->where('category_id', $request->category_id)
                           ->where('type', CATEGORY_TYPE_POST)
                           ->pluck('post_id')->toArray();

            
            $base_query = $base_query->whereIn('posts.id',  $post_categories);
           
        }

        if($request->hashtag_id) {

            $post_hashtag_ids = PostHashtag::select('post_id')
                           ->where('hashtag_id', $request->hashtag_id)
                           ->pluck('post_id')->toArray();

            $base_query = $base_query->whereIn('posts.id',  $post_hashtag_ids);
           
        }

        if($request->user_id) {

            $base_query = $base_query->where('user_id',$request->user_id);
        }

        $user = User::find($request->user_id)??'';

        $hashtag = Hashtag::find($request->hashtag_id)??'';

        $category = Category::find($request->category_id)??'';

        $posts = $base_query->whereHas('user')->paginate($this->take);

        return view('admin.posts.index')
                ->with('page', 'posts')
                ->with('sub_page', 'posts-view')
                ->with('hashtag', $hashtag)
                ->with('user', $user)
                ->with('category',$category)
                ->with('posts', $posts);
    
    }

    /**
     * @method posts_create()
     *
     * @uses create new post
     *
     * @created sakthi 
     *
     * @updated 
     *
     * 
     * @return View page
     *
    */
    public function posts_create() {

        $post = new \App\Models\Post;

        $users = \App\Models\User::Approved()->get();

        $post_category_details = CategoryDetail::where('post_id',$post->id)->where('type', CATEGORY_TYPE_POST)->pluck('category_id')->toArray();

        $categories = Category::APPROVED()->get();

        return view('admin.posts.create')
                ->with('page', 'posts')
                ->with('sub_page', 'posts-create')
                ->with('users', $users)
                ->with('post', $post)
                ->with('categories',$categories)
                ->with('post_category_details',$post_category_details);  

    }

    /**
     * @method posts_save()
     *
     * @uses save new post
     *
     * @created sakthi 
     *
     * @updated Subham
     *
     * 
     * @return View page
     *
    */
    public function posts_save(Request $request) {
        
        try {

            DB::begintransaction();

            $mimes = $request->file_type ? ($request->file_type == FILE_TYPE_AUDIO ? '|mimes:audio/mpeg,mp3,wav' : ($request->file_type == FILE_TYPE_VIDEO ? '|mimes:mp4,mov,webm,flv,avi' : '|mimes:jpg,png,jpeg')) : '';

            $rules = [
                'user_id' => 'required',
                'content' => ($request->has('post_files') || $request->post_id) && $request->file_type ? 'nullable' : 'required',
                'amount' => 'nullable|min:0',
                'category_ids' => 'required',
                'post_files.*' => 'nullable'.$mimes,
            ];

            $errors = [
                'required_if' => 'The :attribute field is required when :other is '. tr('schedule') .'.',
            ];

            Helper::custom_validator($request->all(),$rules, $errors);

            $post = \App\Models\Post::find($request->post_id) ?? new \App\Models\Post;

            $post->user_id = $request->user_id;

            $post->is_published = $request->publish_type ?? PUBLISHED;

            $publish_time = $request->publish_time ?: date('Y-m-d H:i:s');
          
            $post->publish_time = date('Y-m-d H:i:s', strtotime($publish_time));

            $amount = $request->amount ?: ($post->amount ?? 0);

            if(Setting::get('is_only_wallet_payment')) {

                $post->token = $amount;

                $post->amount = $post->token * Setting::get('token_amount');

            } else {

                $post->amount = $amount;

            }

            $post->is_paid_post = $post->amount > 0 ? YES : NO;

            $message = $post->id ? tr('posts_update_success') : tr('posts_create_succes');

            $content = $post->content = $request->content;

            $strlen = strlen($content);

            $data = [];

            for ($i=0; $i < $strlen; $i++) { 

                if (preg_match("/#/", $content)) {

                    $explodedArray = strpos($content,"#")+ strlen('#');

                    $explodedArray = substr($content, $explodedArray);

                    $content = $explodedArray;

                    $data[$i] = preg_replace('/(\s*)([^\s]*)(.*)/', '$2', $explodedArray);
                    $data[$i] = strip_tags($data[$i]);
                    $strlen = strlen($content);

                }
                else{
                    $strlen = 0;
                }

            }

            if($post->save()) {

                if($request->has('post_files')) {

                    $del_post_files = PostFile::where('post_id',$post->id)->get();

                    if($request->post_id && $request->file_type != $del_post_files[0]->file_type){

                        $folder_path_file = POST_PATH.$request->user_id.'/';

                        foreach ($del_post_files as $key => $del_post_file) {

                            Helper::storage_delete_file($del_post_file->file, $folder_path_file);
                            
                            if ($del_post_file->file_type == POSTS_IMAGE) {

                                $folder_path = POST_BLUR_PATH.$request->user_id.'/';

                                Helper::storage_delete_file($del_post_file->blur_file, $folder_path);
                            }
                            else{

                                $folder_path = POST_PATH.$request->user_id.'/';

                                Helper::storage_delete_file($del_post_file->preview_file, $folder_path);

                            }
                        }

                        PostFile::where('post_id',$post->id)->delete();

                    }

                    foreach ($request->post_files as $file){

                        $post_file = new PostFile();

                        $request->request->add(['file_type' => get_file_type($file)]);

                        $filename = rand(1,1000000).'-post-'.$request->file_type ?? 'image';

                        $folder_path = POST_PATH.$post->user_id.'/';

                        $post_file_url = Helper::post_upload_file($file, $folder_path, $filename);

                        $ext = $file->getClientOriginalExtension();

                        if($post_file_url) {

                            $post_file->post_id = $post->id;

                            $post_file->user_id = $post->user_id;
                            
                            $post_file->file = $post_file_url;

                            $post_file->file_type = $request->file_type;

                            $post_file->blur_file = $request->file_type == "image" ? Setting::get('ppv_image_placeholder') : Setting::get('post_video_placeholder');

                            if($request->file_type == FILE_TYPE_VIDEO) { 

                                if ($request->has('preview_file')) {

                                    $preview_filename = rand(1,1000000).'-post-'.$request->file_type ?? 'image';

                                    $preview_file = Helper::post_upload_file($request->preview_file, $folder_path, $preview_filename) ?? Setting::get('post_video_placeholder');
                                }
                                else{

                                    $filename_img = "preview-".rand(1,1000000).'-post-image.jpg';

                                    $video_thumbnail_data['original_video_path'] = storage_path('app/public/'.$folder_path.$filename.'.'.$ext);

                                    $video_thumbnail_data['save_file_path'] = storage_path('app/public/'.$folder_path);

                                    $video_thumbnail_data['thumbnail_file_name'] = $filename_img;

                                    info($video_thumbnail_data);

                                    VideoThumbnailJob::dispatch($video_thumbnail_data);

                                    $preview_file = asset('storage/'.$folder_path.$filename_img) ?? Setting::get('post_image_placeholder');

                                   
                                }

                                $post_file->preview_file = $preview_file;

                                if ($request->has('video_preview_file')) {

                                    $video_preview_filename = rand(1,1000000).'-post-'.$request->file_type ?? 'video';

                                    $video_preview_file = Helper::post_upload_file($request->video_preview_file, $folder_path, $video_preview_filename) ?? Setting::get('post_video_placeholder');

                                    $post_file->video_preview_file = $video_preview_file;

                                }
                                
                            }


                            if(Setting::get('is_watermark_logo_enabled') && Setting::get('watermark_logo')){

                                if($request->file_type == FILE_TYPE_IMAGE){

                                    $storage_file_path = public_path("storage/".$folder_path.get_video_end($post_file_url)); 
                   
                                    CommonRepo::add_watermark_to_image($storage_file_path);
                                 }


                               if($request->file_type == FILE_TYPE_VIDEO){

                                $video_file = public_path("storage/".$folder_path.get_video_end($post_file_url)); 
                            
                                $new_video_path = public_path("storage/".$folder_path."water-".get_video_end($post_file_url)); 
        
                                $job_data['video'] = $video_file;
        
                                $job_data['watermark_video'] = $new_video_path;
                
                                $this->dispatch(new \App\Jobs\VideoWatermarkPositionJob($job_data));
        
                              }
                           }           

                            $post_file->save();

                            if($post_file->file_type == FILE_TYPE_IMAGE) {
                                $job_data['post_file_id'] = $post_file->id;

                                $this->dispatch(new \App\Jobs\PostBlurFile($job_data));
                            }

                        }
                    }

                }

                if($request->post_id && $request->file_type == FILE_TYPE_VIDEO){

                    $post_file_ids = PostFile::where('post_id',$post->id)->pluck('id');

                    if(!empty($post_file_ids)){

                        $folder_path = POST_PATH.$request->user_id.'/';

                        if ($request->has('preview_file')) {

                            $preview_filename = rand(1,1000000).'-post-'.$request->file_type ?? 'image';

                            $preview_file = Helper::post_upload_file($request->preview_file, $folder_path, $preview_filename) ?? Setting::get('post_video_placeholder');

                            PostFile::whereIn('id',$post_file_ids)->update(['preview_file' => $preview_file]);
                        }

                        if ($request->has('video_preview_file')) {

                            $video_preview_filename = rand(1,1000000).'-post-'.$request->file_type ?? 'video';

                            $video_preview_file = Helper::post_upload_file($request->video_preview_file, $folder_path, $video_preview_filename) ?? Setting::get('post_video_placeholder');

                            PostFile::whereIn('id',$post_file_ids)->update(['video_preview_file' => $video_preview_file]);

                        }

                    }
    
                }

                if ($data) {

                    $hashtags = array_filter($data);

                    foreach ($hashtags as $key => $value) {

                        $hashtag = Hashtag::updateOrCreate(['name' => $value],['count' => \DB::raw('count + 1')]);

                        $post_hashtag_values[] = ['user_id' => $post->user_id,
                        'post_id' => $post->id,
                        'hashtag_id' => $hashtag->id,
                        ];

                    }

                    if ($post_hashtag_values) {

                        PostHashtag::where('post_id',$post->id)->delete();

                        $post_hashtags = PostHashtag::insert($post_hashtag_values);

                    }
                }

                if($request->category_ids) {
                    
                    $category_ids = $request->category_ids;
                    
                    if(!is_array($category_ids)) {

                        $category_ids = explode(',', $category_ids);
                        
                    }

                    if($request->user_id) {
                    
                        CategoryDetail::where('post_id', $request->post_id)->where('type', CATEGORY_TYPE_POST)->delete();
                    }                    


                    foreach ($category_ids as $key => $value) {

                        $post_category = new CategoryDetail;

                        $post_category->user_id = $post->user_id;
                        
                        $post_category->post_id = $post->id;
                        
                        $post_category->category_id = $value;

                        $post_category->type = CATEGORY_TYPE_POST;

                        $post_category->status = APPROVED;
                        
                        $post_category->save();

                    } 
                }

                DB::commit(); 

                return redirect()->route('admin.posts.view',['post_id'=>$post->id])->with('flash_success', $message);

            } 

            throw new Exception(tr('post_save_failed'));

        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method posts_edit()
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
    public function posts_edit(Request $request) {

        try {

            $post = \App\Models\Post::find($request->post_id);

            $post_files = PostFile::where('post_id', $request->post_id)->get();

            if(!$post) { 

                throw new Exception(tr('post_not_found'), 101);
            }
            
            $users = \App\Models\User::Approved()->get();

            $post_category_details = CategoryDetail::where('post_id',$post->id)->where('type', CATEGORY_TYPE_POST)->pluck('category_id')->toArray();

            $categories = Category::APPROVED()->get();

            return view('admin.posts.edit')
                        ->with('page', 'posts')
                        ->with('sub_page', 'posts-view')
                        ->with('users', $users)
                        ->with('post', $post)
                        ->with('categories',$categories)
                        ->with('post_category_details',$post_category_details)
                        ->with('post_files', $post_files);

        } catch(Exception $e) {

            return redirect()->route('admin.posts.index')->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method posts_view()
     *
     * @uses displays the specified posts details based on post id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - post Id
     * 
     * @return View page
     *
     */
    public function posts_view(Request $request) {

        try {

            $post = \App\Models\Post::find($request->post_id);
            
            if(!$post) { 

                throw new Exception(tr('post_not_found'), 101);                
            }

            $payment_data = new \stdClass;

            $payment_data->total_earnings = \App\Models\PostPayment::where('post_id',$request->post_id)->sum(Setting::get('is_only_wallet_payment') ? 'token' : 'paid_amount');

            $payment_data->current_month_earnings = \App\Models\PostPayment::where('post_id',$request->post_id)->whereMonth('paid_date',date('m'))->sum(Setting::get('is_only_wallet_payment') ? 'token' : 'paid_amount');

            $payment_data->today_earnings = \App\Models\PostPayment::where('post_id',$request->post_id)->whereDate('paid_date',today())->sum(Setting::get('is_only_wallet_payment') ? 'token' : 'paid_amount');

            $payment_data->likes = PostLike::where('post_id',$request->post_id)->count();

            $post_files = \App\Models\PostFile::where('post_id',$request->post_id)->get() ?? [];

            $post_category_details = CategoryDetail::where('post_id',$request->post_id)->where('type', CATEGORY_TYPE_POST)->pluck('category_id')->unique();

            $categories = Category::whereIn('id',$post_category_details)->pluck('name');

            $payment_data->total_bookmarks = PostBookmark::where('post_id',$post->id)->count();
            
            return view('admin.posts.view')
                    ->with('page', 'posts') 
                    ->with('sub_page','posts-view') 
                    ->with('post', $post)
                    ->with('post_files', $post_files)
                    ->with('payment_data',$payment_data)
                    ->with('categories',$categories);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method posts_delete()
     *
     * @uses delete the post details based on post id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - Post Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function posts_delete(Request $request) {

        try {

            DB::begintransaction();

            $post = \App\Models\Post::find($request->post_id);

            if(!$post) {

                throw new Exception(tr('post_not_found'), 101);                
            }

            PostHashtag::where('post_id',$post->id)->delete();

            if($post->delete()) {

                DB::commit();

                $email_data['subject'] = tr('post_delete_email' , Setting::get('site_name'));

                $email_data['status'] = tr('deleted');

                $email_data['email']  = $post->user->email ?? "-";

                $email_data['name']  = $post->user->name ?? "-";

                $email_data['post_unique_id']  = $post->unique_id;

                $email_data['page'] = "emails.posts.status";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                if($request->page){
                    
                    return redirect()->route('admin.posts.index', ['page'=>$request->page])->with('flash_success', tr('post_deleted_success'));

                } else {

                    return redirect()->route('admin.posts.index')->with('flash_success', tr('post_deleted_success'));
                }

            } 

            throw new Exception(tr('post_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       

    }



    /**
     * @method posts_dashboard()
     *
     * @uses displays the specified posts dashboard based on post id
     *
     * @created Sakthi 
     *
     * @updated 
     *
     * @param object $request - post Id
     * 
     * @return View page
     *
     */
    public function posts_dashboard(Request $request) {

        try {

            $post = \App\Models\Post::find($request->post_id);

            if(!$post) { 

                throw new Exception(tr('post_not_found'), 101);                
            }

            $payment_data = new \stdClass;

            $data = new \stdClass;

            if(Setting::get('is_only_wallet_payment')) {

                $payment_data->total_earnings = \App\Models\PostPayment::where('post_id',$request->post_id)->sum('token');

                $payment_data->today_earnings = \App\Models\PostPayment::where('post_id',$request->post_id)->whereDate('paid_date',today())->sum('token');

                $payment_data->tips_earnings = \App\Models\UserTip::where('post_id',$request->post_id)->sum('token');

                $payment_data->today_tips_earnings = \App\Models\UserTip::where('post_id',$request->post_id)->sum('token');

            } else{

                $payment_data->total_earnings = \App\Models\PostPayment::where('post_id',$request->post_id)->sum('paid_amount');

                $payment_data->today_earnings = \App\Models\PostPayment::where('post_id',$request->post_id)->whereDate('paid_date',today())->sum('paid_amount');

                $payment_data->tips_earnings = \App\Models\UserTip::where('post_id',$request->post_id)->sum('amount');

                $payment_data->today_tips_earnings = \App\Models\UserTip::where('post_id',$request->post_id)->whereDate('paid_date',today())->sum('token');

            }

            $payment_data->total_post_earnings = $payment_data->total_earnings + $payment_data->tips_earnings;

            $number_of_tips = \App\Models\UserTip::where('post_id',$request->post_id)->count();

            $data->recent_comments = \App\Models\PostComment::where('post_id',$request->post_id)->orderBy('post_comments.created_at', 'desc')->get();

            return view('admin.posts.dashboard')
                        ->with('page', 'posts') 
                        ->with('sub_page','posts-view') 
                        ->with('post', $post)
                        ->with('number_of_tips', $number_of_tips)
                        ->with('data',$data)
                        ->with('payment_data',$payment_data);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method posts_status
     *
     * @uses To update post status as DECLINED/APPROVED based on posts id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Post Id
     * 
     * @return response success/failure message
     *
     **/
    public function posts_status(Request $request) {

        try {

            DB::beginTransaction();

            $post = \App\Models\Post::find($request->post_id);

            if(!$post) {

                throw new Exception(tr('post_not_found'), 101);

            }

            $post->status = $post->status ? DECLINED : APPROVED ;

            if($post->save()) {

                DB::commit();

                if($post->status == DECLINED) {

                    $email_data['subject'] = tr('post_decline_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('declined');

                } else {

                    $email_data['subject'] = tr('post_approve_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('approved');
                }

                $email_data['email']  = $post->user->email ?? "-";

                $email_data['name']  = $post->user->name ?? "-";

                $email_data['post_unique_id']  = $post->unique_id;

                $email_data['page'] = "emails.posts.status";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                $message = $post->status ? tr('post_approve_success') : tr('post_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }

            throw new Exception(tr('post_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.posts.index')->with('flash_error', $e->getMessage());

        }

    }


    /**
     * @method posts_publish
     *
     * @uses To publish the scheduled post
     *
     * @created sakthi
     *
     * @updated 
     *
     * @param object $request - Post Id
     * 
     * @return response success/failure message
     *
     **/
    public function posts_publish(Request $request) {

        try {

            DB::beginTransaction();

            $post = \App\Models\Post::find($request->post_id);

            if(!$post) {

                throw new Exception(tr('post_not_found'), 101);

            }

            $post->is_published = YES ;
            
            $post->publish_time = date('Y-m-d H:i:s');

            if($post->save()) {

                DB::commit();

                return redirect()->back()->with('flash_success', tr('posts_publish_success'));
            }

            throw new Exception(tr('post_publish_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.posts.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method post_albums_index()
     *
     * @uses Display the total posts albums index
     *
     * @created Akshata
     *
     * @updated
     *
     * @param -
     *
     * @return view page 
     */
    public function post_albums_index(Request $request) {

        $post_albums = \App\Models\PostAlbum::orderBy('created_at','DESC')->paginate($this->take);

        return view('admin.post_albums.index')
                    ->with('page','post_albums')
                    ->with('post_albums', $post_albums);
    }

    /**
     * @method post_albums_view()
     *
     * @uses displays the specified post album details based on post album id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - Post Album Id
     * 
     * @return View page
     *
     */
    public function post_albums_view(Request $request) {

        try {

            $post_album = \App\Models\PostAlbum::find($request->post_album_id);

            if(!$post_album) {

                throw new Exception(tr('post_album_not_found'), 101);
            }

            $post_ids = explode(',', $post_album->post_ids);

            $posts = \App\Models\Post::whereIn('posts.id', $post_ids)->get();

            return view('admin.post_albums.view')
                        ->with('page', 'post_albums') 
                        ->with('post_album' , $post_album)
                        ->with('posts',$posts);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method post_albums_delete()
     *
     * @uses delete the post album details based on post album id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - Post Album Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function post_albums_delete(Request $request) {

        try {

            DB::begintransaction();

            $post_album = \App\Models\Post::find($request->post_album_id);

            if(!$post_album) {

                throw new Exception(tr('post_album_not_found'), 101);                
            }

            if($post_album->delete()) {

                DB::commit();

                return redirect()->route('admin.post_albums.index')->with('flash_success',tr('post_album_deleted_success'));   

            } 

            throw new Exception(tr('post_album_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       

    }

    /**
     * @method post_albums_status
     *
     * @uses To update post album status as DECLINED/APPROVED based on posts id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Post Album Id
     * 
     * @return response success/failure message
     *
     **/
    public function post_albums_status(Request $request) {

        try {

            DB::beginTransaction();

            $post_album = \App\Models\PostAlbum::find($request->post_album_id);

            if(!$post_album) {

                throw new Exception(tr('post_album_not_found'), 101);

            }

            $post_album->status = $post_album->status ? DECLINED : APPROVED ;

            if($post_album->save()) {

                DB::commit();

                $message = $post_album->status ? tr('post_album_approve_success') : tr('post_album_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }

            throw new Exception(tr('post_album_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.post_albums.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method orders_index
     *
     * @uses Display list of orders
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Order Id
     * 
     * @return response success/failure message
     *
     **/
    public function orders_index(Request $request) {

        $base_query = \App\Models\Order::where('unique_id','!=',NULL);

        if($request->status) {

            $base_query = $base_query->where('orders.status', $request->status);

            if($request->status == SORT_BY_ORDER_PLACED){
                $base_query = $base_query->orWhere('orders.status',0);
            }
        }

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query
                            ->whereHas('user',function($query) use($search_key) {

                                return $query->where('users.name','LIKE','%'.$search_key.'%');

                            })->orWhereHas('deliveryAddressDetails',function($query) use($search_key){

                                return $query->where('delivery_addresses.name','LIKE','%'.$search_key.'%');
                            })->orWhere('orders.unique_id','LIKE','%'.$search_key.'%'); 
        }

        if($request->user_id) {

            $base_query  = $base_query->where('user_id',$request->user_id);
        }

        $sub_page = 'orders-view';

        if($request->new_orders) {

            $base_query  = $base_query->latest('created_at');

            $sub_page = 'orders-new';
        }

        $orders = $base_query->paginate($this->take);

        $user = User::find($request->user_id);

        return view('admin.orders.index')
                    ->with('page','orders')
                    ->with('sub_page',$sub_page)
                    ->with('user', $user)
                    ->with('orders',$orders);
    }


    /**
     * @method orders_view
     *
     * @uses Display the specified order details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Order Id
     * 
     * @return response success/failure message
     *
     **/

    public function orders_view(Request $request) {

        try {

            $order = \App\Models\Order::where('id',$request->order_id)->first();

            if(!$order) {

                throw new Exception(tr('order_not_found'), 1);

            }

            $order_products = \App\Models\OrderProduct::where('order_id',$order->id)->get();

            $order_payment = \App\Models\OrderPayment::where('order_id',$order->id)->first();

            $order = \App\Models\Order::firstWhere('id',$request->order_id);

            return view('admin.orders.view')
                        ->with('page','orders')
                        ->with('sub_page','orders-view')
                        ->with('order', $order)
                        ->with('order_products', $order_products)
                        ->with('order_payment', $order_payment)
                        ->with('order', $order);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error',$e->getMessage());
        }
    }

    /**
     * @method delivery_address_index
     *
     * @uses Display list of all the delivery address
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function delivery_address_index(Request $request) {

        $base_query = \App\Models\DeliveryAddress::where('status',APPROVED);

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('user',function($query) use($search_key){

                return $query->where('users.name','LIKE','%'.$search_key.'%');

            })->orWhere('delivery_addresses.name','LIKE','%'.$search_key.'%')

            ->orWhere('delivery_addresses.state','LIKE','%'.$search_key.'%'); 
        }


        $user = \App\Models\User::find($request->user_id) ?? '';

        if($request->user_id) {

            $base_query = $base_query->where('user_id',$request->user_id);
        }

        $delivery_addresses = $base_query->paginate($this->take);

        return view('admin.delivery_address.index')
                    ->with('page','delivery-address')
                    ->with('user',$user)
                    ->with('delivery_addresses',$delivery_addresses);
    }

    /**
     * @method delivery_address_view
     *
     * @uses Display the specified delivery address details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Delivery Address Id
     * 
     * @return response success/failure message
     *
     **/

    public function delivery_address_view(Request $request) {

        try {

            $delivery_address = \App\Models\DeliveryAddress::where('id',$request->delivery_address_id)->first();

            if(!$delivery_address) {

                throw new Exception(tr('delvery_address_details_not_found'), 101);

            }

            return view('admin.delivery_address.view')
                    ->with('page','delivery-address')
                    ->with('delivery_address_details',$delivery_address);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error',$e->getMessage());
        }
    }


    /**
     * @method delivery_address_delete
     *
     * @uses Display list of all the delivery address
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param $object delivery_address_id
     * 
     * @return response success/failure message
     *
     **/

    public function delivery_address_delete(Request $request) {

        try {
            
            DB::begintransaction();

            $delivery_address = \App\Models\DeliveryAddress::find($request->delivery_address_id);

            if(!$delivery_address) {

                throw new Exception(tr('delivery_address_details_not_found'), 101);                
            }

            if($delivery_address->delete()) {

                DB::commit();

                return redirect()->route('admin.delivery_address.index',['user_id'=>$delivery_address->user_id,'page'=>$request->page])->with('flash_success',tr('delivery_address_deleted_success'));   

            } 

            throw new Exception(tr('delivery_address_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       

    }

    /**
     * @method Bookmarks_index
     *
     * @uses Display list of all the bookmarks
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function post_bookmarks_index(Request $request) {

        $base_query = \App\Models\PostBookmark::Approved()->orderBy('post_bookmarks.created_at', 'desc');

        if($request->user_id) {

            $base_query = $base_query->where('user_id',$request->user_id);
        }

        if($request->search_key) {
            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('post',function($query) use($search_key){

                return $query->where('posts.content','LIKE','%'.$search_key.'%')->orWhere('posts.unique_id','LIKE','%'.$search_key.'%');

            });

        }

        $user = \App\Models\User::find($request->user_id) ?? '';

        $post_bookmarks = $base_query->paginate($this->take);

        return view('admin.bookmarks.index')
                    ->with('page','post_bookmarks')
                    ->with('sub_page','users-view')
                    ->with('user',$user)
                    ->with('post_bookmarks',$post_bookmarks);
    }

    /**
     * @method bookmarks_delete
     *
     * @uses Display list of all the bookmarks
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function post_bookmarks_delete(Request $request) {

        try {

            DB::begintransaction();

            $post_bookmark = \App\Models\PostBookmark::find($request->post_bookmark_id);

            if(!$post_bookmark) {

                throw new Exception(tr('post_bookmark_not_found'), 101);                
            }

            $post_bookmark->where('user_id',$request->user_id);

            if($post_bookmark->delete()) {

                DB::commit();

                return redirect()->route('admin.bookmarks.index',['page'=>$request->page,'user_id'=>$post_bookmark->user_id])->with('flash_success',tr('bookmark_deleted_success'));   

            } 

            throw new Exception(tr('bookmark_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       

    }

    /**
     * @method bookmarks_view
     *
     * @uses view the bookmark
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function post_bookmarks_view(Request $request) {

        try {

            $post_bookmark = \App\Models\PostBookmark::where('id', $request->post_bookmark_id)->where('user_id',$request->user_id)->first();

            if(!$post_bookmark) {

                throw new Exception(tr('bookmark_details_not_found'), 101);

            }

            return view('admin.bookmarks.view')
                    ->with('page','bookmarks')
                    ->with('post_bookmark', $post_bookmark);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error',$e->getMessage());
        }

    }       



    /**
     * @method post_comments
     *
     * @uses List of comments for particular post
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function post_comments(Request $request) {

        $base_query = \App\Models\PostComment::Approved()->orderBy('post_comments.created_at', 'desc');

        if($request->post_id){

            $base_query->where('post_comments.post_id',  $request->post_id);

        }

        if($request->search_key) {

            $search_key = $request->search_key;

            $post_comment_ids = \App\Models\PostComment::whereHas('user',function($query) use($search_key){

                return $query->where('users.name','LIKE','%'.$search_key.'%')->orWhere('users.username','LIKE','%'.$search_key.'%');

            })->orWhere('post_comments.comment','LIKE','%'.$search_key.'%')->pluck('id');

            $base_query = $base_query->whereIn('id',$post_comment_ids);

        }
        
        $post = Post::find($request->post_id)??'';

        $post_comments = $base_query->paginate($this->take);

        return view('admin.posts.comments')
                ->with('page', 'posts')
                ->with('sub_page', 'posts-view')
                ->with('post', $post)
                ->with('post_comments', $post_comments);

      
    }

    /**
     * @method post_comment_delete
     *
     * @uses delete particular comment
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function post_comment_delete(Request $request) {

        try {

            DB::begintransaction();

            $post_comment = \App\Models\PostComment::find($request->comment_id);

            if(!$post_comment) {

                throw new Exception(tr('post_comment_not_found'), 101);                
            }

            $post_comment->where('post_id',$request->post_id);

            if($post_comment->delete()) {

                DB::commit();

                return redirect()->back()->with('flash_success',tr('post_comment_deleted'));   

            } 

            throw new Exception(tr('post_comment_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }   
    }

    /**
     * @method fav_users
     *
     * @uses List of fav users
     *
     * @created Sakthi
     *
     * @updated Vidhya R
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function fav_users(Request $request) {

        $base_query = \App\Models\FavUser::Approved()->orderBy('fav_users.created_at', 'desc');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('favUser',function($query) use($search_key){

                return $query->where('users.name','LIKE','%'.$search_key.'%');

            });

        }

        $user = \App\Models\User::find($request->user_id)??'';

        if($request->user_id) {

            $base_query->where('user_id', $request->user_id);
        }

        $fav_users = $base_query->paginate($this->take);

        return view('admin.fav_users.index')
                    ->with('page','fav_users')
                    ->with('user',$user)
                    ->with('fav_users',$fav_users);
    }

    /**
     * @method fav_users_delete
     *
     * @uses List of fav users
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function fav_users_delete(Request $request) {

        try {

            DB::begintransaction();

            $fav_user = \App\Models\FavUser::where('fav_user_id', $request->fav_user_id)->where('user_id', $request->user_id)->first();

            if(!$fav_user) {

                throw new Exception(tr('fav_user_not_found'), 101);                
            }

            $fav_user->where('fav_user_id',$request->user_id);

            if($fav_user->delete()) {

                DB::commit();

                return redirect()->back()->with('flash_success',tr('fav_user_deleted'));   

            } 

            throw new Exception(tr('fav_user_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }   
    }

    /**
     * @method post_likes
     *
     * @uses List of liked post for users
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function post_likes(Request $request) {

        $base_query = \App\Models\PostLike::Approved()->orderBy('post_likes.created_at', 'desc');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('postUser',function($query) use($search_key){

                return $query->where('users.name','LIKE','%'.$search_key.'%');

            });
        }

        $user = \App\Models\User::find($request->user_id) ?? '';

        if($request->user_id){

            $base_query->where('user_id', $request->user_id);
        }

        $post_likes = $base_query->paginate($this->take);

        return view('admin.post_likes.index')
                    ->with('page','post_likes')
                    ->with('user_id',$request->user_id)
                    ->with('user',$user)
                    ->with('post_likes',$post_likes); 
     }


    /**
     * @method post_likes_delete
     *
     * @uses remove liked post
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function post_likes_delete(Request $request) {

        try {

            DB::begintransaction();

            $post_likes = \App\Models\PostLike::find($request->post_like_id);

            if(!$post_likes) {

                throw new Exception(tr('post_not_found'), 101);                
            }

            $post_likes->where('user_id',$request->user_id);

            if($post_likes->delete()) {

                DB::commit();

                return redirect()->route('admin.post_likes.index',['user_id'=>$request->user_id ?? '','page'=>$request->page])->with('flash_success',tr('like_post_deleted'));   

            } 

            throw new Exception(tr('like_post_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }   
    }


    /**
     * @method posts_bulk_action()
     * 
     * @uses To delete,approve,decline multiple posts
     *
     * @created Ganesh
     *
     * @updated 
     *
     * @param 
     *
     * @return success/failure message
     */
    public function posts_bulk_action(Request $request) {

        try {
            
            $action_name = $request->action_name ;

            $post_ids = explode(',', $request->selected_posts);

            if (!$post_ids && !$action_name) {

                throw new Exception(tr('posts_action_is_empty'));

            }

            DB::beginTransaction();

            if($action_name == 'bulk_delete'){

                $post = \App\Models\Post::whereIn('id', $post_ids)->delete();

                if ($post) {

                    DB::commit();

                    return redirect()->back()->with('flash_success',tr('admin_posts_delete_success'));

                }

                throw new Exception(tr('posts_delete_failed'));

            }elseif($action_name == 'bulk_approve'){

                $post =  \App\Models\Post::whereIn('id', $post_ids)->update(['status' => APPROVED]);

                if ($post) {

                    DB::commit();

                    return back()->with('flash_success',tr('admin_posts_approve_success'))->with('bulk_action','true');
                }

                throw new Exception(tr('posts_approve_failed'));  

            }elseif($action_name == 'bulk_decline'){
                
                $post =  \App\Models\Post::whereIn('id', $post_ids)->update(['status' => DECLINED]);

                if ($post) {
                    
                    DB::commit();

                    return back()->with('flash_success',tr('admin_posts_decline_success'))->with('bulk_action','true');
                }

                throw new Exception(tr('posts_decline_failed')); 
            }

        }catch( Exception $e) {

            DB::rollback();

            return redirect()->back()->with('flash_error',$e->getMessage());
        }

    }



    /**
     * @method post_payments_send_invoice
     *
     * @uses to send user invoice request details based on request id
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return view page
     *
     **/
    
    public function post_payments_send_invoice(Request $request) {

        try {

            $post_payment = \App\Models\PostPayment::where('id', $request->post_payment_id)->first();

            if(!$post_payment) {

                throw new Exception(tr('post_payment_not_found'), 101);
            }

            $user = \App\Models\User::find($post_payment->user_id);

            if(!$user) {

                throw new Exception(tr('user_not_found'), 101);
            }

            $email_data = [];

            $email_data['timezone'] =  Auth::guard('admin')->user()->timezone ?? "";
           
            $email_data['post_payments'] =  $post_payment ?? "";

            $email_data['user'] = $user ?? '';

            $email_data['posts'] = $post_payment->postDetails ?? '';

            $email_data['subject'] =  tr('post_invoice_message')." ".Setting::get('site_name');

            $email_data['page'] = "emails.users.invoice";

            $email_data['email'] = $user->email ?? '';

            $email_data['data'] = $email_data;

            $email_data['filename'] = 'Invoice'.date('m-d-Y_giA').'.pdf';

            $email_data['is_invoice'] = 1;

            // Log::info("Timezone".print_r($email_data['timezone'], true));

            $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

            return redirect()->back()->with('flash_success',tr('invoice_mail_sent_success'));

        } catch(Exception $e) {

            return redirect()->route('admin.post_payments.index')->with('flash_error', $e->getMessage());

        }

    }


    /**
     * @method report_posts_index()
     *
     * @uses To list out reported posts by the users
     *
     * @created Ganesh
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function report_posts_index(Request $request) {

        $base_query = \App\Models\ReportPost::select('report_posts.*', DB::raw('count(`post_id`) as report_user_count'))
                      ->has('post')->groupBy('post_id')->orderBy('created_at','DESC');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query =  $base_query

            ->whereHas('post', function($q) use ($search_key) {

                return $q->Where('posts.unique_id','LIKE','%'.$search_key.'%');

            });

        }

        $report_posts = $base_query->paginate($this->take);

        $title = tr('report_posts');

        return view('admin.posts.report_posts.index')
                    ->with('page','posts')
                    ->with('sub_page', 'report-posts')
                    ->with('title', $title)
                    ->with('report_posts', $report_posts);
    
    }


    /**
     * @method report_posts_view()
     *
     * @uses To list out report posts
     *
     * @created Ganesh
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function report_posts_view(Request $request) {

     try {

        if(!$request->post_id){

            throw new Exception(tr('post_not_found'), 101);
        }

        $base_query = \App\Models\ReportPost::where('post_id',$request->post_id)->orderBy('created_at','DESC');

        $report_posts = $base_query->paginate($this->take);

        $post = \App\Models\Post::find($request->post_id);

        $title = tr('view_report_posts');

        return view('admin.posts.report_posts.view')
                    ->with('page','posts')
                    ->with('sub_page', 'report-posts')
                    ->with('title', $title)
                    ->with('post', $post)
                    ->with('report_posts', $report_posts);

        } catch (Exception $e) {

            return redirect()->route('admin.report_posts.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method report_posts_delete
     *
     * @uses Delete the report user post
     *
     * @created Ganesh
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function report_posts_delete(Request $request) {

        try {

            DB::begintransaction();


            if($request->post_id){

                \App\Models\ReportPost::where('post_id',$request->post_id)->delete();

                 DB::commit();

                return redirect()->route('admin.report_posts.index')->with('flash_success',tr('report_delete_success'));   

            }


            $report_post = \App\Models\ReportPost::find($request->report_post_id);

            if(!$report_post) {

                throw new Exception(tr('report_post_not_found'), 101);                
            }


            if($report_post->delete()) {

                DB::commit();

                return redirect()->back()->with('flash_success',tr('report_delete_success'));   

            } 

            throw new Exception(tr('report_post_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }   
    }

    /**
     * @method hashtags_index()
     *
     * @uses Display the hashtags list
     *
     * @created Subham
     *
     * @updated
     *
     * @param -
     *
     * @return view page 
     */
    public function hashtags_index(Request $request) {

        $base_query = Hashtag::orderBy('created_at','DESC');

        if(isset($request->status)) {

            $base_query = $base_query->where('hashtags.status', $request->status);
        }

        if($request->search_key) {

            $base_query = $base_query
                    ->where('hashtags.name','LIKE','%'.$request->search_key.'%')
                    ->orWhere('hashtags.unique_id','LIKE','%'.$request->search_key.'%');
        }

        $hashtags = $base_query->paginate($this->take);


        return view('admin.hashtags.index')
                ->with('page', 'hashtags')
                ->with('sub_page', 'hashtags-view')
                ->with('hashtags', $hashtags);
    
    }


    /**
     * @method hashtags_create()
     *
     * @uses create new hashtag
     *
     * @created Subham 
     *
     * @updated 
     *
     * 
     * @return View page
     *
    */
    public function hashtags_create() {

        $hashtag = new Hashtag;

        return view('admin.hashtags.create')
                ->with('page', 'hashtags')
                ->with('sub_page', 'hashtags-create')
                ->with('hashtag', $hashtag);  

    }




    /**
     * @method hashtags_save()
     *
     * @uses save new hashtag
     *
     * @created Subham 
     *
     * @updated 
     *
     * 
     * @return View page
     *
    */
    public function hashtags_save(Request $request) {
        
        try {
            
            DB::begintransaction();

            $name = str_replace('#','',$request->title);
            
            $request->request->add(['name' => $name]);

            $rules = [
                'name' => $request->hashtag_id ? 'nullable|max:255|unique:hashtags,name,'.$request->hashtag_id.',id' : 'required|max:255|unique:hashtags,name,NULL,id',
                'description' => 'required',
            ];

            $custom_errors = ['name.unique'=>'The Hashtag has already created'];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $hashtag = Hashtag::find($request->hashtag_id) ?? new Hashtag;

            $hashtag->name = $request->name ?: $hashtag->name;

            $hashtag->description = $request->description ?: $hashtag->description;

            if($hashtag->save()){
                
                DB::commit(); 

                return redirect()->route('admin.hashtags.index')->with('flash_success', tr('hashtag_create_success'));
          
            }

            throw new Exception(tr('hashtag_save_failed'));

        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }
    

    /**
     * @method hashtags_view()
     *
     * @uses view the hashtag details based on id
     *
     * @created Jeevan 
     *
     * @updated 
     *
     * @param object $request - hashtag_id
     * 
     * @return View page
     *
     */
    public function hashtags_view(Request $request) {
       
        try {

            $hashtag = Hashtag::find($request->hashtag_id);
      
            if(!$hashtag ){
    
                throw new Exception(tr('hashtag_not_found'), 101);
            } 

            return view('admin.hashtags.view')
                        ->with('page', 'hashtags')
                        ->with('sub_page', 'hashtags-view')
                        ->with('hashtag', $hashtag);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

     /**
     * @method hashtags_status_change
     *
     * @uses To update hashtag status as DECLINED/APPROVED based on hashtag id
     *
     * @created Subham
     *
     * @updated 
     *
     * @param object $request - HashTag Id
     * 
     * @return response success/failure message
     *
     **/
    public function hashtags_status_change(Request $request) {

        try {

            DB::beginTransaction();

            $hashtag = Hashtag::find($request->hashtag_id);

            if(!$hashtag) {

                throw new Exception(tr('hashtag_not_found'), 101);

            }

            $hashtag->status = $hashtag->status ? DECLINED : APPROVED;

            if($hashtag->save()) {

                DB::commit();
              
                $message = $hashtag->status ? tr('hashtag_approve_success') : tr('hashtag_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }

            throw new Exception(tr('hashtag_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.hashtags.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method hashtags_delete()
     *
     * @uses delete the hashtags based on post id
     *
     * @created Subham 
     *
     * @updated  
     *
     * @param object $request - Post Album Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function hashtags_delete(Request $request) {

        try {

            DB::begintransaction();

            $hashtags_delete = Hashtag::find($request->hashtag_id);

            if(!$hashtags_delete) {

                throw new Exception(tr('hashtag_not_found'), 101);                
            }

            if($hashtags_delete->delete()) {

                DB::commit();

                return redirect()->route('admin.hashtags.index')->with('flash_success',tr('hashtag_deleted_success'));   

            } 

            throw new Exception(tr('hashtag_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       

    }

    /**
     * @method posts_file_delete
     *
     * @uses delete particular file ajax
     *
     * @created Subham Kant
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function posts_file_delete(Request $request) {

        try {

            DB::begintransaction();

            $post = \App\Models\Post::find($request->post_id);

            $post_files = PostFile::where('post_id', $request->post_id)->pluck('id')->toArray();

            if(!$post) { 

                throw new Exception(tr('post_not_found'), 101);
            }

            if(!in_array($request->post_file_id,$post_files)) { 

                throw new Exception(tr('post_file_not_found'), 101);
            }

            $post_file = PostFile::where('id',$request->post_file_id)->first();

            if($post_file->delete()) {

                DB::commit();

                return $this->sendResponse(api_success(152), 152, $data = []);   

            } 

            throw new Exception(tr('post_comment_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());


            // return redirect()->back()->with('flash_error', $e->getMessage());

        }   
    }

}
