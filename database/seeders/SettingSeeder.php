<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'NPL'],
            ['key' => 'logo', 'value' => 'path/to/default/logo.png'],
            ['key' => 'contact_email', 'value' => 'info@npl.com'],
            ['key' => 'contact_phone', 'value' => '+91 9876543210'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
