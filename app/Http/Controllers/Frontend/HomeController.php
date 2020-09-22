<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function language($language)
    {
        if ($language) {
            Session::put('language', $language);
        }
        return redirect()->back();
    }
}
