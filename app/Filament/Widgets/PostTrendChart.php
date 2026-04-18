<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Post;

class PostTrendChart extends ChartWidget
{
    protected ?string $heading = 'Grafik Tawaran Barter (Bulan Ini)';

    // Urutan ke-2 di bawah kartu statistik
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Untuk simulasi portofolio, kita gunakan angka acak yang terlihat naik/turun secara natural.
        // Nanti saat data sudah ribuan, kita bisa pakai query dari Post::whereMonth(...)
        return [
            'datasets' => [
                [
                    'label' => 'Tawaran Baru',
                    'data' => [12, 19, 15, 25, 22, 30, 45], // Data dummy
                    'borderColor' => '#3b82f6', // Warna biru Tailwind
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)', // Biru transparan
                ],
            ],
            'labels' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
