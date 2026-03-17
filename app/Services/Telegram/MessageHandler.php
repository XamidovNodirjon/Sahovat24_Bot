<?php

namespace App\Services\Telegram;

use App\Repositories\CategoryRepository;
use App\Services\UserService;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\User;

class MessageHandler
{
    protected $userService;
    protected $categoryRepository;
    protected $productRepository;

    public function __construct(
        UserService $userService,
        CategoryRepository $categoryRepository,
        \App\Repositories\ProductRepository $productRepository
    ) {
        $this->userService = $userService;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    public function handle($message, User $user)
    {
        $chatId = $message->getChat()->getId();
        $text = $message->getText();
        $contact = $message->getContact();

        if ($text == '/start') {
            if ($user->phone_number) {
                // If the user already has a phone number, they are fully registered
                $this->userService->setStep($user->telegram_id, 'main_menu');
                $this->sendMainMenu($chatId, $user->language);
                return;
            }

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "Assalomu alaykum, {$user->first_name}! SahovatBot ga xush kelibsiz.\n\nTilni tanlang / Выберите язык:",
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => "🇺🇿 O'zbekcha", 'callback_data' => 'lang_uz'],
                            ['text' => "🇷🇺 Русский", 'callback_data' => 'lang_ru']
                        ]
                    ]
                ])
            ]);
            
            // Step o'zgartiramiz, garchi shart bo'lmasa ham
            return;
        }

        // Boshqa barcha xabarlar va holatlar (steps) shu yerda yoziladi
        switch($user->step) {
            case 'asking_for_phone':
                if ($contact) {
                    $phone = $contact->getPhoneNumber();
                    $this->userService->setPhoneNumber($user->telegram_id, $phone);
                    $this->userService->setStep($user->telegram_id, 'main_menu');

                    $this->sendMainMenu($chatId, $user->language);
                } else {
                    $msg = $user->language == 'uz' ? "Iltimos, pastdagi tugmani bosish orqali raqamingizni yuboring." : "Пожалуйста, отправьте свой номер, нажав на кнопку ниже.";
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => $msg
                    ]);
                }
                break;
            case 'main_menu':
                if ($text === "📝 E'lon berish" || $text === "📝 Подать объявление") {
                    $this->userService->setStep($user->telegram_id, 'choosing_category');
                    
                    $categories = $this->categoryRepository->getParents();
                    $keyboard = [];
                    foreach ($categories as $category) {
                        $catName = $user->language == 'uz' ? $category->name_uz : $category->name_ru;
                        $keyboard[] = [
                            ['text' => $catName, 'callback_data' => 'category_' . $category->id]
                        ];
                    }

                    $msg = $user->language == 'uz' ? "Kategoriyani tanlang:" : "Выберите категорию:";
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => $msg,
                        'reply_markup' => json_encode([
                            'inline_keyboard' => $keyboard
                        ])
                    ]);
                } elseif ($text === "🛍 Barcha e'lonlar" || $text === "🛍 Все объявления") {
                    $msg = $user->language == 'uz' ? "🛍 Ko'rish turini tanlang:" : "🛍 Выберите способ просмотра:";
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text'    => $msg,
                        'reply_markup' => json_encode([
                            'inline_keyboard' => [
                                [['text' => $user->language == 'uz' ? "📍 Yaqin atrofdagi e'lonlar" : "📍 Ближайшие объявления", 'callback_data' => 'view_nearby']],
                                [['text' => $user->language == 'uz' ? "📜 Barcha e'lonlar" : "📜 Все объявления", 'callback_data' => 'view_all']]
                            ]
                        ])
                    ]);
                } elseif ($text === "🌐 Tilni o'zgartirish" || $text === "🌐 Изменить язык") {
                    $msg = $user->language == 'uz' ? "Tilni tanlang:" : "Выберите язык:";
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => $msg,
                        'reply_markup' => json_encode([
                            'inline_keyboard' => [
                                [
                                    ['text' => "🇺🇿 O'zbekcha", 'callback_data' => 'lang_change_uz'],
                                    ['text' => "🇷🇺 Русский", 'callback_data' => 'lang_change_ru']
                                ]
                            ]
                        ])
                    ]);
                } elseif ($text === "📋 Mening e'lonlarim" || $text === "📋 Мои объявления") {
                    $products = $this->productRepository->getUserProducts($user->id);
                    if ($products->isEmpty()) {
                        $msg = $user->language == 'uz' ? "Sizda hali e'lonlar yo'q." : "У вас (ещё) нет объявлений.";
                        Telegram::sendMessage(['chat_id' => $chatId, 'text' => $msg]);
                    } else {
                        $headerMsg = $user->language == 'uz' ? "📋 Sizning e'lonlaringiz:" : "📋 Ваши объявления:";
                        Telegram::sendMessage(['chat_id' => $chatId, 'text' => $headerMsg]);

                        $statusMap = [
                            'pending'  => $user->language == 'uz' ? '⏳ Kutilmoqda'   : '⏳ Ожидание',
                            'approved' => $user->language == 'uz' ? '✅ Tasdiqlangan'  : '✅ Одобрено',
                            'rejected' => $user->language == 'uz' ? '❌ Rad etilgan'   : '❌ Отклонено',
                        ];

                        foreach ($products as $product) {
                            $statusLabel = $statusMap[$product->status] ?? $product->status;
                            $price = $product->price ? number_format($product->price, 0, '.', ' ') . ' so\'m' : ($user->language == 'uz' ? 'Tekin (Ehson)' : 'Бесплатно');
                            
                            $caption = "📌 #{$product->id}\n";
                            $caption .= ($user->language == 'uz' ? 'Sarlavha: ' : 'Заголовок: ') . $product->title . "\n";
                            $caption .= ($user->language == 'uz' ? 'Narx: ' : 'Цена: ') . $price . "\n";
                            $caption .= ($user->language == 'uz' ? 'Holat: ' : 'Статус: ') . $statusLabel;

                            $inlineKeyboard = [];
                            // Only allow delete if NOT approved (active)
                            if ($product->status !== 'approved') {
                                $deleteBtnText = $user->language == 'uz' ? "🗑 E'lonni o'chirish" : "🗑 Удалить объявление";
                                $inlineKeyboard[] = [
                                    ['text' => $deleteBtnText, 'callback_data' => 'product_delete_' . $product->id]
                                ];
                            }

                            Telegram::sendMessage([
                                'chat_id' => $chatId,
                                'text' => $caption,
                                'reply_markup' => !empty($inlineKeyboard) ? json_encode(['inline_keyboard' => $inlineKeyboard]) : null,
                            ]);
                        }
                    }
                } else {
                    $this->sendMainMenu($chatId, $user->language);
                }
                break;
            case 'entering_title':
                $draft = $this->productRepository->findPendingDraftByUser($user->id);
                if ($draft) {
                    $draft->update(['title' => $text]);
                    $this->userService->setStep($user->telegram_id, 'entering_photo');
                    
                    $msg = $user->language == 'uz' ? "Sarlavha saqlandi. ✅\n\nEndi mahsulot rasmini yuboring:" : "Заголовок сохранен. ✅\n\nТеперь отправьте фотографию продукта:";
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => $msg
                    ]);
                } else {
                    $this->userService->setStep($user->telegram_id, 'main_menu');
                    $this->sendMainMenu($chatId, $user->language);
                }
                break;
            case 'entering_photo':
                $photo = $message->getPhoto();
                if ($photo) {
                    $draft = $this->productRepository->findPendingDraftByUser($user->id);
                    if ($draft) {
                        // Eng katta o'lchamdagi rasmni olamiz (arrayning oxirgisi)
                        $photoArray = is_array($photo) ? $photo : $photo->all();
                        $lastPhoto = end($photoArray);
                        $fileId = $lastPhoto->getFileId();
                        $draft->images()->create(['file_id' => $fileId]);
                        
                        // User can send more photos. We don't change step yet.
                        // We provide an inline button to finish.
                        $msgUz = "Rasm qabul qilindi ✅\nYana rasm yuborishingiz yoki tugatish uchun quyidagi tugmani bosishingiz mumkin:";
                        $msgRu = "Фото принято ✅\nВы можете отправить еще фото или нажать кнопку ниже для завершения:";
                        
                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'text' => $user->language == 'uz' ? $msgUz : $msgRu,
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    [
                                        ['text' => $user->language == 'uz' ? "➡️ Rasmlarni yakunlash" : "➡️ Завершить фото", 'callback_data' => 'finish_photos']
                                    ]
                                ]
                            ])
                        ]);
                    }
                } else {
                    $msg = $user->language == 'uz' ? "Iltimos, rasm yuboring." : "Пожалуйста, отправьте фотографию.";
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => $msg
                    ]);
                }
                break;
            case 'entering_price':
                $draft = $this->productRepository->findPendingDraftByUser($user->id);
                if ($draft) {
                    $isFree = false;
                    $price = 0;

                    if ($text === "🎁 Ehson (Tekin)" || $text === "🎁 Благотворительность (Бесплатно)") {
                        $isFree = true;
                    } elseif (is_numeric($text)) {
                        $price = $text;
                    } else {
                        // Narx noto'g'ri kiritilsa
                        $msg = $user->language == 'uz' ? "Iltimos, faqat raqam kiriting yoki 'Ehson' tugmasini bosing." : "Пожалуйста, введите только цифры или нажмите кнопку 'Благотворительность'.";
                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'text' => $msg
                        ]);
                        return; // O'zgarishsiz qoldiramiz
                    }

                    $draft->update([
                        'price'  => $isFree ? null : $price,
                        'status' => 'draft',
                    ]);

                    // Location so'raymiz
                    $this->userService->setStep($user->telegram_id, 'entering_location');

                    $msgUz = "📍 Endi mahsulot joylashuvini yuboring. Bu orqali foydalanuvchilar sizga yaqin joydan topadi.\n\nPastdagi tugmani bosib joylashuvingizni yuboring:";
                    $msgRu = "📍 Теперь отправьте местоположение товара. Это поможет пользователям найти вас поближе.\n\nНажмите кнопку ниже для отправки местоположения:";
                    $msg    = $user->language == 'uz' ? $msgUz : $msgRu;
                    $btnLoc = $user->language == 'uz' ? "📍 Joylashuvni yuborish" : "📍 Отправить местоположение";

                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text'    => $msg,
                        'reply_markup' => json_encode([
                            'keyboard' => [
                                [['text' => $btnLoc, 'request_location' => true]]
                            ],
                            'resize_keyboard'  => true,
                            'one_time_keyboard' => true
                        ])
                    ]);
                } else {
                    $this->userService->setStep($user->telegram_id, 'main_menu');
                    $this->sendMainMenu($chatId, $user->language);
                }
                break;
            case 'entering_location':
                $location = $message->getLocation();
                if ($location) {
                    $draft = $this->productRepository->findPendingDraftByUser($user->id);
                    if ($draft) {
                        $draft->update([
                            'latitude'  => $location->getLatitude(),
                            'longitude' => $location->getLongitude(),
                        ]);
                        // Save to user profile as well
                        $user->update([
                            'latitude'  => $location->getLatitude(),
                            'longitude' => $location->getLongitude(),
                        ]);
                    }

                    // Ownership confirmation step
                    $this->userService->setStep($user->telegram_id, 'confirming_ownership');

                    $msgUz = "❓ Bu mahsulot sizniki ekanligini tasdiqlaysizmi?\n\n« Bu tasdiqlash mualliflik huquqini beradi. »";
                    $msgRu = "❓ Подтверждаете ли, что этот товар ваш?\n\n« Это подтверждение даёт право авторства. »";

                    Telegram::sendMessage([
                        'chat_id'      => $chatId,
                        'text'         => $user->language == 'uz' ? $msgUz : $msgRu,
                        'reply_markup' => json_encode([
                            'inline_keyboard' => [
                                [
                                    ['text' => "✅ Ha, meniki",     'callback_data' => 'ownership_yes'],
                                    ['text' => "❌ Yo'q, meniki emas", 'callback_data' => 'ownership_no'],
                                ]
                            ]
                        ])
                    ]);
                } else {
                    $msg = $user->language == 'uz'
                        ? "Iltimos, pastdagi tugmani bosib joylashuvingizni yuboring."
                        : "Пожалуйста, нажмите кнопку ниже для отправки местоположения.";
                    Telegram::sendMessage(['chat_id' => $chatId, 'text' => $msg]);
                }
                break;
            case 'confirming_ownership':
                // If user somehow sends a text message instead of using inline buttons
                $this->userService->setStep($user->telegram_id, 'main_menu');
                $this->sendMainMenu($chatId, $user->language);
                break;
            case 'browsing_location':
                $location = $message->getLocation();
                if ($location) {
                    $lat = $location->getLatitude();
                    $lng = $location->getLongitude();
                    $categoryId = $user->browse_category_id;

                    if (!$categoryId) {
                        $this->sendMainMenu($chatId, $user->language);
                        break;
                    }

                    // Save the user's location permanently for later use/pagination
                    $user->update([
                        'latitude' => $lat,
                        'longitude' => $lng
                    ]);
                    
                    $offset = 0;
                    $limit  = 2;
                    // Fetch nearest products (now using repository default, e.g. 50km)
                    $products = $this->productRepository->getNearbyByCategory($categoryId, $lat, $lng, 50, $offset, $limit + 1);
                    
                    // Cleanup category for next search
                    $this->userService->setBrowseCategory($user->telegram_id, null);
                    $this->userService->setStep($user->telegram_id, 'main_menu');

                    if ($products->isEmpty()) {
                        $empty = $user->language == 'uz'
                            ? "🔍 Ushbu kategoriya bo'yicha yaqin atrofda e'lonlar topilmadi."
                            : "🔍 Поблизи не найдено объявлений в этой категории.";
                        Telegram::sendMessage(['chat_id' => $chatId, 'text' => $empty]);
                    } else {
                        $header = $user->language == 'uz'
                            ? "📍 Sizga eng yaqin e'lonlar:"
                            : "📍 Ближайшие к вам объявления:";
                        Telegram::sendMessage(['chat_id' => $chatId, 'text' => $header]);

                        $hasNextPage = $products->count() > $limit;
                        if ($hasNextPage) {
                            $products->pop(); // remove the extra item
                        }

                        $count = count($products);
                        foreach ($products as $index => $product) {
                            $keyboard = null;
                            // Add 'Next' button to the last item if there might be more
                            if ($index === $count - 1 && $hasNextPage) {
                                $nextBtn = $user->language == 'uz' ? "Keyingi ➡️" : "Далее ➡️";
                                $keyboard = [
                                    [ ['text' => $nextBtn, 'callback_data' => "page_nearby_{$categoryId}_" . ($offset + $limit)] ]
                                ];
                            }
                            $this->sendProductCard($chatId, $product, $user->language, $product->distance, $keyboard);
                        }
                    }
                    $this->sendMainMenu($chatId, $user->language);
                } else {
                    $msg = $user->language == 'uz'
                        ? "Iltimos, joylashuvingizni yuboring."
                        : "Пожалуйста, отправьте своё местоположение.";
                    Telegram::sendMessage(['chat_id' => $chatId, 'text' => $msg]);
                }
                break;
            default:
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Tushunarsiz buyruq.'
                ]);
        }
    }

    protected function sendMainMenu($chatId, $lang)
    {
        $text     = $lang == 'uz' ? "Asosiy menyuga xush kelibsiz! Marhamat, bo'lim tanlang." : "Пожалуйста, выберите раздел.";
        $btnAdd   = $lang == 'uz' ? "📝 E'lon berish"         : "📝 Подать объявление";
        $btnView  = $lang == 'uz' ? "🛍 Barcha e'lonlar"     : "🛍 Все объявления";
        $btnLang  = $lang == 'uz' ? "🌐 Tilni o'zgartirish" : "🌐 Изменить язык";
        $btnMine  = $lang == 'uz' ? "📋 Mening e'lonlarim" : "📋 Мои объявления";

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => json_encode([
                'keyboard' => [
                    [ ['text' => $btnAdd], ['text' => $btnView] ],
                    [ ['text' => $btnLang], ['text' => $btnMine] ]
                ],
                'resize_keyboard' => true
            ])
        ]);
    }

    /**
     * Send a product card: photo (if any) + formatted caption with Maps link.
     */
    protected function sendProductCard($chatId, $product, string $lang, ?float $distanceKm = null, ?array $keyboard = null): void
    {
        $price = $product->price
            ? number_format($product->price, 0, '.', ' ') . ' so\'m'
            : ($lang == 'uz' ? 'Tekin 🎁' : 'Бесплатно 🎁');

        $caption  = "📌 <b>{$product->title}</b>\n";
        $caption .= "────────\n";
        $caption .= ($lang == 'uz' ? '💰 Narx: ' : '💰 Цена: ') . $price . "\n";

        if ($distanceKm !== null) {
            $caption .= ($lang == 'uz' ? '📍 Masofa: ' : '📍 Расстояние: ') . round($distanceKm, 2) . " km\n";
        }

        if ($product->latitude && $product->longitude) {
            $mapsUrl  = "https://maps.google.com/?q={$product->latitude},{$product->longitude}";
            $caption .= ($lang == 'uz'
                ? "🗺 <a href='{$mapsUrl}'>Xaritada ko'rish</a>"
                : "🗺 <a href='{$mapsUrl}'>Посмотреть на карте</a>") . "\n";
        }

        $img = $product->images()->first();

        if ($img) {
            $params = [
                'chat_id'    => $chatId,
                'photo'      => $img->file_id,
                'caption'    => $caption,
                'parse_mode' => 'HTML',
            ];
            if ($keyboard) {
                $params['reply_markup'] = json_encode(['inline_keyboard' => $keyboard]);
            }
            Telegram::sendPhoto($params);
        } else {
            $params = [
                'chat_id'    => $chatId,
                'text'       => $caption,
                'parse_mode' => 'HTML',
            ];
            if ($keyboard) {
                $params['reply_markup'] = json_encode(['inline_keyboard' => $keyboard]);
            }
            Telegram::sendMessage($params);
        }
    }
}
