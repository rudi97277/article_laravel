<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use Carbon\Carbon;

class CardController extends Controller
{
    public function download($key)
    {
        try {
            $id = str_replace("salt", "", decrypt($key));
        } catch (\Throwable $th) {
            return null;
        }


        $membership = Membership::whereNotNull('evidence_id')->find($id);

        if (!$membership)
            return null;

        return view('downloads.card', [
            'memberId' => $membership->registration_number,
            'linkSchooler' => $membership->link_schooler,
            'linkScopus' => $membership->link_scoopus,
            'name' => $membership->name,
            'status' => $membership->status,
            'expired_at' => Carbon::parse($membership->expired_at)->format('M Y')
        ]);
    }
}
