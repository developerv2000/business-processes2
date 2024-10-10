<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Support\Helper;
use App\Support\Traits\DestroysModelRecords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AttachmentController extends Controller
{
    use DestroysModelRecords;

    public $model = Attachment::class; // used in multiple destroy/restore traits

    public function index(Request $request)
    {
        $modelBaseName = $request->route('modelName');

        // Secure request
        $this->authorizeGates($modelBaseName);

        $modelID = $request->route('modelID');
        $modelFullName = Helper::addFullNamespaceToModel($modelBaseName);

        $records = $modelFullName::find($modelID)->attachments;

        return view('attachments.index', compact('request', 'records'));
    }

    private function authorizeGates($modelBaseName)
    {
        switch ($modelBaseName) {
            case 'Manufacturer':
                Gate::authorize('edit-epp');
                break;
            case 'Product':
                Gate::authorize('edit-ivp');
                break;
            default:
                abort(404);
        }
    }
}
