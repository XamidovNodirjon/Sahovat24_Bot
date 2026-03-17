<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password(),
                TextInput::make('telegram_id')
                    ->tel()
                    ->numeric(),
                TextInput::make('first_name'),
                TextInput::make('last_name'),
                TextInput::make('username'),
                TextInput::make('phone_number')
                    ->tel(),
                TextInput::make('language')
                    ->required()
                    ->default('uz'),
                TextInput::make('step')
                    ->required()
                    ->default('start'),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                TextInput::make('browse_category_id')
                    ->numeric(),
            ]);
    }
}
