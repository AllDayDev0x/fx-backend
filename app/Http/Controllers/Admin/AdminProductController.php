<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

use App\Models\User;

use App\Models\UserProduct, App\Models\UserProductPicture;

use App\Models\OrderProduct;

use App\Models\ProductCategory, App\Models\ProductSubCategory;


class AdminProductController extends Controller
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
     * @method user_products_index()
     *
     * @uses To list out stardom products details 
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function user_products_index(Request $request) {

        $base_query = UserProduct::orderBy('created_at','DESC');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('user',function($query) use($search_key) {

                return $query->where('users.name','LIKE','%'.$search_key.'%');

            })->orWhere('user_products.name','LIKE','%'.$search_key.'%');
        }

        if($request->product_category_id){

            $base_query = $base_query->where('product_category_id',$request->product_category_id);
        }

        if($request->product_sub_category_id){

            $base_query = $base_query->where('product_sub_category_id',$request->product_sub_category_id);
        }

        if($request->user_id){

            $base_query = $base_query->where('user_id',$request->user_id);
        }

        $product_category = ProductCategory::find($request->product_category_id)??'';

        $product_sub_category = ProductSubCategory::find($request->product_sub_category_id)??'';

        $user = User::find($request->user_id)??'';
       
        $user_products = $base_query->paginate($this->take);

        return view('admin.user_products.index')
                ->with('page', 'user_products')
                ->with('sub_page' , 'user_products-view')
                ->with('product_category',$product_category)
                ->with('product_sub_category',$product_sub_category)
                ->with('user',$user)
                ->with('user_products' , $user_products);
    }

    /**
     * @method user_products_create()
     *
     * @uses To create stardom product details
     *
     * @created  Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function user_products_create() {

        $user_product = new UserProduct;

        $users = User::Approved()->where('is_content_creator', CONTENT_CREATOR)->where('status', APPROVED)->get();

        $product_categories = ProductCategory::orderby('name', 'asc')->where('status',APPROVED)->get();

        $product_sub_categories = [];

        return view('admin.user_products.create')
                ->with('page', 'user_products')
                ->with('sub_page', 'user_products-create')
                ->with('user_product', $user_product)
                ->with('product_categories', $product_categories)
                ->with('product_sub_categories', $product_sub_categories)
                ->with('users', $users);           
    }

    /**
     * @method user_products_edit()
     *
     * @uses To display and update stardom product details based on the stardom product id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return redirect view page 
     *
     */
    public function user_products_edit(Request $request) {

        try {

            $user_product = UserProduct::find($request->user_product_id);

            if(!$user_product) { 

                throw new Exception(tr('user_product_not_found'), 101);
            }

            $users = User::Approved()->where('status', APPROVED)->get();

            foreach ($users as $key => $user) {

                $user->is_selected = NO;

                if($user_product->user_id == $user->id){
                    
                    $user->is_selected = YES;
                }

            }

            $product_categories = selected(ProductCategory::orderby('name', 'asc')->where('status',APPROVED)->get(), $user_product->product_category_id, 'id');

            $product_sub_categories = selected(ProductSubCategory::where('product_category_id' , $user_product->product_category_id)->where('status',APPROVED)->get(), $user_product->product_sub_category_id, 'id');
            
            return view('admin.user_products.edit')
                        ->with('page' , 'user_products')
                        ->with('sub_page', 'user_products-view')
                        ->with('user_product', $user_product)
                        ->with('users', $users)
                        ->with('product_categories', $product_categories)
                        ->with('product_sub_categories', $product_sub_categories); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.user_products.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method user_products_save()
     *
     * @uses To save the stardom products details of new/existing stardom product object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object request - Stardom Product Form Data
     *
     * @return success message
     *
     */
    public function user_products_save(Request $request) {
        
        try {
            
            DB::begintransaction();

            $rules = [
                'name' => 'required|max:191',
                'quantity' => 'required|max:100',
                'price' => 'required|max:100',
                'picture' => 'required|mimes:jpg,png,jpeg',
                'description' => 'max:199',
                'user_id' => 'required|exists:users,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            $user_product = UserProduct::find($request->user_product_id) ?? new UserProduct;

            if($user_product->id) {

                $message = tr('user_product_updated_success'); 

            } else {

                $message = tr('user_product_created_success');

            }

            $user_product->user_id = $request->user_id ?: $user_product->user_id;

            $user_product->name = $request->name ?: $user_product->name;

            $user_product->quantity = $request->quantity ?: $user_product->quantity;

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

            $user_product->is_outofstock = $user_product->quantity > 0 ? IN_STOCK : OUT_OF_STOCK;

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

                return redirect(route('admin.user_products.view', ['user_product_id' => $user_product->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('user_product_save_failed'));
            
        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method user_products_view()
     *
     * @uses displays the specified user product details based on user product id
     *
     * @created Akshata 
     *
     * @updated Subham Kant
     *
     * @param object $request - user product Id
     * 
     * @return View page
     *
     */
    public function user_products_view(Request $request) {
       
        try {
      
            $user_product = UserProduct::find($request->user_product_id);

            if(!$user_product) { 

                throw new Exception(tr('user_product_not_found'), 101);                
            }

            $product_galleries = UserProductPicture::where('user_product_id',$user_product->id)->get() ?? [];

            return view('admin.user_products.view')
                    ->with('page', 'user_products') 
                    ->with('sub_page', 'user_products-view')
                    ->with('user_product', $user_product)
                    ->with('product_galleries', $product_galleries);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method user_products_delete()
     *
     * @uses delete the stardom product details based on stardom id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - Stardom Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function user_products_delete(Request $request) {

        try {

            DB::begintransaction();

            $user_product = UserProduct::find($request->user_product_id);
            
            if(!$user_product) {

                throw new Exception(tr('user_product_not_found'), 101);                
            }

            if($user_product->delete()) {

                DB::commit();

                return redirect()->route('admin.user_products.index')->with('flash_success',tr('user_product_deleted_success'));   

            } 
            
            throw new Exception(tr('user_product_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method user_products_status
     *
     * @uses To update stardom product status as DECLINED/APPROVED based on stardom product id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return response success/failure message
     *
     **/
    public function user_products_status(Request $request) {

        try {

            DB::beginTransaction();

            $user_product = UserProduct::find($request->user_product_id);

            if(!$user_product) {

                throw new Exception(tr('user_product_not_found'), 101);
                
            }

            $user_product->status = $user_product->status ? DECLINED : APPROVED ;

            if($user_product->save()) {

                DB::commit();

                if($user_product->status == DECLINED) {

                    $email_data['subject'] = tr('product_decline_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('declined');

                } else {

                    $email_data['subject'] = tr('product_approve_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('approved');
                }

                $email_data['email']  = $user_product->user->email ?? "-";

                $email_data['name']  = $user_product->user->name ?? "-";

                $email_data['product_name']  = $user_product->name;

                $email_data['page'] = "emails.products.status";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                $message = $user_product->status ? tr('user_product_approve_success') : tr('user_product_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('user_product_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.user_products.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method user_products_dashboard()
     *
     * @uses 
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - stardom_wallet_id
     * 
     * @return View page
     *
     */
    public function user_products_dashboard(Request $request) {

        try {

            $user_product = UserProduct::where('id',$request->user_product_id)->first();

            if(!$user_product) {

                throw new Exception(tr('user_product_not_found'), 101);
            }

            $data = new \stdClass;

            $data->total_orders = OrderProduct::where('user_product_id',$user_product->id)->count();

            $data->today_orders = OrderProduct::where('user_product_id',$user_product->id)->whereDate('created_at',today())->count();

            $order_products_ids = OrderProduct::where('user_product_id',$user_product->id)->pluck('order_id');

            $data->total_revenue = $order_products_ids->count() > 0 ? \App\Models\OrderPayment::whereIn('order_id',$order_products_ids)->sum(Setting::get('is_only_wallet_payment') ? 'token' : 'total') : 0;

            $data->today_revenue = $order_products_ids->count() > 0 ? \App\Models\OrderPayment::whereIn('order_id',$order_products_ids)->whereDate('paid_date',today())->sum(Setting::get('is_only_wallet_payment') ? 'token' : 'total') : 0;

            $ids = count($order_products_ids) > 0 ? $order_products_ids : [];
            
            $data->analytics = last_x_days_revenue(7,$ids);
           
            return view('admin.user_products.dashboard')
                        ->with('page','user_products')
                        ->with('sub_page' , 'user_products-view')
                        ->with('data', $data)
                        ->with('user_product',$user_product);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }


    /**
     * @method order_products
     *
     * @uses Display all orders based the product details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Stardom Product Id
     * 
     * @return response success/failure message
     *
     **/
    public function order_products(Request $request) {

        try {

            DB::beginTransaction();

            $base_query = OrderProduct::where('user_product_id',$request->user_product_id)->orderBy('created_at','desc');

            if($request->search_key) {

                $search_key = $request->search_key;

                $order_product_ids = $base_query->whereHas('userOrder',function($query) use($search_key) {

                    return $query->where('orders.unique_id','LIKE','%'.$search_key.'%');

                })->orWhere('order_products.quantity','LIKE','%'.$search_key.'%')
                  ->orWhere('order_products.per_quantity_price','LIKE','%'.$search_key.'%')->pluck('id');

                $base_query = $base_query->whereIn('id',$order_product_ids);
            }

            $order_products = $base_query->paginate($this->take);

            $product = UserProduct::where('user_products.id', $request->user_product_id)->first();
            
            $title = tr('view_orders');

            return view('admin.user_products.order_products')
                        ->with('page', 'user_products')
                        ->with('sub_page', 'user_products-view')
                        ->with('title', $title)
                        ->with('product', $product)
                        ->with('order_products', $order_products);

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.user_products.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method product_categories_index()
     *
     * @uses To list out categories details 
     *
     * @created Akshata
     *
     * @updated Jeevan
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function product_categories_index(Request $request) {



        $base_query = ProductCategory::orderBy('updated_at','desc');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query =  $base_query->where('product_categories.name', 'LIKE','%'.$search_key.'%');
        }

        $product_categories = $base_query->paginate($this->take);

        return view('admin.product_categories.index')
                ->with('page', 'product_categories')
                ->with('sub_page' , 'product_categories-view')
                ->with('product_categories' , $product_categories);
    }

    /**
     * @method product_categories_create()
     *
     * @uses To create category details
     *
     * @created  Akshata
     *
     * @updated Jeevan
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function product_categories_create() {

        $product_category = new ProductCategory;

        return view('admin.product_categories.create')
                ->with('page', 'product_categories')
                ->with('sub_page', 'product_categories-create')
                ->with('product_category', $product_category);           
    }

    /**
     * @method product_categories_edit()
     *
     * @uses To display and update category details based on the category id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Category Id 
     * 
     * @return redirect view page 
     *
     */
    public function product_categories_edit(Request $request) {

        try {

            $product_category = ProductCategory::find($request->product_category_id);

            if(!$product_category) { 

                throw new Exception(tr('product_category_not_found'), 101);
            }

            return view('admin.product_categories.edit')
                ->with('page' , 'product_categories')
                ->with('sub_page', 'product_categories-view')
                ->with('product_category', $product_category); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.product_categories.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method product_categories_save()
     *
     * @uses To save the category details of new/existing category object based on details
     *
     * @created Akshata
     *
     * @updated Jeevan
     *
     * @param object request - Category Form Data
     *
     * @return success message
     *
     */
    public function product_categories_save(Request $request) {
        
        try {
            
            DB::begintransaction();

            $rules = [
                'name' => 'required|max:191',
                'picture' => 'mimes:jpg,png,jpeg',
                'discription' => 'max:199',
            ];

            Helper::custom_validator($request->all(),$rules);

            $product_category = ProductCategory::find($request->product_category_id) ?? new ProductCategory;

            if($product_category->id) {

                $message = tr('product_category_updated_success'); 

            } else {

                $message = tr('product_category_created_success');

            }

            $product_category->name = $request->name ?: $product_category->name;

            $product_category->description = $request->description ?: '';

            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->product_category_id) {

                    Helper::storage_delete_file($product_category->picture, CATEGORY_FILE_PATH); 
                    // Delete the old pic
                }

                $product_category->picture = Helper::storage_upload_file($request->file('picture'), CATEGORY_FILE_PATH);
            }

            if($product_category->save()) {

                DB::commit(); 

                return redirect(route('admin.product_categories.view', ['product_category_id' => $product_category->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('product_category_save_failed'));
            
        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method product_categories_view()
     *
     * @uses displays the specified category details based on category id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - category Id
     * 
     * @return View page
     *
     */
    public function product_categories_view(Request $request) {
       
        try {
      
            $product_category = ProductCategory::find($request->product_category_id);

            if(!$product_category) { 

                throw new Exception(tr('product_category_not_found'), 101);                
            }

            return view('admin.product_categories.view')
                    ->with('page', 'product_categories') 
                    ->with('sub_page', 'product_categories-view')
                    ->with('product_category', $product_category);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method product_categories_delete()
     *
     * @uses delete the category details based on category id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - Category Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function product_categories_delete(Request $request) {

        try {

            DB::begintransaction();

            $product_category = ProductCategory::find($request->product_category_id);
            
            if(!$product_category) {

                throw new Exception(tr('product_category_not_found'), 101);                
            }

            if($product_category->delete()) {

                DB::commit();

                return redirect()->route('admin.product_categories.index')->with('flash_success',tr('product_category_deleted_success'));   

            } 
            
            throw new Exception(tr('product_category_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method product_categories_status
     *
     * @uses To update category status as DECLINED/APPROVED based on category id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Category Id
     * 
     * @return response success/failure message
     *
     **/
    public function product_categories_status(Request $request) {

        try {

            DB::beginTransaction();

            $product_category = ProductCategory::find($request->product_category_id);

            if(!$product_category) {

                throw new Exception(tr('product_category_not_found'), 101);
                
            }

            $product_category->status = $product_category->status ? DECLINED : APPROVED ;

            if($product_category->save()) {

                DB::commit();

                $message = $product_category->status ? tr('product_category_approve_success') : tr('product_category_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('product_category_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.product_categories.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method product_sub_categories_index()
     *
     * @uses To list out sub_categories details 
     *
     * @created Akshata
     *
     * @updated Jeevan
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function product_sub_categories_index(Request $request) {

        $base_query = ProductSubCategory::orderBy('updated_at','desc');

        $search_key = $request->search_key;

        if($request->search_key) {

            $base_query = $base_query 
            ->whereHas('productCategory', function($q) use ($search_key) {

                return $q->Where('product_categories.name','LIKE','%'.$search_key.'%');

            })->orWhere('product_sub_categories.name','LIKE','%'.$search_key.'%');
        }
        
        if($request->product_category_id){

            $base_query = $base_query->where('product_category_id',$request->product_category_id);
        }

        $product_category = ProductCategory::find($request->product_category_id)??'';

        $product_sub_categories = $base_query->paginate($this->take);

        return view('admin.product_sub_categories.index')
                ->with('page', 'product_sub_categories')
                ->with('sub_page' , 'product_sub_categories-view')
                ->with('product_sub_categories' , $product_sub_categories)
                ->with('product_category',$product_category);
    }

    /**
     * @method product_sub_categories_create()
     *
     * @uses To create category details
     *
     * @created  Akshata
     *
     * @updated Jeevan
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function product_sub_categories_create() {

        $product_sub_category = new ProductSubCategory;

        $product_categories = selected(ProductCategory::where('status', APPROVED)->get(), '', 'id');

        return view('admin.product_sub_categories.create')
                ->with('page', 'product_sub_categories')
                ->with('sub_page', 'product_sub_categories-create')
                ->with('product_sub_category', $product_sub_category)
                ->with('product_categories',$product_categories);           
    }

    /**
     * @method product_sub_categories_edit()
     *
     * @uses To display and update category details based on the sub category id
     *
     * @created Akshata
     *
     * @updated Jeevan
     *
     * @param object $request - ProductSubCategory Id 
     * 
     * @return redirect view page 
     *
     */
    public function product_sub_categories_edit(Request $request) {

        try {

            $product_sub_category = ProductSubCategory::find($request->product_sub_category_id);

            if(!$product_sub_category) { 

                throw new Exception(tr('product_sub_category_not_found'), 101);
            }

            $product_categories = selected(ProductCategory::where('status', APPROVED)->get(), $product_sub_category->product_category_id, 'id');

            return view('admin.product_sub_categories.edit')
                ->with('page' , 'product_sub_categories')
                ->with('sub_page', 'product_sub_categories-view')
                ->with('product_sub_category', $product_sub_category)
                ->with('product_categories',$product_categories); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.product_sub_categories.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method product_sub_categories_save()
     *
     * @uses To save the sub category details of new/existing sub category object based on details
     *
     * @created Akshata
     *
     * @updated Jeevan
     *
     * @param object request - ProductSubCategory Form Data
     *
     * @return success message
     *
     */
    public function product_sub_categories_save(Request $request) {
        
        try {
            
            DB::begintransaction();

            $rules = [
                'name' => 'required|max:191',
                'picture' => 'mimes:jpg,png,jpeg',
                'discription' => 'max:199',
                'product_category_id' => 'required',
            ];

            Helper::custom_validator($request->all(),$rules);

            $product_sub_category = ProductSubCategory::find($request->product_sub_category_id) ?? new ProductSubCategory;

            if($product_sub_category->id) {

                $message = tr('product_sub_category_updated_success'); 

            } else {

                $message = tr('product_sub_category_created_success');

            }

            $product_sub_category->name = $request->name ?: $product_sub_category->name;

            $product_sub_category->product_category_id = $request->product_category_id ?: $product_sub_category->product_category_id;

            $product_sub_category->description = $request->description ?: '';

            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->product_sub_category_id) {

                    Helper::storage_delete_file($product_sub_category->picture, CATEGORY_FILE_PATH); 
                    // Delete the old pic
                }

                $product_sub_category->picture = Helper::storage_upload_file($request->file('picture'), CATEGORY_FILE_PATH);
            }

            if($product_sub_category->save()) {

                DB::commit(); 

                return redirect(route('admin.product_sub_categories.view', ['product_sub_category_id' => $product_sub_category->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('product_sub_category_save_failed'));
            
        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method product_sub_categories_view()
     *
     * @uses displays the specified category details based on category id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - category Id
     * 
     * @return View page
     *
     */
    public function product_sub_categories_view(Request $request) {
       
        try {
      
            $product_sub_category = ProductSubCategory::find($request->product_sub_category_id);
            
            if(!$product_sub_category) { 

                throw new Exception(tr('product_sub_category_not_found'), 101);                
            }

            return view('admin.product_sub_categories.view')
                    ->with('page', 'product_sub_categories') 
                    ->with('sub_page', 'product_sub_categories-view')
                    ->with('product_sub_category', $product_sub_category);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method product_sub_categories_delete()
     *
     * @uses delete the sub category details based on category id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - ProductSubCategory Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function product_sub_categories_delete(Request $request) {

        try {

            DB::begintransaction();

            $product_category = ProductSubCategory::find($request->product_sub_category_id);
            
            if(!$product_category) {

                throw new Exception(tr('product_sub_category_not_found'), 101);                
            }

            if($product_category->delete()) {

                DB::commit();

                return redirect()->route('admin.product_sub_categories.index')->with('flash_success',tr('product_sub_category_deleted_success'));   

            } 
            
            throw new Exception(tr('product_sub_category_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method product_sub_categories_status
     *
     * @uses To update sub category status as DECLINED/APPROVED based on sub category id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - ProductSubCategory Id
     * 
     * @return response success/failure message
     *
     **/
    public function product_sub_categories_status(Request $request) {

        try {

            DB::beginTransaction();

            $product_sub_category = ProductSubCategory::find($request->product_sub_category_id);

            if(!$product_sub_category) {

                throw new Exception(tr('product_sub_category_not_found'), 101);
                
            }

            $product_sub_category->status = $product_sub_category->status ? DECLINED : APPROVED ;

            if($product_sub_category->save()) {

                DB::commit();

                $message = $product_sub_category->status ? tr('product_sub_category_approve_success') : tr('product_sub_category_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('product_sub_category_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.product_sub_categories.index')->with('flash_error', $e->getMessage());

        }

    }

     /**
     * @method get_product_sub_categories()
     * 
     * @uses - Used to get ProductSubCategory list based on the selected category
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

    public function get_product_sub_categories(Request $request) {
       
        $product_category_id = $request->product_category_id;

        $product_sub_categories = ProductSubCategory::where('product_category_id', '=', $product_category_id)
                            ->where('status' , APPROVED)
                            ->orderBy('name', 'asc')
                            ->get();
        
        $view_page = view('admin.user_products._sub_categories_list')->with('product_sub_categories' , $product_sub_categories)->render();

        $response_array = ['success' =>  true , 'view' => $view_page];

        return response()->json($response_array , 200);
    
    }
}
