<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Post;
use App\Models\Bookmark;

class StatsOverview extends BaseWidget
{
    // Mengatur agar widget ini muncul paling atas
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Mahasiswa (User)', User::count())
                ->description('Peningkatan pendaftar bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Efek grafik kecil (sparkline)

            Stat::make('Tawaran Barter Aktif', Post::where('status', 'open')->count())
                ->description('Menunggu untuk di-match')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('warning')
                ->chart([2, 5, 3, 7, 5, 10, 8]),

            Stat::make('Total Interaksi (Bookmark)', Bookmark::count())
                ->description('Tawaran yang saling disimpan')
                ->descriptionIcon('heroicon-m-bookmark-square')
                ->color('info'),
        ];
    }
}
