<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index( Request $request)
    {
        $records = $request->user()->notifications()->paginate(40);

        return view('notifications.index', compact('request', 'records'));
    }

    public function markAsRead(Request $request)
    {
        $IDs = $request->input('ids', []);

        foreach($IDs as $id) {
            $notification = $request->user()->notifications()->find($id);
            $notification->markAsRead();
        }

        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $IDs = $request->input('ids', []);

        foreach($IDs as $id) {
            $notification = $request->user()->notifications()->find($id);
            $notification->delete();
        }

        return redirect()->back();
    }
}
