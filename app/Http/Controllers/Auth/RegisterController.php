<?php

namespace App\Http\Controllers\Auth;

use App\Magasin;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected function redirectTo()
    {
        if (Auth::user()->est(User::ADHERENT))
            return route("compte.adr.list");

        return route("root");
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    protected static function num_adherent(){
    $result = User::select('*')
                ->where("t_e_adherent_adh.adh_numadherent", '>=', "t_e_adherent_adh.adh_numadherent")
                ->orderby("t_e_adherent_adh.adh_numadherent","desc");

        foreach ($result as $key => $value) {
            foreach ($result[$key] as $key => $value) {
                if ($key === "adh_numadherent") {
                    return $value;
                }
            }
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'cop_mel' => 'required|string|email|max:80|unique:t_e_compte_cop',
            'cop_motpasse' => 'required|string|min:8|confirmed',
            'adh_pseudo' => 'required|string|max:20',
            'adh_civilite' => 'required|string|in:M.,Mme,Mlle',
            'adh_nom' => 'required|string|max:50',
            'adh_prenom' => 'required|string|max:50',
            'adh_telfixe' => 'max:15|required_without_all:adh_telportable',
            'adh_telportable' => 'max:15|required_without_all:adh_telfixe',
            'mag_id' => 'nullable|exists:t_r_magasin_mag,mag_id',
        ], [
            "cop_motpasse.confirmed" => "Les mots de passe ne correspondent pas.",
            "cop_mel.unique" => "Un compte avec cette adresse e-mail existe déjà.",
            "cop_mel.email" => "L'adresse e-mail n'est pas valide.",
            "mag_id.exists" => "Le magasin sélectionné n'existe pas.",
            "adh_civilite.required" => "Vous devez sélectionnez une civilité.",
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */

    protected function create(array $data)
    {
        $res = array_merge($data, [
            'adh_datefinadhesion' => date("Y-m-d"),
            'cop_motpasse' => bcrypt($data['cop_motpasse'])
        ]);
        unset($res["cop_motpasse_confirmation"]);
        return User::create($res);
    }

    public function showRegistrationForm()
    {
        return view('auth.register', ["mags" => Magasin::all()]);
    }

}
