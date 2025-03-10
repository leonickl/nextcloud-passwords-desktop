<?php

use App\Client;
use App\Folder;
use App\Http\Middleware\CheckMaster;
use App\Http\Middleware\LifeSaver;
use App\Master;
use App\Password;
use App\RootFolder;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('passwords');
})->name('index');

Route::get('/login', function () {
    return redirect()->route('index');
});

Route::post('/login', function () {
    if (request()->has('master')) {
        Master::set(request('master'));
        Client::login();
    }

    return redirect()->route('passwords');
})->name('login.action');

Route::get('/check-auth', function () {
    return response()->json([
        'authenticated' => !Master::empty(),
    ]);
})->name('check-auth');

Route::middleware([CheckMaster::class, LifeSaver::class])->group(function () {

    Route::get('/passwords', function () {
        return view('passwords', ['passwords' => Password::all()->map->decrypt()->sortBy('label')]);
    })->name('passwords');

    Route::get('/passwords/{id}', function (string $id) {
        $password = Password::find($id);

        return view('password', [
            'password' => $password->decrypt(['label', 'username', 'notes', 'customFields', 'url', 'password']),
            'folder' => Folder::find($password->folder())?->decrypt(),
        ]);
    })->name('password');

    Route::get('/folders/{id?}', function (?string $id = null) {
        $base_folder = $id ? Folder::find($id) : new RootFolder;
        $sub_folders = $base_folder->children();
        $passwords = Password::all()
            ->filter(fn(Password $password) => $password->folder() === $base_folder->id())
            ->map->decrypt();

        return view('folders', [
            'base_folder' => $base_folder->decrypt(),
            'sub_folders' => $sub_folders->map->decrypt(),
            'passwords' => $passwords,
        ]);
    })->name('folders');

    Route::get('/folders/{id}', function (string $id) {
        return view('folder', ['folder' => Folder::find($id)]);
    })->name('folder');

    Route::any('/logout', function () {
        Client::logout();
        return redirect()->route('index');
    })->name('logout');

    Route::get('/sync', function () {
        Client::sync();
        return back();
    })->name('sync');

});
