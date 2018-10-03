<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Model\Video;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailable;
use App\User;
use Hash;
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
        $user_info = User::where('email', $email)->get()->toArray();
        $name = $user_info[0]['name'];
        $id = $user_info[0]['_id'];
        $digits = 6;
        $new_password = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
        $hash_password = array(
            'password' => Hash::make($new_password)
        );
        User::where('_id', $id)->update($hash_password);
        Mail::to($email)->send(new SendMailable($name, $new_password));
        echo "Basic Email Sent. Check your inbox.";
        return 'success';
    }
   
}
