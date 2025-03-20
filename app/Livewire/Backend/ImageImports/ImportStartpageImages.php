<?php

namespace App\Livewire\Backend\ImageImports;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class ImportStartpageImages extends Component
{
    public $message = '';

    public function importImages()
    {
        $directory = public_path('img/startpage');
        $files = collect(File::files($directory));

        // Filtere nur Dateien mit 'beste_reisezeit'
        $filteredFiles = $files->filter(function ($file) {
            return str_contains($file->getFilename(), 'beste_reisezeit');
        });

        // Gruppiere nach Basename (ohne Suffix und Extension)
        $groupedFiles = $filteredFiles->groupBy(function ($file) {
            return preg_replace('/(_b|_s)?\\.[^.]+$/', '', $file->getFilename());
        });

        // Alle bestehenden Einträge abrufen
        $existingEntries = DB::table('header_contents')->pluck('id')->toArray();

        foreach ($groupedFiles as $basename => $filesGroup) {
            // Hole die bevorzugte Datei für bg_img
            $bgImgFile = $filesGroup->first(function ($file) {
                return str_contains($file->getFilename(), '_b') && $file->getExtension() === 'webp';
            }) ?? $filesGroup->first(function ($file) {
                return str_contains($file->getFilename(), '_b') && $file->getExtension() === 'jpg';
            });

            // Hole die bevorzugte Datei für main_img
            $mainImgFile = $filesGroup->first(function ($file) {
                return str_contains($file->getFilename(), '_s') && $file->getExtension() === 'webp';
            }) ?? $filesGroup->first(function ($file) {
                return str_contains($file->getFilename(), '_s') && $file->getExtension() === 'jpg';
            });

            // Überspringe, wenn keine relevanten Dateien vorhanden sind
            if (!$bgImgFile || !$mainImgFile) {
                $this->message .= "Übersprungen: {$basename} (fehlende Dateien)<br>";
                continue;
            }

            $bgImgPath = 'img/startpage/' . $bgImgFile->getFilename();
            $mainImgPath = 'img/startpage/' . $mainImgFile->getFilename();

            // Entscheide, ob ein neuer Eintrag hinzugefügt oder ein alter aktualisiert wird
            $entryId = array_shift($existingEntries);

            if ($entryId) {
                // Update vorhandener Eintrag
                DB::table('header_contents')
                    ->where('id', $entryId)
                    ->update([
                        'bg_img' => $bgImgPath,
                        'main_img' => $mainImgPath,
                        'updated_at' => now(),
                    ]);
                $this->message .= "Aktualisiert: {$basename}<br>";
            } else {
                // Neuer Eintrag
                DB::table('header_contents')->insert([
                    'bg_img' => $bgImgPath,
                    'main_img' => $mainImgPath,
                    'main_text' => '<h1>Neuer Titel</h1>',
                    'title' => 'Neuer Eintrag',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->message .= "Hinzugefügt: {$basename}<br>";
            }
        }

        // Alte Einträge löschen
        if (!empty($existingEntries)) {
            DB::table('header_contents')->whereIn('id', $existingEntries)->delete();
            $this->message .= "Gelöscht: " . implode(', ', $existingEntries) . "<br>";
        }

        $this->dispatch('importCompleted');
    }



    public function importImagesAndText()
    {
        $directory = public_path('img/startpage');
        $files = collect(File::files($directory));

        // Filtere nur Dateien mit 'beste_reisezeit'
        $filteredFiles = $files->filter(function ($file) {
            return str_contains($file->getFilename(), 'beste_reisezeit');
        });

        // Gruppiere nach Basename (ohne Suffix und Extension)
        $groupedFiles = $filteredFiles->groupBy(function ($file) {
            return preg_replace('/(_b|_s)?\\.[^.]+$/', '', $file->getFilename());
        });

        // Alle bestehenden Einträge abrufen
        $existingEntries = DB::table('header_contents')->pluck('id')->toArray();

        foreach ($groupedFiles as $basename => $filesGroup) {
            // Hole die bevorzugte Datei für bg_img
            $bgImgFile = $filesGroup->first(function ($file) {
                return str_contains($file->getFilename(), '_b') && $file->getExtension() === 'webp';
            }) ?? $filesGroup->first(function ($file) {
                return str_contains($file->getFilename(), '_b') && $file->getExtension() === 'jpg';
            });

            // Hole die bevorzugte Datei für main_img
            $mainImgFile = $filesGroup->first(function ($file) {
                return str_contains($file->getFilename(), '_s') && $file->getExtension() === 'webp';
            }) ?? $filesGroup->first(function ($file) {
                return str_contains($file->getFilename(), '_s') && $file->getExtension() === 'jpg';
            });

            // Hole den Text aus der .txt-Datei
            $textFile = $filesGroup->first(function ($file) {
                return $file->getExtension() === 'txt';
            });

            $textContent = $textFile ? File::get($textFile->getPathname()) : '<h1>Neuer Titel</h1>';

            // Überspringe, wenn keine relevanten Dateien vorhanden sind
            if (!$bgImgFile || !$mainImgFile) {
                $this->message .= "Übersprungen: {$basename} (fehlende Dateien)<br>";
                continue;
            }

            $bgImgPath = 'img/startpage/' . $bgImgFile->getFilename();
            $mainImgPath = 'img/startpage/' . $mainImgFile->getFilename();

            // Entscheide, ob ein neuer Eintrag hinzugefügt oder ein alter aktualisiert wird
            $entryId = array_shift($existingEntries);

            if ($entryId) {
                // Update vorhandener Eintrag
                DB::table('header_contents')
                    ->where('id', $entryId)
                    ->update([
                        'bg_img' => $bgImgPath,
                        'main_img' => $mainImgPath,
                        'main_text' => $textContent,
                        'updated_at' => now(),
                    ]);
                $this->message .= "Aktualisiert: {$basename}<br>";
            } else {
                // Neuer Eintrag
                DB::table('header_contents')->insert([
                    'bg_img' => $bgImgPath,
                    'main_img' => $mainImgPath,
                    'main_text' => $textContent,
                    'title' => 'Neuer Eintrag',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->message .= "Hinzugefügt: {$basename}<br>";
            }
        }

        // Alte Einträge löschen
        if (!empty($existingEntries)) {
            DB::table('header_contents')->whereIn('id', $existingEntries)->delete();
            $this->message .= "Gelöscht: " . implode(', ', $existingEntries) . "<br>";
        }

        $this->dispatch('importCompleted');
    }




    public function render()
    {
        return view('livewire.backend.image-imports.import-startpage-images')
        ->layout('raadmin.layout.master');
    }
}
