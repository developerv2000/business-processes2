<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Support\Helper;
use App\Support\Traits\DestroysModelRecords;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    use DestroysModelRecords;

    public $model = Attachment::class; // used in multiple destroy/restore traits

    public function index(Request $request)
    {
        $modelBaseName = $request->route('modelName');
        $modelID = $request->route('modelID');
        $modelFullName = Helper::addFullNamespaceToModel($modelBaseName);

        $records = $modelFullName::find($modelID)->attachments;

        return view('attachments.index', compact('request', 'records'));
    }
}
