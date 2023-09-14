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
            'image' => 'required|image'
        ]);

        $image = $request->file('image');
        $document = $this->documentService->upload($image);
        return $this->showOne($document);
    }
}
