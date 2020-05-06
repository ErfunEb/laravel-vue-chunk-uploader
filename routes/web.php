<?php

// Storing chunked files post route
Route::post('/upload', 'UploadsController@upload');

// Get and upload info
Route::get('/file/{fileID}', 'UploadsController@show');

// list of uploads saved in database
Route::get('/list', 'UploadsController@list');






// Rendering main page for SPA
Route::get('/', 'IndexController@index');
