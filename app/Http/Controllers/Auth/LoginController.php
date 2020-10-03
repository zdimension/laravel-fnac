<?php

namespace App\Http\Controllers\Auth;

use App\Adherent;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers
    {
        logout as performLogout;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo()
    {
        return @$_GET["redirect"] ?: route("root");
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function username()
    {
        return "login";
    }

    public function logout(Request $request)
    {
        $this->performLogout($request);

        session()->flash("loggedout", 1);
        return redirect(@$_GET["redirect"] ?: route("root"));
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('cop_mel', $request->login)->first();

        if (!$user)
        {
            $adh = Adherent::where('adh_pseudo', $request->login)->first();

            if ($adh)
            {
                $user = $adh->compte;
            }
        }

        if ($user)
        {
            if (password_verify($request->password, $user->cop_motpasse))
            {
                Auth::login($user, $request->remember);
                $request->session()->put("panier", []);
                $request->session()->put("panier_liv", null);
                $request->session()->put("votes", []);
                return redirect()->intended($this->redirectTo());
            }
        }

        $this->sendFailedLoginResponse($request);
    }
}
