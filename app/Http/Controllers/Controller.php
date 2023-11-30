<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function Index(){

// ExampleController.php

$total = Invoice::sum('Total');
$totalPaid = round((Invoice::where('value_status',1)->sum('Total')/$total)*100,2);
$totalUnPaid = round((Invoice::where('value_status',2)->sum('Total')/$total)*100,2);
$totalPartialPaid = round((Invoice::where('value_status',3)->sum('Total')/$total)*100,2);




$chartjs = app()->chartjs
         ->name('barChartTest')
         ->type('bar')
         ->size(['width' => 400, 'height' => 200])
         ->labels(['الفواتير الغير مدفوعه', 'الفواتير المدفوعه','الفواتير المدفوعه جزئيا'])
         ->datasets([
             [
                 "label" => "الغير مدفوعه",
                 'backgroundColor' => ['rgba(232,40,33 ,0.5)'],
                 'data' => [$totalUnPaid]
             ],
             [
                 "label" => "المدفوعه",
                 'backgroundColor' => ['rgba(33, 123, 232, 0.5)','rgba(33, 123, 232, 0.5)'],
                 'data' => ['',$totalPaid]
             ],
             [
                 "label" => "المدفوعه جزئيا",
                 'backgroundColor' => ['rgba(237, 172, 33, 0.5)','rgba(237, 172, 33, 0.5)','rgba(237, 172, 33, 0.5)'],
                 'data' => ['','',$totalPartialPaid]
             ]
         ])
         ->options([]);

         $chartjs_2 = app()->chartjs
         ->name('pieChartTest')
         ->type('pie')
         ->size(['width' => 340, 'height' => 200])
         ->labels(['الفواتير الغير المدفوعة', 'الفواتير المدفوعة','الفواتير المدفوعة جزئيا'])
         ->datasets([
             [
                 'backgroundColor' => ['#ec5858', 'rgba(33, 123, 232, 0.5)','#ff9642'],
                 'data' => [$totalUnPaid, $totalPaid,$totalPartialPaid]
             ]
         ])
         ->options([]);
return view('dashboard', compact('chartjs','chartjs_2'));

    }
}


