<?php

namespace Database\Seeders;

use App\Models\HeaderContent;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HeaderContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HeaderContent::create([
            'bg_img' => 'img/startpage/01_beste_reisezeit_b.jpg',
            'main_img' => 'img/startpage/01_beste_reisezeit_s.jpg',
            'main_text' => '<h1>EINDRUCKSVOLLE<br>NATURSCHAUSPIELE<br>ERLEBEN</h1>',
            'title' => 'Beeindruckende Reisen',
        ]);
    }
}
