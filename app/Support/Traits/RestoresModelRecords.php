<?php

namespace App\Support\Traits;

use Illuminate\Http\Request;

/**
 * Trait RestoresModelRecords
 *
 * This trait provides functionality to restore model records from trash.
 *
 * @package App\Support\Traits
 */
trait RestoresModelRecords
{
    /**
     * Restore model records from trash based on the request parameters.
     *
     * @param Request $request The request object.
     * @return \Illuminate\Http\RedirectResponse Redirect back to the previous page.
     */
    public function restore(Request $request)
    {
        // Extract id or ids from request as array to restore through loop
        $ids = (array) ($request->input('id') ?: $request->input('ids'));

        // Restore records
        foreach ($ids as $id) {
            // Check if model exists in trash before restoring
            $model = $this->model::onlyTrashed()->find($id);
            if ($model) {
                $model->restore();
            }
        }

        return redirect()->back();
    }
}
