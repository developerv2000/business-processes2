<?php

namespace App\Support\Traits;

use Illuminate\Http\Request;

trait MultipleDestroyable
{
    /**
     * Destroy multiple model records.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // Extract ids from request
        $ids = (array) $request->input('ids');

        // Force delete records
        if ($request->input('force_delete')) {
            foreach ($ids as $id) {
                $this->model::withTrashed()->find($id)->forceDelete();
            }
        } else {
            // Soft delete or trash records
            foreach ($ids as $id) {
                $this->model::find($id)->delete();
            }
        }

        return redirect()->back();
    }
}
