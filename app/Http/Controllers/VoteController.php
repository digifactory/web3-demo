<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('vote');
    }
}
