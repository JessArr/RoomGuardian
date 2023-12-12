<?php

use App\Http\Controllers\AuthsController;
use App\Http\Controllers\RoomsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post( 'register', [AuthsController::class, 'register'] );
Route::post( 'login', [AuthsController::class, 'login'] );
Route::get( '/verificaremail', [AuthsController::class, 'verificarmail'] )->name( 'verificaremail' )->middleware( 'signed' );
Route::get('validarToken', [AuthsController::class, 'tokenvalidate']);

Route::group( ['middleware' => 'validartoken'], function () {
    Route::post( 'logout', [AuthsController::class, 'logout'] );
    Route::post( 'refresh', [AuthsController::class, 'refresh'] );
    Route::get('roomsUser', [Roomscontroller::class, 'roomsUser']);
    Route::get('roomdetail/{id}', [Roomscontroller::class, 'roomdetail']);
    Route::post('roomsStore', [Roomscontroller::class, 'store']);
    Route::put('roomsUpdate/{id}', [Roomscontroller::class, 'update']);
    Route::delete('roomsDestroy/{id}', [Roomscontroller::class, 'destroy']);
});

