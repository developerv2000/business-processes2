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

    /**
     * Update table columns including orders, widths, and visibility.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateTableColumns(Request $request)
    {
        $table = $request->input('table');
        $key = $table . '_table_columns';
        $user = $request->user();
        $settings = $user->settings;

        // Check if the key exists in user settings
        if (!isset($settings[$key])) {
            abort(404, 'Settings key not found');
        }

        $columns = collect($settings[$key]);

        // Update column only if it exists in user settings
        $requestColumns = collect($request->columns)->keyBy('name');
        $columns = $columns->map(function ($column) use ($requestColumns) {
            if ($requestColumns->has($column['name'])) {
                $requestColumn = $requestColumns->get($column['name']);
                $column['order'] = $requestColumn['order'];
                $column['width'] = $requestColumn['width'];
                $column['visible'] = $requestColumn['visible'];
            }
            return $column;
        });

        // Sort columns by order and update user settings
        $orderedColumns = $columns->sortBy('order')->values()->all();
        $user->updateSetting($key, $orderedColumns);

        return response()->json(['message' => 'Table columns updated successfully']);
    }
}
