<?php

// routes digunakan untuk list sebuah endpoint

require_once __DIR__ . '/../config/Route.php';

use Config\Route;

$base_url = "/smkti/restapi-bookshelf-level2";

Route::post( $base_url . "/api/registrasi", function () {
    echo json_encode([
        "message"=> "ini registrasi"
    ]);
});



// Add more routes here
Route::run();