<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Question;
use App\Tag;
use App\Answer;
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Model::unguard();

        DB::table('users')->delete();
        DB::table('questions')->delete();
        DB::table('tags')->delete();

        factory(App\User::class)->create(['name' => 'test', 'email' => 'test@example.com']);
        factory(App\User::class, 19)->create();

        factory(App\Tag::class, 15)->create();

        User::all()->random(10)->each(function ($user) {
           factory(App\Question::class, rand(1, 3))->create(['user_id' => $user->id]);
        });

        Question::all()->random(10)->each(function ($question) {
            $question->tags()->sync(Tag::all()->random(rand(1, 3)));
            User::all()->random(rand(1, 5))->each(function ($user) use ($question) {
               factory(App\Answer::class)->create(['user_id' => $user->id, 'question_id' => $question->id]);
            });
            User::all()->random(rand(1, 10))->each(function ($user) use ($question) {
                $vote_type = rand(1, 2);
                if ($vote_type == 2) {
                    $vote_type = -1;
                }
                $question->votes()->attach($user->id, ['vote_type' => $vote_type]);
            });
        });

        Answer::all()->random(10)->each(function ($answer) {
            User::all()->random(rand(1, 10))->each(function ($user) use ($answer) {
                $vote_type = rand(1, 2);
                if ($vote_type == 2) {
                    $vote_type = -1;
                }
                $answer->votes()->attach($user->id, ['vote_type' => $vote_type]);
            });
        });

        Model::reguard();
    }
}
