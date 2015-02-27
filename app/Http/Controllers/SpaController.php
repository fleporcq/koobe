<?php namespace App\Http\Controllers;


class SpaController extends KoobeController
{
    public function index()
    {
        return view('spa/index');
    }
}