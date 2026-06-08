<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EventTheme;

class SetupEventThemes extends Command
{
    protected $signature   = 'event-themes:setup {--fresh : Hapus semua tema sebelum mengisi ulang}';
    protected $description = 'Migrasi dan isi data tema event otomatis (HUT RI, STEMAN, Hari Besar Nasional, dll.)';

    public function handle(): int
    {
        $this->info('🎨 Menyiapkan Tema Event Otomatis STEMAN...');

        // Run migration if table doesn't exist
        if (! \Illuminate\Support\Facades\Schema::hasTable('event_themes')) {
            $this->info('📦 Menjalankan migrasi tabel event_themes...');
            \Illuminate\Support\Facades\Artisan::call('migrate', [
                '--path'  => 'database/migrations/2026_06_07_100000_create_event_themes_table.php',
                '--force' => true,
            ]);
            $this->info(\Illuminate\Support\Facades\Artisan::output());
        } else {
            $this->line('✅ Tabel event_themes sudah ada.');
        }

        if ($this->option('fresh')) {
            EventTheme::truncate();
            EventTheme::flushCache();
            $this->warn('⚠️  Semua tema lama dihapus.');
        }

        $this->info('🌱 Mengisi data tema event...');
        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--class' => 'EventThemeSeeder',
            '--force' => true,
        ]);
        $this->info(\Illuminate\Support\Facades\Artisan::output());

        // Display active theme
        EventTheme::flushCache();
        $active = EventTheme::getActive();

        $this->newLine();
        if ($active) {
            $this->info("🎉 Tema aktif saat ini: [{$active->name}] — {$active->banner_text}");
        } else {
            $this->line('ℹ️  Tidak ada tema event yang aktif hari ini (tampilan default STEMAN).');
        }

        $count = EventTheme::count();
        $this->info("✅ Total tema terdaftar: {$count} tema.");
        $this->newLine();

        return self::SUCCESS;
    }
}
