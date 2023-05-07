<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Models\User, App\Models\Story, App\Models\StoryFile;

use Carbon\Carbon;

use App\Repositories\CommonRepository as CommonRepo;

class StoriesApiController extends Controller
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
     * @method stories_list()
     *
     * @uses To display all the stories
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function stories_list(Request $request) {

        try {

            $base_query = $total_query = Story::where('user_id', $request->id)->where('is_published',PUBLISHED)->where('publish_time', '>=', 
                    Carbon::now()->subDay())->with('storyFiles')->orderBy('stories.created_at', 'desc');

            if($request->type && $request->type != STORY_ALL) {

                $type = $request->type;

                $base_query = $base_query->whereHas('storyFiles', function($q) use($type) {
                        $q->where('story_files.file_type', $type);
                    });
            }

            $stories = $base_query->skip($this->skip)->take($this->take)->get();


            $data['stories'] = $stories ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

     /**
     * @method stories_save()
     *
     * @uses get the selected stories details
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function stories_save(Request $request) {

        try {
          
            DB::begintransaction();

            $rules = [

                'content' => 'required',
                'publish_time' => 'nullable',
                'amount' => 'nullable|numeric|min:1',
                'story_file_id' => 'required|exists:story_files,id',
                'story_id' => $request->story_id ? 'required|exists:stories,id' : '',

            ];

            Helper::custom_validator($request->all(),$rules);

            $user = User::find($request->id);

            if(!$user->is_content_creator == CONTENT_CREATOR) {

                throw new Exception(api_error(218), 218);
            }

            $story = Story::find($request->story_id) ?? new Story;

            $success_code = $story->id ? 215 : 214;

            $story->user_id = $request->id;

            $story->content = $request->content ?: $story->content;

            $publish_time = $request->publish_time ?: date('Y-m-d H:i:s');

            $story->publish_time = date('Y-m-d H:i:s', strtotime($publish_time));
            
            if(!$story->content) {

                 throw new Exception(api_error(219), 219);  
            }

            if($story->save()) {

                $story_file = \App\Models\StoryFile::find($request->story_file_id);

                $story_file->story_id = $story->id;

                $story_file->save();

                if ($request->amount) {
                    
                    $amount = $request->amount ?: ($story->amount ?? 0);

                    $story->amount = $amount;

                    $story->is_paid_story = $amount > 0 ? YES : NO;

                    $story->save();
                }

                DB::commit(); 

                $data = $story;

                return $this->sendResponse(api_success($success_code), $success_code, $data);

            } 

            throw new Exception(api_error(128), 128);

        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method story_files_upload()
     *
     * @uses get the selected story details
     *
     * @created Jeevan
     *
     * @updated
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function story_files_upload(Request $request) {

        try {
           
            $rules = [
                'file' => 'required|file',
                'file_type' => 'required',
                'story_id' => 'nullable|exists:stories,id'
            ];

            Helper::custom_validator($request->all(),$rules);

            $user = User::find($request->id);

            if(!$user->is_content_creator == CONTENT_CREATOR) {

                throw new Exception(api_error(218), 218);
            }

            $story = new Story;

            $story->user_id = $request->id;

            $story->content = $request->content ?: $story->content;

            $publish_time = $request->publish_time ?: date('Y-m-d H:i:s');

            $story->publish_time = date('Y-m-d H:i:s', strtotime($publish_time));
            
            $amount = $request->amount ?: ($story->amount ?? 0);

            $story->amount = $amount;

            $story->is_paid_story = $amount > 0 ? YES : NO;

            if($story->save()) {

                $filename = rand(1,1000000).'-story-'.$request->file_type;

                $folder_path = STORY_PATH.$request->id.'/';
                
                $story_file_url = Helper::storage_upload_file($request->file, $folder_path, $filename);

                if($story_file_url) {

                    $story_file = new StoryFile;

                    $story_file->user_id = $request->id;

                    $story_file->story_id = $story->id;

                    $story_file->file = $story_file_url;

                    $story_file->file_type = $request->file_type;

                    $story_file->blur_file = Setting::get('post_video_placeholder');
                        
                    $story_file->preview_file = Setting::get('post_video_placeholder');

                    if($request->file_type == 'video') {

                        $filename_img = rand(1,1000000).'-story-image.jpg';

                        $file = $request->file;
                        
                        $ext = $file->getClientOriginalExtension();

                        \VideoThumbnail::createThumbnail(storage_path('app/public/'.$folder_path.$filename.'.'.$ext),storage_path('app/public/'.$folder_path),$filename_img, 2);

                        $story_file->preview_file = asset('storage/'.$folder_path.$filename_img);

                    }

                    $story_file->save();
                }

                DB::commit();

            } 

            $data = $story;

            return $this->sendResponse(api_success(214), 214, $data);

            
        } catch(Exception $e){ 

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method story_files_remove()
     *
     * @uses remove the selected file
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param integer $story_file_id
     *
     * @return JSON Response
     */
    public function story_files_remove(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = [
                'file' => 'required',
                'file_type' => 'required',
                'blur_file' => 'required_if:file_type,==,'.STORY_IMAGE,
                'preview_file' => 'required_if:file_type,==,'.STORY_VIDEO,
                'story_file_id' => 'nullable|exists:story_files,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            if($request->story_file_id) {

                StoryFile::where('id', $request->story_file_id)->delete();

            } else {

                StoryFile::where('file', $request->file)->delete();

            }

            $folder_path = STORY_PATH.$request->id.'/';

            Helper::storage_delete_file($request->file, $folder_path);

            if ($request->file_type == STORY_IMAGE) {

                $folder_path = STORY_BLUR_PATH.$request->id.'/';

                Helper::storage_delete_file($request->blur_file, $folder_path);
            }
            else{

                Helper::storage_delete_file($request->preview_file, $folder_path);

            }

            DB::commit(); 

            return $this->sendResponse(api_success(152), 152, $data = []);
           
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method stories_view()
     *
     * @uses get the selected story details
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function stories_view(Request $request) {

        try {

            $rules = [
                'story_id' => 'required|exists:stories,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules);

            $story = Story::with('storyFiles')->find($request->story_id);

            if(!$story) {
                throw new Exception(api_error(220), 220);   
            }

            $data['story'] = $story;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

     /**
     * @method stories_delete()
     *
     * @uses To delete content creators story
     *
     * @created Jeevan
     *
     * @updated  
     *
     * @param
     * 
     * @return response of details
     *
     */
    public function stories_delete(Request $request) {

        try {

            DB::begintransaction();

            $rules = [
                'story_id' => 'required|exists:stories,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules,$custom_errors = []);

            $story = Story::find($request->story_id);

            $story_file = StoryFile::where('story_id', $request->story_id)->first();
           
            if(!$story || !$story_file) {
                throw new Exception(api_error(220), 220);   
            }

            $folder_path = STORY_PATH.$request->id.'/';

            Helper::storage_delete_file($story_file->file, $folder_path);

            if ($story_file->file_type == STORY_IMAGE) {

                $folder_path = STORY_BLUR_PATH.$request->id.'/';

                Helper::storage_delete_file($story_file->blur_file, $folder_path);
            }
            else{

                Helper::storage_delete_file($story_file->preview_file, $folder_path);

            }

            $story = Story::destroy($request->story_id);
           
            DB::commit();

            $data['story_id'] = $request->story_id;

            return $this->sendResponse(api_success(216), $success_code = 216, $data);
            
        } catch(Exception $e){

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }       
         
    }

    /**
     * @method stories_for_creators()
     *
     * @uses To display all the stories for the selected model
     *
     * @created Vithya
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function stories_for_creators(Request $request) {

        try {

            $base_query = $total_query = Story::where('user_id', $request->user_id)->where('is_published',PUBLISHED)->where('publish_time', '>=', 
                    Carbon::now()->subDay())->with('storyFiles')->orderBy('stories.created_at', 'desc');

            if($request->type && $request->type != STORY_ALL) {

                $type = $request->type;

                $base_query = $base_query->whereHas('storyFiles', function($q) use($type) {
                        $q->where('story_files.file_type', $type);
                    });
            }

            // $stories = $base_query->skip($this->skip)->take($this->take)->get();
            $stories = $base_query->get();

            $data['stories'] = $stories ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method stories_home()
     *
     * @uses To display all the stories
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function stories_home(Request $request) {

        try {

            $follower_ids = get_follower_ids($request->id);

            $blocked_users = blocked_users($request->id);

            $user_ids = Story::Approved()->whereNotIn('stories.user_id',$blocked_users)->whereIn('stories.user_id', $follower_ids)->where('publish_time', '>=', 
                    Carbon::now()->subDay())->groupBy('user_id')->pluck('user_id');

            $base_query = $total_query = User::CommonResponse()->whereIn('id', $user_ids);

            $users = $base_query->skip($this->skip)->take($this->take)->get();

            $stories = \App\Repositories\StoriesRepository::stories_list_response($users, $request);

            $data['stories'] = $stories ?? [];

            $data['total'] = $total_query->count() ?? 0;

            $data['user'] = $this->loginUser;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

     /**
     * @method stories_single_view()
     *
     * @uses get the selected story details
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function stories_single_view(Request $request) {

        try {

            $rules = ['story_unique_id' => 'required|exists:stories,unique_id'];

            Helper::custom_validator($request->all(),$rules);
            
            $blocked_users = blocked_users($request->id);
            
            $story = Story::with('storyFiles')->Approved()->whereNotIn('stories.user_id',$blocked_users)->where('stories.unique_id', $request->story_unique_id)->first();

            if(!$story) {
                throw new Exception(api_error(220), 220);   
            }

            $story = \App\Repositories\StoriesRepository::stories_single_response($story, $request);

            $data['story'] = $story;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

}