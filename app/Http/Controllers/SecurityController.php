<?php namespace App\Http\Controllers;



use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class SecurityController extends Controller
{

    public function showLogin()
    {
        return view('security/login');
    }

    public function login()
    {

        $userData = array(
            "email" => Input::get("email"),
            "password" => Input::get("password")
        );

        if (Auth::attempt($userData)) {
            return Redirect::to(URL::action("HomeController@index"));
        }

        return Redirect::to(URL::action("SecurityController@login"))->with("error", Lang::get('messages.loginError'));

    }

    public function logout()
    {
        Auth::logout();
        return Redirect::to(URL::action('SecurityController@login'));
    }
}
