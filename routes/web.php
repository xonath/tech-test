<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

Route::get('/', function () {
    return response('Unauthorized!!!', Response::HTTP_UNAUTHORIZED);
});
