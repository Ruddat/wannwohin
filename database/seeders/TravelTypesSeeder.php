<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TravelTypesSeeder extends Seeder
{
    public function run()
    {
        // Mapping: Slug zu `list_*` Kategorie
        $urlaubTypeMap = [
            'strand-reise' => 'list_beach',
            'staedte-reise' => 'list_citytravel',
            'sport-reise' => 'list_sports',
            'insel-reise' => 'list_island',
            'kultur-reise' => 'list_culture',
            'natur-reise' => 'list_nature',
            'wassersport-reise' => 'list_watersport',
            'wintersport-reise' => 'list_wintersport',
            'mountainsport-reise' => 'list_mountainsport',
            'biking-reise' => 'list_biking',
            'fishing-reise' => 'list_fishing',
            'amusement-park-reise' => 'list_amusement_park',
            'water-park-reise' => 'list_water_park',
            'animal-park-reise' => 'list_animal_park',
        ];

        // Erweiterte Reise-Typen
        $travelTypes = [
            [
                'slug' => 'strand-reise',
                'image' => 'img/startpage/meer-wasser-und-strand.webp',
                'title' => 'Meer, Wasser und Strand',
                'title_text' => 'Was will man mehr!',
                'description' => 'Du liebst die Weite des Meeres, den feinen Strandsand und möchtest am Liebsten im Rauschen der Wellen Deine Seele baumeln lassen? Hier findest Du die besten Urlaubsziele in Wassernähe.',
                'filter_months' => [5, 6, 7, 8, 9] // Mai - September
            ],
            [
                'slug' => 'natur-reise',
                'image' => 'img/startpage/urlaub-in-der-natur.webp',
                'title' => 'Natürlich Urlaub',
                'title_text' => 'aber nur in der Natur!',
                'description' => 'Dem Alltags-Stress entkommst du am Besten im Grünen. Morgens schon mit Vogelgezwitscher aufwachen oder einfach die Weite der Berge geniessen? Beeindruckende Natur-Reiseziele sind hier zu finden.',
                'filter_months' => [4, 5, 6, 7, 8, 9, 10]
            ],
            [
                'slug' => 'staedte-reise',
                'image' => 'img/startpage/kein-stillstand-hauptsache-in-der-stadt.webp',
                'title' => 'Kein Stillstand',
                'title_text' => 'Hauptsache Stadt',
                'description' => 'Trubel, Action und das pulsierende Lebensgefühl einer Stadt lässt Dich höher und weiter treiben? Hier sind die schönsten Städtereisen zum Shoppen, Genießen oder einfach Spaß haben zusammengefasst.',
                'filter_months' => range(1, 12) // Ganzjährig
            ],
            [
                'slug' => 'kultur-reise',
                'image' => 'img/startpage/culture-beat-kultur-und-geschichte.webp',
                'title' => 'Culture Beat',
                'title_text' => 'Kultur und Geschichte',
                'description' => 'Du interessierst Dich für monumentale Bauwerke, geschichtsträchtige Orte und Menschen, die in der Vergangenheit Großes geleistet haben? Hier findest Du die kulturell interessantesten Reiseziele.',
                'filter_months' => [3, 4, 5, 6, 9, 10]
            ],
            [
                'slug' => 'insel-reise',
                'image' => 'img/startpage/insel-urlaub.webp',
                'title' => 'Insel-Feeling',
                'title_text' => 'Weit weg von allem!',
                'description' => 'Deinen Urlaub verbringst Du am liebsten weit weg von allem - Hauptsache auf einer Insel? Hier sind die schönsten Inselziele, egal ob klein oder groß.',
                'filter_months' => [6, 7, 8, 9, 10]
            ],
            [
                'slug' => 'wintersport-reise',
                'image' => 'img/startpage/winter-urlaub.webp',
                'title' => 'Cooler Urlaub',
                'title_text' => 'Wintersport & Schnee!',
                'description' => 'Du freust Dich auf Schneeflocken und liebst das Glitzern der Sonne auf dem Schnee? Dann ist ein Winterurlaub genau das Richtige für Dich!',
                'filter_months' => [12, 1, 2, 3]
            ],
            [
                'slug' => 'sport-reise',
                'image' => 'img/startpage/aktiv-urlaub.webp',
                'title' => 'Sport & Action',
                'title_text' => 'Zeit für mich',
                'description' => 'Sport ist für Dich eine Passion und du schaltest am besten ab, wenn du aktiv bist? Hier gibt es die besten Sporturlaub-Ziele!',
                'filter_months' => [4, 5, 6, 7, 8, 9, 10]
            ],
            [
                'slug' => 'wassersport-reise',
                'image' => 'img/startpage/wassersport-urlaub.webp',
                'title' => 'Wassersport',
                'title_text' => 'Tauchen, Surfen & mehr!',
                'description' => 'Ob Tauchen, Surfen oder Segeln - Wassersportliebhaber kommen hier voll auf ihre Kosten.',
                'filter_months' => [5, 6, 7, 8, 9]
            ],
            [
                'slug' => 'biking-reise',
                'image' => 'img/startpage/biking-urlaub.webp',
                'title' => 'Biking Abenteuer',
                'title_text' => 'Radreisen & Mountainbike',
                'description' => 'Du liebst es mit dem Fahrrad die Welt zu erkunden? Dann sind diese Reiseziele perfekt für dich!',
                'filter_months' => [4, 5, 6, 7, 8, 9]
            ],
            [
                'slug' => 'fishing-reise',
                'image' => 'img/startpage/angeln-urlaub.webp',
                'title' => 'Angeln & Fishing',
                'title_text' => 'Ruhe & Entspannung am Wasser',
                'description' => 'Angelfreunde finden hier die besten Spots für einen entspannten Angelurlaub.',
                'filter_months' => [3, 4, 5, 6, 7, 8, 9]
            ]
        ];

        // Einfügen in die Datenbank
        foreach ($travelTypes as $index => $type) {
            // Prüfen, ob der Eintrag existiert
            $exists = DB::table('mod_quick_filter_items')->where('slug', $type['slug'])->exists();
            if (!$exists) {
                DB::table('mod_quick_filter_items')->insert([
                    'slug' => $type['slug'],
                    'thumbnail' => $type['image'],
                    'title' => $type['title'],
                    'title_text' => $type['title_text'],
                    'content' => $type['description'],
                    'filter_months' => json_encode($type['filter_months']),
                    'status' => 1,
                    'sort_order' => $index + 1,
                 //   $urlaubTypeMap[$type['slug']] => true, // Automatische Kategorie-Zuweisung
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
