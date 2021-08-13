<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
        if (!Session::exists('lang')) {
            $locale = $request->getLocale();
            if (! in_array($locale, ['en', 'es'])) {
                $locale = 'en';
            }

            App::setLocale($locale);
            Session::remove('lang');
            Session::push('lang', $locale);
        } else {
            $locale = Session::get('lang')[0];
            App::setLocale($locale);
        }

        return view('landing.home');
    }

    public function changeLang(Request $request)
    {
        $locale = $request->lang;
        if (! in_array($locale, ['en', 'es'])) {
            $locale = 'en';
        }

        App::setLocale($locale);
        Session::remove('lang');
        Session::push('lang', $locale);

        return view('landing.home');
    }

    public function terms(Request $request)
    {
        if (!Session::exists('lang')) {
            $locale = $request->getLocale();
            if (! in_array($locale, ['en', 'es'])) {
                $locale = 'en';
            }

            App::setLocale($locale);
            Session::remove('lang');
            Session::push('lang', $locale);
        } else {
            $locale = Session::get('lang')[0];
            App::setLocale($locale);
        }

        return view('landing.terms');
    }

    public function changeLangTerms(Request $request)
    {
        $locale = $request->lang;
        if (! in_array($locale, ['en', 'es'])) {
            $locale = 'en';
        }

        App::setLocale($locale);
        Session::remove('lang');
        Session::push('lang', $locale);

        return view('landing.terms');
    }

    public function privacy(Request $request)
    {
        if (!Session::exists('lang')) {
            $locale = $request->getLocale();
            if (! in_array($locale, ['en', 'es'])) {
                $locale = 'en';
            }
            App::setLocale($locale);
            Session::remove('lang');
            Session::push('lang', $locale);
        } else {
            $locale = Session::get('lang')[0];
            App::setLocale($locale);
        }

        return view('landing.privacy');
    }

    public function changeLangPrivacy(Request $request)
    {
        $locale = $request->lang;
        if (! in_array($locale, ['en', 'es'])) {
            $locale = 'en';
        }

        App::setLocale($locale);
        Session::remove('lang');
        Session::push('lang', $locale);

        return view('landing.privacy');
    }
}
