<?php

use App\Folder;
use App\Http\Middleware\CheckMaster;
use App\Master;
use App\Password;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('passwords');
});

Route::post('login', function () {
    if (request()->has('master')) {
        Master::set(request('master'));
        // TODO: check password here
    }

    return redirect()->route('passwords');
})->name('login.action');

Route::middleware(CheckMaster::class)->group(function () {

    Route::get('/passwords', function () {
        return view('passwords', ['passwords' => Password::all()->map->decrypt()]);
    })->name('passwords');

    Route::get('/passwords/{id}', function (string $id) {
        return view('password', ['password' => Password::find($id)]);
    })->name('password');

    Route::get('/folders', function () {
        return view('folders', ['folders' => Folder::all()->map->decrypt()]);
    })->name('folders');

    Route::get('/folders/{id}', function (string $id) {
        return view('folder', ['folder' => Folder::find($id)]);
    })->name('folder');

});
