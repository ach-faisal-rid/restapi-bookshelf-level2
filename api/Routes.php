<?php

// routes digunakan untuk list sebuah endpoint

require_once __DIR__ . '/../config/Route.php';
use Config\Route;

require_once __DIR__ .'/controllers/AuthController.php';
use controllers\AuthController;

$base_url = "/smkti/restapi-bookshelf-level2";

Route::post( $base_url . "/api/registrasi", function () {
    echo json_encode([
        "message"=> "ini registrasi"
    ]);
});

/**
 * api auth user
 */
Route::post( $base_url . "/api/auth/registrasi", function () {
    $controller = new AuthController();
    $controller->registrasi();
});



// Add more routes here
Route::run();