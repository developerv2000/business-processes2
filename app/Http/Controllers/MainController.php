<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function redirectToHomePage(Request $request)
    {
        $homePage = $request->user()->detectHomeRouteName();

        return redirect()->to($homePage);
    }
}
