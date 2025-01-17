<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    public function download()
    {
        // Define the filename and path
        $filename = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/' . $filename);

        // Get database configuration
        $dbHost = env('DB_HOST');
        $dbPort = env('DB_PORT');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        // Create the database dump command
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbName),
            escapeshellarg($path)
        );

        // Execute the command
        $result = null;
        $output = null;
        exec($command, $output, $result);

        // Check if the dump was created successfully
        if ($result === 0) {
            // Return the file as a download response
            return response()->download($path)->deleteFileAfterSend(true);
        } else {
            // Set an error message if the dump failed
            Session::flash('error', 'Failed to create database backup.');
            return redirect()->route('dashboard');
        }
    }
}
