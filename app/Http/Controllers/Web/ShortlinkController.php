<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use Illuminate\Support\Facades\Redirect;

class ShortlinkController extends Controller
{
    public function schooler($id)
    {
        $membership = Membership::select('link_schooler')->where('shortlink_id', $id)->first();
        \header("Location: $membership->link_schooler");
    }

    public function scoopus($id)
    {
        $membership = Membership::select('link_scoopus')->where('shortlink_id', $id)->first();
        \header("Location: $membership->link_scoopus");
    }
}
