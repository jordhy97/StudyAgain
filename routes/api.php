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
Route::get('questions/tagged/{tagName}', 'QuestionController@tagged');
Route::post('questions/{id}/upVote', 'QuestionController@upVote');
Route::post('questions/{id}/downVote', 'QuestionController@downVote');

#Answer
Route::get('answers/{id}', 'AnswerController@show');
Route::post('questions/{id}/answers', 'AnswerController@store');
Route::put('answers/{id}', 'AnswerController@update');
Route::delete('answers/{id}', 'AnswerController@destroy');
Route::post('answers/{id}/upVote', 'AnswerController@upVote');
Route::post('answers/{id}/downVote', 'AnswerController@downVote');

#Tag
Route::get('tags', 'TagController@index');
