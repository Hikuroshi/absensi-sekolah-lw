<?php

namespace App\Http\Controllers\Imports;

use App\Http\Controllers\Controller;
use App\Imports\SubjectsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SubjectImportController extends Controller
{
    public function index()
    {
        return view('dashboard.import.subject', [
            'title' => 'Import mata pelajaran'
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('file');

            Excel::import(new SubjectsImport, $file);

            return redirect()->back()->with('success', 'Data mata pelajaran berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
