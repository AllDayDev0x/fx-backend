<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Models\User, App\Models\Post, App\Models\PostFile, App\Models\CategoryDetail;

use App\Models\Hashtag, App\Models\PostHashtag, App\Models\Category;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Repositories\CommonRepository as CommonRepo;

use App\Models\PostCommentLike, App\Models\PostComment, App\Models\PostCommentReply, App\Models\PromoCode, App\Models\ReportReason;

use App\Jobs\PostCommentLikeJob, App\Jobs\PostCommentReplyJob, App\Jobs\VideoThumbnailJob;

use Carbon\Carbon;

class PostsApiController extends Controller
{
    protected $loginUser;

    protected $skip, $take;

    public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /**
     * @method home()
     *
     * @uses To display all the posts
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function home(Request $request) {

        try {

            $follower_ids = get_follower_ids($request->id);

            $report_posts = report_posts($request->id);

            $blocked_users = blocked_users($request->id);

            $base_query = $total_query = Post::Approved()->whereNotIn('posts.user_id',$blocked_users)->whereNotIn('posts.id',$report_posts)->whereHas('user')->whereIn('posts.user_id', $follower_ids)->orderBy('posts.created_at', 'desc');

            $data['total'] = $total_query->count() ?? 0;

            $posts = $base_query->skip($this->skip)->take($this->take)->get();

            $posts = \App\Repositories\PostRepository::posts_list_response($posts, $request);

            $data['posts'] = $posts ?? [];

            $data['user'] = $this->loginUser;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method posts_search()
     *
     * @uses To display all the posts
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function posts_search(Request $request) {

        try {

            // Validation start
            $rules = ['user_unique_id' => 'required|exists:users,unique_id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\Models\User::where('users.unique_id', $request->user_unique_id)->first();

            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $report_post_ids = report_posts($request->id);

            $base_query = $total_query = \App\Models\Post::with('postFiles')->whereNotIn('posts.id',$report_post_ids)->whereHas('user')->where('user_id', $user->id);

            if($request->search_key) {

                $base_query = $base_query->where('posts.content','LIKE','%'.$request->search_key.'%');
                                   
            }

            $posts = $base_query->skip($this->skip)->take($this->take)->get();

            $posts = \App\Repositories\PostRepository::posts_list_response($posts, $request);

            $data['posts'] = $posts ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method posts_view_for_others()
     *
     * @uses get the selected post details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function posts_view_for_others(Request $request) {

        try {

            $rules = ['post_unique_id' => 'required|exists:posts,unique_id'];

            Helper::custom_validator($request->all(),$rules);

            $report_posts = report_posts($request->id);
            
            $blocked_users = blocked_users($request->id);
            
            $post = Post::with('postFiles')->Approved()
                ->whereNotIn('posts.user_id',$blocked_users)
                ->whereNotIn('posts.id',$report_posts)
                ->where('posts.unique_id', $request->post_unique_id)->first();

            if(!$post) {
                throw new Exception(api_error(139), 139);   
            }

            $post = \App\Repositories\PostRepository::posts_single_response($post, $request);

            $data['post'] = $post;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method posts_for_owner()
     *
     * @uses To display all the posts
     *
     * @created Vithya R
     *
     * @updated Subham Kant
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function posts_for_owner(Request $request) {

        try {

            $base_query = $total_query = Post::where('user_id', $request->id)->with('postFiles');

            switch ($request->sort) {

                case SORT_BY_ASC:
                    $base_query = $base_query->orderBy('created_at','asc');
                    break;

                case SORT_BY_CONTENT_ASC:
                    $base_query = $base_query->orderBy('content','asc');
                    break;

                case SORT_BY_CONTENT_DESC:
                    $base_query = $base_query->orderBy('content','desc');
                    break;
               
                default:
                    $base_query = $base_query->orderBy('created_at','desc');
                    
                    break;
            }

            if($request->type != POSTS_ALL) {

                $type = $request->type;

                if($type)

                    $base_query = $base_query->whereHas('postFiles', function($q) use($type) {
                        $q->where('post_files.file_type', $type);
                    });
            }

            $data['total'] = $total_query->count() ?? 0;

            $posts = $base_query->skip($this->skip)->take($this->take)->get();

            $posts = \App\Repositories\PostRepository::posts_list_response($posts, $request);

            $data['total'] = $total_query->count() ?? 0;

            $data['posts'] = $posts ?? [];

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method posts_view_for_owner()
     *
     * @uses get the selected post details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function posts_view_for_owner(Request $request) {

        try {

            $rules = [
                'post_id' => 'required|exists:posts,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules);

            $post = Post::with('postFiles')->find($request->post_id);

            if(!$post) {
                throw new Exception(api_error(139), 139);   
            }

            $data['post'] = $post;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

     /**
     * @method posts_save_for_owner()
     *
     * @uses get the selected post details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function posts_save_for_owner(Request $request) {

        try {

            DB::begintransaction();
            
            $rules = [
                'content' => 'required',
                'publish_time' => 'nullable',
                'amount' => 'nullable|numeric|min:1',
                'post_files' => 'nullable',
                'post_file_id' => 'required',
                // 'post_file_id' => 'required_if:post_files,id|exists:post_files,id',
                'post_id' => 'nullable|exists:posts,id',
            ];

            Helper::custom_validator($request->all(),$rules);


            if(!$request->post_file_id) {

                $formatted_text = str_replace(['<p>', '</p>'], '', $request->content);

                if($formatted_text == ''){
                    
                    throw new Exception(api_error(255), 255);
                }
            
            }
            

            $user = User::find($request->id);

            if(!$user->is_content_creator == CONTENT_CREATOR) {

                    throw new Exception(api_error(218), 218);
            }

            $post = Post::find($request->post_id) ?? new Post;

            $success_code = $post->id ? 131 : 130;

            $post->user_id = $request->id;

            $post->save();

            $content = $post->content = $request->content ?: $post->content;

            $strlen = strlen($content);

            $data = [];

            for ($i=0; $i < $strlen; $i++) { 

                $content = preg_replace('/href=".*?"/', '', $content);

                if (preg_match("/#/", $content)) {

                    $explodedArray = strpos($content,"#")+ strlen('#');

                    $explodedArray = strip_tags(substr($content, $explodedArray));

                    $content = $explodedArray;

                    $data[$i] = preg_replace('/(\s*)([^\s]*)(.*)/', '$2', $explodedArray);

                    $strlen = strlen($content);

                }
                else{
                    $strlen = 0;
                }

            }

            if ($data) {

                $hashtags = array_filter($data);

                foreach ($hashtags as $key => $value) {

                    $hashtag = Hashtag::updateOrCreate(['name' => $value],['count' => \DB::raw('count + 1')]);

                    $post_hashtag_values[] = [
                        'user_id' => $request->id,
                        'post_id' => $post->id,
                        'hashtag_id' => $hashtag->id,
                    ];

                }

                if ($post_hashtag_values) {

                    PostHashtag::where('post_id',$post->id)->delete();

                    $post_hashtags = PostHashtag::insert($post_hashtag_values);

                }
            } 

            $publish_time = $request->publish_time ?: date('Y-m-d H:i:s');

            $post->publish_time = date('Y-m-d H:i:s', strtotime($publish_time));


            if(!$post->content){

                throw new Exception(api_error(180), 180);  
            }

            if($post->save()) {

                if($request->post_file_id) {

                    $files = explode(',', $request->post_file_id);

                    foreach ($files as $key => $post_file_id) {

                        $post_file = PostFile::find($post_file_id);

                        $post_file->post_id = $post->id;

                        if ($request->hasFile('preview_file')) {

                            $folder_path = POST_PATH.$request->id.'/';

                            Helper::storage_delete_file($post_file->preview_file, $folder_path);

                            $post_file->preview_file = Helper::storage_upload_file($request->file('preview_file'), $folder_path) ?? Setting::get('ppv_image_placeholder');
                        }

                        if ($request->hasFile('video_preview_file')) {

                            $folder_path = POST_PATH.$request->id.'/';

                            Helper::storage_delete_file($post_file->video_preview_file, $folder_path);

                            $post_file->video_preview_file = Helper::storage_upload_file($request->file('video_preview_file'), $folder_path);
                        }

                        $post_file->save();

                        if(Setting::get('s3_bucket') != STORAGE_TYPE_S3 && $post_file->file_type == FILE_TYPE_IMAGE) {

                            $job_data['post_file_id'] = $post_file->id;

                            $this->dispatch(new \App\Jobs\PostBlurFile($job_data));
                        }

                    }

                    $amount = $request->amount ?: ($post->amount ?? 0);

                    if(Setting::get('is_only_wallet_payment')) {

                        $post->token = $amount;

                        $post->amount = $post->token * Setting::get('token_amount');

                    } else {

                        $post->amount = $amount;

                    }

                    $post->is_paid_post = $amount > 0 ? YES : NO;

                    $post->save();

                }

                if($request->category_ids) {
                        
                    $category_ids = $request->category_ids;
                    
                    if(!is_array($category_ids)) {

                        $category_ids = explode(',', $category_ids);
                        
                    }

                    if($request->post_id) {
                    
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

                $tagged_users = explode("</a>",$request->content);

                $user = User::find($request->id);

                $job_data['user'] = $user;
            
                $job_data['tagged_users'] = $tagged_users;

                $job_data['post'] = Setting::get('frontend_url')."post/".$post->unique_id;

                $job_data['message'] = $user->name." ".tr('tagged_you');

                $job_data['post_details'] = $post;

                $this->dispatch(new \App\Jobs\SendTagEmailNotificationJob($job_data));

                $data = $post;

                return $this->sendResponse(api_success($success_code), $success_code, $data);

            } 

            throw new Exception(api_error(128), 128);

        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getLine());

        } 
    
    }

    /**
     * @method post_files_upload()
     *
     * @uses get the selected post details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function post_files_upload(Request $request) {

        try {

            $rules = [
                'file' => 'required',
                'file_type' => 'required',
                'post_id' => 'nullable|exists:posts,id'
            ];

            Helper::custom_validator($request->all(),$rules);

            $user = User::find($request->id);

            if(!$user->is_content_creator == CONTENT_CREATOR) {

                throw new Exception(api_error(218), 218);
            }

            $data = $file_urls = $post_file_ids = $post_blur_files = $file_data = [];

            $post_file_id = $post_blur_file = '';

            $file_url = [];

            $files = $request->file;

            if(!$files) {

                throw new Exception(api_error(227), 227);
            }

            if(!is_array($files)) {

               $file = $files;

               $filename = rand(1,1000000).'-post-'.$request->file_type;

               $folder_path = POST_PATH.$request->id.'/';

               $post_file_url = Helper::post_upload_file($file, $folder_path, $filename);

               $ext = $file->getClientOriginalExtension();

               $video_extensions = ['mp4', 'mp3', 'mov', 'flv', 'avi', 'webm', 'mkv'];

               if($post_file_url) {

                    $post_file = new PostFile;

                    $post_file->user_id = $request->id;

                    $post_file->post_id = 0;

                    $post_file->file = $post_file_url;

                    $post_file->file_type = $request->file_type;

                    $post_file->blur_file = $request->file_type == "image" && !in_array($ext, $video_extensions) ? Setting::get('ppv_image_placeholder') : Setting::get('post_video_placeholder');

                    if($request->file_type == 'video') {

                        $filename_img = rand(1,1000000).'-post-image.jpg';

                        $video_thumbnail_data['original_video_path'] = storage_path('app/public/'.$folder_path.$filename.'.'.$ext);

                        $video_thumbnail_data['save_file_path'] = storage_path('app/public/'.$folder_path);

                        $video_thumbnail_data['thumbnail_file_name'] = $filename_img;

                        VideoThumbnailJob::dispatch($video_thumbnail_data);

                        $post_file->preview_file = asset('storage/'.$folder_path.$filename_img);

                        if(Setting::get('is_watermark_logo_enabled') && Setting::get('watermark_logo')){

                            $video_file = public_path("storage/".$folder_path.get_video_end($post_file_url));

                            $new_video_path = public_path("storage/".$folder_path."water-".get_video_end($post_file_url));

                            $job_data['video'] = $video_file;

                            $job_data['watermark_video'] = $new_video_path;

                            $this->dispatch(new \App\Jobs\VideoWatermarkPositionJob($job_data));
                        }
                    }

                    $post_file->save();
                }

                if($request->file_type=='image' && Setting::get('is_watermark_logo_enabled') && Setting::get('watermark_logo')){

                   $storage_file_path = public_path("storage/".$folder_path.get_video_end($post_file_url));

                   CommonRepo::add_watermark_to_image($storage_file_path);
               }

               $file_data['post_file'] = $post_file;

               $post_file_id != "" && $post_file_id .= ",";

               $post_file_id .= $post_file->post_file_id;

               // $file_url != "" && $file_url .= ",";

               $file_url[] = $post_file_url;

               $post_blur_file != "" && $post_blur_file .= ",";

               $post_blur_file .= $post_file->blur_file;

               $data['post_file'] = $post_file;
           }

           else {

                foreach($files as $file){

                    $filename = rand(1,1000000).'-post-'.$request->file_type;

                    $folder_path = POST_PATH.$request->id.'/';

                    $post_file_url = Helper::post_upload_file($file, $folder_path, $filename);

                    $ext = $file->getClientOriginalExtension();

                    $video_extensions = ['mp4', 'mp3', 'mov', 'flv', 'avi', 'webm', 'mkv'];

                    if($post_file_url) {

                        $post_file = new PostFile;

                        $post_file->user_id = $request->id;

                        $post_file->post_id = 0;

                        $post_file->file = $post_file_url;

                        $post_file->file_type = $request->file_type;

                        $post_file->blur_file = $request->file_type == "image" && !in_array($ext, $video_extensions) ? Setting::get('ppv_image_placeholder') : Setting::get('post_video_placeholder');

                        if($request->file_type == 'video') {

                            $filename_img = rand(1,1000000).'-post-image.jpg';

                            $video_thumbnail_data['original_video_path'] = storage_path('app/public/'.$folder_path.$filename.'.'.$ext);

                            $video_thumbnail_data['save_file_path'] = storage_path('app/public/'.$folder_path);

                            $video_thumbnail_data['thumbnail_file_name'] = $filename_img;

                            VideoThumbnailJob::dispatch($video_thumbnail_data);

                            $post_file->preview_file = asset('storage/'.$folder_path.$filename_img) ?? Setting::get('post_video_placeholder');

                            if(Setting::get('is_watermark_logo_enabled') && Setting::get('watermark_logo')){

                                $video_file = public_path("storage/".$folder_path.get_video_end($post_file_url)); 

                                $new_video_path = public_path("storage/".$folder_path."water-".get_video_end($post_file_url)); 

                                $job_data['video'] = $video_file;

                                $job_data['watermark_video'] = $new_video_path;

                                $this->dispatch(new \App\Jobs\VideoWatermarkPositionJob($job_data));

                            }
                        }

                        $post_file->save();

                    }

                    if($request->file_type=='image' && Setting::get('is_watermark_logo_enabled') && Setting::get('watermark_logo')){

                       $storage_file_path = public_path("storage/".$folder_path.get_video_end($post_file_url)); 

                       CommonRepo::add_watermark_to_image($storage_file_path);
                    }


                   // $file_url != "" && $file_url .= ",";

                   $file_url[] = $post_file_url;


                   $post_file_id != "" && $post_file_id .= ",";

                   $post_file_id .= $post_file->post_file_id;


                   $post_blur_file != "" && $post_blur_file .= ",";

                   $post_blur_file .= $post_file->blur_file;

                   $post_file->post_file = $post_file->file;

                   array_push($file_data, $post_file);

                }

                $data['post_file'] = $file_data;
            }

           $data['post_file_id'] = $post_file_id;

           $data['file'] = $file_url;

           $data['blur_file'] = $post_blur_file;

           return $this->sendResponse(api_success(151), 151, $data);


        } catch(Exception $e){ 

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method post_files_remove()
     *
     * @uses remove the selected file
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer $post_file_id
     *
     * @return JSON Response
     */
    public function post_files_remove(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = [
                'file' => 'nullable',
                // 'file_type' => 'required',
                // 'blur_file' => 'required_if:file_type,==,'.POSTS_IMAGE,
                // 'preview_file' => 'required_if:file_type,==,'.POSTS_VIDEO,
                // 'post_file_id' => 'nullable|exists:post_files,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            if($request->file) {

                $post_file = \App\Models\PostFile::where('file', $request->file)->first();

            } else {

                $post_file = \App\Models\PostFile::where('id', $request->post_file_id)->first();

            }

            $post_file_ids = explode(',', $request->post_file_id);

            if($post_file) {

                $pos = array_search($post_file->id, $post_file_ids);

                unset($post_file_ids[$pos]);

                $post_file->delete();

                DB::commit(); 

            }

            $post_files = \App\Models\PostFile::whereIn('id',$post_file_ids)->pluck('file');

            $post_file_ids = $post_file_ids ? implode(',', $post_file_ids) : '';
            
            $data['post_file_id'] = $post_file_ids ?? '';

            $data['post_file'] = $post_files ?? '';

            return $this->sendResponse(api_success(152), 152, $data = $data);
           
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method posts_delete_for_owner()
     *
     * @uses To delete content creators post
     *
     * @created Bhawya
     *
     * @updated  
     *
     * @param
     * 
     * @return response of details
     *
     */
    public function posts_delete_for_owner(Request $request) {

        try {

            DB::begintransaction();

            $rules = [
                'post_id' => 'required|exists:posts,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules,$custom_errors = []);

            $post = Post::find($request->post_id);

            $post_files = \App\Models\PostFile::where('post_id', $request->post_id)->get();

            if(!$post || !$post_files) {
                throw new Exception(api_error(139), 139);   
            }

            $folder_path_file = POST_PATH.$request->id.'/';

            foreach ($post_files as $key => $post_file) {

                Helper::storage_delete_file($post_file->file, $folder_path_file);

                $folder_path = POST_BLUR_PATH.$request->id.'/';
                
                if ($post_file->file_type == POSTS_IMAGE) {

                    Helper::storage_delete_file($post_file->blur_file, $folder_path);
                }
                else{

                    Helper::storage_delete_file($post_file->preview_file, $folder_path);

                }
            }
            $post = \App\Models\Post::destroy($request->post_id);
            
            DB::commit();

            $data['post_id'] = $request->post_id;

            return $this->sendResponse(api_success(134), $success_code = 134, $data);
            
        } catch(Exception $e){

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }       
         
    }

    /**
     * @method posts_status_for_owner
     *
     * @uses To update post status
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object $request
     * 
     * @return response success/failure message
     *
     **/
    public function posts_status_for_owner(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'post_id' => 'required|exists:posts,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules,$custom_errors = []);

            $post = Post::find($request->post_id);

            if(!$post) {
                throw new Exception(api_error(139), 139);   
            }

            $post->is_published = $post->is_published ? UNPUBLISHED : PUBLISHED;

            if($post->save()) {

                DB::commit();

                $success_code = $post->is_published ? 135 : 136;

                $data['post'] = $post;

                return $this->sendResponse(api_success($success_code),$success_code, $data);

            }
            
            throw new Exception(api_error(130), 130);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /** 
     * @method posts_payment_by_stripe()
     *
     * @uses pay for subscription using paypal
     *
     * @created Vithya R
     *
     * @updated Subham
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function posts_payment_by_stripe(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                'post_id' => 'required|exists:posts,id',
                'promo_code'=>'nullable|exists:promo_codes,promo_code',
            ];

            $custom_errors = ['post_id' => api_error(139)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

           // Check the subscription is available

            $post = \App\Models\Post::PaidApproved()->firstWhere('posts.id',  $request->post_id);

            if(!$post) {

                throw new Exception(api_error(146), 146);
                
            }

            if($request->id == $post->user_id) {

                throw new Exception(api_error(171), 171);
                
            }

            $check_post_payment = \App\Models\PostPayment::UserPaid($request->id, $request->post_id)->first();

            if($check_post_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $request->request->add(['payment_mode' => CARD]);

            $post_amount = $post->amount;

            $user_details = $this->loginUser;

            $promo_amount = 0;

            if ($request->promo_code) {

                $promo_code = PromoCode::where('promo_code', $request->promo_code)->first();
 
                $check_promo_code = CommonRepo::check_promo_code_applicable_to_user($user_details,$promo_code)->getData();

                if ($check_promo_code->success == false) {

                    throw new Exception($check_promo_code->error_messages, $check_promo_code->error_code);
                }else{

                    $promo_amount = promo_calculation($post_amount,$request);

                    $post_amount = $post_amount - $promo_amount;
                }

            }

            $total = $user_pay_amount = $post_amount ?: 0.00;

            if($user_pay_amount > 0) {

                $user_card = \App\Models\UserCard::where('user_id', $request->id)->firstWhere('is_default', YES);

                if(!$user_card) {

                    throw new Exception(api_error(120), 120); 

                }
                
                $request->request->add([
                    'total' => $total, 
                    'customer_id' => $user_card->customer_id,
                    'card_token' => $user_card->card_token,
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount,
                ]);

                $card_payment_response = PaymentRepo::posts_payment_by_stripe($request, $post)->getData();
                
                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                    
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);
                
            }
             



            $payment_response = PaymentRepo::post_payments_save($request, $post, $promo_amount)->getData();

            if($payment_response->success) {
            
            DB::commit();

            $job_data['post_payments'] = $request->all();

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new \App\Jobs\PostPaymentJob($job_data));

            return $this->sendResponse(api_success(140), 140, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }
     
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method posts_payment_by_wallet()
     * 
     * @uses send money to other user
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function posts_payment_by_wallet(Request $request) {

        try {
            
            DB::beginTransaction();

            // Validation start

            $rules = [
                'post_id' => 'required|exists:posts,id',
                'promo_code'=>'nullable|exists:promo_codes,promo_code',
            ];

            $custom_errors = ['post_id' => api_error(139)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

           // Check the subscription is available

            $is_only_wallet_payment = Setting::get('is_only_wallet_payment');
            
            $post = \App\Models\Post::PaidApproved()
              ->where('posts.id', $request->post_id)
              ->when($is_only_wallet_payment == NO, function ($q) use ($is_only_wallet_payment) {
                return $q->OriginalResponse();
              })
              ->when($is_only_wallet_payment == YES, function($q) use ($is_only_wallet_payment) {
                return $q->TokenResponse();
              })
              ->first();

            if(!$post) {

                throw new Exception(api_error(146), 146);
                
            }

            if($request->id == $post->user_id) {

                throw new Exception(api_error(171), 171);
                
            }

            $check_post_payment = \App\Models\PostPayment::UserPaid($request->id, $request->post_id)->first();

            if($check_post_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $post_amount = $post->amount;

            $user_details = $this->loginUser;

            $promo_amount = 0;

            if ($request->promo_code) {

                $promo_code = PromoCode::where('promo_code', $request->promo_code)->first();
 
                $check_promo_code = CommonRepo::check_promo_code_applicable_to_user($user_details,$promo_code)->getData();

                if ($check_promo_code->success == false) {

                    throw new Exception($check_promo_code->error_messages, $check_promo_code->error_code);
                }else{

                    $promo_amount = promo_calculation($post_amount,$request);

                    $post_amount = $post_amount - $promo_amount;
                }

            }

            // Check the user has enough balance 

            $user_wallet = \App\Models\UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet->remaining ?? 0;

            if(Setting::get('is_referral_enabled')) {

                $remaining = $remaining + $user_wallet->referral_amount;
                
            }
            
            if($remaining < $post_amount) {
                throw new Exception(api_error(147), 147);    
            }
            
            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $post_amount * Setting::get('token_amount'), 
                'user_pay_amount' => $post_amount,
                'paid_amount' => $post_amount * Setting::get('token_amount'),
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'WPP-'.rand(),
                'usage_type' => USAGE_TYPE_PPV,
                'tokens' => $post_amount,
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $payment_response = PaymentRepo::post_payments_save($request, $post, $promo_amount)->getData();

                if(!$payment_response->success) {

                    throw new Exception($payment_response->error, $payment_response->error_code);
                }

                $job_data['post_payments'] = $request->all();

                $job_data['timezone'] = $this->timezone;

                $this->dispatch(new \App\Jobs\PostPaymentJob($job_data));

                return $this->sendResponse(api_success(140), 140, $payment_response->data ?? []);

            } else {

                throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method post_comments()
     * 
     * @uses list comments based on the post
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function post_comments(Request $request) {

        try {
            
            // Validation start

            $rules = ['post_id' => 'required|exists:posts,id'];

            $custom_errors = ['post_id' => api_error(139)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

            // Check the subscription is available

            $base_query = $total_query = PostComment::Approved()->where('post_comments.post_id', $request->post_id)->orderBy('post_comments.created_at', 'desc');

            $data['total'] = $base_query->count() ?? 0;

            $post_comments = $base_query->skip($this->skip)->take($this->take)->get();

            if($request->device_type != DEVICE_WEB) {

                $post_comments = array_reverse($post_comments->toArray());

            }

            $data['post_comments'] = $post_comments ?? [];

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method post_comments_save()
     *
     * @uses save the comments for the posts
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_comments_save(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = [
                'comment' => 'required',
                'post_id' => 'required|exists:posts,id',
                'post_comment_id'=>'nullable|exists:post_comments,id'
            ];

            $custom_errors = ['post_id.required' => api_error(146)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $post = \App\Models\Post::find($request->post_id);

            if(!$post){

                throw new Exception(api_error(139), 139);
            }

            $today = Carbon::now()->format('Y-m-d H:i:s');
            
            if(strtotime($post->publish_time) > strtotime($today)){

                throw new Exception(api_error(169), 169);
            }

            $formatted_text = str_replace(['<p>', '</p>'], '', $request->comment);

            if($formatted_text == ''){
                
                throw new Exception(api_error(252), 252);
            }
            
            $is_post_published = \App\Models\Post::where('id',$request->post_id)->where('is_published',YES)->first();

            
            if(!$is_post_published){

                throw new Exception(api_error(169), 169);
            }
            
            $custom_request = new Request();


            if($request->post_comment_id){

                $custom_request->request->add(['id'=>$request->post_comment_id,'user_id' => $request->id, 'post_id' => $request->post_id, 'comment' => $request->comment]);

                PostComment::where('id',$request->post_comment_id)->update($custom_request->request->all());

                $post_comment = PostComment::find($request->post_comment_id);
            }
            else{

               $custom_request->request->add(['user_id' => $request->id, 'post_id' => $request->post_id, 'comment' => $request->comment]);

               $post_comment = PostComment::create($custom_request->request->all());

            }

            DB::commit(); 

            $tagged_users = explode("</a>",$request->comment);

            $user = User::find($request->id);

            $job_data['user'] = $user;
        
            $job_data['tagged_users'] = $tagged_users;

            $job_data['post'] = Setting::get('frontend_url')."post/".$post->unique_id;

            $job_data['post_details'] = $post;

            $job_data['message'] = $user->name." ".tr('tagged_you');

            $this->dispatch(new \App\Jobs\SendTagEmailNotificationJob($job_data));

            $job_data['post_comment'] = $post_comment;

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new \App\Jobs\PostCommentJob($job_data));

            $data = $post_comment;

            $code = $request->post_comment_id ? 163 : 141;

            return $this->sendResponse(api_success($code), $code, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method post_comments_delete()
     *
     * @uses save the comments for the posts
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_comments_delete(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['post_comment_id' => 'required|exists:post_comments,id'];

            $custom_errors = ['post_comment_id.required' => api_error(151)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $post_comment = PostComment::destroy($request->post_comment_id);

            DB::commit(); 

            $data = $post_comment;

            return $this->sendResponse(api_success(142), 142, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method post_bookmarks()
     * 
     * @uses list of bookmarks
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function post_bookmarks(Request $request) {

        try {

           // Check the subscription is available

            $report_posts = report_posts($request->id);

            $blocked_users = blocked_users($request->id);

            $base_query = \App\Models\PostBookmark::where('user_id', $request->id)->Approved()->orderBy('post_bookmarks.created_at', 'desc');

            $post_ids = $base_query->pluck('post_id');

            $post_ids = $post_ids ? $post_ids->toArray() : [];

            if($post_ids) {

                $post_base_query = $total_query = \App\Models\Post::with('postFiles')->Approved()
                                                    ->whereIn('posts.id', $post_ids)
                                                    ->whereNotIn('posts.user_id',$blocked_users)
                                                    ->whereNotIn('posts.id',$report_posts)
                                                    ->whereHas('user')
                                                    ->orderBy('posts.created_at', 'desc');


                if($request->type != POSTS_ALL) {

                    $type = $request->type;

                    $post_base_query = $post_base_query->whereHas('postFiles', function($q) use($type) {
                            $q->where('post_files.file_type', $type);
                        });
                }

                $total = $total_query->count() ?? 0;

                $posts = $post_base_query->with('postBookmark')->skip($this->skip)->take($this->take)->get();

                $posts = \App\Repositories\PostRepository::posts_list_response($posts, $request);

            }

            $data['posts'] = $posts ?? [];

            $data['total'] = $total ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }
    
    /**
     * @method post_bookmarks_photo()
     * 
     * @uses list of bookmarks
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function post_bookmarks_photos(Request $request) {

        try {

           // Check the subscription is available

            $report_posts = report_posts($request->id);

            $blocked_users = blocked_users($request->id);

            $base_query = \App\Models\PostBookmark::where('user_id', $request->id)->Approved()->orderBy('post_bookmarks.created_at', 'desc');

            $post_ids = $base_query->pluck('post_id');

            $post_ids = $post_ids ? $post_ids->toArray() : [];

            if($post_ids) {

                $post_base_query = $total_query = \App\Models\Post::with('postFiles')->Approved()->whereIn('posts.id', $post_ids)->whereNotIn('posts.user_id',$blocked_users)->whereNotIn('posts.id',$report_posts)->whereHas('user')->orderBy('posts.created_at', 'desc');

                $type = POSTS_IMAGE;

                $post_base_query = $post_base_query->whereHas('postFiles', function($q) use($type) {
                        $q->where('post_files.file_type', POSTS_IMAGE);
                    });

                $total = $total_query->count() ?? 0;

                $posts = $post_base_query->skip($this->skip)->take($this->take)->get();

                $posts = \App\Repositories\PostRepository::posts_list_response($posts, $request);

            }

            $data['posts'] = $posts ?? [];

            $data['total'] = $total ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method post_bookmarks_videos()
     * 
     * @uses list of bookmarks
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function post_bookmarks_videos(Request $request) {

        try {

           // Check the subscription is available

            $report_posts = report_posts($request->id);

            $blocked_users = blocked_users($request->id);

            $base_query = \App\Models\PostBookmark::where('user_id', $request->id)->Approved()->orderBy('post_bookmarks.created_at', 'desc');

            $post_ids = $base_query->pluck('post_id');

            $post_ids = $post_ids ? $post_ids->toArray() : [];

            if($post_ids) {

                $post_base_query = $total_query = \App\Models\Post::with('postFiles')->Approved()->whereIn('posts.id', $post_ids)->whereNotIn('posts.user_id',$blocked_users)->whereNotIn('posts.id',$report_posts)->whereHas('user')->orderBy('posts.created_at', 'desc');

                $type = POSTS_VIDEO;

                $post_base_query = $post_base_query->whereHas('postFiles', function($q) use($type) {
                        $q->where('post_files.file_type', POSTS_VIDEO);
                    });

                $total = $total_query->count() ?? 0;

                $posts = $post_base_query->skip($this->skip)->take($this->take)->get();

                $posts = \App\Repositories\PostRepository::posts_list_response($posts, $request);

            }

            $data['posts'] = $posts ?? [];

            $data['total'] = $total ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method post_bookmarks_save()
     *
     * @uses save the comments for the posts
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_bookmarks_save(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['post_id' => 'nullable|exists:posts,id'];

            $custom_errors = ['post_id.required' => api_error(146)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $check_post_bookmark = \App\Models\PostBookmark::where('user_id', $request->id)->where('post_id', $request->post_id)->first();

            $post = \App\Models\Post::Approved()->find($request->post_id);

            if(!$post) {

                throw new Exception(api_error(139), 139);   
            }

            // Check the bookmark already exists 

            if($check_post_bookmark) {

                $post_bookmark = \App\Models\PostBookmark::destroy($check_post_bookmark->id);

                $code = 154;

            } else {

                $custom_request = new Request();

                $custom_request->request->add(['user_id' => $request->id, 'post_id' => $request->post_id]);

                $post_bookmark = \App\Models\PostBookmark::updateOrCreate($custom_request->request->all());

                $code = 143;

            }

            DB::commit(); 

            $data = \App\Repositories\PostRepository::posts_single_response($post, $request);

            return $this->sendResponse(api_success($code), $code, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method post_bookmarks_delete()
     *
     * @uses delete the bookmarks
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_bookmarks_delete(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['post_bookmark_id' => 'required|exists:post_bookmarks,id'];

            $custom_errors = ['post_bookmark_id.required' => api_error(152)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $post_bookmark = \App\Models\PostBookmark::destroy($request->post_bookmark_id);

            DB::commit(); 

            $data = $post_bookmark;

            return $this->sendResponse(api_success(154), 154, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method post_likes()
     * 
     * @uses list of post likes
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function post_likes(Request $request) {

        try {

           // Check the subscription is available

            $base_query = $total_query = \App\Models\PostLike::where('user_id', $request->id)->Approved()->orderBy('post_likes.created_at', 'desc');

            $post_likes = $base_query->skip($this->skip)->take($this->take)->get();

            $data['post_likes'] = $post_likes ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method post_likes_save()
     *
     * @uses Add posts to fav list
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_likes_save(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['post_id' => 'required|exists:posts,id'];
             
            $custom_errors = ['post_id.required' => api_error(139)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $post = \App\Models\Post::Approved()->find($request->post_id);

            if(!$post) {

                throw new Exception(api_error(139), 139);   
            }

            $post_like = \App\Models\PostLike::where('user_id', $request->id)->where('post_id', $request->post_id)->first();

            $code = 149;

            if(!$post_like) {

                $custom_request = new Request();

                $custom_request->request->add(['user_id' => $request->id, 'post_id' => $request->post_id, 'post_user_id' => $post->user_id]);

                $post_like = \App\Models\PostLike::create($custom_request->request->all());

            } else{

                $post_like->delete();

                $code = 150;
            }

            DB::commit();

            $job_data['post_like'] = $post_like;

            $job_data['timezone'] = $this->timezone;

            $job_data['code'] = $code;

            $this->dispatch(new \App\Jobs\PostLikeJob($job_data));

            $data = \App\Repositories\PostRepository::posts_single_response($post, $request);

            return $this->sendResponse(api_success($code), $code, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method post_likes_delete()
     *
     * @uses delete the fav posts
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_likes_delete(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['post_like_id' => 'required|exists:post_likes,id'];

            $custom_errors = ['post_like_id.required' => api_error(153)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $post_like = \App\Models\FavUser::destroy($request->post_like_id);

            DB::commit(); 

            $data = $post_like;

            return $this->sendResponse(api_success(145), 145, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method fav_users()
     * 
     * @uses list of fav posts
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function fav_users(Request $request) {

        try {

           // Check the subscription is available

            $base_query = $total_query = \App\Models\FavUser::where('user_id', $request->id)->Approved()->orderBy('fav_users.created_at', 'desc')->whereHas('favUser');

            $fav_users = $base_query->skip($this->skip)->take($this->take)->get();

            $fav_users = \App\Repositories\CommonRepository::favorites_list_response($fav_users, $request);

            $data['fav_users'] = $fav_users ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method fav_users_save()
     *
     * @uses Add posts to fav list
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function fav_users_save(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['user_id' => 'required|exists:users,id'];

            $custom_errors = ['user_id.required' => api_error(146)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $to_user = \App\Models\User::Approved()->find($request->user_id);

            if(!$to_user) {
                throw new Exception(api_error(135), 135);
            }

            $is_user_blocked = Helper::is_block_user($request->id,$request->user_id);
             
            if($is_user_blocked){

                throw new Exception(api_error(168), 168);
            }

            $check_fav_user = $fav_user = \App\Models\FavUser::where('user_id', $request->id)->where('fav_user_id', $request->user_id)->first();

            if(!$check_fav_user) {

                $custom_request = new Request();

                $custom_request->request->add(['user_id' => $request->id, 'fav_user_id' => $request->user_id]);

                $fav_user = \App\Models\FavUser::create($custom_request->request->all());

                $code = 144;

            } else {

                $check_fav_user->delete();

                $code = 145;

            }

            DB::commit(); 

            $data = $fav_user;

            return $this->sendResponse(api_success($code), $code, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method fav_users_delete()
     *
     * @uses delete the fav posts
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function fav_users_delete(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['fav_user_id' => 'required|exists:fav_users,id'];

            $custom_errors = ['fav_user_id.required' => api_error(153)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $fav_user = \App\Models\FavUser::destroy($request->fav_user_id);

            DB::commit(); 

            return $this->sendResponse(api_success(145), 145, $data = []);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /** 
     * @method tips_payment_by_stripe()
     *
     * @uses send tips to the user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function tips_payment_by_stripe(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                    'post_id' => 'nullable|exists:posts,id',
                    'user_id' => 'required|exists:users,id',
                    'amount' => 'required|numeric|min:1',
                    'user_card_id' => 'required|exists:user_cards,id,user_id,'.$request->id
                ];

            $custom_errors = ['post_id' => api_error(139), 'user_id' => api_error(135)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

            if($request->id == $request->user_id) {
                throw new Exception(api_error(154), 154);
                
            }

            $post = \App\Models\Post::PaidApproved()->firstWhere('posts.id',  $request->post_id);

            $user = \App\Models\User::Approved()->firstWhere('users.id',  $request->user_id);

            if(!$user) {

                throw new Exception(api_error(135), 135);
                
            }

            $request->request->add(['payment_mode' => CARD]);

            $total = $user_pay_amount = $request->amount ?: 1;

            if($user_pay_amount > 0) {

                $user_card = \App\Models\UserCard::firstWhere(['id' => $request->user_card_id, 'user_id' => $request->id]);

                if(!$user_card) {

                    throw new Exception(api_error(120), 120); 

                }
                
                $request->request->add([
                    'total' => $total, 
                    'customer_id' => $user_card->customer_id,
                    'card_token' => $user_card->card_token,
                    'user_card_id' => $user_card->id,
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount,
                ]);

                $card_payment_response = PaymentRepo::tips_payment_by_stripe($request, $post)->getData();
                
                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);

            }

            $request->request->add(['to_user_id' => $request->user_id]);

            $payment_response = PaymentRepo::tips_payment_save($request, $post)->getData();

            if($payment_response->success) {
            
            DB::commit();
            
            $job_data['user_tips'] = $request->all();

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new \App\Jobs\TipPaymentJob($job_data));

            return $this->sendResponse(api_success(146), 146, $payment_response->data);

            } else {
                
                throw new Exception($payment_response->error, $payment_response->error_code);
            }

        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method tips_payment_by_wallet()
     * 
     * @uses send tips to the user
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function tips_payment_by_wallet(Request $request) {

        try {
            
            DB::beginTransaction();

            // Validation start

            $rules = [
                    'post_id' => 'nullable|exists:posts,id',
                    'user_id' => 'required|exists:users,id',
                    'amount' => 'required|numeric|min:1'
                ];

            $custom_errors = ['post_id' => api_error(139), 'user_id' => api_error(135)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

            if($request->id == $request->user_id) {
                throw new Exception(api_error(154), 154);
                
            }

            $user = \App\Models\User::Approved()->firstWhere('users.id',  $request->user_id);

            if(!$user) {

                throw new Exception(api_error(135), 135);
                
            }

            // Check the user has enough balance 

            $user_wallet = \App\Models\UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet->remaining ?? 0;

            if(Setting::get('is_referral_enabled')) {

                $remaining = $remaining + $user_wallet->referral_amount;
                
            }

            if($remaining < $request->amount) {
                throw new Exception(api_error(147), 147);    
            }
            
            $amount = $request->amount;

            if(Setting::get('is_only_wallet_payment')) {

                $amount = $request->amount * Setting::get('token_amount'); 

                $request->request->add([
                    'tokens' => $request->amount,
                ]);

            }

            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $amount, 
                'user_pay_amount' => $request->amount,
                'paid_amount' => $amount,
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'WPP-'.rand(),
                'usage_type' => USAGE_TYPE_TIP
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $request->request->add(['to_user_id' => $request->user_id]);

                $payment_response = PaymentRepo::tips_payment_save($request)->getData();

                if(!$payment_response->success) {

                    throw new Exception($payment_response->error, $payment_response->error_code);
                }

                // // Update the to user

                // $to_user_inputs = [
                //     'id' => $request->user_id,
                //     'received_from_user_id' => $request->id,
                //     'total' => $request->amount, 
                //     'user_pay_amount' => $request->amount,
                //     'paid_amount' => $request->amount,
                //     'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                //     'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                //     'payment_id' => 'CD-'.rand(),
                //     'usage_type' => USAGE_TYPE_TIP
                // ];

                // $to_user_request = new \Illuminate\Http\Request();

                // $to_user_request->replace($to_user_inputs);

                // $to_user_payment_response = PaymentRepo::user_wallets_payment_save($to_user_request)->getData();

                // if($to_user_payment_response->success) {

                    DB::commit();

                    $user_tips = new \Illuminate\Http\Request();

                    $user_tips->amount = $request->amount;

                    $user_tips->user_id = $request->user_id;

                    $user_tips->id = $request->id;

                    $job_data['user_tips'] = $user_tips;

                    $job_data['timezone'] = $this->timezone;
        
                    $this->dispatch(new \App\Jobs\TipPaymentJob($job_data));

                    return $this->sendResponse(api_success(140), 140, $payment_response->data ?? []);

                // } else {

                //     throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
                // }

            } else {

                throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }



    /**
     * @method report_posts_save()
     *
     * @uses report the user post
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function report_posts_save(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = [
                'post_id' => 'required|exists:posts,id',
                'report_reason_id' => 'required|exists:report_reasons,id',
                'reason'=>'nullable|max:255'
            ];

            $custom_errors = ['post_id.required' => api_error(146)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $post = \App\Models\Post::find($request->post_id);

            // Check the post already reported

            if($post->user_id == $request->id){

                throw new Exception(api_error(164), 164);  
            }

            $check_report_post = \App\Models\ReportPost::where('block_by', $request->id)->where('post_id', $request->post_id)->first();

            if($check_report_post) {

                $report_post = $check_report_post->delete();

                $code = 158;

            } else {

                $custom_request = new Request();

                $custom_request->request->add(['block_by' => $request->id, 'post_id' => $request->post_id,'reason'=>$request->reason]);

                $report_post = \App\Models\ReportPost::updateOrCreate($custom_request->request->all());

                if ($request->report_reason_id) {
                    
                    $report_reason = ReportReason::find($request->report_reason_id);

                    $report_post->report_reason_id = $report_reason->id ?? 0;

                    $report_post->reason = $report_reason->title ?? '';
                }

                $report_post->save();

                $report_post->blocked_user = $report_post->blockeduser->name ?? '';

                $report_post->post = $report_post->post ?? '';

                $report_post->reason = $request->reason ?? '';

                $code = 157;

            }

            DB::commit(); 

            $data = $report_post;

            return $this->sendResponse(api_success($code), $code, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method report_posts()
     * 
     * @uses list of posts reported by user
     *
     * @created Ganesh 
     *
     * @updated Ganesh
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function report_posts(Request $request) {

        try {

            $base_query = $total_query = \App\Models\ReportPost::where('block_by', $request->id)->orderBy('report_posts.created_at', 'DESC');

            $report_posts = $base_query->with('post')->with('blockeduser')->skip($this->skip)->take($this->take)->get();

            $data['report_posts'] = $report_posts ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }



    /** 
     * @method tips_payment_by_paypal()
     *
     * @uses tip payment to user
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function tips_payment_by_paypal(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                    'payment_id'=>'required',
                    'post_id' => 'nullable|exists:posts,id',
                    'user_id' => 'required|exists:users,id',
                    'amount' => 'required|numeric|min:1'
                ];

            $custom_errors = ['post_id' => api_error(139), 'user_id' => api_error(135)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

            if($request->id == $request->user_id) {

                throw new Exception(api_error(154), 154);
                
            }

            $post = \App\Models\Post::PaidApproved()->firstWhere('posts.id',  $request->post_id);

            $user = \App\Models\User::Approved()->firstWhere('users.id',  $request->user_id);

            if(!$user) {

                throw new Exception(api_error(135), 135);
                
            }


            $user_pay_amount = $request->amount ?: 1;

            $request->request->add(['payment_mode' => PAYPAL,'paid_amount'=>$user_pay_amount, 'user_pay_amount' => $user_pay_amount,'paid_status' => PAID_STATUS]);

            // $store_wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();
            
            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $user_pay_amount, 
                'user_pay_amount' => $user_pay_amount,
                'paid_amount' => $user_pay_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'WPP-'.rand(),
                'usage_type' => USAGE_TYPE_PPV
            ]);

            $request->request->add(['to_user_id' => $request->user_id]);
           
            $payment_response = PaymentRepo::tips_payment_save($request, $post)->getData();

            if($payment_response->success) {
            
            DB::commit();
            
            $job_data['user_tips'] = $request->all();

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new \App\Jobs\TipPaymentJob($job_data));

            return $this->sendResponse(api_success(146), 146, $payment_response->data);

            } else {
              
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


    /** 
     * @method posts_payment_by_paypal()
     *
     * @uses pay for subscription using paypal
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function posts_payment_by_paypal(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                'payment_id'=>'required',
                'post_id' => 'required|exists:posts,id',
                'promo_code'=>'nullable|exists:promo_codes,promo_code',
            ];

            $custom_errors = ['post_id' => api_error(139)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

           // Check the subscription is available

            $post = \App\Models\Post::PaidApproved()->firstWhere('posts.id',  $request->post_id);

            if(!$post) {

                throw new Exception(api_error(146), 146);
                
            }

            if($request->id == $post->user_id) {

                throw new Exception(api_error(171), 171);
                
            }

            $check_post_payment = \App\Models\PostPayment::UserPaid($request->id, $request->post_id)->first();

            if($check_post_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $post_amount = $post->amount;

            $user_details = $this->loginUser;

            $promo_amount = 0;

            if ($request->promo_code) {

                $promo_code = PromoCode::where('promo_code', $request->promo_code)->first();
 
                $check_promo_code = CommonRepo::check_promo_code_applicable_to_user($user_details,$promo_code)->getData();

                if ($check_promo_code->success == false) {

                    throw new Exception($check_promo_code->error_messages, $check_promo_code->error_code);
                }else{

                    $promo_amount = promo_calculation($post_amount,$request);

                    $post_amount = $post_amount - $promo_amount;
                }

            }

            $user_pay_amount = $post_amount ?: 0.00;

            $request->request->add(['payment_mode'=> PAYPAL,'user_pay_amount' => $user_pay_amount,'paid_amount' => $user_pay_amount, 'payment_id' => $request->payment_id, 'paid_status' => PAID_STATUS]);

            $payment_response = PaymentRepo::post_payments_save($request, $post, $promo_amount)->getData();

            if($payment_response->success) {
            
            $job_data['post_payments'] = $request->all();

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new \App\Jobs\PostPaymentJob($job_data));
            
            DB::commit();

            return $this->sendResponse(api_success(140), 140, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method post_comment_likes()
     * 
     * @uses list of post comment likes
     *
     * @created Arun
     *
     * @updated 
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function post_comment_likes(Request $request) {

        try {

           // Validation start

            $rules = [
                'post_comment_id'=> $request->post_comment_id ? 'required|exists:post_comments,id' : '',
                'post_comment_reply_id' => $request->post_comment_reply_id ? 'required|exists:post_comment_replies,id' :'',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // Validation end

            if ($request->post_comment_id ) {

                $base_query = $total_query = PostCommentLike::where('post_comment_id', $request->post_comment_id)->Approved()->orderBy('post_comment_likes.created_at', 'desc');
            }

            if (!$request->post_comment_id && $request->post_comment_reply_id) {
                
                $base_query = $total_query = PostCommentLike::where('post_comment_reply_id', $request->post_comment_reply_id)->Approved()->orderBy('post_comment_likes.created_at', 'desc');
            }
            
            $post_likes = $base_query->skip($this->skip)->take($this->take)->get();

            $data['post_comment_likes'] = $post_likes ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method post_comment_likes_save()
     *
     * @uses Add posts comment like/dislike
     *
     * @created Arun
     *
     * @updated 
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_comment_likes_save(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = [
                'post_comment_id' => $request->post_comment_id ? 'required|exists:post_comments,id' :'',
                'post_comment_reply_id' => $request->post_comment_reply_id ? 'required|exists:post_comment_replies,id' :'',
                'status' => 'required|gte:0|lt:3',
            ];
             
            $custom_errors = ['post_comment_id.required' => api_error(151)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $post_comment = PostComment::Approved()->find($request->post_comment_id) ?? PostCommentReply::Approved()->find($request->post_comment_reply_id);

            $post = Post::Approved()->find($post_comment->post_id);

            if(!$post) {

                throw new Exception(api_error(151), 151);   
            }

            $custom_request = new Request();

            $custom_request->request->add(['user_id' => $request->id, 'post_user_id' => $post->user_id, 'status' => $request->status]);

            $base_query = PostCommentLike::where('user_id', $request->id);

            if ($request->post_comment_id ) {

                $base_query = $base_query->where('post_comment_id', $request->post_comment_id); 

                $custom_request->request->add(['post_comment_id' => $request->post_comment_id]);
            }

            if (!$request->post_comment_id && $request->post_comment_reply_id) {
                
                $base_query = $base_query->where('post_comment_reply_id', $request->post_comment_reply_id); 

                $custom_request->request->add(['post_comment_reply_id' => $request->post_comment_reply_id]);
            }

            $post_comment_like = $base_query->first();

            $code = 149;

            if ($request->status == REMOVE_LIKE_OR_DISLIKE) {

                if ($post_comment_like) {

                    $code = $post_comment_like->status == LIKE ? 150 : 163;
                    
                    $post_comment_like->delete();

                    DB::commit();

                    throw new Exception(api_success($code), $code); 
                } 

                throw new Exception(api_error(183), 183); 
            }

            if(!$post_comment_like) {

                $post_comment_like = PostCommentLike::create($custom_request->request->all());

            } else{

                $post_comment_like->status = $request->status;

                $post_comment_like->save();
            }

            DB::commit(); 

            $code = $post_comment_like->status == LIKE ? 149 : 162;

            $job_data['post_comment_like'] = $post_comment_like;

            $job_data['post'] = $post;

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new PostCommentLikeJob($job_data));

            $data = $post_comment_like;

            return $this->sendResponse(api_success($code), $code, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getLine());

        } 
    
    }

    /**
     * @method post_comment_replies()
     * 
     * @uses list replies based on the post comment
     *
     * @created Arun
     *
     * @updated 
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function post_comment_replies(Request $request) {

        try {
            
            // Validation start

            $rules = [
                'post_id' => 'required|exists:posts,id',
                'post_comment_id' => 'required|exists:post_comments,id',
            ];

            $custom_errors = ['post_id' => api_error(139)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

            $base_query = $total_query = PostCommentReply::Approved()
                                            ->where('post_id', $request->post_id)
                                            ->where('post_comment_id', $request->post_comment_id)
                                            ->orderBy('created_at', 'desc');

            $data['total'] = $base_query->count() ?? 0;

            $post_comment_replies = $base_query->skip($this->skip)->take($this->take)->get();

            $post_comment_replies = $post_comment_replies->map(function ($post_comment_reply, $key) use ($request) {

                $post_comment_reply->is_user_liked = $post_comment_reply->postCommentReplyLikes
                                                        ->where('post_comment_reply_id', $post_comment_reply->post_comment_reply_id)
                                                        ->where('user_id', $request->id)->count() ? YES : NO;

                $post_comment_reply->unsetRelation('postCommentLikes');

                return $post_comment_reply;
            });

            if($request->device_type != DEVICE_WEB) {

                $post_comment_replies = array_reverse($post_comment_replies->toArray());

            }
            
            $data['post_comment_replies'] = $post_comment_replies ?? [];

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method post_comments_replies_save()
     *
     * @uses save the comment replies for the posts
     *
     * @created Arun
     *
     * @updated 
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_comments_replies_save(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = [
                'reply' => 'required',
                'post_id' => 'required|exists:posts,id',
                'post_comment_id' => 'required|exists:post_comments,id',
                'post_comment_reply_id' => 'nullable|exists:post_comment_replies,id',
            ];

            $custom_errors = ['post_id.required' => api_error(146)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $post = Post::find($request->post_id);

            if(!$post){

                throw new Exception(api_error(139), 139);
            }

            $today = Carbon::now()->format('Y-m-d H:i:s');
            
            if(strtotime($post->publish_time) > strtotime($today)){

                throw new Exception(api_error(169), 169);
            }

            
            $is_post_published = Post::where('id',$request->post_id)->where('is_published',YES)->first();

            
            if(!$is_post_published){

                throw new Exception(api_error(169), 169);
            }
            
            $formatted_text = str_replace(['<p>', '</p>'], '', $request->reply);

            if($formatted_text == ''){
                
                throw new Exception(api_error(253), 253);
            }
            
            $custom_request = new Request();

            $custom_request->request->add(['user_id' => $request->id, 'post_id' => $request->post_id, 'post_comment_id' => $request->post_comment_id, 'reply' => $request->reply]);

            if ($request->post_comment_reply_id){

                $post_comment_reply = PostCommentReply::where('id',$request->post_comment_reply_id)->first();

                $post_comment_reply->reply = $request->reply;

                $post_comment_reply->save();

                $message = 166;

            } 

            else{

                $post_comment_reply = PostCommentReply::create($custom_request->request->all());

                $message = 164;
            }

            DB::commit(); 

            $tagged_users = explode("</a>",$request->reply);

            $user = User::find($request->id);

            $job_data['user'] = $user;
        
            $job_data['tagged_users'] = $tagged_users;

            $job_data['post'] = Setting::get('frontend_url')."post/".$post->unique_id;

            $job_data['post_details'] = $post;

            $job_data['message'] = $user->name." ".tr('tagged_you_reply');

            $this->dispatch(new \App\Jobs\SendTagEmailNotificationJob($job_data));

            $job_data['post_comment_reply'] = $post_comment_reply;

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new PostCommentReplyJob($job_data));

            $data = $post_comment_reply;

            return $this->sendResponse(api_success($message), $message, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method post_comments_replies_delete()
     *
     * @uses Delete the comment replies for the posts
     *
     * @created Arun
     *
     * @updated 
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_comments_replies_delete(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['post_comment_reply_id' => 'required|exists:post_comment_replies,id'];

            Helper::custom_validator($request->all(),$rules, $custom_errors = []);

            $post_comment_reply = PostCommentReply::destroy($request->post_comment_reply_id);

            DB::commit(); 

            $data = $post_comment_reply;

            return $this->sendResponse(api_success(165), 165, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method explore()
     *
     * @uses To display random posts
     *
     * @created Arun
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function explore(Request $request) {

        try {

            $user_id = $request->id;

            $report_posts = report_posts($request->id);

            $blocked_users = blocked_users($request->id);

            $follower_ids = get_follower_ids($request->id);

            $free_post = Post::whereIn('posts.user_id', $follower_ids)->where('is_paid_post', UNPAID)->pluck('id');

            $paid_post = Post::whereIn('posts.user_id', $follower_ids)->PaidApproved()
                        ->whereHas('postPayments', function($q) use ($user_id) {

                            return $q->Where('post_payments.user_id','=',$user_id);

                        })->pluck('id');

            $post_ids = $free_post->merge($paid_post);

            // location based search
            
            if($request->latitude && $request->longitude) {
                
                $distance = Setting::get('search_radius', 100);

                $latitude = $request->latitude; $longitude = $request->longitude;

                $location_query = "SELECT users.id as user_id, 1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) AS distance FROM users
                                        WHERE (1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance 
                                        ORDER BY distance";

                $location_users = DB::select(DB::raw($location_query));
                
                $user_ids = array_column($location_users, 'user_id');

                $location_posts = Post::whereIn('user_id', $user_ids)->pluck('id');

                $post_ids = array_intersect($post_ids->toArray(), $location_posts->toArray());
                
            }

            $base_query = $total_query = Post::Approved()->where('posts.user_id', '!=' ,$user_id)
                                        ->whereNotIn('posts.user_id',$blocked_users)
                                        ->whereNotIn('posts.id',$report_posts)
                                        ->whereIn('id', $post_ids)
                                        ->whereHas('user')->whereHas('postFiles')
                                        ->inRandomOrder()
                                        ->groupBy('posts.user_id');

            $data['total'] = count($total_query->get()) ?? 0;

            $posts = $base_query->skip($this->skip)->take($this->take)->get();

            $posts = $posts->map(function ($post, $key) use ($request) {

                        $post->postFiles = PostFile::where('post_id', $post->post_id)
                                            ->OriginalResponse()
                                            ->first();

                        $post->is_user_liked = $post->postLikes->where('user_id', $request->id)->count() ? YES : NO;

                        $post->share_link = Setting::get('frontend_url')."post/".$post->post_unique_id;

                        return $post;
                    });

            $data['posts'] = $posts ?? [];

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

     /**
     * @method hashtags_index()
     * 
     * @uses list hashtags
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function hashtags_index(Request $request) {

        try {

            $base_query = $total_query = Hashtag::Approved()->orderBy('hashtags.created_at', 'desc');

            $hashtags = $base_query->skip($this->skip)->take($this->take)->get();

            $data['hashtags'] = $hashtags ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method hashtags_search()
     * 
     * @uses search hashtags
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function hashtags_search(Request $request) {

        try {

            $rules = ['search_key' => 'required'];

            Helper::custom_validator($request->all(),$rules, $custom_errors = []);

            $base_query = $total_query = Hashtag::Approved()->where('name','LIKE','%'.$request->search_key.'%')->orderBy('hashtags.created_at', 'desc');

            $hashtags = $base_query->skip($this->skip)->take($this->take)->get();

            $data['hashtags'] = $hashtags ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /** 
     * @method post_categories_list()
     *
     * @uses Post categories List
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function post_categories_list(Request $request) {

        try {

            $base_query = $total_query = Category::Approved();

            $categories = $base_query->orderBy('created_at', 'desc')->get();

            $data['post_categories'] = $categories;

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method post_categories_view()
     *
     * @uses post categories single view
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return json response with details
     */

    public function post_categories_view(Request $request) {

        try {

            $rules = ['category_unique_id' => 'required|exists:u_categories,unique_id'];

            $custom_errors = ['category_unique_id.exists' => api_error(300)];

            Helper::custom_validator($request->all(),$rules,$custom_errors);

            $post_category = Category::where('unique_id', $request->category_unique_id)->first();

            $data['post_category'] = $post_category;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method post_bookmarks_audio()
     * 
     * @uses list of bookmarks
     *
     * @created Arun
     *
     * @updated 
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function post_bookmarks_audio(Request $request) {

        try {

           // Check the subscription is available

            $report_posts = report_posts($request->id);

            $blocked_users = blocked_users($request->id);
            
            $base_query = \App\Models\PostBookmark::where('user_id', $request->id)->Approved()->orderBy('post_bookmarks.created_at', 'desc');

            $post_ids = $base_query->pluck('post_id');

            $post_ids = $post_ids ? $post_ids->toArray() : [];

            if($post_ids) {

                $post_base_query = $total_query = \App\Models\Post::with('postFiles')->Approved()->whereIn('posts.id', $post_ids)->whereNotIn('posts.user_id',$blocked_users)->whereNotIn('posts.id',$report_posts)->whereHas('user')->orderBy('posts.created_at', 'desc');

                $type = POSTS_AUDIO;

                $post_base_query = $post_base_query->whereHas('postFiles', function($q) use($type) {
                        $q->where('post_files.file_type', POSTS_AUDIO);
                    });

                $total = $total_query->count() ?? 0;

                $posts = $post_base_query->skip($this->skip)->take($this->take)->get();

                $posts = \App\Repositories\PostRepository::posts_list_response($posts, $request);

            }

            $data['posts'] = $posts ?? [];

            $data['total'] = $total ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    
    /**
     * @method report_reasons_index()
     *
     * @uses To display all the posts
     *
     * @created Subham
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function report_reasons_index(Request $request) {

        try {

            $base_query = $total_query = ReportReason::Approved();

            if($request->search_key){

                $base_query = $base_query->where('title',$request->search_key);

            }

            $data['report_reason'] = $base_query->get();

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /** 
     * @method post_category_listing()
     *
     * @uses List of Post based on category
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function post_category_listing(Request $request) {

        try {

            $user_id = $request->id;

            $report_posts = report_posts($request->id);

            $blocked_users = blocked_users($request->id);

            // $follower_ids = get_follower_ids($request->id);

            $free_post = Post::where('is_paid_post', UNPAID)->pluck('id');

            $paid_post = Post::PaidApproved()
                        ->whereHas('postPayments', function($q) use ($user_id) {

                            return $q->Where('post_payments.user_id','=',$user_id);

                        })->pluck('id');

            $post_ids = $free_post->merge($paid_post);

            $posts_base_query = Post::Approved()->where('posts.user_id', '!=' ,$user_id)
                                        ->whereNotIn('posts.user_id',$blocked_users)
                                        ->whereNotIn('posts.id',$report_posts)
                                        ->whereHas('user')->whereHas('postFiles')
                                        ->whereIn('id', $post_ids)
                                        ->inRandomOrder()
                                        ->orderBy('created_at', 'desc');

            if ($request->category_id) {

                $selected_post_category_ids = CategoryDetail::where('category_id', $request->category_id)->where('type', CATEGORY_TYPE_POST)->pluck('post_id');
                
                $posts_base_query = $posts_base_query->whereIn('id', $selected_post_category_ids);
            }

            $data['total'] = $posts_base_query->count() ?? 0;

            $posts = $posts_base_query->skip($this->skip)->take($this->take)->get();

            $posts = $posts->map(function ($post, $key) use ($request) {

                        $post->postFiles = PostFile::where('post_id', $post->post_id)
                                            ->OriginalResponse()
                                            ->first();

                        $post->is_user_liked = $post->postLikes->where('user_id', $request->id)->count() ? YES : NO;

                        $post->share_link = Setting::get('frontend_url')."post/".$post->post_unique_id;

                        return $post;
                    });

            $data['posts'] = $posts ?? [];

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


}