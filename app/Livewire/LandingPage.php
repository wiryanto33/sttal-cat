<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LandingHero;
use App\Models\LandingStat;
use App\Models\LandingGallery;

class LandingPage extends Component
{
    public function render()
    {
        // 1. Ambil Banner Aktif (Ambil yang terakhir diupload/dibuat)
        $hero = LandingHero::where('is_active', true)->latest()->first();

        // 2. Ambil Statistik (Urutkan sesuai sort_order)
        $stats = LandingStat::orderBy('sort_order', 'asc')->get();

        // 3. Ambil Galeri (Ambil 6 foto terbaru)
        $galleries = LandingGallery::where('is_active', true)
            ->latest('event_date')
            ->take(6)
            ->get();

        return view('livewire.landing-page', [
            'hero' => $hero,
            'stats' => $stats,
            'galleries' => $galleries,
        ]);
    }
}
