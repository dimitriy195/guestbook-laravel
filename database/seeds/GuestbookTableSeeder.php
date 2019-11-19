<?php

use Illuminate\Database\Seeder;

class GuestbookTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::all()->each(function ($user) {
            $user->messages()->save(factory(App\GuestbookMessage::class)->make());
        });
    }
}
