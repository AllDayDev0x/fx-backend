<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Models\User, App\Models\UserProductPicture, App\Models\UserProduct, App\Models\Order, App\Models\DeliveryAddress;

use App\Models\Cart, App\Models\OrderProduct, App\Models\UserCard, App\Models\ProductSubCategory, App\Models\ProductCategory;

use App\Repositories\ProductRepository;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Jobs\SendEmailJob;

class UserProductApiController extends Controller
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
     * @method order_payments_list()
     *
     * @uses To display all the order payment list of perticular user
     *
     * @created Subham
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function order_payments_list(Request $request) {

        try {

            $base_query = $total_query = Order::where('user_id',$request->id)->orderBy('orders.created_at', 'desc');

            $orders = $base_query->skip($this->skip)->take($this->take)->get();

            $orders = ProductRepository::orders_list_response($orders, $request);

            $data['orders'] = $orders ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method delivery_addresses_list()
     *
     * @uses To display all the Address Details of perticular user
     *
     * @created Subham
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function delivery_addresses_list(Request $request) {

        try {

            $base_query = $total_query = DeliveryAddress::where('user_id',$request->id)->orderBy('delivery_addresses.created_at', 'desc');

            if($request->search_key){

                $base_query = $base_query->where('unique_id',$request->search_key);

            }

            $delivery_addresses = $base_query->get();

            $data['delivery_addresses'] = $delivery_addresses ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method orders_list_for_others()
     *
     * @uses To display all the order list of perticular user
     *
     * @created Subham
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function orders_list_for_others(Request $request) {

        try {

            $base_query = $total_query = Order::where('user_id',$request->id)->orderBy('orders.created_at', 'desc');

            $orders = $base_query->skip($this->skip)->take($this->take)->get();

            $orders = ProductRepository::orders_list_response($orders, $request);

            $data['orders'] = $orders ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method orders_view_for_others()
     *
     * @uses get the selected post details
     *
     * @created Subham
     *
     * @updated 
     *
     * @param
     *
     * @return JSON Response
     */
    public function orders_view_for_others(Request $request) {

        try {

            $rules = ['order_unique_id' => 'required|exists:orders,unique_id'];

            Helper::custom_validator($request->all(),$rules);
            
            $order = Order::where('unique_id', $request->order_unique_id)->first();

            if(!$order) {
                throw new Exception(api_error(139), 139);   
            }

            $order = ProductRepository::order_view_single_response($order, $request);

            $data['order'] = $order;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method ecommerce_home()
     *
     * @uses To display all the product
     *
     * @created Subham
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function ecommerce_home(Request $request) {

        try {

            $base_query = $total_query = UserProduct::Approved()->where('user_id', '!=', $request->id);

            if ($request->filled('search_key')) {
                
                $user_product_ids = UserProduct::where('name','LIKE','%'.$request->search_key.'%')
                                ->orWhere('description','LIKE','%'.$request->search_key.'%')->pluck('id');

                $base_query = $base_query->whereIn('id', $user_product_ids);

            }

            if ($request->filled('sort_by')) {
                
                $sort_by = $request->sort_by == 'price_hl' ? 'desc' : 'asc';

                $base_query = $base_query->orderBy('price', $sort_by);
            }
            else {

                $base_query = $base_query->orderBy('user_products.created_at', 'desc');
            }

            $data['total'] = $total_query->count() ?? 0;

            $user_products = $base_query->skip($this->skip)->take($this->take)->get();

            $user_products = ProductRepository::user_products_list_response($user_products, $request);

            $data['user_products'] = $user_products ?? [];

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method user_products_view_for_others()
     *
     * @uses get the selected post details
     *
     * @created Subham
     *
     * @updated 
     *
     * @param
     *
     * @return JSON Response
     */
    public function user_products_view_for_others(Request $request) {

        try {

            $rules = ['user_products_unique_id' => 'required|exists:user_products,unique_id'];

            Helper::custom_validator($request->all(),$rules);
            
            $user_product = UserProduct::with('userProductPictures')
                ->with('user')
                ->whereHas('user')
                ->Approved()
                ->where('user_products.unique_id', $request->user_products_unique_id)
                ->first();

            if(!$user_product) {
                throw new Exception(api_error(139), 139);   
            }

            $user_product = ProductRepository::user_product_single_response($user_product, $request);

            $data['user_product'] = $user_product;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

	/**
     * @method user_products_index()
     *
     * @uses To list out stardom products details 
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param 
     * 
     * @return JSON Response
     *
     */
    public function user_products_index(Request $request) {

    	try {

            $base_query = $total_query = UserProduct::where('user_id', $request->id);

            if ($request->search_key) {
                
                $user_product_ids = UserProduct::where('name','LIKE','%'.$request->search_key.'%')
                                ->orWhere('description','LIKE','%'.$request->search_key.'%')->pluck('id');

                $base_query = $base_query->whereIn('id', $user_product_ids);
            }

            $user_products = $base_query->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();

	        $data['user_products'] = $user_products ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method user_products_save()
     *
     * @uses To save the stardom products details of new/existing
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object request - Stardom Product Form Data
     *
     * @return JSON Response
     *
     */
    public function user_products_save(Request $request) {
        
        try {
            
            DB::begintransaction();

            $rules = [
                'name' => 'required|max:191',
                'quantity' => 'required|max:100',
                'price' => 'required|max:100',
                'picture' => 'mimes:jpg,png,jpeg',
                'product_category_id' => 'required|exists:product_categories,id',
                'product_sub_category_id' => 'required|exists:product_sub_categories,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product = UserProduct::find($request->user_product_id) ?? new UserProduct;

            $success_code = $user_product->id ? 122 : 121;

            $user_product->user_id = $request->id;

            $user_product->name = $request->name ?: $user_product->name;

            $user_product->quantity = $request->quantity ?: $user_product->quantity;

            $user_product->is_outofstock = $request->quantity > 0 ? IN_STOCK : OUT_OF_STOCK;

            $amount = $request->price ?? 0;

            if(Setting::get('is_only_wallet_payment')) {

                $user_product->token = $amount;

                $user_product->price = $user_product->token * Setting::get('token_amount');

            } else {

                $user_product->price = $amount;

            }

            $user_product->product_category_id = $request->product_category_id ?: $user_product->product_category_id;

            $user_product->product_sub_category_id = $request->product_sub_category_id ?: $user_product->product_sub_category_id;

            $user_product->description = $request->description ?: '';

            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->user_product_id) {

                    Helper::storage_delete_file($user_product->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $user_product->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
            }

            if($user_product->save()) {

                DB::commit(); 

                $data = UserProduct::find($user_product->id);

                return $this->sendResponse(api_success($success_code), $success_code, $data);

            } 

            throw new Exception(api_error(130), 130);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 

    }

    /**
     * @method user_products_view()
     *
     * @uses displays the specified user product details based on user product id
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object $request - user product Id
     * 
     * @return JSON Response
     *
     */
    public function user_products_view(Request $request) {
       
        try {
      	
      		$rules = [
                'user_product_id' => 'required|exists:user_products,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules);

            $is_only_wallet_payment = Setting::get('is_only_wallet_payment');
            
            $user_product = UserProduct::where('id',$request->user_product_id)
              ->when($is_only_wallet_payment == NO, function ($q) use ($is_only_wallet_payment) {
                return $q->OriginalResponse();
              })
              ->when($is_only_wallet_payment == YES, function($q) use ($is_only_wallet_payment) {
                return $q->TokenResponse();
              })
              ->first();

            if(!$user_product) { 

                throw new Exception(api_error(133), 133);                
            }

            $data['user_product'] = $user_product;

            return $this->sendResponse($message = "", $success_code = "", $data);
            
        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method user_products_delete()
     *
     * @uses delete the stardom product details based on stardom id
     *
     * @created Bhawya
     *
     * @updated  
     *
     * @param object $request - Stardom Id
     * 
     * @return response of details
     *
     */
    public function user_products_delete(Request $request) {

        try {

            DB::begintransaction();

            $rules = [
                'user_product_id' => 'required|exists:user_products,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules,$custom_errors = []);

            $user_product = UserProduct::find($request->user_product_id);

            if(!$user_product) { 

                throw new Exception(api_error(133), 133);                
            }

            $user_product = UserProduct::destroy($request->user_product_id);

            DB::commit();

            $data['user_product_id'] = $request->user_product_id;

            return $this->sendResponse(api_success(123), $success_code = 123, $data);
            
        } catch(Exception $e){

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }       
         
    }

    /**
     * @method user_products_update_availability
     *
     * @uses To update stardom product - Available / Out of Stock
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return response success/failure message
     *
     **/
    public function user_products_update_availability(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'user_product_id' => 'required|exists:user_products,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product = UserProduct::find($request->user_product_id);

            if(!$user_product) { 

                throw new Exception(api_error(133), 133);                
            }

            $user_product->is_outofstock = $user_product->is_outofstock ? PRODUCT_NOT_AVAILABLE : PRODUCT_AVAILABLE;

            if($user_product->save()) {

                DB::commit();

                $success_code = $user_product->is_outofstock ? 126 : 127;

                $data['user_product'] = $user_product;

                return $this->sendResponse(api_success($success_code),$success_code, $data);

            }
            
            throw new Exception(api_error(130), 130);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method user_products_set_visibility
     *
     * @uses To update stardom product status as DECLINED/APPROVED
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return response success/failure message
     *
     **/
    public function user_products_set_visibility(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'user_product_id' => 'required|exists:user_products,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product = UserProduct::find($request->user_product_id);

            if(!$user_product) { 

                throw new Exception(api_error(133), 133);                
            }

            $user_product->is_visible = $user_product->is_visible ? NO : YES;

            if($user_product->save()) {

                DB::commit();

                $success_code = $user_product->is_visible ? 124 : 125;

                $data['user_product'] = $user_product;

                return $this->sendResponse(api_success($success_code),$success_code, $data);

            }
            
            throw new Exception(api_error(130), 130);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method product_categories
     *
     * @uses List Product Categories
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return response success/failure message
     *
     **/
    public function product_categories(Request $request) {

        try {

            $product_categories = ProductCategory::where('status',APPROVED)->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();

            $product_categories = selected($product_categories, '', 'id');

            if($request->user_product_id) {

                $user_product = UserProduct::find($request->user_product_id);

                $product_category_id = $user_product->product_category_id;

                $product_categories = selected($product_categories, $product_category_id, 'id');
            }

            $data['product_categories'] = $product_categories;

            return $this->sendResponse($message = "", $success_code = "", $data);
            

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method product_sub_categories
     *
     * @uses List Product Sub Categories
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return response success/failure message
     *
     **/
    public function product_sub_categories(Request $request) {

        try {

            $base_query = ProductSubCategory::where('status',APPROVED);

            if($request->product_category_id){

                $base_query = $base_query->where('product_category_id',$request->product_category_id);
            }

            $product_sub_categories = $base_query->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();

            $product_sub_categories = selected($product_sub_categories, '', 'id');

            if($request->user_product_id) {

                $user_product = UserProduct::find($request->user_product_id);
                
                $product_sub_category_id = $user_product->product_sub_category_id;

                $product_sub_categories = selected($product_sub_categories, $product_sub_category_id, 'id');

            }

            $data['product_sub_categories'] = $product_sub_categories;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method user_products_search
     *
     * @uses Search Products
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object $request - search_key
     * 
     * @return response success/failure message
     *
     **/
    public function user_products_search(Request $request) {

        try {

            $rules = [
                'search_key' => 'nullable',
            ];

            Helper::custom_validator($request->all(),$rules);

            $base_query = UserProduct::Approved();

            $search_key = $request->search_key;

            if ($request->product_category_id) {
                
                $base_query = $base_query->where('product_category_id',$request->product_category_id);
            }

            if ($request->product_sub_category_id) {
                
                $base_query = $base_query->where('product_sub_category_id',$request->product_sub_category_id);
            }

            if($search_key) {

                $base_query = $base_query 

                    ->where(function ($query) use ($search_key) {

                        return $query->Where('user_products.name','LIKE','%'.$search_key.'%');

                    })->orWhereHas('productCategories', function($q) use ($search_key) {

                        return $q->Where('product_categories.name','LIKE','%'.$search_key.'%');

                    })->orWhereHas('productSubCategories', function($q) use ($search_key) {

                        return $q->Where('product_sub_categories.name','LIKE','%'.$search_key.'%');

                    });
            }

            if ($request->price_type) {
                
                $base_query = $base_query->orderBy('price', $request->price_type);
            }

            $user_products = $base_query->orderBy('updated_at','desc')->skip($this->skip)->take($this->take)->get();

            $user_products = ProductRepository::user_products_list_response($user_products, $request);

            $data['user_products'] = $user_products;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method user_product_pictures()
     *
     * @uses To load product images
     *
     * @created Bhawya N
     *
     * @updated 
     *
     * @param model image object - $request
     *
     * @return response of succes failure 
     */
    public function user_product_pictures(Request $request) {

        try {

            $rules = [
                'user_product_id' => 'required|exists:user_products,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product_pictures = UserProductPicture::where('user_product_id', $request->user_product_id)
                ->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();
               
            $data['user_product_pictures'] = $user_product_pictures;

            return $this->sendResponse($message = "", $success_code = "", $data);
                
        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method user_product_pictures_save()
     *
     * @uses To save gallery product pictures
     *
     * @created Bhawya N
     *
     * @updated 
     *
     * @param object $request - Model Object
     *
     * @return response of success / Failure
     */
    public function user_product_pictures_save(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'user_product_id' => 'required|exists:user_products,id,user_id,'.$request->id,
                'picture' => 'required|mimes:jpg,jpeg,png',
            ];

            Helper::custom_validator($request->all(),$rules);
            
            if($request->hasfile('picture')) {

                ProductRepository::user_product_pictures_save($request->file('picture'), $request->user_product_id);

                DB::commit();

                return $this->sendResponse(api_success(133), $success_code = 133, $data = '');
            
            }

            throw new Exception(api_error(130), 130);
            
        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method user_product_pictures_delete()
     *
     * @uses To delete Product Image
     *
     * @created  Bhawya N
     *
     * @updated  
     *
     * @param model image object - $request
     *
     * @return response of succes failure 
     */
    public function user_product_pictures_delete(Request $request) {

        try {

            $rules = [
                'user_product_picture_id' => 'required|exists:user_product_pictures,id'
            ];

            Helper::custom_validator($request->all(),$rules);
                
            $user_product_pictures = UserProductPicture::find($request->user_product_picture_id);

            if(!$user_product_pictures) { 

                throw new Exception(api_error(133), 133);                
            }

            $user_product_pictures = UserProductPicture::destroy($request->user_product_picture_id);

            DB::commit();

            $data['user_product_picture_id'] = $request->user_product_picture_id;

            return $this->sendResponse(api_success(132), $success_code = 132, $data);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method carts_list()
     *
     * @uses To list out carts based on user id 
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param 
     * 
     * @return JSON Response
     *
     */
    public function carts_list(Request $request)
    {
        try {

            $base_query = $total_query = Cart::where('user_id', $request->id)->whereHas('user_product')->with('user_product');

            $carts = $base_query->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();

            $sub_total = Cart::where('user_id', $request->id)->sum('sub_total');

            $total = Cart::where('user_id', $request->id)->sum('total');

            $data['carts'] = $carts ?? [];

            $data['total'] = $total_query->count() ?? 0;

            $data['sub_total_amount'] = $sub_total ?? 0;

            $data['total_amount'] = $total ?? 0;

            $data['sub_total_formatted'] = formatted_amount($sub_total) ?? 0;

            $data['total_formatted'] = formatted_amount($total) ?? 0;
    
            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method carts_save()
     *
     * @uses To save the carts
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param 
     * 
     * @return JSON Response
     *
     */
    public function carts_save(Request $request)
    {
        try {
            
            DB::begintransaction();

            $rules = [
                'user_product_id' => 'required|exists:user_products,id',
                'quantity' => 'required|max:100',
            ];

            Helper::custom_validator($request->all(),$rules);

            $product = UserProduct::find($request->user_product_id);

            if (Setting::get('buy_single_user_products')) {
                
                $cart = Cart::where('user_id', $request->id)->first();

                if ($cart) {
                    
                    $user_product = UserProduct::find($cart->user_product_id);

                    if ($product->user_id != $user_product->user_id) {
                        
                        throw new Exception(api_error(249), 249);  
                    }
                }
                
            }

            if ($product->user_id == $request->id) {
                
                throw new Exception(api_error(244), 244);  
            }

            if ($product->quantity < $request->quantity) {
                
                throw new Exception(api_error(245), 245);  
            }

            $product_price = Setting::get('is_only_wallet_payment') ? $product->token : $product->price;

            $cart = Cart::find($request->cart_id) ?? new Cart;

            $success_code = $cart->id ? 218 : 217;

            $cart->user_id = $request->id;

            $cart->order_id = "";

            $cart->user_product_id = $request->user_product_id;

            $cart->quantity = $request->quantity ?: $cart->quantity;

            $cart->per_quantity_price = $product_price;

            $cart->sub_total = ($request->quantity * $product_price) ?? $cart->sub_total;

            $cart->total = ($request->quantity * $product_price) ?? $cart->total;

            if($cart->save()) {

                DB::commit(); 

                $data = Cart::find($cart->id);

                return $this->sendResponse(api_success($success_code), $success_code, $data);

            } 

            throw new Exception(api_error(222), 222);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    }

    /**
     * @method carts_remove()
     *
     * @uses Removes the products from the cart
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param 
     * 
     * @return JSON Response
     *
     */
    public function carts_remove(Request $request) {

        try {

            DB::begintransaction();

            $cart = Cart::where('user_id', $request->id);
            
            if(!$cart) { 

                throw new Exception(api_error(221), 221);                
            }

            if($request->cart_id){

                $cart = Cart::where('id', $request->cart_id)->where('user_id', $request->id);
            }

            $cart->delete();

            DB::commit();

            $data['user_id'] = $request->id;

            $data['cart_id'] = $request->cart_id ?? [];

            return $this->sendResponse(api_success(220), $success_code = 220, $data);
            
        } catch(Exception $e){

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 

    }    

    /**
     * @method orders_payment_by_wallet()
     * 
     * @uses Create new order
     *
     * @created Arun
     *
     * @updated
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function orders_payment_by_wallet(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                    'cart_ids' => 'required',
                    'delivery_address_id' => 'nullable|exists:delivery_addresses,id,user_id,'.$request->id,
                    'name' => 'required_without:delivery_address_id',
                    'address' => 'required_without:delivery_address_id',
                    'pincode' => 'required_without:delivery_address_id',
                    'state' => 'required_without:delivery_address_id',
                    'landmark' => 'required_without:delivery_address_id',
                    'contact_number' => 'required_without:delivery_address_id',
                ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $delivery_address = DeliveryAddress::find($request->delivery_address_id) ?? new DeliveryAddress;

            $delivery_address->user_id = $request->id;

            $delivery_address->name = $request->name ?? $delivery_address->name;

            $delivery_address->address = $request->address ?? $delivery_address->address;

            $delivery_address->pincode = $request->pincode ?? $delivery_address->pincode;

            $delivery_address->state = $request->state ?? $delivery_address->state;

            $delivery_address->landmark = $request->landmark ?? $delivery_address->landmark;

            $delivery_address->contact_number = $request->contact_number ?? $delivery_address->contact_number;

            $delivery_address->is_default = $request->is_default ?? ($delivery_address->is_default ?? NO);

            $delivery_address->save();

            if ($request->is_default) {
                
                DeliveryAddress::where('id', '!=', $delivery_address->id)->update(['is_default' => NO]);
            }

            if(!is_array($request->cart_ids)) {

                $request->cart_ids = explode(',', $request->cart_ids);
                
            }

            $sub_total = $total = 0.00;

            $carts = Cart::whereIn('id',$request->cart_ids)->get();

            $order = Order::find($request->order_id) ?? new Order;

            $order->user_id = $request->id;

            $order->delivery_address_id = $delivery_address->id ?? 0;

            $order->total_products = count($request->cart_ids) ?? 0;

            $order->save();

            if ($order) {

                foreach ($carts as $key => $cart) {

                    $order_product_response = PaymentRepo::order_product_save($request, $order, $cart)->getData();

                    if(!$order_product_response->success) {

                        throw new Exception($order_product_response->error, $order_product_response->error_code);
                    }

                    $sub_total = $sub_total + $cart->sub_total;

                    $total = $total + $cart->total;
                }

                $order->sub_total = $sub_total;

                $order->total = $total;

                $order->save();
                
            }

            $request->request->add(['payment_mode' => PAYMENT_MODE_WALLET]);

            $user_pay_amount = $total;

            $user_wallet = \App\Models\UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet->remaining ?? 0;

            if(Setting::get('is_referral_enabled')) {

                $remaining = $remaining + $user_wallet->referral_amount;
                
            }            

            if($remaining < $user_pay_amount) {
                throw new Exception(api_error(147), 147);    
            }

            if($user_pay_amount > 0) {
                
                $request->request->add([
                    'total' => $total * Setting::get('token_amount'),
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount * Setting::get('token_amount'), 
                    'payment_id' => 'AC-'.rand(),
                    'payment_mode' => PAYMENT_MODE_WALLET,
                    'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                    'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                    'usage_type' => USAGE_TYPE_ORDER,
                    'tokens' => $user_pay_amount,
                ]);

                $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();
                
                if($wallet_payment_response->success) {

                    $payment_response = PaymentRepo::order_payments_save($request, $order)->getData();

                    if(!$payment_response->success) {

                        throw new Exception($payment_response->error, $payment_response->error_code);
                    }

                    PaymentRepo::order_product_quantity_update($order)->getData();

                    $carts = Cart::whereIn('id',$request->cart_ids)->delete();

                    DB::commit();

                    return $this->sendResponse(api_success(239), 239, $order);

                } else {

                    throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                    
                }
            
            }

            $payment_response = PaymentRepo::order_payments_save($request, $order)->getData();
           
            if($payment_response->success) {

                PaymentRepo::order_product_quantity_update($order)->getData();

                $carts = Cart::whereIn('id',$request->cart_ids)->delete();
                
                DB::commit();
               
                return $this->sendResponse(api_success(239), 239, $order);

            } else {
              
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }


        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method orders_payment_by_stripe()
     * 
     * @uses Create new order
     *
     * @created Subham Kant
     *
     * @updated
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function orders_payment_by_stripe(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                    'cart_ids' => 'required',
                    'delivery_address_id' => 'nullable|exists:delivery_addresses,id,user_id,'.$request->id,
                    'name' => 'required_without:delivery_address_id',
                    'address' => 'required_without:delivery_address_id',
                    'pincode' => 'required_without:delivery_address_id',
                    'state' => 'required_without:delivery_address_id',
                    'landmark' => 'required_without:delivery_address_id',
                    'contact_number' => 'required_without:delivery_address_id',
                ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $delivery_address = DeliveryAddress::find($request->delivery_address_id) ?? new DeliveryAddress;

            $delivery_address->user_id = $request->id;

            $delivery_address->name = $request->name ?? $delivery_address->name;

            $delivery_address->address = $request->address ?? $delivery_address->address;

            $delivery_address->pincode = $request->pincode ?? $delivery_address->pincode;

            $delivery_address->state = $request->state ?? $delivery_address->state;

            $delivery_address->landmark = $request->landmark ?? $delivery_address->landmark;

            $delivery_address->contact_number = $request->contact_number ?? $delivery_address->contact_number;

            $delivery_address->is_default = $request->is_default ?? ($delivery_address->is_default ?? NO);

            $delivery_address->save();

            if ($request->is_default) {
                
                DeliveryAddress::where('id', '!=', $delivery_address->id)->update(['is_default' => NO]);
            }

            if(!is_array($request->cart_ids)) {

                $request->cart_ids = explode(',', $request->cart_ids);
                
            }

            $sub_total = $total = 0.00;

            $carts = Cart::whereIn('id',$request->cart_ids)->get();

            $order = Order::find($request->order_id) ?? new Order;

            $order->user_id = $request->id;

            $order->delivery_address_id = $delivery_address->id ?? 0;

            $order->total_products = count($request->cart_ids) ?? 0;

            $order->save();

            if ($order) {

                foreach ($carts as $key => $cart) {

                    $order_product_response = PaymentRepo::order_product_save($request, $order, $cart)->getData();

                    if(!$order_product_response->success) {

                        throw new Exception($order_product_response->error, $order_product_response->error_code);
                    }

                    $sub_total = $sub_total + $cart->sub_total;

                    $total = $total + $cart->total;
                }

                $order->sub_total = $sub_total;

                $order->total = $total;

                $order->save();
                
            }

            $request->request->add(['payment_mode' => CARD]);

            $user_pay_amount = $total ?? 0;

            if($user_pay_amount > 0) {

                $user_card = UserCard::where('user_id', $request->id)->firstWhere('is_default', YES);

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

                $card_payment_response = PaymentRepo::orders_payment_by_stripe($request)->getData();
                
                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                    
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);
                
            }


            $payment_response = PaymentRepo::order_payments_save($request, $order)->getData();

            if($payment_response->success) {

                PaymentRepo::order_product_quantity_update($order)->getData();
            
                $carts = Cart::whereIn('id',$request->cart_ids)->delete();

                DB::commit();

                return $this->sendResponse(api_success(239), 239, $order);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }
               
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method orders_payment_by_paypal()
     * 
     * @uses Create new order using Paypal
     *
     * @created Subham Kant
     *
     * @updated
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function orders_payment_by_paypal(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                    'payment_id'=>'required',
                    'cart_ids' => 'required',
                    'delivery_address_id' => 'nullable|exists:delivery_addresses,id,user_id,'.$request->id,
                    'name' => 'required_without:delivery_address_id',
                    'address' => 'required_without:delivery_address_id',
                    'pincode' => 'required_without:delivery_address_id',
                    'state' => 'required_without:delivery_address_id',
                    'landmark' => 'required_without:delivery_address_id',
                    'contact_number' => 'required_without:delivery_address_id',
                ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $delivery_address = DeliveryAddress::find($request->delivery_address_id) ?? new DeliveryAddress;

            $delivery_address->user_id = $request->id;

            $delivery_address->name = $request->name ?? $delivery_address->name;

            $delivery_address->address = $request->address ?? $delivery_address->address;

            $delivery_address->pincode = $request->pincode ?? $delivery_address->pincode;

            $delivery_address->state = $request->state ?? $delivery_address->state;

            $delivery_address->landmark = $request->landmark ?? $delivery_address->landmark;

            $delivery_address->contact_number = $request->contact_number ?? $delivery_address->contact_number;

            $delivery_address->is_default = $request->is_default ?? ($delivery_address->is_default ?? NO);

            $delivery_address->save();

            if ($request->is_default) {
                
                DeliveryAddress::where('id', '!=', $delivery_address->id)->update(['is_default' => NO]);
            }

            if(!is_array($request->cart_ids)) {

                $request->cart_ids = explode(',', $request->cart_ids);
                
            }

            $sub_total = $total = 0.00;

            $carts = Cart::whereIn('id',$request->cart_ids)->get();

            $order = Order::find($request->order_id) ?? new Order;

            $order->user_id = $request->id;

            $order->delivery_address_id = $delivery_address->id ?? 0;

            $order->total_products = count($request->cart_ids) ?? 0;

            $order->save();

            if ($order) {

                foreach ($carts as $key => $cart) {

                    $order_product_response = PaymentRepo::order_product_save($request, $order, $cart)->getData();

                    if(!$order_product_response->success) {

                        throw new Exception($order_product_response->error, $order_product_response->error_code);
                    }

                    $sub_total = $sub_total + $cart->sub_total;

                    $total = $total + $cart->total;
                }

                $order->sub_total = $sub_total;

                $order->total = $total;

                $order->save();
                
            }

            $request->request->add(['payment_mode' => CARD]);

            $user_pay_amount = $total ?? 0;

            $request->request->add(['payment_mode'=> PAYPAL,'user_pay_amount' => $user_pay_amount,'paid_amount' => $user_pay_amount, 'payment_id' => $request->payment_id, 'paid_status' => PAID_STATUS]);

            $payment_response = PaymentRepo::order_payments_save($request, $order)->getData();
            
            if($payment_response->success) {

                PaymentRepo::order_product_quantity_update($order)->getData();
            
                $carts = Cart::whereIn('id',$request->cart_ids)->delete();

                DB::commit();

                return $this->sendResponse(api_success(239), 239, $order);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method user_products_orders_list()
     *
     * @uses To display all the orders list of perticular product
     *
     * @created Arun
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function user_products_orders_list(Request $request) {

        try {

            $rules = [
                    'user_product_id' => 'required|exists:user_products,id,user_id,'.$request->id,
                ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $order_ids = OrderProduct::where('user_product_id', $request->user_product_id)->pluck('order_id');

            $base_query = $total_query = Order::whereIn('id',$order_ids)->orderBy('orders.created_at', 'desc');

            $orders = $base_query->skip($this->skip)->take($this->take)->get();

            $orders = ProductRepository::user_products_orders_list_response($orders, $request);

            $data['orders'] = $orders ?? [];

            $data['total'] = $total_query->count() ?? 0;

            $data['user_product'] = UserProduct::find($request->user_product_id) ?? [];

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method order_cancel()
     *
     * @uses To cancel the order via order id
     *
     * @created Subham Kant
     *
     * @updated 
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function order_cancel(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                        'order_id' => 'required|exists:orders,id'
                    ];

            Helper::custom_validator($request->all(), $rules);

            $order = Order::where('user_id',$request->id)->where('id',$request->order_id)->where('status','!=',ORDER_CACELLED)->first();

            if(!$order) {
                throw new Exception(api_error(246), 246);   
            }

            $order_products = OrderProduct::where('order_id',$order->id)->where('status','!=',NO)->count();

            if($order_products > 0){
                throw new Exception(api_error(247), 247);
            }

            $order->status = ORDER_CACELLED;

            OrderProduct::where('order_id',$order->id)->update(['status' => ORDER_CACELLED]);

            if($order->save()){

                $request->request->add(['paid_amount' => $order->total, 'payment_id' => 'OC-'.rand(), 'paid_status' => USER_WALLET_PAYMENT_PAID,'total' => $order->total,'user_pay_amount' => $order->total]);

                $payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

                if($payment_response->success) {
                    
                    DB::commit();

                    $order_products = OrderProduct::where('order_id',$order->id)->get();

                    foreach($order_products as $order_product){

                        $email_data['subject'] = tr('order_cancel_email' , Setting::get('site_name'));

                        $email_data['email']  = $order_product->userProductDetails->user->email ?? tr('n_a');

                        $email_data['order_unique_id']  = $order->unique_id ?: tr('n_a');

                        $email_data['refunded_amount']  = $order_product->total ?: 0;

                        $email_data['message'] = tr('order_cancelled_by_user' , $order_product->user->name);

                        $email_data['page'] = "emails.users.order-cancel";

                        $this->dispatch(new SendEmailJob($email_data));

                    }

                    $data['payment_response'] = $payment_response->data;

                    $data['order_response'] = $order;

                    return $this->sendResponse(api_success(117), 117, $data);

                } else {

                    throw new Exception($payment_response->error, $payment_response->error_code);
                    
                }

            }

            throw new Exception(api_error(222), 222);

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method order_status_update()
     *
     * @uses To change the status of the order placed by the buyer
     *
     * @created Subham Kant
     *
     * @updated 
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function order_status_update(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                        'status' => 'required|numeric',
                        'order_product_id' => 'required|exists:order_products,id'
                    ]; 

            Helper::custom_validator($request->all(), $rules);

            $order_product = OrderProduct::find($request->order_product_id);

            $order_product->status = $request->status;

            $order_product->save();

            $data = $order_product;
            
            DB::commit();

            return $this->sendResponse(api_success(241), 241, $data);

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method order_product_cancel()
     *
     * @uses To cancel the order via order id
     *
     * @created Subham Kant
     *
     * @updated 
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function order_product_cancel(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                        'order_product_id' => 'required|exists:order_products,id'
                    ];

            Helper::custom_validator($request->all(), $rules);

            $order_product = OrderProduct::where('id',$request->order_product_id)->where('status',NO)->first();

            if(!$order_product){
                throw new Exception(api_error(248), 248);
            }

            $order_product->status = ORDER_CACELLED;

            if($order_product->save()){

                $request->request->add(['paid_amount' => $order_product->total, 'payment_id' => 'OPC-'.rand(), 'paid_status' => USER_WALLET_PAYMENT_PAID,'total' => $order_product->total,'user_pay_amount' => $order_product->total]);

                $payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

                if($payment_response->success) {
                    
                    DB::commit();

                    $email_data['subject'] = tr('order_cancel_email' , Setting::get('site_name'));

                    $email_data['email']  = $order_product->userProductDetails->user->email ?? tr('n_a');

                    $email_data['order_unique_id']  = $order_product->userOrder->unique_id ?: tr('n_a');

                    $email_data['refunded_amount']  = $order_product->total ?: 0;

                    $email_data['message'] = tr('order_cancelled_by_user' , $order_product->user->name);

                    $email_data['page'] = "emails.users.order-cancel";

                    $this->dispatch(new SendEmailJob($email_data));

                    $data['payment_response'] = $payment_response->data;

                    $data['order_product_response'] = $order_product;

                    return $this->sendResponse(api_success(117), 117, $data);

                } else {

                    throw new Exception($payment_response->error, $payment_response->error_code);
                    
                }

            }

            throw new Exception(api_error(222), 222);

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method other_model_product_list()
     *
     * @uses To display all the product of a model
     *
     * @created Arun
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function other_model_product_list(Request $request) {

        try {

            $rules = [
                        'user_unique_id' => 'required|exists:users,unique_id'
                    ];

            Helper::custom_validator($request->all(), $rules);

            $user = User::firstWhere('unique_id', $request->user_unique_id);

            $base_query = $total_query = UserProduct::Approved()->where('user_id', $user->id);

            if ($request->filled('search_key')) {
                
                $user_product_ids = UserProduct::where('name','LIKE','%'.$request->search_key.'%')
                                ->orWhere('description','LIKE','%'.$request->search_key.'%')->pluck('id');

                $base_query = $base_query->whereIn('id', $user_product_ids);

            }

            if ($request->filled('sort_by')) {
                
                $sort_by = $request->sort_by == 'price_hl' ? 'desc' : 'asc';

                $base_query = $base_query->orderBy('price', $sort_by);
            }
            else {

                $base_query = $base_query->orderBy('user_products.created_at', 'desc');
            }

            $user_products = $base_query->skip($this->skip)->take($this->take)->get();

            $user_products = ProductRepository::user_products_list_response($user_products, $request);

            $data['user_products'] = $user_products ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

}