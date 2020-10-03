@extends('layouts.app')

<?php
function old_($name)
{
    $default = "";
    if (auth()->check() && auth()->user()->est(App\User::ADHERENT))
    {
        if (starts_with($name, "cop_"))
            $default = auth()->user()->$name;
        else if (starts_with($name, ["adh_", "mag_"]))
            $default = auth()->user()->adherent->$name;
    }
    return old($name, $default);
}
?>

@section('content')
    <div class="w-100">
        <div class="card mx-auto text-center" style="width: 550px">
            <div class="card-header"><h4
                        class="mb-0">{{isset($edit) ? "Modifier les informations" : "Informations"}}</h4></div>

            <div class="card-body">
                <form method="POST" action="{{ request()->getRequestUri()  }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    @if(isset($edit))
                        {{method_field("PATCH")}}
                    @endif

                    @include("widgets.info", ["src" => "adh"])

                    @if (isset($edit))
                        <div class="form-group">
                            <input id="password_old" type="password"
                                   class="form-control{{ $errors->has('password_old') ? ' is-invalid' : '' }}"
                                   name="password_old" required
                                   placeholder="Ancien mot de passe *">

                            <div class="feedback mb-3">
                                Par mesure de sécurité, veuillez saisir votre mot de passe actuel afin de pouvoir
                                effectuer des modifications.
                            </div>

                            @include("widgets.field-error", ["field" => "password_old"])
                        </div>
                    @endif

                    <div class="feedback mb-3 mt-0">
                        Les champs marqués d'une astérisque sont obligatoires.
                    </div>

                    <div class="form-group">
                        <input id="adh_pseudo" type="text"
                               class="form-control{{ $errors->has('adh_pseudo') ? ' is-invalid' : '' }}"
                               name="adh_pseudo"
                               placeholder="Nom d'utilisateur *"
                               value="{{ old_('adh_pseudo') }}" required
                               maxlength="20" autofocus>

                        @include("widgets.field-error", ["field" => "adh_pseudo"])
                    </div>

                    <div class="form-row">
                        <div class="col" style="max-width: 120px">
                            <div class="form-group">
                                <select name="adh_civilite" id="adh_civilite"
                                        class="form-control{{ $errors->has('adh_civilite') ? ' is-invalid' : '' }}">
                                    <option value="" selected disabled>Civilité *</option>
                                    <?php
                                    $civ = [
                                        "M.",
                                        "Mme",
                                        "Mlle"
                                    ]
                                    ?>
                                    @foreach($civ as $c)
                                        <option
                                                value="{{$c}}" {{old_("adh_civilite")==$c?"selected":""}}>{{$c}}</option>
                                    @endforeach
                                </select>


                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <input id="adh_prenom" type="text"
                                       class="form-control{{ $errors->has('adh_prenom') ? ' is-invalid' : '' }}"
                                       name="adh_prenom"
                                       placeholder="Prénom *"
                                       value="{{ old_('adh_prenom') }}" required autofocus maxlength="50">


                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <input id="adh_nom" type="text"
                                       class="form-control{{ $errors->has('adh_nom') ? ' is-invalide' : '' }}"
                                       name="adh_nom"
                                       placeholder="Nom *"
                                       value="{{ old_('adh_nom') }}" required autofocus maxlength="50">


                            </div>
                        </div>
                    </div>

                    @include("widgets.field-error", ["field" => "adh_civilite"])

                    @include("widgets.field-error", ["field" => "adh_prenom"])

                    @include("widgets.field-error", ["field" => "adh_nom"])

                    <div class="form-group">
                        <input id="cop_mel" type="email"
                               class="form-control{{ $errors->has('cop_mel') ? ' is-invalid' : '' }}" name="cop_mel"
                               placeholder="Adresse e-mail *"
                               value="{{ old_('cop_mel') }}" required autofocus>

                        @include("widgets.field-error", ["field" => "cop_mel"])
                    </div>

                    <div class="form-row">
                        <div class="col">
                            <div class="form-group mb-0">
                                <input id="cop_motpasse" type="password" minlength="8"
                                       class="form-control{{ $errors->has('cop_motpasse') ? ' is-invalid' : '' }}"
                                       name="cop_motpasse"
                                       placeholder="Mot de passe @if(!isset($edit))*@endif">


                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group mb-0">
                                <input id="cop_motpasse_confirmation" type="password" minlength="8"
                                       class="form-control{{ $errors->has('cop_motpasse') ? ' is-invalid' : '' }}"
                                       name="cop_motpasse_confirmation"
                                       placeholder="Confirmer le mot de passe @if(!isset($edit))*@endif">
                            </div>
                        </div>
                        <div class="feedback mb-3">
                            Le mot de passe doit comporter au moins 8 caractères.
                        </div>
                    </div>

                    @include("widgets.field-error", ["field" => "cop_motpasse"])

                    <div class="form-row">
                        <div class="col">
                            <div class="form-group mb-0">
                                <input id="adh_telfixe" type="tel"
                                       class="form-control{{ $errors->has('adh_telfixe') ? ' is-invalid' : '' }}"
                                       name="adh_telfixe"
                                       placeholder="Téléphone fixe"
                                       value="{{ old_('adh_telfixe') }}" autofocus maxlength="15">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group mb-0">
                                <input id="adh_telportable" type="tel"
                                       class="form-control{{ $errors->has('adh_telportable') ? ' is-invalid' : '' }}"
                                       name="adh_telportable"
                                       placeholder="Téléphone mobile"
                                       value="{{ old_('adh_telportable') }}" autofocus maxlength="15">
                            </div>
                        </div>
                        <div class="feedback mb-3">
                            Vous devez saisir au moins un numéro de téléphone.
                        </div>
                    </div>

                    @if ($errors->has('adh_telportable') || $errors->has('adh_telfixe'))
                        @include("widgets.alert", ["error" => true, "message" => "Vous devez saisir au moins un numéro de téléphone valide."])
                    @endif

                    <div class="form-group">
                        <select name="mag_id" id="mag_id"
                                class="form-control{{ $errors->has('mag_id') ? ' is-invalid' : '' }}">
                            <option value="" selected>Magasin Fnack préféré</option>
                            @foreach($mags as $m)
                                <option
                                        value="{{$m->mag_id}}" {{old_("mag_id")==$m->mag_id?"selected":""}}>
                                    {{$m->mag_ville}} &mdash; {{$m->mag_nom}}</option>
                            @endforeach
                        </select>

                        @include("widgets.field-error", ["field" => "mag_id"])
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">
                            {{isset($edit) ? "Envoyer" : "Inscription"}}
                        </button>
                    </div>

                    @if(!isset($edit))
                        <div class="form-group mt-3 mb-0">
                            <small class="text-secondary d-block">En cliquant sur Inscription, vous acceptez les
                                conditions
                                générales de vente de Fnack ainsi que la cession immédiate de votre âme à Fnack pour
                                l'éternité.</small>
                        </div>
                    @endif
                </form>
            </div>
        </div>

    </div>
@endsection
