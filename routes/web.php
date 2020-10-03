<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', "LivreController@index")->name("root");

Route::prefix("/genres")->name("genres.")->group(function() {
    Route::get('/', "GenreController@index")->name("list");
    Route::put('/ajouter', "GenreController@add")->name("add");
});

Route::prefix("/livres")->name("livres.")->group(function() {
    Route::get('/', "LivreController@index")->name("list");
    Route::get('/{id}', "LivreController@detail")->name('detail')->where('id', '[0-9]+');
    Route::put('/photos/ajouter/{redirect?}', 'LivreController@addPhotos')->name('photos.add');
});

Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');

Route::prefix("/avis")->name("avis.")->group(function() {
    Route::post('/{id}/vote', 'AvisController@vote')->where('id', '[0-9]+');
    Route::post('/{id}/abusif', 'AvisController@abusif')->where('id', '[0-9]+');
    Route::delete('/{id}/supprimer', 'AvisController@delete')->where('id', '[0-9]+')->name("delete");
    Route::put('/ajouter/{liv_id}', 'AvisController@add')->where('liv_id', '[0-9]+');
    Route::get('/abusifs', 'AvisController@voirAbusifs')->name("abusifs.list");
});

Route::prefix("/compte")->name("compte.")->group(function() {
    Route::get('/', 'AdherentController@voirProfil')->name("view");
    Route::patch('/', 'AdherentController@modifierProfil')->name("edit");

    Route::prefix("/relais")->name("relais.")->group(function() {
        Route::get('/', 'AdherentController@listRelais')->name("list");
        Route::put('/ajouter', 'AdherentController@addRelais')->name("add");
        Route::delete('/supprimer/{id}', 'AdherentController@deleteRelais')->name("delete")->where('id', '[0-9]+');
    });

    Route::prefix("/adresses")->name("adr.")->group(function() {
        Route::get('/', 'AdherentController@listAdr')->name("list");
        Route::put('/ajouter', 'AdherentController@addAdr')->name("add");
        Route::delete('/supprimer/{id}', 'AdherentController@deleteAdr')->name("delete")->where('id', '[0-9]+');
    });

    Route::prefix("/commandes")->name("comm.")->group(function() {
        Route::get('/', 'CommandeController@listOrderUser')->name("list");
    });
});

Route::prefix("/commandes")->name("comm.")->group(function() {
    Route::get('/', 'CommandeController@listOrder')->name("list");
});

Route::prefix("/panier")->name("panier.")->group(function() {
    Route::get("/", "PanierController@panier")->name("view");
    Route::put('/ajouter/{liv_id}', 'PanierController@add')->name("add")->where('liv_id', '[0-9]+');
    Route::delete('/supprimer/{liv_id}', 'PanierController@delete')->name("delete")->where('liv_id', '[0-9]+');
    Route::patch('/quantite/{id}', 'PanierController@quantite')->name("quantite")->where('id', '[0-9]+');
    Route::post('/commander', 'PanierController@order')->name("order");
});
