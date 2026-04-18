<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('nim')
                    ->required(),
                TextInput::make('whatsapp_number')
                    ->required(),
                TextInput::make('ktm_path'),
                Select::make('role')
                    ->options(['student' => 'Student', 'admin' => 'Admin'])
                    ->default('student')
                    ->required(),
                Toggle::make('is_verified')
                    ->required(),
            ]);
    }
}
