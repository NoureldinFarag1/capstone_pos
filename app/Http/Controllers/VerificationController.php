<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $isValid = Hash::check($request->password, Auth::user()->password);

        if ($isValid) {
            session(['data_verified' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
}
