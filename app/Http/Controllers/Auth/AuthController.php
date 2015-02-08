<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;
	}

	public function login()
	{
		return view('auth/login');
	}

	public function authenticate()
	{

		$userData = array(
			"email" => Input::get("email"),
			"password" => Input::get("password")
		);

		if ($this->auth->attempt($userData)) {
			return Redirect::to(URL::action("HomeController@index"));
		}

		return Redirect::to(URL::action("Auth\AuthController@login"))->with("error", Lang::get('messages.loginError'));

	}

	public function logout()
	{
		$this->auth->logout();
		return Redirect::to(URL::action('Auth\AuthController@login'));
	}

}
