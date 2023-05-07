<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

use App\Models\ReportReason;

class AdminLookupController extends Controller
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
     * @method report_reasons_index()
     *
     * @uses Used to list the report
     *
     * @created Subham
     *
     * @updated   
     *
     * @param -
     *
     * @return
     */

    public function report_reasons_index(Request $request) {

        $base_query = ReportReason::orderBy('updated_at' , 'desc');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query =  $base_query->Where('report_reasons.title','LIKE','%'.$search_key.'%');

        }

        $report_reasons = $base_query->paginate($this->take);

        return view('admin.report_reasons.index')
                    ->with('page', 'report_reasons')
                    ->with('sub_page', 'report_reasons-view')
                    ->with('report_reasons', $report_reasons);
    
    }

    /**
     * @method report_reasons_create()
     *
     * @uses To create report_reason details
     *
     * @created Subham
     *
     * @updated    
     *
     * @param
     *
     * @return view page   
     *
     */
    public function report_reasons_create() {

        $report_reason = new ReportReason;

        return view('admin.report_reasons.create')
                ->with('page', 'report_reasons')
                ->with('sub_page', 'report_reasons-create')
                ->with('report_reason', $report_reason);
   
    }

    /**
     * @method report_reasons_edit()
     *
     * @uses To display and update report_reason details based on the report_reason id
     *
     * @created Subham
     *
     * @updated 
     *
     * @param object $request - report_reason Id
     * 
     * @return redirect view page 
     *
     */
    public function report_reasons_edit(Request $request) {

        try {

            $report_reason = ReportReason::find($request->report_reason_id);

            if(!$report_reason) {

                throw new Exception(tr('report_reason_not_found'), 101);
            }
 
            return view('admin.report_reasons.edit')
                    ->with('page', 'report_reasons')
                    ->with('sub_page', 'report_reasons-view')
                    ->with('report_reason', $report_reason);

        } catch(Exception $e) {

            return redirect()->route('admin.report_reasons.index')->with('flash_error' , $e->getMessage());

        }
    }

    /**
     * @method report_reasons_save()
     *
     * @uses To save the page details of new/existing page object based on details
     *
     * @created Subham
     *
     * @updated 
     *
     * @param
     *
     * @return index page    
     *
     */
    public function report_reasons_save(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'title' =>  $request->report_reason_id ? 'required' : 'required|max:191|unique:report_reasons,title',
            ]; 
            
            Helper::custom_validator($request->all(), $rules);

            $report_reason = ReportReason::find($request->report_reason_id) ?? new ReportReason;

            $message = $request->report_reason_id ? tr('report_reason_updated_success') : tr('report_reason_created_success');

            $report_reason->title = $request->title ?: $report_reason->title;

            $report_reason->description = $request->description ?: $report_reason->description;

            if($report_reason->save()) {

                DB::commit();
                
                return redirect()->route('admin.report_reasons.view', ['report_reason_id' => $report_reason->id] )->with('flash_success', $message);

            } 

            throw new Exception(tr('report_reason_save_failed'), 101);
                      
        } catch(Exception $e) {

            DB::rollback();

            return back()->withInput()->with('flash_error', $e->getMessage());

        }
    
    }

    /**
     * @method report_reasons_delete()
     *
     * Used to view file of the create the static page 
     *
     * @created Subham
     *
     * @updated 
     *
     * @param -
     *
     * @return view page   
     */

    public function report_reasons_delete(Request $request) {

        try {

            DB::beginTransaction();

            $report_reason = ReportReason::find($request->report_reason_id);

            if(!$report_reason) {

                throw new Exception(tr('report_reason_not_found'), 101);
                
            }

            if($report_reason->delete()) {

                DB::commit();

                return redirect()->route('admin.report_reasons.index',['page'=>$request->page])->with('flash_success', tr('report_reason_deleted_success')); 

            } 

            throw new Exception(tr('report_reason_error'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.report_reasons.index')->with('flash_error', $e->getMessage());

        }
    
    }

    /**
     * @method report_reasons_view()
     *
     * @uses view the report_reasons details based on report_reasons id
     *
     * @created Subham 
     *
     * @updated 
     *
     * @param object $request - report_reason Id
     * 
     * @return View page
     *
     */
    public function report_reasons_view(Request $request) {

        $report_reason = ReportReason::find($request->report_reason_id);

        if(!$report_reason) {
           
            throw new Exception(tr('report_reasons_not_found'), 101);

        }

        return view('admin.report_reasons.view')
                    ->with('page', 'report_reasons')
                    ->with('sub_page', 'report_reasons-view')
                    ->with('report_reason', $report_reason);
    }

    /**
     * @method report_reasons_status_change()
     *
     * @uses To update report_reason status as DECLINED/APPROVED based on report_reason id
     *
     * @created Subham
     *
     * @updated 
     *
     * @param - integer report_reason_id
     *
     * @return view page 
     */

    public function report_reasons_status_change(Request $request) {

        try {

            DB::beginTransaction();

            $report_reason = ReportReason::find($request->report_reason_id);

            if(!$report_reason) {

                throw new Exception(tr('report_reason_not_found'), 101);
                
            }

            $report_reason->status = $report_reason->status == DECLINED ? APPROVED : DECLINED;

            $report_reason->save();

            DB::commit();

            $message = $report_reason->status == DECLINED ? tr('report_reason_decline_success') : tr('report_reason_approve_success');

            return redirect()->back()->with('flash_success', $message);

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method documents_index()
     *
     * @uses To list out decoments details 
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
    public function documents_index(Request $request) {

        $documents = \App\Models\Document::orderBy('created_at', 'desc')->paginate($this->take);

        return view('admin.documents.index')
                    ->with('page','documents')
                    ->with('sub_page', 'documents-view')
                    ->with('documents', $documents);
    }

    /**
     * @method documents_create()
     *
     * @uses To create documents details
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
    public function documents_create() {

        $document = new \App\Models\Document;

        return view('admin.documents.create')
                    ->with('page', 'documents')
                    ->with('sub_page','documents-create')
                    ->with('document', $document);           
    }

    /**
     * @method documents_edit()
     *
     * @uses To display and update documents details based on the document id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Document Id
     * 
     * @return redirect view page 
     *
     */
    public function documents_edit(Request $request) {

        try {

            $document = \App\Models\Document::find($request->document_id);

            if(!$document) { 

                throw new Exception(tr('document_not_found'), 101);
            }

            return view('admin.documents.edit')
                    ->with('page', 'documents')
                    ->with('sub_page','documents-create')
                    ->with('document' , $document); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.documents.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method documents_save()
     *
     * @uses To save the document details of new/existing document object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object request - Document Form Data
     *
     * @return success message
     *
     */
    public function documents_save(Request $request) {
            
        try {

            DB::begintransaction();

            $rules = [
                'name' => 'required|max:191',
                'picture' => 'mimes:jpg,png,jpeg',
                'document_id' => 'exists:documents,id|nullable',
                'description' => 'max:199',
            ];

            Helper::custom_validator($request->all(),$rules);

            $document = $request->document_id ? \App\Models\Document::find($request->document_id) : new \App\Models\Document;

            if($document->id) {

                $message = tr('document_updated_success'); 

            } else {

                $message = tr('document_created_success');

                $document->picture = asset('document.jpeg');

            }

            $document->name = $request->name ?: $document->name;

            $document->description = $request->description ?: '';

            //$document->is_required = $request->is_required == YES ? YES : NO;

            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->document_id) {

                    Helper::storage_delete_file($document->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $document->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
            }

            if($document->save()) {

                DB::commit(); 

                return redirect(route('admin.documents.view', ['document_id' => $document->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('document_save_failed'));
            
        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method documents_view()
     *
     * @uses displays the specified document details based on dosument id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - document Id
     * 
     * @return View page
     *
     */
    public function documents_view(Request $request) {
       
        try {
      
            $document = \App\Models\Document::find($request->document_id);

            if(!$document) { 

                throw new Exception(tr('document_not_found'), 101);                
            }

            return view('admin.documents.view')
                        ->with('page', 'documents') 
                        ->with('sub_page', 'documents-view') 
                        ->with('document', $document);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method documents_delete()
     *
     * @uses delete the document details based on document id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - document Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function documents_delete(Request $request) {

        try {

            DB::begintransaction();

            $document = \App\Models\Document::find($request->document_id);
            
            if(!$document) {

                throw new Exception(tr('document_not_found'), 101);                
            }

            if($document->delete()) {

                DB::commit();

                return redirect()->route('admin.documents.index',['page'=>$request->page])->with('flash_success',tr('document_deleted_success'));   

            } 
            
            throw new Exception(tr('document_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method documents_status
     *
     * @uses To update document status as DECLINED/APPROVED based on document id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Document Id
     * 
     * @return response success/failure message
     *
     **/
    public function documents_status(Request $request) {

        try {

            DB::beginTransaction();

            $document = \App\Models\Document::find($request->document_id);

            if(!$document) {

                throw new Exception(tr('document_not_found'), 101);
                
            }

            $document->status = $document->status ? DECLINED : APPROVED ;

            if($document->save()) {

                DB::commit();

                $message = $document->status ? tr('document_approve_success') : tr('document_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('document_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.documents.index')->with('flash_error', $e->getMessage());

        }

    }

     /**
     * @method static_pages_index()
     *
     * @uses Used to list the static pages
     *
     * @created vithya
     *
     * @updated vithya  
     *
     * @param -
     *
     * @return List of pages   
     */

    public function static_pages_index() {

        $static_pages = \App\Models\StaticPage::orderBy('updated_at' , 'desc')->paginate($this->take);

        return view('admin.static_pages.index')
                    ->with('page', 'static_pages')
                    ->with('sub_page', 'static_pages-view')
                    ->with('static_pages', $static_pages);
    
    }

    /**
     * @method static_pages_create()
     *
     * @uses To create static_page details
     *
     * @created Akshata
     *
     * @updated    
     *
     * @param
     *
     * @return view page   
     *
     */
    public function static_pages_create() {

        $static_keys = ['about' , 'contact' , 'privacy' , 'terms' , 'help' , 'faq' , 'refund', 'cancellation'];

        foreach ($static_keys as $key => $static_key) {

            // Check the record exists

            $check_page = \App\Models\StaticPage::where('type', $static_key)->first();

            if($check_page) {
                unset($static_keys[$key]);
            }
        }

        $section_types = static_page_footers(0, $is_list = YES);

        $static_keys[] = 'others';

        $static_page = new \App\Models\StaticPage;

        return view('admin.static_pages.create')
                ->with('page', 'static_pages')
                ->with('sub_page', 'static_pages-create')
                ->with('static_keys', $static_keys)
                ->with('static_page', $static_page)
                ->with('section_types',$section_types);
   
    }

    /**
     * @method static_pages_edit()
     *
     * @uses To display and update static_page details based on the static_page id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - static_page Id
     * 
     * @return redirect view page 
     *
     */
    public function static_pages_edit(Request $request) {

        try {

            $static_page = \App\Models\StaticPage::find($request->static_page_id);

            if(!$static_page) {

                throw new Exception(tr('static_page_not_found'), 101);
            }

            $static_keys = ['about' , 'contact' , 'privacy' , 'terms' , 'help' , 'faq' , 'refund', 'cancellation'];

            foreach ($static_keys as $key => $static_key) {

                // Check the record exists

                $check_page = \App\Models\StaticPage::where('type', $static_key)->first();

                if($check_page) {
                    unset($static_keys[$key]);
                }
            }

            $static_keys[] = 'others';

            $static_keys[] = $static_page->type;


            $section_types = static_page_footers(0, $is_list = YES);
 
            return view('admin.static_pages.edit')
                    ->with('page', 'static_pages')
                    ->with('sub_page', 'static_pages-view')
                    ->with('static_keys', array_unique($static_keys))
                    ->with('static_page', $static_page)
                    ->with('section_types',$section_types);
        } catch(Exception $e) {

            return redirect()->route('admin.static_pages.index')->with('flash_error' , $e->getMessage());

        }
    }

    /**
     * @method static_pages_save()
     *
     * @uses To save the page details of new/existing page object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param
     *
     * @return index page    
     *
     */
    public function static_pages_save(Request $request) {

        try {

            $request->request->add([
                'content' => $request->description ? trim(str_replace('&nbsp;','',strip_tags($request->description))) : '',
            ]);

            DB::beginTransaction();

            $rules = [
                'title' =>  !$request->static_page_id ? 'required|max:191|unique:static_pages,title' : 'required',
                'content' => 'required',
                'type' => !$request->static_page_id ? 'required' : ""
            ]; 

            $custom_errors = ['content.required'=>tr('description__required')];
            
            Helper::custom_validator($request->all(), $rules, $custom_errors);

            if($request->static_page_id != '') {

                $static_page = \App\Models\StaticPage::find($request->static_page_id);

                $message = tr('static_page_updated_success');                    

            } else {

                $check_page = "";

                // Check the staic page already exists

                if($request->type != 'others') {

                    $check_page = \App\Models\StaticPage::where('type',$request->type)->first();

                    if($check_page) {

                        return back()->with('flash_error',tr('static_page_already_alert'));
                    }

                }

                $message = tr('static_page_created_success');

                $static_page = new \App\Models\StaticPage;

                $static_page->status = APPROVED;

            }

            $static_page->title = $request->title ?: $static_page->title;

            $static_page->description = $request->description ?: $static_page->description;

            $static_page->type = $request->type ?: $static_page->type;
            
            $static_page->section_type = $request->section_type ?: $static_page->section_type;

            $unique_id = $request->type ?: $static_page->type;

            // Dont change the below code. If any issue, get approval from vithya and change

            if(!in_array($unique_id, ['about', 'privacy', 'terms', 'contact', 'help', 'faq'])) {

                $unique_id = routefreestring($request->heading ?? rand());

                $unique_id = in_array($unique_id, ['about', 'privacy', 'terms', 'contact', 'help', 'faq']) ? $unique_id : $unique_id;

            }

            $static_page->unique_id = $unique_id ?? rand();

            if($static_page->save()) {

                DB::commit();

                Helper::settings_generate_json();
                
                return redirect()->route('admin.static_pages.view', ['static_page_id' => $static_page->id] )->with('flash_success', $message);

            } 

            throw new Exception(tr('static_page_save_failed'), 101);
                      
        } catch(Exception $e) {

            DB::rollback();

            return back()->withInput()->with('flash_error', $e->getMessage());

        }
    
    }

    /**
     * @method static_pages_delete()
     *
     * Used to view file of the create the static page 
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param -
     *
     * @return view page   
     */

    public function static_pages_delete(Request $request) {

        try {

            DB::beginTransaction();

            $static_page = \App\Models\StaticPage::find($request->static_page_id);

            if(!$static_page) {

                throw new Exception(tr('static_page_not_found'), 101);
                
            }

            if($static_page->delete()) {

                DB::commit();

                return redirect()->route('admin.static_pages.index',['page'=>$request->page])->with('flash_success', tr('static_page_deleted_success')); 

            } 

            throw new Exception(tr('static_page_error'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.static_pages.index')->with('flash_error', $e->getMessage());

        }
    
    }

    /**
     * @method static_pages_view()
     *
     * @uses view the static_pages details based on static_pages id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - static_page Id
     * 
     * @return View page
     *
     */
    public function static_pages_view(Request $request) {

        $static_page = \App\Models\StaticPage::find($request->static_page_id);

        if(!$static_page) {
           
            return redirect()->route('admin.static_pages.index')->with('flash_error',tr('static_page_not_found'));

        }

        $section_types = static_page_footers(0, $is_list = YES);

        return view('admin.static_pages.view')
                    ->with('page', 'static_pages')
                    ->with('sub_page', 'static_pages-view')
                    ->with('section_types', $section_types)
                    ->with('static_page', $static_page);
    }

    /**
     * @method static_pages_status_change()
     *
     * @uses To update static_page status as DECLINED/APPROVED based on static_page id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param - integer static_page_id
     *
     * @return view page 
     */

    public function static_pages_status_change(Request $request) {

        try {

            DB::beginTransaction();

            $static_page = \App\Models\StaticPage::find($request->static_page_id);

            if(!$static_page) {

                throw new Exception(tr('static_page_not_found'), 101);
                
            }

            $static_page->status = $static_page->status == DECLINED ? APPROVED : DECLINED;

            $static_page->save();

            DB::commit();

            $message = $static_page->status == DECLINED ? tr('static_page_decline_success') : tr('static_page_approve_success');

            return redirect()->back()->with('flash_success', $message);

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }

    }

    
    
    /**
     * @method faqs_index()
     *
     * @uses To list out faq details 
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
    public function faqs_index() {
       
        $faqs = \App\Models\Faq::orderBy('created_at','desc')->paginate($this->take);

        return view('admin.faqs.index')
                    ->with('main_page','faqs-crud')
                    ->with('page','faqs')
                    ->with('sub_page' , 'faqs-view')
                    ->with('faqs' , $faqs);
    }

    /**
     * @method faqs_create()
     *
     * @uses To create faq details
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
    public function faqs_create() {

        $faq = new \App\Models\Faq;

        return view('admin.faqs.create')
                    ->with('main_page','faqs-crud')
                    ->with('page' , 'faqs')
                    ->with('sub_page','faqs-create')
                    ->with('faq', $faq);
                
    }

    /**
     * @method faqs_edit()
     *
     * @uses To display and update faqs details based on the faq id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Faq Id
     * 
     * @return redirect view page 
     *
     */
    public function faqs_edit(Request $request) {

        try {

            $faq = \App\Models\Faq::find($request->faq_id);

            if(!$faq) { 

                throw new Exception(tr('faq_not_found'), 101);

            }
           
            return view('admin.faqs.edit')
                    ->with('main_page','faqs-crud')
                    ->with('page' , 'faqs')
                    ->with('sub_page','faqs-view')
                    ->with('faq' , $faq); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.faqs.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method faqs_save()
     *
     * @uses To save the faqs details of new/existing Faq object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object request - Faq Form Data
     *
     * @return success message
     *
     */
    public function faqs_save(Request $request) {

        try {

            DB::begintransaction();

            $rules = [
                'question' => 'required',
                'answer' => 'required',
            
            ];

            Helper::custom_validator($request->all(),$rules);

            $faq = $request->faq_id ? \App\Models\Faq::find($request->faq_id) : new \App\Models\Faq;

            if(!$faq) {

                throw new Exception(tr('faq_not_found'), 101);
            }

            $faq->question = $request->question;

            $faq->answer = $request->answer;

            $faq->status = APPROVED;

            if($faq->save() ) {

                DB::commit();

                $message = $request->faq_id ? tr('faq_update_success')  : tr('faq_create_success');

                return redirect()->route('admin.faqs.view', ['faq_id' => $faq->id])->with('flash_success', $message);
            } 

            throw new Exception(tr('faq_saved_error') , 101);

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());
        } 

    }

    /**
     * @method faqs_view()
     *
     * @uses view the faqs details based on faq id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - Faq Id
     * 
     * @return View page
     *
     */
    public function faqs_view(Request $request) {
       
        try {
      
            $faq = \App\Models\Faq::find($request->faq_id);
            
            if(!$faq) { 

                throw new Exception(tr('faq_not_found'), 101);                
            }

            return view('admin.faqs.view')
                        ->with('main_page','faqs-crud')
                        ->with('page', 'faqs') 
                        ->with('sub_page','faqs-view') 
                        ->with('faq' , $faq);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method faqs_delete()
     *
     * @uses delete the faq details based on faq id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - Faq Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function faqs_delete(Request $request) {

        try {

            DB::begintransaction();

            $faq = \App\Models\Faq::find($request->faq_id);
            
            if(!$faq) {

                throw new Exception(tr('faq_not_found'), 101);                
            }

            if($faq->delete()) {

                DB::commit();

                return redirect()->route('admin.faqs.index',['page'=>$request->page])->with('flash_success',tr('faq_deleted_success'));   

            } 
            
            throw new Exception(tr('faq_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method faqs_status
     *
     * @uses To update faq status as DECLINED/APPROVED based on faqs id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Faq Id
     * 
     * @return response success/failure message
     *
     **/
    public function faqs_status(Request $request) {

        try {

            DB::beginTransaction();

            $faq = \App\Models\Faq::find($request->faq_id);

            if(!$faq) {

                throw new Exception(tr('faq_not_found'), 101);
                
            }

            $faq->status = $faq->status ? DECLINED : APPROVED ;

            if($faq->save()) {

                DB::commit();

                $message = $faq->status ? tr('faq_approve_success') : tr('faq_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('faq_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.faqs.index')->with('flash_error', $e->getMessage());

        }

    }


}
