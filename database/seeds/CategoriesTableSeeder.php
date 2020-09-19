<?php

use App\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = collect(['Framework', 'Code', 'Bug', 'Begadang']);
        $categories->each(function($c){
            Category::create([
                'name' => $c,
                'slug' => \Str::slug($c)
            ]);
        });
    }
}
