<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Model\Video;
// use Illuminate\Mail\Mailable;
// use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailable;

class PasswordController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin/video');
    }
    public function remember(Request $request)
    {
        $email = $request->get('email');
        $name = 'Krunal';
        Mail::to($email)->send(new SendMailable($name));
        echo "Basic Email Sent. Check your inbox.";
        return 'success';
    }
   
}
