<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('password'),
        'remember_token' => str_random(10)
    ];
});

$factory->define(App\Question::class, function (Faker\Generator $faker) {
    $time = $faker->dateTimeBetween('-2 years');
    return [
        'user_id' => null,
        'title' => $faker->sentence,
        'body' => $faker->paragraph(5),
        'created_at' => $time,
        'updated_at' => $time
    ];
});

$factory->define(App\Answer::class, function (Faker\Generator $faker) {
    return [
        'user_id' => null,
        'question_id' => null,
        'body' => $faker->paragraph(5)
    ];
});

$factory->define(App\Tag::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->unique()->word
    ];
});