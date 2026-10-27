<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Backend API pur (section 1) : pas de vue serveur, pas de page de
| connexion. L'application mobile Flutter et l'interface web NextJS sont
| des projets séparés qui consomment l'API REST (routes/api.php).
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => response()->json([
    'name' => config('app.name'),
    'status' => 'ok',
]));
