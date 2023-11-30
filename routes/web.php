<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomersReportController;
use App\Http\Controllers\InvoiceArchiveController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceDetailController;
use App\Http\Controllers\InvoiceAttachmentController;
use App\Http\Controllers\InvoicesReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Invoice;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});


Route::get('/dashboard', [Controller::class , "Index"])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/logout', [LogoutController::class ,'perform'])->name('logout.perform');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
   
});

Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
});


require __DIR__.'/auth.php';
Route::resource('invoices', InvoiceController::class);
Route::resource('InvoiceAttachments', InvoiceAttachmentController::class);
Route::resource('Archive', InvoiceArchiveController::class);
Route::get('InvoiceDetails/{id}', [InvoiceDetailController::class,'show'])->name('InvoiceDetails.show');
Route::get('Status_show/{id}', [InvoiceController::class,'statusShow'])->name('Status_show');
Route::post('Status_Update/{id}', [InvoiceController::class,'statusUpdate'])->name('Status_Update');
Route::get('paid', [InvoiceController::class,'getFullyPaidInvoces'])->name('Paid');
Route::get('unpaid', [InvoiceController::class,'getUnPaidInvoces'])->name('unpaid');
Route::get('export_invoices', [InvoiceController::class,'export'])->name('export_invoices');
Route::get('partial_paid', [InvoiceController::class,'getPartialPaidInvoces'])->name('partial_paid');
Route::get('Archieved_invoices', [InvoiceController::class,'getArchievedInvoces'])->name('Archieved_invoices');

Route::get('viewFile/{invoice_number}/{file_name}', [InvoiceDetailController::class,'openFile']);
Route::get('download/{invoice_number}/{file_name}', [InvoiceDetailController::class,'getFile']);
Route::post('delete_file', [InvoiceDetailController::class,'destroy'])->name('delete_file');
Route::post('Search_invoices', [InvoicesReportController::class,'searchInvoices'])->name('Search_invoices');
Route::get('MarkRead', [NotificationController::class,'MarkAsRead'])->name('MarkRead');


Route::get('/section/{id}', [InvoiceController::class, "getproducts"]);

Route::resource('sections', SectionController::class);
Route::resource('products', ProductController::class);

Route::get('/reports', [InvoicesReportController::class, 'index']);
Route::get('/reports/customers', [CustomersReportController::class, 'index']);
Route::post('Search_customers', [CustomersReportController::class, 'SearchCustomers']);


Route::get('/page', [AdminController::class, 'index']);
