<?php

namespace App\Http\Controllers\Backend\Imports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ContinentImportService; // Importiere den Service

class ContinentImportController extends Controller
{
    public function import(Request $request, ContinentImportService $continentImportService): \Illuminate\Http\RedirectResponse
    {
        // Validierung der Datei
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('excel_file');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Invalid file upload.');
        }

        try {
            // Verarbeite die Datei
            $filePath = $file->getRealPath();
            $result = $continentImportService->import($filePath);

            if (!$result) {
                return redirect()->back()->with('error', 'Failed to import continents.');
            }

            return redirect()->back()->with('success', 'Continents imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }
}
