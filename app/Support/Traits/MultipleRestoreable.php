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

        // Restore only soft deleted models
        $models = $this->model::withTrashed()->whereIn('id', $ids)->get();

        foreach ($models as $model) {
            if ($model->trashed()) {
                $model->restore();
            }
        }

        return redirect()->back();
    }
}
