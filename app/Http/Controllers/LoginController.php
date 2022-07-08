<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\user_role;
 
class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function index(Request $request){

        if (Auth::check()) {
            return redirect('dashboard');
        }

        return view('login');
    }
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
 
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = User::where('id', Auth::user()->id)->first();
            $userSession = session()->get('user', []);

            if(isset($userSession[Auth::user()->id])) {
                unset($userSession);
                session()->forget('user');
                session()->put('user', []);
                $userSession[$id] = [
                    "id" => $user->id,
                    "name" => $user->name,
                    "role_id" => $user->role_id,
                ];
            } else {
                unset($userSession);
                session()->forget('user');
                $userSession[Auth::user()->id] = [
                    "id" => $user->id,
                    "name" => $user->name,
                    "role_id" => $user->role_id,
                ];
            }

 
            return redirect()->intended('dashboard');
        }
 
        return back();
    }

    public function pickStore($id)
    {
        $stores = pos_pc_desktop::where('id', $id)->first();

        $store = session()->get('store', []);

        if(isset($store[$id])) {
            unset($store);
            session()->forget('store');
            session()->put('store', []);
            $store[$id] = [
                "id" => $stores->id,
                "store_id" => $stores->store_id,
                "name" => $stores->name,
            ];
        } else {
            unset($store);
            session()->forget('store');
            $store[$id] = [
                "id" => $stores->id,
                "store_id" => $stores->store_id,
                "name" => $stores->name,
            ];
        }

        session()->put('store', $store);
        return redirect('/app');
    }
}