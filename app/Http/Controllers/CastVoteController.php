<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CastVoteController extends Controller
{
    use ValidatesRequests;

    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'employee_id' => 'required|exists:employees,id',
        ]);

        /** @var User $user */
        $user = Auth::user();

        abort_if($user->vote, Response::HTTP_FORBIDDEN);

        $user->vote()->create([
            'employee_id' => $request->input('employee_id'),
        ]);
    }
}
