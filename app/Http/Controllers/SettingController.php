<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function updateLocale(Request $request)
    {
        $request->user()->updateSetting('locale', $request->locale);

        return redirect()->back();
    }

    public function updateBodyWidth(Request $request)
    {
        $reversed = !$request->user()->settings['shrinkBodyWidth'];
        $request->user()->updateSetting('shrinkBodyWidth', $reversed);

        return true;
    }
}
