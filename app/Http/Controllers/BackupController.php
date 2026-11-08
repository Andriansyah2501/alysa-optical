<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    protected $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        if (!File::isDirectory($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0775, true, true);
        }
    }

    /**
     * Tampilkan halaman Pusat Backup & Riwayat Cadangan
     */
    public function index(Request $request)
    {
        $files = File::files($this->backupPath);
        $backups = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $extension = $file->getExtension();
            
            if (!in_array($extension, ['sql', 'gz', 'zip'])) {
                continue;
            }

            $sizeBytes = $file->getSize();
            $lastModified = Carbon::createFromTimestamp($file->getMTime());

            $type = 'Full System';
            if (str_contains($filename, '_db_')) {
                $type = 'Database Saja';
            } elseif (str_contains($filename, '_app_')) {
                $type = 'Source Code Saja';
            }

            $scope = 'Manual / Instan';
            if (str_contains($filename, 'daily_')) {
                $scope = 'Harian (Daily)';
            } elseif (str_contains($filename, 'monthly_')) {
                $scope = 'Bulanan (Monthly)';
            }

            $backups[] = [
                'filename' => $filename,
                'path' => $file->getPathname(),
                'size' => $this->formatSizeUnits($sizeBytes),
                'size_raw' => $sizeBytes,
                'type' => $type,
                'scope' => $scope,
                'created_at' => $lastModified,
            ];
        }

        usort($backups, function ($a, $b) {
            return $b['created_at']->timestamp <=> $a['created_at']->timestamp;
        });

        $dbSize = $this->getDatabaseSize();

        return view('admin.backups.index', compact('backups', 'dbSize'));
    }

    /**
     * Eksekusi Pembuatan Backup (Pilihan: Lengkap / DB Saja / App Saja, Scope: Instan / Harian / Bulanan)
     */
    public function create(Request $request)
    {
        $type = $request->input('type', 'full');
        $scope = $request->input('scope', 'manual');
        
        $dateStr = date('Ymd_His');
        $prefix = ($scope === 'daily') ? 'daily_' : (($scope === 'monthly') ? 'monthly_' . date('Y_m') . '_' : 'manual_');

        $dbName = config('database.connections.mysql.database', 'alysa_glasses');
        $dbHost = config('database.connections.mysql.host', '127.0.0.1');
        $dbPort = config('database.connections.mysql.port', '3306');
        $dbUser = config('database.connections.mysql.username', 'alysa_user');
        $dbPass = config('database.connections.mysql.password', '');

        $cnfFile = tempnam(sys_get_temp_dir(), 'mycnf_');
        $cnfContent = "[client]\nhost = {$dbHost}\nport = {$dbPort}\nuser = {$dbUser}\npassword = \"{$dbPass}\"\n";
        file_put_contents($cnfFile, $cnfContent);
        chmod($cnfFile, 0600);

        try {
            if ($type === 'db_only') {
                $filename = "alysa_glasses_db_{$prefix}{$dateStr}.sql";
                $targetPath = $this->backupPath . '/' . $filename;
                
                $cmd = "mysqldump --defaults-extra-file={$cnfFile} {$dbName} > {$targetPath} 2>&1";
                exec($cmd, $output, $returnCode);

                if ($returnCode !== 0) {
                    return redirect()->back()->with('error', 'Gagal membuat dump database: ' . implode(' ', $output));
                }

                $msg = "Backup Database ({$filename}) berhasil dibuat!";
            } elseif ($type === 'app_only') {
                $filename = "alysa_glasses_app_{$prefix}{$dateStr}.tar.gz";
                $targetPath = $this->backupPath . '/' . $filename;

                $cmd = "tar --exclude='storage/logs/*.log' --exclude='storage/framework/cache/data/*' --exclude='storage/framework/sessions/*' --exclude='storage/framework/views/*' --exclude='storage/app/backups/*' --exclude='.git' -czf {$targetPath} -C /opt alysa-glasses";
                exec($cmd, $output, $returnCode);

                if ($returnCode !== 0) {
                    return redirect()->back()->with('error', 'Gagal mengompresi source code aplikasi.');
                }

                $msg = "Backup Source Code ({$filename}) berhasil dibuat!";
            } else {
                $tempDb = "/tmp/alysa_db_{$dateStr}.sql";
                exec("mysqldump --defaults-extra-file={$cnfFile} {$dbName} > {$tempDb} 2>&1");

                $filename = "BACKUP_ALYSA_FULL_{$prefix}{$dateStr}.tar.gz";
                $targetPath = $this->backupPath . '/' . $filename;

                $cmd = "tar --exclude='storage/logs/*.log' --exclude='storage/framework/cache/data/*' --exclude='storage/framework/sessions/*' --exclude='storage/framework/views/*' --exclude='storage/app/backups/*' --exclude='.git' -czf {$targetPath} -C /opt alysa-glasses -C /tmp " . basename($tempDb);
                exec($cmd, $output, $returnCode);
                @unlink($tempDb);

                if ($returnCode !== 0) {
                    return redirect()->back()->with('error', 'Gagal membuat Full Backup aplikasi.');
                }

                $msg = "Backup Lengkap Aplikasi & Database ({$filename}) berhasil dibuat!";
            }

            @unlink($cnfFile);
            return redirect()->route('admin.backups.index')->with('success', $msg);
        } catch (\Throwable $e) {
            @unlink($cnfFile);
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Download Berkas Backup
     */
    public function download($filename)
    {
        $filePath = $this->backupPath . '/' . basename($filename);

        if (!File::exists($filePath)) {
            return redirect()->back()->with('error', 'Berkas backup tidak ditemukan di server.');
        }

        return response()->download($filePath);
    }

    /**
     * Hapus Berkas Backup
     */
    public function destroy($filename)
    {
        $filePath = $this->backupPath . '/' . basename($filename);

        if (File::exists($filePath)) {
            File::delete($filePath);
            return redirect()->back()->with('success', "Berkas backup {$filename} berhasil dihapus.");
        }

        return redirect()->back()->with('error', 'Berkas tidak ditemukan.');
    }

    private function getDatabaseSize()
    {
        try {
            $dbName = config('database.connections.mysql.database', 'alysa_glasses');
            $res = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
            return ($res[0]->size_mb ?? '0') . ' MB';
        } catch (\Throwable $e) {
            return 'N/A';
        }
    }

    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }
}
