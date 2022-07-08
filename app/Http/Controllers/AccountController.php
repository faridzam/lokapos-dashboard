<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\user_role;

class AccountController extends Controller
{
    public function changePassword(Request $request){
        $thisUserID = Auth::user()->id;

        $user = User::where('id', $thisUserID)->first();

        if (Hash::check(request('currentPassword'), $user->password)) {
            if (request('newPassword') === request('cPassword')) {
                $userSelected = User::find($thisUserID);
                $userSelected->password = bcrypt(request('newPassword'));
                $userSelected->update();

                return response()->json([
                    'message' => 'success: password chhanged!'
                ]);
            } else{
                return response()->json([
                    'message' => 'failed: new password and confirmation password not match'
                ], 500);
            }
        } else{
            return response()->json([
                'message' => 'failed: wrong current password',
            ], 500);
        }
    }
}
