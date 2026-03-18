<?php

namespace App\Filament\Resources\Advertisements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Advertisement;
use Filament\Actions\Action;

class AdvertisementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('button_text')
                    ->searchable(),
                TextColumn::make('button_url')
                    ->searchable(),
                TextColumn::make('sent_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('send')
                    ->label('Reklamani yuborish')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Advertisement $record) {
                        $users = \App\Models\User::all();
                        $sentCount = 0;

                        foreach ($users as $user) {
                            try {
                                $params = [
                                    'chat_id' => $user->telegram_id,
                                    'parse_mode' => 'HTML',
                                ];

                                $keyboard = [];
                                if ($record->button_text && $record->button_url) {
                                    $keyboard = [
                                        'inline_keyboard' => [
                                            [
                                                ['text' => $record->button_text, 'url' => $record->button_url]
                                            ]
                                        ]
                                    ];
                                    $params['reply_markup'] = json_encode($keyboard);
                                }

                                if ($record->image) {
                                    $params['photo'] = \Illuminate\Support\Facades\Storage::disk('public')->path($record->image);
                                    $params['caption'] = $record->message;
                                    \Telegram\Bot\Laravel\Facades\Telegram::sendPhoto($params);
                                } else {
                                    $params['text'] = $record->message;
                                    \Telegram\Bot\Laravel\Facades\Telegram::sendMessage($params);
                                }
                                $sentCount++;
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error("Failed to send advert to {$user->telegram_id}: " . $e->getMessage());
                            }
                        }

                        $record->update(['sent_count' => $sentCount]);

                        \Filament\Notifications\Notification::make()
                            ->title("Reklama {$sentCount} ta foydalanuvchiga yuborildi!")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
