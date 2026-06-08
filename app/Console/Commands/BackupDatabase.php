<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    protected $signature = 'steman:backup';
    protected $description = 'Perform a secure database backup to local storage.';

    public function handle()
    {
        $this->info('Starting Database Backup...');

        $filename = 'steman-backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
        $path     = storage_path("app/backups/{$filename}");

        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        // ─── FIX CRITICAL: Jangan gunakan -p{password} langsung di command ───
        // Password yang di-pass sebagai argumen CLI terlihat oleh `ps aux` dan log shell.
        // Gunakan file konfigurasi sementara (my.cnf) yang di-chmod 600 agar aman.
        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $database = config('database.connections.mysql.database');

        // Buat file konfigurasi sementara yang hanya bisa dibaca proses ini
        $myCnfPath = tempnam(sys_get_temp_dir(), 'db_backup_');
        chmod($myCnfPath, 0600);
        file_put_contents($myCnfPath, implode("\n", [
            '[client]',
            "host={$host}",
            "port={$port}",
            "user={$username}",
            "password={$password}",
        ]));

        try {
            $escapedDb   = escapeshellarg($database);
            $escapedPath = escapeshellarg($path);
            $escapedConf = escapeshellarg($myCnfPath);

            $command = "mariadb-dump --defaults-extra-file={$escapedConf} --skip-ssl {$escapedDb} > {$escapedPath}";

            $output    = [];
            $returnVar = 0;
            exec($command . ' 2>&1', $output, $returnVar);

            if ($returnVar === 0) {
                $this->info("Backup successful: {$filename}");

                // Kompresi backup
                exec('gzip ' . escapeshellarg($path));

                // Hapus backup lebih dari 7 hari
                $files = glob(storage_path('app/backups/*.gz')) ?: [];
                $now   = time();
                foreach ($files as $file) {
                    if (is_file($file) && ($now - filemtime($file)) >= 7 * 24 * 60 * 60) {
                        unlink($file);
                    }
                }

                Log::info("System Pulse: Database backup selesai: {$filename}.gz");
            } else {
                // JANGAN log $output mentah jika mengandung password — dalam konfigurasi ini aman
                // karena password tidak ada di command string, tapi tetap sanitasi
                $errorMsg = implode("\n", array_map('strip_tags', $output));
                $this->error("Backup gagal.");
                Log::error("System Pulse: Database backup GAGAL. Kode exit: {$returnVar}. Output: {$errorMsg}");
            }

            return $returnVar === 0 ? Command::SUCCESS : Command::FAILURE;

        } finally {
            // Selalu hapus file konfigurasi sementara meski terjadi error
            if (file_exists($myCnfPath)) {
                unlink($myCnfPath);
            }
        }
    }
}
