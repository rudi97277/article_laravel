<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class DocumentController extends Controller
{
    use ApiResponser;
    protected $documentService;
    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        $file = $request->file('file');
        $document = $this->documentService->upload($file);
        return $this->showOne($document);
    }
}
