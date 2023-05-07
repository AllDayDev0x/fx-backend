<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use DB, Log, Hash, Validator, Exception, Setting, Helper;

use App\Models\User, App\Models\CategoryDetail, App\Models\Category;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Repositories\WalletRepository as WalletRepo;


class CategoryApiController extends Controller
{
    protected $loginUser, $skip, $take;

	public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

        $request->request->add(['timezone' => $this->timezone]);

    }    

    /** 
     * @method categories_list()
     *
     * @uses categories List (These categories will be displayed in the home page)
     *
     * @created Ganesh
     *
     * @updated Vithya R
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function categories_list(Request $request) {

        try {

            $base_query = $total_query = Category::CommonResponse()->Approved();

            $categories = $base_query->orderBy('categories.name', 'asc')->get();

            foreach($categories as $key => $category) {

                $category->total_creators = CategoryDetail::where('category_id', $category->category_id)
                                            ->where('type', CATEGORY_TYPE_PROFILE)
                                            ->whereHas('user', function($query) use ($request){

                    return $query->DocumentVerified()->Approved()->where('users.is_content_creator', CONTENT_CREATOR);

                })->count();

            }

            $data['categories'] = $categories;

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $categories);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method categories_view()
     *
     * @uses categories single view
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param
     *
     * @return json response with details
     */

    public function categories_view(Request $request) {

        try {

            $rules = ['category_unique_id' => 'required|exists:categories,unique_id'];

            $custom_errors = ['category_unique_id.exists' => api_error(300)];

            Helper::custom_validator($request->all(),$rules,$custom_errors);

            $category = Category::where('unique_id', $request->category_unique_id)->CommonResponse()->first();

            $data['category'] = $category;

            $base_query = $category->userCategoryDetails()->whereHas('user')->get();

            foreach($base_query as $user) {

                $user->followers_count = $user->user->followers->where('status',FOLLOWER_ACTIVE)->count() ?? 0;

            };

            $data['users'] = $base_query->sortByDesc('followers_count')->values() ?? [];

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
}
