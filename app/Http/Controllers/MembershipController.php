<?php

namespace App\Http\Controllers;

use App\Http\Resources\MembershipResource;
use App\Mail\MemberRegister;
use App\Models\Membership;
use App\Services\DocumentService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

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
        $memberships =  Membership::where('verified', $request->input('verified', 1))
            ->when(
                $request->keyword,
                fn ($query) => $query
                    ->where(
                        fn ($search) => $search
                            ->where('name', 'LIKE', "%$request->keyword%")
                            ->orWhere('status', 'LIKE', "%$request->keyword%")
                            ->orWhere('link_schooler', 'LIKE', "%$request->keyword%")
                            ->orWhere('link_scoopus', 'LIKE', "%$request->keyword%")
                    )
            )
            ->paginate($request->input('page_size', 10));
        return $this->showPaginate('memberships', collect(MembershipResource::collection($memberships)), collect($memberships));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'status' => 'required|string',
            'link_schooler' => 'required|url',
            'link_scoopus' => 'required|url',
            'email' => 'required|email'
        ]);

        $membership = Membership::create($data);
        $membership->registration_number = $this->generateRegistrationNumber($membership->id);
        $membership->save();

        register_shutdown_function(function ()  {
            Artisan::call('-q queue:work --stop-when-empty');

        });

        $defaultLink = 'http://localhost:5173/membership';
        $encryptId = encrypt($membership->id);
        Mail::to($membership->email)->send(new MemberRegister($membership->name, "$defaultLink?key=$encryptId"));

        return $this->showOne(new MembershipResource($membership));
    }


    public function show($id)
    {
        
        if(!ctype_digit($id))
            $id = decrypt($id);

        $membership = Membership::findOrFail($id);
        return $this->showOne(new MembershipResource($membership));
    }


    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'nullable|string',
            'status' => 'nullable|string',
            'link_schooler' => 'nullable|url',
            'link_scoopus' => 'nullable|url',
            'email' => 'nullable|email',
            'evidence_id' => 'nullable|exists:documents,id',
            'verified' => 'nullable|boolean'
        ]);

        $membership = Membership::findOrFail($id);
        $deletedId = [];
        if ($request->has('evidence_id') && $membership->evidence_id != $request->evidence_id) {
            $deletedId = [$membership->evidence_id];
        }

        $membership->update($data);
        $this->documentService->deleteResource($deletedId);
        return $this->showOne(new MembershipResource($membership));
    }

    public function updateEvidence(Request $request, $id)
    {
        $data = $request->validate([
            'evidence_id' => 'required|exists:documents,id'
        ]);

        $membership = Membership::findOrFail($id);
        $deletedId = [];
        if ($request->has('evidence_id') && $membership->evidence_id != $request->evidence_id) {
            $deletedId = [$membership->evidence_id];
        }

        $membership->update($data);
        $this->documentService->deleteResource($deletedId);
        return $this->showOne(new MembershipResource($membership));
    }


    public function destroy($id)
    {
        $membership = Membership::findOrFail($id);
        $deletedId = [$membership->evidence_id];
        $status = $membership->delete();
        $this->documentService->deleteResource($deletedId);
        return $this->showOne($status);
    }

    public function generateRegistrationNumber($id)
    {
        $now = Carbon::now();
        return "$id/IEIA/{$now->format('m')}/{$now->format('Y')}";
    }
}
