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
        $reversed = !$request->user()->settings['shrink_body_width'];
        $request->user()->updateSetting('shrink_body_width', $reversed);

        return true;
    }
}
