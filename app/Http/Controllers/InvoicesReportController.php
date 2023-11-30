<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;

class InvoicesReportController extends Controller
{
    public function index(){
        return view("reports.invoices-report");       
    }

    public function searchInvoices(Request $request){

        $radio = $request->rdio;
        $type=$request->type;
        $start_at=date($request->start_at);
        $end_at=date($request->end_at) ;
        $invoice_number=$request->invoice_number;

        //$dateNow=date("Y-m-d");
        $addDays= Carbon::parse($start_at)->addDay(30);
       

        
        if ($radio ==1) {
            if ($type && $start_at=='' && $end_at=='') {
                $invoices = Invoice::where('status',$type)->get();
                return view("reports.invoices-report",compact('type','start_at'))->with('invoices',$invoices);
                }
                elseif($type && $start_at){
                    if ($end_at=='') {
                        $invoices = Invoice::where('status',$type)->wherebetween('invoice_date',[$start_at,$addDays])->get();
                        return view("reports.invoices-report",compact('type','start_at','end_at'))->with('invoices',$invoices);
                    }
                    else{
                        $invoices = Invoice::where('status',$type)->wherebetween('invoice_date',[$start_at,$end_at])->get();
                        return view("reports.invoices-report",compact('type','start_at','end_at'))->with('invoices',$invoices);
                    }
                   
                }
        }
        else{
            $invoices = Invoice::where('invoice_number', $request->invoice_number)->get();
            return view("reports.invoices-report",compact('invoice_number'))->with('invoices',$invoices);
        }
       
        

        //return view("reports.invoices-report");       
    }


}
