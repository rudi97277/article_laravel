<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\DocumentService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ArticleController extends Controller
{
    use ApiResponser;

    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index(Request $request)
    {
        $articles =  Article::with('cover')
            ->when($request->keyword, fn ($query) => $query->where(
                fn ($query) => $query->where('title', 'LIKE', "%$request->keyword%")
                    ->orWhere('description', 'LIKE', "%$request->keyword%")
            ))->paginate($request->input('page_size', 10));
        return $this->showPaginate('articles', collect(ArticleResource::collection($articles)), collect($articles));
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'document_ids' => 'required|array',
            'document_ids.*' => 'required|exists:documents,id',
            'cover_id' => 'required|exists:documents,id'
        ]);

        $user = request()->user();

        $article = Article::create(array_merge($request->only('title', 'description', 'cover_id'), ['created_by' => $user->id]));

        $modelData = [];

        foreach ($request->document_ids as $document) {
            $modelData[] = [
                'documentable_type' => Article::class,
                'documentable_id' => $article->id,
                'document_id' => $document
            ];
        }

        $article->modelDocuments()->insert($modelData);
        return $this->showOne(new ArticleResource($article->load('documents')));
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);
        return $this->showOne(new ArticleResource($article->load('documents')));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'document_ids' => 'nullable|array',
            'document_ids.*' => 'nullable|exists:documents,id',
            'cover_id' => 'nullable|exists:documents,id'
        ]);

        DB::beginTransaction();

        try {
            $article = Article::with('documents')->findOrFail($id);

            $deletedDocument  = [];
            if ($request->has('cover_id') && $request->cover_id !== $article->cover_id)
                $deletedDocument[] = $article->cover_id;

            if ($request->has('document_ids')) {
                $documentIds = $article->documents->pluck('id');
                $article->modelDocuments()->delete();

                $modelData = [];

                foreach ($request->document_ids as $document) {
                    $modelData[] = [
                        'documentable_type' => Article::class,
                        'documentable_id' => $article->id,
                        'document_id' => $document
                    ];
                }
                $article->modelDocuments()->insert($modelData);
                $deletedDocument = array_merge($deletedDocument, ($documentIds->diff($request->document_ids)->values())->toArray());
            }

            $data = $request->only('title', 'description', 'cover_id');
            $status = $article->update($data);


            $this->documentService->deleteResource($deletedDocument);
            DB::commit();
            return $this->showOne($status);
        } catch (Exception $e) {
            throw new HttpException(429, 'Failed to update Article!');
        }
    }

    public function destroy($id)
    {
        $article = Article::with('modelDocuments')->findOrFail($id);
        $deletedDocuments =  $article->modelDocuments->pluck('document_id')->toArray();
        $deletedDocuments = array_merge($deletedDocuments, [$article->cover_id]);
        $article->modelDocuments()->delete();
        $status = $article->delete();
        $this->documentService->deleteResource($deletedDocuments);
        return $this->showOne($status);
    }
}
