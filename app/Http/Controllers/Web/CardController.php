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
            return view('errors.404');
        }

        $membership = Membership::whereNotNull('evidence_id')->find($id);

        if (!$membership)
            return view('errors.404');

        return view('downloads.card', [
            'memberId' => $membership->registration_number,
            'linkSchooler' =>  "ristek.or.id/sch/$membership->shortlink_id",
            'linkScopus' =>  "ristek.or.id/sco/$membership->shortlink_id",
            'name' => $membership->name,
            'status' => $membership->status,
            'expired_at' => Carbon::parse($membership->expired_at)->format('M Y')
        ]);
    }
}
