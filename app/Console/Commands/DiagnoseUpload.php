<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DiagnoseUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:diagnose';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnostica problemas com upload de arquivos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Diagnóstico de Upload');
        $this->line('');

        // 1. Verificar configuração
        $this->info('1️⃣  CONFIGURAÇÃO');
        $this->line('   FILESYSTEM_DISK: ' . config('filesystems.default'));
        $this->line('   APP_URL: ' . config('app.url'));
        $this->line('   Storage Path: ' . storage_path('app'));
        $this->line('');

        // 2. Verificar discos
        $this->info('2️⃣  DISCOS DISPONÍVEIS');
        $disks = config('filesystems.disks');
        foreach ($disks as $name => $config) {
            $this->line("   • $name: {$config['driver']}");
        }
        $this->line('');

        // 3. Verificar permissões
        $this->info('3️⃣  PERMISSÕES');
        $paths = [
            'storage' => storage_path('app'),
            'storage/private' => storage_path('app/private'),
            'storage/public' => storage_path('app/public'),
            'public' => public_path(),
        ];

        foreach ($paths as $label => $path) {
            $exists = file_exists($path);
            $writable = is_writable($path);
            $perms = substr(sprintf('%o', fileperms($path)), -4);
            
            $status = '✓';
            if (!$exists) $status = '✗ NÃO EXISTE';
            elseif (!$writable) $status = '✗ NÃO GRAVÁVEL';

            $this->line("   $status $label: $path ($perms)");
        }
        $this->line('');

        // 4. Teste de escrita
        $this->info('4️⃣  TESTE DE ESCRITA');
        try {
            $testFile = 'test_' . time() . '.txt';
            Storage::disk('local')->put($testFile, 'teste');
            $this->line("   ✅ Arquivo criado: $testFile");
            
            Storage::disk('local')->delete($testFile);
            $this->line("   ✅ Arquivo deletado com sucesso");
        } catch (\Exception $e) {
            $this->error("   ✗ ERRO: " . $e->getMessage());
        }
        $this->line('');

        // 5. Listar pasta reports
        $this->info('5️⃣  CONTEÚDO DE storage/app/private/reports');
        try {
            $files = Storage::disk('local')->files('reports');
            if (empty($files)) {
                $this->line('   (pasta vazia ou não existe)');
            } else {
                foreach ($files as $file) {
                    $size = Storage::disk('local')->size($file);
                    $this->line("   • $file (" . human_filesize($size) . ")");
                }
            }
        } catch (\Exception $e) {
            $this->error("   ✗ Erro ao listar: " . $e->getMessage());
        }
        $this->line('');

        $this->info('✨ Diagnóstico concluído!');
    }
}

function human_filesize($bytes, $dec = 2)
{
    $size   = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}
