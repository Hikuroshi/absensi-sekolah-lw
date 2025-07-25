<?php

namespace App\Http\Controllers\Imports;

use App\Http\Controllers\Controller;
use App\Imports\ScheduleImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ScheduleImportController extends Controller
{
    public function index()
    {
        return view('dashboard.import.schedule', [
            'title' => 'Import jadwal'
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('file');

            Excel::import(new ScheduleImport, $file);

            return redirect()->back()->with('success', 'Data jadwal berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
