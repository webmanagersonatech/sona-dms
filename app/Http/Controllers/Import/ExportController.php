<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Department;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use App\Exports\FilesExport;
use App\Exports\TransfersExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ImportExportController extends Controller
{
    public function index()
    {
        return view('import-export.index');
    }

    // Export methods
    public function exportUsers(Request $request)
    {
        $format = $request->get('format', 'excel');
        $filters = $request->except('format');

        if ($format === 'excel') {
            return Excel::download(new UsersExport($filters), 'users.xlsx');
        } elseif ($format === 'csv') {
            return Excel::download(new UsersExport($filters), 'users.csv', \Maatwebsite\Excel\Excel::CSV);
        } else {
            $users = User::with(['role', 'department'])->get();
            $pdf = Pdf::loadView('exports.users-pdf', compact('users'));
            return $pdf->download('users.pdf');
        }
    }

    public function exportFiles(Request $request)
    {
        $format = $request->get('format', 'excel');
        $filters = $request->except('format');

        if ($format === 'excel') {
            return Excel::download(new FilesExport($filters), 'files.xlsx');
        } elseif ($format === 'csv') {
            return Excel::download(new FilesExport($filters), 'files.csv', \Maatwebsite\Excel\Excel::CSV);
        } else {
            $files = File::with(['owner', 'department'])->get();
            $pdf = Pdf::loadView('exports.files-pdf', compact('files'));
            return $pdf->download('files.pdf');
        }
    }

    public function exportTransfers(Request $request)
    {
        $format = $request->get('format', 'excel');
        $filters = $request->except('format');

        if ($format === 'excel') {
            return Excel::download(new TransfersExport($filters), 'transfers.xlsx');
        } elseif ($format === 'csv') {
            return Excel::download(new TransfersExport($filters), 'transfers.csv', \Maatwebsite\Excel\Excel::CSV);
        } else {
            $transfers = Transfer::with(['sender', 'receiver'])->get();
            $pdf = Pdf::loadView('exports.transfers-pdf', compact('transfers'));
            return $pdf->download('transfers.pdf');
        }
    }

    // Import methods
    public function showImportForm($type)
    {
        return view('import-export.import', compact('type'));
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'import',
                'module' => 'users',
                'description' => 'Imported users from file',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('users.index')
                ->with('success', 'Users imported successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    public function downloadTemplate($type)
    {
        $headers = [
            'users' => ['name', 'email', 'password', 'role_id', 'department_id', 'phone', 'status'],
            'files' => ['name', 'description', 'department_id', 'tags'],
            'transfers' => ['receiver_name', 'receiver_email', 'purpose', 'expected_delivery_time'],
        ];

        if (!isset($headers[$type])) {
            abort(404);
        }

        $filename = $type . '_template.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fputcsv($handle, $headers[$type]);
        fputcsv($handle, $this->getSampleData($type));
        
        fclose($handle);
        exit;
    }

    private function getSampleData($type)
    {
        switch ($type) {
            case 'users':
                return ['John Doe', 'john@example.com', 'password123', '3', '1', '+1234567890', 'active'];
            case 'files':
                return ['Sample File', 'This is a sample file', '1', 'sample,test'];
            case 'transfers':
                return ['Jane Doe', 'jane@example.com', 'Document delivery', now()->addDays(3)->format('Y-m-d H:i:s')];
            default:
                return [];
        }
    }
}