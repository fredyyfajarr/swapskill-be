<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('needed_skill_id')
                    ->relationship('neededSkill', 'name')
                    ->required(),
                Select::make('offered_skill_id')
                    ->relationship('offeredSkill', 'name')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(['open' => 'Open', 'in_progress' => 'In progress', 'completed' => 'Completed'])
                    ->default('open')
                    ->required(),
            ]);
    }
}
