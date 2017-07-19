<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

#Authentication
Route::post('login', 'AuthController@login');
Route::post('register', 'AuthController@register');
Route::get('user', 'AuthController@userInfo');

#Question
Route::get('questions', 'QuestionController@index');
Route::get('questions/{id}', 'QuestionController@show');
Route::post('questions', 'QuestionController@store');
Route::put('questions/{id}', 'QuestionController@update');
Route::delete('questions/{id}', 'QuestionController@destroy');

#Answer
Route::post('questions/{id}/answers', 'AnswerController@store');
Route::put('answers/{id}', 'AnswerController@update');
Route::delete('answers/{id}', 'AnswerController@destroy');

