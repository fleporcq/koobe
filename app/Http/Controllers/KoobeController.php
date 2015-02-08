<?php namespace App\Http\Controllers;

use Config;
use Route;
use View;

class KoobeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        View::share('appName', Config::get('app.name'));
        View::share('currentAction', Route::current()->getActionName());
    }

    protected function notFoundIfNull($object)
    {
        if ($object == null) {
            abort(404);
        }
    }
}
