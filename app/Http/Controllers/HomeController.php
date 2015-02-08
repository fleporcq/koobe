<?php namespace App\Http\Controllers;


class HomeController extends KoobeController {

	public function index()
	{
		return view('home/index');
	}

}
