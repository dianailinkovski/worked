<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {

	/**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'user1',
                'email' => 'user1@gmail.com',
                'password' => bcrypt('secret'),
            ],
            [
                'name' => 'user2',
                'email' => 'user2l@gmail.com',
                'password' => bcrypt('secret'),
            ],
        ];
 
        foreach($users as $u) {
            App\User::create($u);
        }
    }

}
