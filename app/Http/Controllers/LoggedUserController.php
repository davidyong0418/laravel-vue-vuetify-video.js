<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/**
 * Class LoggedUserController.
 *
 * @package App\Http\Controllers
 */
class LoggedUserController extends Controller
{
    /**
     * Update user.
     * @param Request $request
     * @return \App\User|null
     */
    public function update(Request $request)
    {
        Auth::user()->update(Auth::user()->_id,$request->get('name'),$request->get('email'));
        return Auth::user();
    }
}
