<?php

namespace App\Support\Traits;

use Illuminate\Http\Request;

trait MultipleRestoreable
{
    /**
     * Restore multiple model records from trash.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Request $request)
    {
        // Extract ids from request
        $ids = (array) $request->input('ids');

        foreach ($ids as $id) {
            $this->model::withTrashed()->find($id)->restore();
        }

        return redirect()->back();
    }
}
