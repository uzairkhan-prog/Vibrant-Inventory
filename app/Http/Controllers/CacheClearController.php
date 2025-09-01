<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CacheClearController extends Controller
{
    /**
     * Clear all caches
     */
    public function clear()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return redirect()->back()->with('cache-clear-success', 'Cache cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('cache-clear-error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
}
