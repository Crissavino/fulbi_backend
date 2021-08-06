<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('setLocale');
    }

    public function index(Request $request)
    {
        $locale = $request->getLocale();
        if (! in_array($locale, ['en', 'es'])) {
            $locale = 'en';
        }

        App::setLocale($locale);

        return view('landing.home');
    }

    public function changeLang(Request $request)
    {
        $locale = $request->lang;
        if (! in_array($locale, ['en', 'es'])) {
            $locale = 'en';
        }

        App::setLocale($locale);

        return view('landing.home');
    }
}
