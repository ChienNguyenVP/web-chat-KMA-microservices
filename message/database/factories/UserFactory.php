<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Message;
use App\Conversation;
use Illuminate\Support\Arr;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});
$factory->define(Message::class, function (Faker $faker) {
    do {
        $from = rand(1, 10);
        $to = rand(1, 10);
    } while ($from === $to);

    return [
        'sender_id' => $from,
        'receiver_id' => $to,
        'content' => $faker->sentence(),
        'conversation_id' => rand(1,10)
    ];
});

$factory->define(Conversation::class, function (Faker $faker) {
    do {
        $from = rand(1, 10);
        $to = rand(1, 10);
    } while ($from === $to);
    return [
        'users' => [
            $from, $to
        ],
    ];
});