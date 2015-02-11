<?php namespace App\Http\Controllers;


use App\Models\Theme;

class HomeController extends KoobeController {

	public function index()
	{
		$themes = Theme::orderBy('name')->get();
		return view('home/index', ['themes' => $themes]);
	}

}
