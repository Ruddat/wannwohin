<?php

namespace Database\Seeders;

use App\Models\HeaderContent;
use Illuminate\Database\Seeder;

class HeaderContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $headerContents = [
            [
                'bg_img' => 'img/startpage/01_beste_reisezeit_b.jpg',
                'main_img' => 'img/startpage/01_beste_reisezeit_s.jpg',
                'main_text' => '<h1>EINDRUCKSVOLLE<br>NATURSCHAUSPIELE<br>ERLEBEN</h1>',
                'title' => 'Beeindruckende Reisen',
            ],
            [
                'bg_img' => 'img/startpage/02_beste_reisezeit_b.jpg',
                'main_img' => 'img/startpage/02_beste_reisezeit_s.jpg',
                'main_text' => '<h1>EINDRUCKSVOLLE<br>NATURSCHAUSPIELE<br>ERLEBEN</h1>',
                'title' => 'Beeindruckende Reisen',
            ],
            [
                'bg_img' => 'img/startpage/03_beste_reisezeit_b.jpg',
                'main_img' => 'img/startpage/03_beste_reisezeit_s.jpg',
                'main_text' => '<h1>EINDRUCKSVOLLE<br>NATURSCHAUSPIELE<br>ERLEBEN</h1>',
                'title' => 'Beeindruckende Reisen',
            ],
            [
                'bg_img' => 'img/startpage/04_beste_reisezeit_b.jpg',
                'main_img' => 'img/startpage/04_beste_reisezeit_s.jpg',
                'main_text' => '<h1>EINDRUCKSVOLLE<br>NATURSCHAUSPIELE<br>ERLEBEN</h1>',
                'title' => 'Beeindruckende Reisen',
            ],
            [
                'bg_img' => 'img/startpage/05_beste_reisezeit_b.jpg',
                'main_img' => 'img/startpage/05_beste_reisezeit_s.jpg',
                'main_text' => '<h1>EINDRUCKSVOLLE<br>NATURSCHAUSPIELE<br>ERLEBEN</h1>',
                'title' => 'Beeindruckende Reisen',
            ],
            [
                'bg_img' => 'img/startpage/06_beste_reisezeit_b.jpg',
                'main_img' => 'img/startpage/06_beste_reisezeit_s.jpg',
                'main_text' => '<h1>EINDRUCKSVOLLE<br>NATURSCHAUSPIELE<br>ERLEBEN</h1>',
                'title' => 'Beeindruckende Reisen',
            ],
        ];

        foreach ($headerContents as $content) {
            HeaderContent::create($content);
        }
    }
}
