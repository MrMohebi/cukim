<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $resId = Str::random(2);
        DB::table('food_groups')->insert([
            "persian_name"=>"رستوران ".$resId,
            "english_name"=>"Res".$resId
        ]);
    }
}
