<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/text-mining', 'CrawlController@index');

Route::resource('kata', 'KataController');

Route::get('/preprocessing', 'PreprocessingController@index' );
// Route::get('/preprocessing/datalatih/', 'PreprocessingController@insert_into_tbl_latih');
// Route::get('/preprocessing/datauji/', 'PreprocessingController@insert_into_tbl_uji');

Route::get('/ngram', 'NGramController@index');
Route::get('/ngram/save', 'NGramController@saveNgram');

Route::get('/tf', 'TfController@index');
Route::get('/tf/save', 'TfController@save');

Route::get('/probabilitas/trigram', 'ProbabilitasController@trigram');
Route::get('/probabilitas/tf', 'ProbabilitasController@tf');
Route::get('/probabilitas/trigram/save', 'ProbabilitasController@saveTrigram');
Route::get('/probabilitas/tf/save', 'ProbabilitasController@saveTf');

Route::get('/uji', 'UjiController@index')->name('uji');
Route::get('/uji/{id}/tf', 'UjiController@tesTf')->name('uji.tf');
Route::get('/uji/{id}/trigram', 'UjiController@tesTrigram')->name('uji.trigram');


// query select insert
// Route::get('/data/insert/data-uji', 'KataController@select_insert_latih');
// Route::get('/data/insert/data-tes', 'KataController@select_insert_tes');
