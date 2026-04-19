<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action; // <--- KITA PAKAI NAMESPACE INI
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->label('Email address')->searchable(),
                TextColumn::make('nim')->searchable(),
                TextColumn::make('whatsapp_number')->searchable(),
                IconColumn::make('is_verified')->label('Status')->boolean(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([ // <--- KEMBALI MENGGUNAKAN INI
                Action::make('acc_and_chat')
                    ->label('Acc & WA')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Mahasiswa Baru')
                    ->modalDescription(fn (Model $record) => "Pastikan foto KTM sesuai dengan nama '{$record->name}' dan NIM '{$record->nim}'. Klik 'Ya' untuk mengaktifkan akun dan mengirim pesan WhatsApp otomatis.")
                    ->modalSubmitActionLabel('Ya, Verifikasi & Buka WA')
                    ->hidden(fn (Model $record) => $record->is_verified)
                    ->action(function (Model $record) {
                        // 1. Update status
                        $record->update(['is_verified' => true]);

                        // 2. Format nomor WA
                        $phone = $record->whatsapp_number;
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }

                        // 3. Template Pesan WA
                        $message = "Halo *{$record->name}*! 👋\n\nSelamat, akun SwapSkill kamu sudah berhasil kami verifikasi. 🎉\n\nSekarang kamu sudah bisa login dan mulai memposting tawaran barter keahlian dengan teman-teman yang lain.\n\nSemangat berkarya dan bangun portofoliomu dari sekarang! 🚀";

                        // 4. Generate URL WA & Buka Tab Baru
                        $url = "https://wa.me/{$phone}?text=" . urlencode($message);
                        return redirect()->to($url);
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([ // <--- KEMBALI MENGGUNAKAN INI
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
