<?php

namespace App\Http\Controllers;

use App\Http\Resources\MembershipResource;
use App\Models\Membership;
use App\Services\DocumentService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class MembershipController extends Controller
{

    use ApiResponser;
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index(Request $request)
    {
        $memberships =  Membership::where('verified', $request->input('verified', 1))->paginate($request->input('page_size', 10));
        return $this->showPaginate('articles', collect(MembershipResource::collection($memberships)), collect($memberships));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string',
            'name' => 'required|string',
            'status' => 'required|string',
            'link_schooler' => 'required|url',
            'link_scoopus' => 'required|url',
            'evidence_id' => 'required|exists:documents,id'
        ]);

        $membership = Membership::create($data);
        return $this->showOne(new MembershipResource($membership->load('evidence')));
    }


    public function show($id)
    {
        $membership = Membership::findOrFail($id);
        return $this->showOne(new MembershipResource($membership->load('evidence')));
    }


    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'nullable|string',
            'status' => 'nullable|string',
            'link_schooler' => 'nullable|url',
            'link_scoopus' => 'nullable|url',
            'evidence_id' => 'nullable|exists:documents,id',
            'verified' => 'nullable|boolean'
        ]);

        $membership = Membership::findOrFail($id);
        $deletedId = [];
        if ($request->has('evidence_id') && $membership->evidence_id != $request->evidence_id) {
            $deletedId = [$membership->evidence_id];
        }

        $status = $membership->update($data);
        $this->documentService->deleteResource($deletedId);
        return $this->showOne($status);
    }


    public function destroy($id)
    {
        $membership = Membership::findOrFail($id);
        $deletedId = [$membership->evidence_id];
        $status = $membership->delete();
        $this->documentService->deleteResource($deletedId);
        return $this->showOne($status);
    }
}
