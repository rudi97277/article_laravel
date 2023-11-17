<?php

namespace App\Http\Controllers;

use App\Http\Resources\MembershipResource;
use App\Mail\KartuMembership;
use App\Mail\PendaftaranMembership;
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
        $memberships =  Membership::when($request->verified, fn ($query) => $query->where('verified', $request->input('verified', 1)))
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

        $randomShortId = $this->generateRandomString();

        while (Membership::where('shortlink_id', $randomShortId)->first()) {
            $randomShortId = $this->generateRandomString();
        }

        $data['shortlink_id'] = $randomShortId;

        $membership = Membership::create($data);
        $membership->registration_number = $this->generateRegistrationNumber($membership->id);
        $membership->save();

        register_shutdown_function(function () {
            Artisan::call('-q queue:work --stop-when-empty');
        });

        $defaultLink = env('APP_URL') . '/membership';
        $encryptId = encrypt("salt$membership->id");
        Mail::to($membership->email)->send(new PendaftaranMembership($membership->name, "$defaultLink?key=$encryptId"));

        return $this->showOne(new MembershipResource($membership));
    }


    public function show(Request $request, $id)
    {

        if (!ctype_digit($id)) {
            try {
                $decryted = decrypt($id);
            } catch (\Throwable $th) {
                return $this->errorResponse('Unauthorized', 401, 40100);
            }

            $trueId = str_replace("salt", "", $decryted);
            $membership = Membership::whereNull('evidence_id')->findOrFail($trueId);
            $request->merge(['trueId' => $id]);
        } else
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

        if ($request->has('verified')) {
            register_shutdown_function(function () {
                Artisan::call('-q queue:work --stop-when-empty');
            });

            if ($membership->expired_at == null) {
                $data['expired_at'] = Carbon::now()->addYear()->format('Y-m-d H:i:s');
            }

            $encryptId = \encrypt("salt$membership->id");
            $url = "https://articles.iarn.or.id/card/$encryptId";
            Mail::to($membership->email)->send(new KartuMembership($membership->name, $request->verified, $url));
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

        if (ctype_digit($id))
            return $this->errorResponse('Unauthorized', 401, 40100);

        try {
            $decryted = decrypt($id);
        } catch (\Throwable $th) {
            return $this->errorResponse('Unauthorized', 401, 40100);
        }

        $trueId = str_replace("salt", "", $decryted);
        $membership = Membership::whereNull('evidence_id')->findOrFail($trueId);
        $request->merge(['trueId' => $id]);

        $membership->update($data);
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

    function generateRandomString($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
