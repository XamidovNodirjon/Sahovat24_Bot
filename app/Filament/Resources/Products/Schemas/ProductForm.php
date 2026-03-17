<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->schema([
                        TextInput::make('title')
                            ->required(),
                        Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(1),
                Section::make('Relationships & Status')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'first_name')
                            ->required()
                            ->searchable(),
                        Select::make('category_id')
                            ->relationship('category', 'name_uz')
                            ->required()
                            ->searchable(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'draft' => 'Draft',
                            ])
                            ->required()
                            ->default('pending'),
                        Toggle::make('owner_verified')
                            ->required(),
                    ])->columns(2),
                Section::make('Pricing & Location')
                    ->schema([
                        TextInput::make('price')
                            ->numeric()
                            ->prefix('so\'m'),
                        TextInput::make('latitude')
                            ->numeric(),
                        TextInput::make('longitude')
                            ->numeric(),
                    ])->columns(3),
            ]);
    }
}
