<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice_attachment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
class InvoiceArchiveController extends Controller
{


    function __construct()
    {
         $this->middleware('permission:ارشيف الفواتير', ['only' => ['index','update','destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::onlyTrashed()->get();        
        return view('invoices.Archive_Invoices' , compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $invoice = Invoice::onlyTrashed()->where('id',$request->invoice_id)->restore();


        return redirect('invoices')->with("archieve_invoice" , "bg-success");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $attachment= Invoice_attachment::where('invoice_id',$request->invoice_id)->first();
        if($attachment->count()!=0){
           
            Storage::disk('public_uploads')->deleteDirectory($attachment->invoice_number);
            
        }
        
         
        Invoice::withTrashed()->findorfail($request->invoice_id)->forceDelete();
        
        return redirect('invoices')->with("delete_invoice" , "bg-danger");
    }
}
