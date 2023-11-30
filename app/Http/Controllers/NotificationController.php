<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function MarkAsRead(){

    $user = Auth::user();

    $user->unreadNotifications->markAsRead();

    return back();

    }
   
}
