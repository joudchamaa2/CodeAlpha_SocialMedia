<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware('api')->group(function (){
    require __DIR__.'/api.php';
});
