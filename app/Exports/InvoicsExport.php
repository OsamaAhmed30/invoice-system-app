<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;

class InvoicsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        return Invoice::all();

        /* 
        >> export custom column from table
        return Invoice::get(['invoice_number','invoice_Date','Amount_Commission']);
       */
    }
}
