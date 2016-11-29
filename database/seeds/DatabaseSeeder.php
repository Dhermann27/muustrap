<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(StatecodesTableSeeder::class);
        $this->call(StaticdatesTableSeeder::class);
        $this->call(TimeslotsTableSeeder::class);
    }
}
