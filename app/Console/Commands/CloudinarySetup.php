<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CloudinarySetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudinary:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup dan test koneksi Cloudinary untuk upload image';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking Cloudinary Configuration...');
        $this->newLine();

        // Check environment variables
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        $cloudinaryUrl = env('CLOUDINARY_URL');

        $this->info('1️⃣ Environment Variables:');
        $this->line('   CLOUDINARY_CLOUD_NAME: ' . ($cloudName ? "✅ {$cloudName}" : '❌ Not set'));
        $this->line('   CLOUDINARY_API_KEY: ' . ($apiKey ? '✅ Set' : '❌ Not set'));
        $this->line('   CLOUDINARY_API_SECRET: ' . ($apiSecret ? '✅ Set' : '❌ Not set'));
        $this->line('   CLOUDINARY_URL: ' . ($cloudinaryUrl ? '✅ Set' : '❌ Not set'));
        $this->newLine();

        if (!$cloudinaryUrl) {
            $this->error('❌ CLOUDINARY_URL is not configured!');
            $this->newLine();
            $this->warn('📝 Untuk memperbaiki, tambahkan baris ini ke file .env:');
            $this->line("CLOUDINARY_URL=cloudinary://{$apiKey}:{$apiSecret}@{$cloudName}");
            $this->newLine();
            $this->info('Setelah update .env, jalankan:');
            $this->line('php artisan config:clear');
            $this->line('php artisan cloudinary:setup');
            return 1;
        }

        // Test disk configuration
        $this->info('2️⃣ Testing Disk Configuration...');
        try {
            $config = config('filesystems.disks.cloudinary');
            $this->line('   Config loaded: ✅');
            $this->line('   Driver: ' . ($config['driver'] ?? 'not set'));
            $this->line('   Cloud Name: ' . ($config['cloud_name'] ?? 'not set'));
        } catch (\Exception $e) {
            $this->error('   ❌ Error: ' . $e->getMessage());
            return 1;
        }
        $this->newLine();

        // Test storage disk instantiation
        $this->info('3️⃣ Testing Storage Disk...');
        try {
            $disk = Storage::disk('cloudinary');
            $this->line('   Disk created: ✅');
            $this->line('   Class: ' . get_class($disk));
        } catch (\Exception $e) {
            $this->error('   ❌ Error: ' . $e->getMessage());
            return 1;
        }
        $this->newLine();

        // Test upload
        if ($this->confirm('Test upload file ke Cloudinary?', true)) {
            $this->info('4️⃣ Testing Upload...');
            try {
                // Create a temporary test file
                $testContent = 'Test upload at ' . now()->toDateTimeString();
                $tempFile = tmpfile();
                $tempPath = stream_get_meta_data($tempFile)['uri'];
                fwrite($tempFile, $testContent);
                fseek($tempFile, 0);
                
                $testFileName = 'test-sangkara-' . time() . '.txt';
                
                $this->line('   Uploading test file...');
                $path = Storage::disk('cloudinary')->putFileAs('tests', $tempPath, $testFileName);
                $this->line('   Upload successful: ✅');
                $this->line('   Path: ' . $path);
                
                // Get URL
                $url = Storage::disk('cloudinary')->url($path);
                $this->line('   URL: ' . $url);
                
                // Clean up
                $this->line('   Cleaning up...');
                Storage::disk('cloudinary')->delete($path);
                fclose($tempFile);
                $this->line('   Cleanup done: ✅');
                
            } catch (\Exception $e) {
                $this->error('   ❌ Upload Error: ' . $e->getMessage());
                return 1;
            }
            $this->newLine();
        }

        $this->info('✅ Cloudinary configured successfully!');
        $this->line('🎉 Avatar upload akan otomatis tersimpan di Cloudinary.');
        
        return 0;
    }
}
