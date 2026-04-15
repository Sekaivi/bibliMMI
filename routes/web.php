<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControleurUsager;
use App\Http\Controllers\ControleurOuvrages;
use App\Http\Controllers\ControleurExemplaires;
use App\Http\Controllers\TableauDeBordController;
use App\Http\Controllers\AccueilControleur;

Route::get('/', function () {
    return view('accueil');
});

Route::get('/', [AccueilControleur::class, 'index'])
    ->name('accueil');


Route::get('/search', [AccueilControleur::class, 'search'])
    ->name('accueil.search');

Route::resource('usagers', ControleurUsager::class);

Route::post('ouvrages/{ouvrage}/delete_image', [ControleurOuvrages::class, 'delete_image'])->name('ouvrages.delete_image');
Route::resource('ouvrages', ControleurOuvrages::class);

Route::resource('ouvrages.exemplaires', ControleurExemplaires::class);


Route::any('/tableaudebord/{action?}/{objet?}', [TableauDeBordController::class, 'handleAny'])
    ->middleware(['auth', 'verified'])
    ->name('tableaudebord.any');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
