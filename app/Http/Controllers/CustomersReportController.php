<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomersReportController extends Controller
{
    public function Index(){
        $sections = Section::all();
        return view('reports.customers-report', compact('sections'));
    }
    public function SearchCustomers(Request $request){


        /**
         * 
         * {
   * "_token": "n3HxJDX22nDjfKPwnYRsIF0wGiZvmA2ys0rrMtmw",
    *"Section": "1",
    *"product": "ccc",
    *"start_at": null,
    *"end_at": null
     *   }
         * 
         */
        $section = $request->Section;
        $product = $request->product;
        $startDate = date($request->start_at);
        $endDate = date($request->end_at);
        $sections = Section::all();
        $addDays= Carbon::parse($startDate)->addDay(30);

        if ($section && $product && $startDate == ''&& $endDate == '') {
            $invoices = Invoice::where('section_id',$section)->where('product',$product)->get();
            return view('reports.customers-report', compact('sections','section','product'))->with('invoices',$invoices);
        }
        elseif($section && $product && $startDate){
            if ($endDate) {
                $invoices = Invoice::where('section_id',$section)->where('product',$product)->wherebetween('invoice_Date',[$startDate,$endDate])->get();
            return view('reports.customers-report', compact('sections','section','product','startDate','endDate'))->with('invoices',$invoices);
            }
            else{
                $invoices = Invoice::where('section_id',$section)->where('product',$product)->wherebetween('invoice_Date',[$startDate,$addDays])->get();
                    return view('reports.customers-report', compact('sections','section','product','startDate',))->with('invoices',$invoices);
            }
        }
        return view('reports.customers-report', compact('sections'));
    }

}
