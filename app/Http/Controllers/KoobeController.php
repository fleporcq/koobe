<?php namespace App\Http\Controllers;

use Config;
use Illuminate\Auth\Guard;
use Route;
use View;

class KoobeController extends Controller
{

    protected $connectedUser;

    public function __construct(Guard $auth)
    {
        $this->connectedUser = $auth->user();
        $this->middleware('auth');
        View::share('appName', Config::get('app.name'));
        View::share('currentAction', Route::current()->getActionName());
        View::share('connectedUser', $this->connectedUser);
    }

    protected function notFoundIfNull($object)
    {
        if ($object == null) {
            abort(404);
        }
    }
}
