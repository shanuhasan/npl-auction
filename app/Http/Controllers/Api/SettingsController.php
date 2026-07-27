<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Get application settings and version info.
     */
    public function index()
    {
        $baseUrl = url('/');
        $logo = setting('logo');
        
        return response()->json([
            'success' => true,
            'data' => [
                'app_name' => setting('app_name', config('app.name')),
                'season' => setting('season', date('Y')),
                'logo_url' => $logo ? $baseUrl . '/storage/' . $logo : null,
                'contact_email' => setting('contact_email'),
                'contact_phone' => setting('contact_phone'),
                'social_links' => [
                    'facebook' => setting('facebook'),
                    'instagram' => setting('instagram'),
                    'twitter' => setting('twitter'),
                    'youtube' => setting('youtube'),
                ],
                'version' => '1.0.0', // App version checking
                'force_update' => false,
                'maintenance_mode' => app()->isDownForMaintenance(),
                'base_url' => $baseUrl,
            ]
        ]);
    }
}
