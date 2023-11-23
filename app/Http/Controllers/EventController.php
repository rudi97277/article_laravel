<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Services\DocumentService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EventController extends Controller
{

    use ApiResponser;

    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index(Request $request)
    {
        $events =  Event::with('cover')
            ->when($request->keyword, fn ($query) => $query->where(
                fn ($query) => $query->where('title', 'LIKE', "%$request->keyword%")
                    ->orWhere('description', 'LIKE', "%$request->keyword%")
            ))
            ->paginate($request->input('page_size', 10));
        return $this->showPaginate('events', collect(EventResource::collection($events)), collect($events));
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

        $event = Event::create($request->only('title', 'description', 'cover_id'));

        $modelData = [];

        foreach ($request->document_ids as $document) {
            $modelData[] = [
                'documentable_type' => Event::class,
                'documentable_id' => $event->id,
                'document_id' => $document
            ];
        }

        $event->modelDocuments()->insert($modelData);
        return $this->showOne(new EventResource($event->load('documents')));
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return $this->showOne(new EventResource($event->load('documents')));
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
            $event = Event::with('documents')->findOrFail($id);

            $deletedDocument  = [];
            if ($request->has('cover_id') && $request->cover_id !== $event->cover_id)
                $deletedDocument[] = $event->cover_id;

            if ($request->has('document_ids')) {
                $documentIds = $event->documents->pluck('id');
                $event->modelDocuments()->delete();

                $modelData = [];

                foreach ($request->document_ids as $document) {
                    $modelData[] = [
                        'documentable_type' => Event::class,
                        'documentable_id' => $event->id,
                        'document_id' => $document
                    ];
                }
                $event->modelDocuments()->insert($modelData);
                $deletedDocument = array_merge($deletedDocument, ($documentIds->diff($request->document_ids)->values())->toArray());
            }

            $data = $request->only('title', 'description', 'cover_id');
            $status = $event->update($data);


            $this->documentService->deleteResource($deletedDocument);
            DB::commit();
            return $this->showOne($status);
        } catch (Exception $e) {
            throw new HttpException(429, 'Failed to update Event!');
        }
    }

    public function destroy($id)
    {
        $event = Event::with('modelDocuments')->findOrFail($id);
        $deletedDocuments =  $event->modelDocuments->pluck('document_id')->toArray();
        $deletedDocuments = array_merge($deletedDocuments, [$event->cover_id]);
        $event->modelDocuments()->delete();
        $status = $event->delete();
        $this->documentService->deleteResource($deletedDocuments);
        return $this->showOne($status);
    }

    public function calenderView(Request $request){
        $request->validate([
            'date' => 'required|date_format:Y-m'
        ]);
        $template = $this->calendarTemplate($request->date);
        $events  = Event::selectRaw("id,title,description,cover_id,DATE(created_at) AS date")->with('cover:id,url')->whereRaw("DATE_FORMAT(created_at,'%Y-%m') = '$request->date'")->get()->groupBy('date');
        foreach($events as $date => $list)
        {
            $template[$date]= array_merge($template[$date],$list->toArray());
        }
        return $this->showOne($template);
    }

    public function calendarTemplate($yearMonth)
    {
        $start = "$yearMonth-01";
        $end = Carbon::parse($start)->lastOfMonth()->format('Y-m-d');
        collect(CarbonPeriod::create($start,$end))->map(function($date) use (&$template) {
            $template[$date->format('Y-m-d')] = [];
        });

        return $template;
        
    }
}
