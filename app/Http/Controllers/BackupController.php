<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        // Define the full path to the mysqldump executable
        $mysqldumpPath = 'C:\xampp\mysql\bin\mysqldump.exe'; // Update this path as needed

        // Create the database dump command
        $command = sprintf(
            '%s --user=%s --password=%s --host=%s --port=%s %s > %s',
            escapeshellarg($mysqldumpPath),
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbName),
            escapeshellarg($path)
        );

        // Log the command for debugging
        Log::info('Executing command: ' . $command);

        // Execute the command
        $result = null;
        $output = null;
        exec($command . ' 2>&1', $output, $result);

        // Log the command output for debugging
        Log::error('Backup command output: ' . implode("\n", $output));

        // Check if the dump was created successfully
        if ($result === 0) {
            // Check if the file is not empty
            if (filesize($path) > 0) {
                // Return the file as a download response
                return response()->download($path)->deleteFileAfterSend(true);
            } else {
                Log::error('Backup file is empty.');
                Session::flash('error', 'Failed to create database backup. The backup file is empty.');
                return redirect()->route('dashboard');
            }
        } else {
            // Log the error details
            Log::error('Failed to execute mysqldump command.', ['result' => $result, 'output' => $output]);
            // Set an error message if the dump failed
            Session::flash('error', 'Failed to create database backup.');
            return redirect()->route('dashboard');
        }
    }
}