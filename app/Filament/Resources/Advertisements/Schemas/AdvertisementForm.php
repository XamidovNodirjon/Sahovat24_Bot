<?php

namespace App\Filament\Resources\Advertisements\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AdvertisementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image')
                    ->image(),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('button_text'),
                TextInput::make('button_url')
                    ->url(),
                TextInput::make('sent_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
