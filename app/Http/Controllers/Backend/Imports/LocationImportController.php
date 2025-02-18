<?php

namespace App\Http\Controllers\Backend\Imports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\LocationImportService;

class LocationImportController extends Controller
{
    public function import(Request $request, LocationImportService $locationImportService): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('excel_file');
        $skipImages = $request->boolean('skip_images');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Invalid file upload.');
        }

        try {
            $filePath = $file->getRealPath();
            $result = $locationImportService->import($filePath, $skipImages);

            if (!$result) {
                return redirect()->back()->with('error', 'Failed to import locations.');
            }

            return redirect()->back()->with('success', 'Locations imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }
}
