<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Invoice_attachment;
use App\Models\Invoice_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; 

class InvoiceDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show($id)
    {
       
        $invoices = Invoice::findorfail($id);
        //return $invoices;
        $details = Invoice_detail::where('id_Invoice',$id)->get();
        $attachments = Invoice_attachment::where('invoice_id',$id)->get();
        //return $attachments;
        return view("invoices.details_invoice", compact('invoices','details','attachments'));
    }

    /*
        Open File
    */

    public function openFile($invoice_number,$file_name)
    {
        //open file we create public path in config -> file system in root we call public path from it 
        $files= public_path('Attachments/' .$invoice_number.'/'.$file_name);
        //return $files;
        //$files = Storage::disk('public')->get('Attachments/' .$invoice_number.'/'.$file_name);
        return response()->file($files);  
    }
    /*
        Download File
    */
    public function getFile($invoice_number,$file_name)
    {
        //open file we create public path in config -> file system in root we call public path from it 
        $files= public_path('Attachments/' .$invoice_number.'/'.$file_name);
        //return $files;
        //$files = Storage::disk('public')->get('Attachments/' .$invoice_number.'/'.$file_name);
        return response()->download($files);  
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $invoice = Invoice_attachment::findorfail($request->id_file);
        $invoice->delete();

       
        Storage::disk('public_uploads')->delete($request->invoice_number.'/'.$request->file_name);
        
        return back();
    }
}
