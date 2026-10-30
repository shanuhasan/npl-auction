<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Livewire\Livewire;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Gallery;

echo "1. Checking initial galleries count...\n";
$beforeCount = Gallery::count();
echo "Initial count: {$beforeCount}\n";

echo "2. Testing Livewire Admin Gallery component...\n";
Storage::fake('public');

$file1 = UploadedFile::fake()->image('photo1.jpg', 600, 400);
$file2 = UploadedFile::fake()->image('photo2.png', 400, 800);

$component = Livewire::test(\App\Livewire\Admin\Gallery\Index::class)
    ->call('openBulkModal')
    ->assertSet('isBulkModalOpen', true)
    ->set('bulk_photos', [$file1, $file2])
    ->call('saveBulkPhotos')
    ->assertHasNoErrors()
    ->assertSet('isBulkModalOpen', false);

$afterCount = Gallery::count();
echo "After count: {$afterCount}\n";
echo "Difference: " . ($afterCount - $beforeCount) . "\n";

// Clean up fake records if any were created
$newItems = Gallery::whereNull('title')->where('type', 'photo')->latest()->take(2)->get();
foreach ($newItems as $item) {
    if (str_starts_with($item->file_path, 'gallery/')) {
        $item->delete();
    }
}
echo "Cleaned up test records.\n";
echo "LIVEWIRE TEST COMPLETED SUCCESSFULLY!\n";

