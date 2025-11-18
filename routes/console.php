<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\Process\Process;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('print:test {--quantity=1} {--dry-run} {--file=} {--no-paper}', function () {
    $quantity = (int)($this->option('quantity') ?? 1);
    $dryRun = (bool) $this->option('dry-run');
    $inputFile = $this->option('file');
    $noPaper = (bool) $this->option('no-paper');

    if (PHP_OS_FAMILY !== 'Windows') {
        $this->warn('This test is designed for Windows (SumatraPDF). You can still generate the test PDF locally.');
    }

    // Read printer configuration
    $configPath = base_path('printer_config.json');
    if (!file_exists($configPath)) {
        $this->error('printer_config.json not found at: ' . $configPath);
        return 1;
    }
    $cfg = json_decode(file_get_contents($configPath), true) ?: [];
    $printerName = $cfg['windows'] ?? null;
    $sumatraPath = $cfg['windows_sumatra_path'] ?? null;

    if (!$printerName) {
        $this->error('Missing "windows" printer name in printer_config.json');
        return 1;
    }

    // Resolve SumatraPDF path if not provided
    if (!$sumatraPath) {
        $candidates = [
            'C:\\Program Files\\SumatraPDF\\SumatraPDF.exe',
            'C:\\Program Files (x86)\\SumatraPDF\\SumatraPDF.exe',
        ];
        foreach ($candidates as $cand) {
            if (file_exists($cand)) {
                $sumatraPath = $cand;
                break;
            }
        }
        if (!$sumatraPath) {
            // Fallback to default location; may still work if in PATH
            $sumatraPath = 'C:\\Program Files\\SumatraPDF\\SumatraPDF.exe';
        }
    }

    // Prepare test PDF
    $tempDir = storage_path('app/temp');
    if (!is_dir($tempDir)) {
        @mkdir($tempDir, 0755, true);
    }

    $pdfPath = $inputFile ?: ($tempDir . DIRECTORY_SEPARATOR . 'print_test.pdf');
    if (!$inputFile) {
        // Create a tiny test PDF roughly matching the label size
        $pdf = Pdf::loadHTML('<html><body style="font-family: sans-serif; font-size:9pt;">'
            . '<div><strong>POS Label Test</strong></div>'
            . '<div>' . now()->format('Y-m-d H:i:s') . '</div>'
            . '<div>Qty: ' . $quantity . '</div>'
            . '</body></html>');

        $width = 36.5 * 2.83465;  // ~103.46pt
        $height = 25 * 2.83465;   // ~70.87pt
        $pdf->setPaper([0, 0, $width, $height], 'landscape');
        $pdf->save($pdfPath);
        $this->info('Generated test PDF at: ' . $pdfPath);
    } else {
        if (!file_exists($pdfPath)) {
            $this->error('Provided --file does not exist: ' . $pdfPath);
            return 1;
        }
    }

    // Build Sumatra commands
    $quotedSumatra = '"' . $sumatraPath . '"';
    $quotedPrinter = '"' . $printerName . '"';
    $quotedFile = '"' . $pdfPath . '"';

    $base = $quotedSumatra . ' -silent -exit-on-print -print-to ' . $quotedPrinter;
    $settingsWithPaper = ' -print-settings ' . '"' . $quantity
        . ($noPaper ? '' : ',paper=Custom.36.5x25mm')
        . ',fit=NoScaling,offset-x=0,offset-y=0"';
    $settingsNoPaper = ' -print-settings ' . '"' . $quantity
        . ',fit=NoScaling,offset-x=0,offset-y=0"';

    $command1 = $base . $settingsWithPaper . ' ' . $quotedFile;
    $command2 = $base . $settingsNoPaper . ' ' . $quotedFile;

    $this->line('Attempt 1 command: ' . $command1);
    if ($dryRun) {
        $this->info('Dry-run enabled. Not executing print commands.');
        return 0;
    }

    // Execute attempt 1
    $proc1 = Process::fromShellCommandline($command1);
    $proc1->setTimeout(120);
    $proc1->run();

    if ($proc1->isSuccessful()) {
        $this->info('Printed successfully with custom paper setting.');
        $out = trim($proc1->getOutput());
        if ($out) $this->line($out);
        return 0;
    }

    $err1 = trim($proc1->getErrorOutput() . ' ' . $proc1->getOutput());
    $this->warn('Attempt 1 failed: ' . $err1);
    $this->line('Attempt 2 command: ' . $command2);

    // Execute attempt 2
    $proc2 = Process::fromShellCommandline($command2);
    $proc2->setTimeout(120);
    $proc2->run();

    if ($proc2->isSuccessful()) {
        $this->info('Printed successfully without custom paper setting.');
        $out2 = trim($proc2->getOutput());
        if ($out2) $this->line($out2);
        return 0;
    }

    $err2 = trim($proc2->getErrorOutput() . ' ' . $proc2->getOutput());
    $this->error('Attempt 2 failed: ' . $err2);
    return 1;
})->purpose('Test Windows label printing via SumatraPDF using the current printer_config.json');
