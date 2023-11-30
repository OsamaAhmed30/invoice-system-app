<?php

namespace App\Http\Controllers;

use App\Exports\InvoicsExport;
use App\Exports\UsersExport;
use App\Models\Invoice;
use App\Models\Invoice_attachment;
use App\Models\Invoice_detail;
use App\Models\Section;
use App\Models\User;
use App\Notifications\AddInvoice;
use App\Notifications\AddInvoicebyDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:الفواتير|قائمة الفواتير',['only'=>['index']]);
        $this->middleware('permission:الفواتير المدفوعة',['only'=>['getFullyPaidInvoces']]);
        $this->middleware('permission:الفواتير الغير مدفوعة',['only'=>['getUnPaidInvoces']]);
        $this->middleware('permission:الفواتير المدفوعة جزئيا',['only'=>['getPartialPaidInvoces']]);
        $this->middleware('permission:اضافة فاتورة',['only'=>['create','store']]);
        $this->middleware('permission:حذف الفاتورة',['only'=>['destroy','store']]);
        $this->middleware('permission:تعديل الفاتورة',['only'=>['update', 'edit']]);
        $this->middleware('permission:تغير حالة الدفع',['only'=>['statusShow','statusUpdate']]);
        
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $invoices = Invoice::all();
        
        return view('invoices.invoices' , compact('invoices'));
    }
    public function getFullyPaidInvoces()
    {
         $invoices = Invoice::where('Value_Status' , 1)->get();
        
        return view('invoices.invoices_paid' , compact('invoices'));
    }
    public function getPartialPaidInvoces()
    {
        $invoices = Invoice::where('Value_Status' , 3)->get();
        
        return view('invoices.invoices_Partial' , compact('invoices'));
    }
    public function getUnPaidInvoces()
    {
        $invoices = Invoice::where('Value_Status' , 2)->get();        
        return view('invoices.invoices_unpaid' , compact('invoices'));
    }
   
    // print invoice preview page
    public function show($id)
    {
        $invoice = Invoice::findorfail($id);
        return view("invoices.Print_invoice" , compact('invoice'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = Section::all();
        return view("invoices.add_invoice" , compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Invoice::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
        ]);

        $invoice_id = Invoice::latest()->first()->id;
        Invoice_detail::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);
        

        if ($request->hasFile('pic')) {

            $invoice_id = Invoice::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;
            
            $attachments = new Invoice_attachment();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->Created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();
            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
        }
        /**
         * Send notification by email
         *  $user = User::first();
         *  $user->notify(new AddInvoice( $invoice_id));
         *  Notification::send(Auth::user()->email,new AddInvoice($invoice_id));
         */
        //Send notification by Database
        $user = User::where('roles_name','Owner')->orWhere('roles_name','Admin')->get();
       
        $invoices = Invoice::latest()->first();
        Notification::send($user, new AddInvoicebyDatabase($invoices));
        return redirect('invoices');
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoice = Invoice::findorfail($id);
        $sections = Section::all();

        return view('invoices.edit_invoice',compact('invoice','sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $invoice = Invoice::findorfail($request->invoice_id);
        $invoice->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'note' => $request->note,
        ]);

        $invoice_id = $request->invoice_id;
        $invoice_detail = Invoice_detail::where('id_Invoice' , $invoice_id )->first();
        $invoice_detail->update([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => ' مدفوعة',
            'Value_Status' => 1,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);


        return redirect('invoices')->with("edit" , "تم تعديل الفاتوره بنجاح");
    }

    public function statusShow($id)
    {
        $invoice = Invoice::findorfail($id);

        return view('invoices.status_update',compact('invoice'));
    }

    public function statusUpdate(Request $request)
    {

        //return $request;

        $invoice = Invoice::findorfail($request->invoice_id);
        $invoice->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Status' => $request->Status,
            'Value_Status' =>( $request->Status == 'مدفوعة')? 1:(($request->Status =='مدفوعة جزئيا') ? 3 : 2) ,
            'Total' => $request->Total,
            'note' => $request->note,
        ]);

        $invoice_id = $request->invoice_id;
       
        Invoice_detail::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => $request->Status,
            'Value_Status' =>$invoice->Value_Status,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);


        return redirect('invoices')->with("edit" , "تم تعديل الفاتوره بنجاح");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if($request->id_page != null){
            $data = Invoice::find($request->invoice_id)->Delete();
           
            return redirect('Archive')->with("archieve_invoice" , "bg-danger");
        }
        $attachment= Invoice_attachment::where('invoice_id',$request->invoice_id)->first();
        if(  $attachment!=null && $attachment->count()!=0){
           
            Storage::disk('public_uploads')->deleteDirectory($attachment->invoice_number);
            
        }
        
         
        Invoice::findorfail($request->invoice_id)->forceDelete();
        
        return redirect('invoices')->with("delete_invoice" , "bg-danger");
    }

   
   
    public function getproducts($id)
    {
        $products = DB::table("products")->where("section_id" , $id)->pluck("product_name" , "id");
        return json_encode($products);
     }

     public function export() 
     {
         return Excel::download(new InvoicsExport, 'invoices.xlsx');
     }
}
