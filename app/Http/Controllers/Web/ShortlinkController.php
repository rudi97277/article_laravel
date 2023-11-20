<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Membership;

class ShortlinkController extends Controller
{
    public function schooler($id)
    {
        $membership = Membership::select('link_schooler')->where('shortlink_id', $id)->first();
        if (!$membership)
            return view('errors.404');
        header("HTTP/1.1 301 Moved Permanently");
        // header("Location: $membership->link_schooler", true, 301);
        header("Location: $membership->link_schooler");
        exit();
    }

    public function scoopus($id)
    {
        $membership = Membership::select('link_scoopus')->where('shortlink_id', $id)->first();
        if (!$membership)
            return view('errors.404');
        // header("Location: $membership->link_scoopus", true, 301);
        header("Location: $membership->link_scoopus");
        exit();
    }
}
