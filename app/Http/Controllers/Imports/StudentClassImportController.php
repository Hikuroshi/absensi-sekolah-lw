<?php

namespace App\Http\Controllers\Imports;

use App\Http\Controllers\Controller;
use App\Imports\StudentClassImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentClassImportController extends Controller
{
    public function index()
    {
        return view('dashboard.import.student', [
            'title' => 'Import siswa & kelas'
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('file');

            Excel::import(new StudentClassImport, $file);

            return redirect()->back()->with('success', 'Data siswa & kelas berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
