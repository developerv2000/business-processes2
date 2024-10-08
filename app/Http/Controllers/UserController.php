<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the records.
     */
    public function index(Request $request)
    {
        User::mergeQueryingParamsToRequest($request);
        $records = User::getRecordsFinalized($request, finaly: 'paginate');

        return view('users.index', compact('request', 'records'));
    }

    /**
     * Show the form for creating a new record.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created record in storage.
     */
    public function store(UserStoreRequest $request)
    {
        User::createFromRequest($request);

        return to_route('users.index');
    }

    /**
     * Show the form for editing the specified record.
     */
    public function edit(User $instance)
    {
        return view('users.edit', compact('instance'));
    }

    /**
     * Update the specified record in storage.
     */
    public function update(UserUpdateRequest $request, User $instance)
    {
        $instance->updateByAdmin($request);

        return redirect($request->input('previous_url'));
    }

    /**
     * Update users password.
     */
    public function updatePassword(PasswordUpdateRequest $request, User $instance)
    {
        $instance->updatePasswordByAdmin($request);

        return redirect($request->input('previous_url'));
    }

    public function updatePermissions(Request $request, User $instance)
    {
        $instance->updatePermissions($request);

        return redirect($request->input('previous_url'));
    }

    public function destroy(Request $request)
    {
        $user = User::find($request->id);
        $user->deleteByAdmin();

        return to_route('users.index');
    }
}
