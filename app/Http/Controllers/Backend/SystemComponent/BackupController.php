<?php

namespace App\Http\Controllers\Backend\SystemComponent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\BackupDestination\BackupDestination;

class BackupController extends Controller
{
    public function index()
    {
        $backupDestination = BackupDestination::create('backups', config('backup.backup.name'));
        $backups = $backupDestination->backups()->map(function ($backup) {
            return [
                'path' => $backup->path(),
                'date' => $backup->date()->toDateTimeString(),
                'size' => $this->humanReadableSize($backup->sizeInBytes()),
            ];
        })->toArray();

        return view('backend.admin.backup.index', compact('backups'));
    }

    public function run()
    {
        Artisan::call('backup:run');
        return redirect()->route('verwaltung.seo-table-manager.backup.index')->with('success', 'Backup wurde erfolgreich gestartet.');
    }

    public function download($path)
    {

        $exists = Storage::disk('backups')->exists($path);
        $fullPath = Storage::disk('backups')->path($path);

        if ($exists) {
            $fileName = basename($path);
            return response()->download($fullPath, $fileName);
        }
        return redirect()->route('verwaltung.seo-table-manager.backup.index')
            ->with('error', "Backup-Datei nicht gefunden: {$path}");
    }

    public function delete($path)
    {
        if (Storage::disk('backups')->exists($path)) {
            Storage::disk('backups')->delete($path);
            return redirect()->route('verwaltung.seo-table-manager.backup.index')->with('success', 'Backup wurde erfolgreich gelöscht.');
        }
        return redirect()->route('verwaltung.seo-table-manager.backup.index')->with('error', 'Backup-Datei nicht gefunden.');
    }

    private function humanReadableSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = max($size, 0);
        $pow = floor(($size ? log($size) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $size /= pow(1024, $pow);
        return round($size, 2) . ' ' . $units[$pow];
    }
}
