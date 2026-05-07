<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SetupStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configura e verifica o setup de storage para uploads';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Iniciando setup de storage...');

        // 1. Criar pastas necessárias
        $directories = [
            'reports',
            'temp',
            'backups',
        ];

        foreach ($directories as $dir) {
            $path = storage_path("app/private/$dir");
            
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0755, true);
                $this->info("✅ Pasta criada: storage/app/private/$dir");
            } else {
                $this->line("✓ Pasta já existe: storage/app/private/$dir");
            }
        }

        // 2. Criar symlink se necessário
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (is_link($link)) {
            $this->info('✅ Symlink já existe: public/storage → storage/app/public');
        } else {
            try {
                symlink($target, $link);
                $this->info('✅ Symlink criado: public/storage → storage/app/public');
            } catch (\Exception $e) {
                $this->warn('⚠️  Não foi possível criar symlink: ' . $e->getMessage());
                $this->line('   Execute: php artisan storage:link');
            }
        }

        // 3. Verificar permissões
        $this->info('');
        $this->info('📁 Verificando permissões...');
        
        $storagePath = storage_path('app');
        $isWritable = is_writable($storagePath);
        
        if ($isWritable) {
            $this->info("✅ Storage é gravável: $storagePath");
        } else {
            $this->warn("⚠️  Storage NÃO é gravável: $storagePath");
            $this->line('   Execute: chmod -R 755 storage');
        }

        // 4. Resumo
        $this->info('');
        $this->info('✨ Setup concluído!');
        $this->line('Configuração final:');
        $this->line('  • FILESYSTEM_DISK: ' . config('filesystems.default'));
        $this->line('  • Storage Path: ' . storage_path('app'));
        $this->line('  • Public URL: ' . config('filesystems.disks.public.url'));
    }
}
