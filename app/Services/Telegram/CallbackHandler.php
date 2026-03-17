<?php

namespace App\Services\Telegram;

use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\CategoryRepository;

class CallbackHandler
{
    protected $userService;
    protected $productRepository;
    protected $categoryRepository;

    public function __construct(
        UserService $userService,
        \App\Repositories\ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->userService = $userService;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function handle($callbackQuery, User $user)
    {
        $chatId = $callbackQuery->getMessage()->getChat()->getId();
        $messageId = $callbackQuery->getMessage()->getMessageId();
        $data = $callbackQuery->getData();

        if ($data === 'lang_uz' || $data === 'lang_ru') {
            $lang = str_replace('lang_', '', $data);
            
            // Tilni boshqa so'ramaymiz, qadamni asking_for_phone ga o'tkazamiz
            $this->userService->setLanguage($user->telegram_id, $lang);
            $this->userService->setStep($user->telegram_id, 'asking_for_phone');

            // Botning maqsadi matni
            $purposeUz = "🤝 **SahovatBot** - bu ortib qolgan yoki sizga kerak bo'lmagan, lekin boshqalar uchun asqotishi mumkin bo'lgan buyumlarni bepul ulashish yoki arzon narxda sotish uchun mo'ljallangan platforma.\n\nMaqsadimiz - isrofgarchilikning oldini olish va odamlarga o'zaro yordam berish imkonini yaratish.";
            $purposeRu = "🤝 **SahovatBot** - это платформа, предназначенная для бесплатного обмена или продажи по низкой цене вещей, которые вам больше не нужны, но могут пригодиться другим.\n\nНаша цель - предотвратить расточительство и создать возможность для взаимопомощи.";
            
            $purposeText = $lang == 'uz' ? $purposeUz : $purposeRu;
            $text = $lang == 'uz' ? "Iltimos, ro'yxatdan o'tish uchun telefon raqamingizni yuboring." : "Пожалуйста, отправьте свой номер телефона для регистрации.";
            $btnText = $lang == 'uz' ? "📞 Raqamni yuborish" : "📞 Отправить номер";

            // 1. Oldingi xabarni tahrirlab maqsadni yozamiz
            Telegram::editMessageText([
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text' => $purposeText,
                'parse_mode' => 'Markdown'
            ]);

            // 2. Yangi xabar bilan raqam so'raymiz
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => $btnText, 'request_contact' => true]
                        ]
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ])
            ]);
        } elseif ($data === 'lang_change_uz' || $data === 'lang_change_ru') {
            $lang = str_replace('lang_change_', '', $data);
            $this->userService->setLanguage($user->telegram_id, $lang);
            
            $text = $lang == 'uz' ? "Til muvaffaqiyatli o'zgartirildi! 🇺🇿" : "Язык успешно изменен! 🇷🇺";
            
            Telegram::editMessageText([
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text' => $text
            ]);

            $menuText = $lang == 'uz' ? "Asosiy menyuga xush kelibsiz! Marhamat, kerakli bo'limni tanlang." : "Добро пожаловать в главное меню! Пожалуйста, выберите нужный раздел.";
            $btnAdd  = $lang == 'uz' ? "📝 E'lon berish"         : "📝 Подать объявление";
            $btnView = $lang == 'uz' ? "🛍 Barcha e'lonlar"     : "🛍 Все объявления";
            $btnLang = $lang == 'uz' ? "🌐 Tilni o'zgartirish" : "🌐 Изменить язык";
            $btnMine = $lang == 'uz' ? "📋 Mening e'lonlarim" : "📋 Мои объявления";

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $menuText,
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            ['text' => $btnAdd],
                            ['text' => $btnView]
                        ],
                        [
                            ['text' => $btnLang],
                            ['text' => $btnMine]
                        ]
                    ],
                    'resize_keyboard' => true
                ])
            ]);
        } elseif (str_starts_with($data, 'product_delete_')) {
            $productId = (int) str_replace('product_delete_', '', $data);
            $deleted = $this->productRepository->deleteById($productId, $user->id);

            if ($deleted) {
                $text = $user->language == 'uz' ? "✅ E'lon muvaffaqiyatli o'chirildi." : "✅ Объявление успешно удалено.";
            } else {
                $text = $user->language == 'uz' ? "❌ E'lonni o'chirib bo'lmadi. Iltimos qayta urining." : "❌ Не удалось удалить объявление. Пожалуйста, попробуйте еще раз.";
            }

            Telegram::editMessageText([
                'chat_id'    => $chatId,
                'message_id' => $messageId,
                'text'       => ($deleted ? "🗑 " : "❌ ") . ($deleted
                    ? ($user->language == 'uz' ? "E'lon o'chirildi." : "Объявление удалено.")
                    : ($user->language == 'uz' ? "O'chirib bo'lmadi." : "Не удалось удалить.")
                ),
            ]);
        } elseif ($data === 'ownership_yes' || $data === 'ownership_no') {
            $isOwner = $data === 'ownership_yes';

            // Find the pending draft (status is still 'draft' here)
            $product = $this->productRepository->findPendingDraftByUser($user->id);

            $this->userService->setStep($user->telegram_id, 'main_menu');

            if ($isOwner) {
                // Confirm ownership and publish the product
                if ($product) {
                    $product->update([
                        'owner_verified' => true,
                        'status'         => 'approved',
                    ]);
                }

                Telegram::editMessageText([
                    'chat_id'    => $chatId,
                    'message_id' => $messageId,
                    'text'       => $user->language == 'uz' ? "✅ Mualliflik tasdiqlandi!" : "✅ Авторство подтверждено!",
                ]);

                $successMsg = $user->language == 'uz'
                    ? "🎉 E'loningiz muvaffaqiyatli joylashtirildi!\nBoshqalar uni 'Barcha e'lonlar' bo'limida ko'ra oladi."
                    : "🎉 Ваше объявление успешно опубликовано!\nДругие могут увидеть его в разделе 'Все объявления'.";

                Telegram::sendMessage(['chat_id' => $chatId, 'text' => $successMsg]);
                $this->sendProductCard($chatId, $product, $user->language);
            } else {
                // NOT owner — delete the product entirely
                if ($product) {
                    $product->images()->delete();
                    $product->delete();
                }

                Telegram::editMessageText([
                    'chat_id'    => $chatId,
                    'message_id' => $messageId,
                    'text'       => $user->language == 'uz' ? "❌ E'lon bekor qilindi." : "❌ Объявление отменено.",
                ]);

                $warnMsg = $user->language == 'uz'
                    ? "⚠️ Faqat o'zingizning mahsulotlaringizni kira olasiz.\nBoshqaning narsasini e'lon qilish taqiqlangan."
                    : "⚠️ Вы можете разместить только свои товары.\nРазмещение чужих вещей запрещено.";

                Telegram::sendMessage(['chat_id' => $chatId, 'text' => $warnMsg]);
            }

            // Always return to main menu
            $this->sendMainMenu($chatId, $user->language);

        } elseif ($data === 'view_nearby' || $data === 'view_all') {
            $isNearby = $data === 'view_nearby';
            $categories = $this->categoryRepository->getParents();
            $prefix = $isNearby ? 'browse_cat_' : 'browse_all_cat_';

            $keyboard = [];
            foreach ($categories as $category) {
                $catName = $user->language == 'uz' ? $category->name_uz : $category->name_ru;
                $keyboard[] = [['text' => $catName, 'callback_data' => $prefix . $category->id]];
            }

            $msg = $isNearby
                ? ($user->language == 'uz' ? "📍 Qaysi kategoriyani ko'rmoqchisiz?" : "📍 Выберите категорию:")
                : ($user->language == 'uz' ? "📜 Qaysi kategoriyani ko'rmoqchisiz?" : "📜 Выберите категорию:");

            Telegram::editMessageText([
                'chat_id'    => $chatId,
                'message_id' => $messageId,
                'text'       => $msg,
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
            ]);
        } elseif (str_starts_with($data, 'category_')) {
            $categoryId = str_replace('category_', '', $data);
            
            // 1. Yangi qorlama (draft) e'lon yaratamiz
            $this->productRepository->updateOrCreateDraft($user->id, [
                'category_id' => $categoryId,
                'title' => '',
                'description' => ''
            ]);

            // 2. Foydalanuvchi qadamini sarlavha yozishga o'tkazamiz
            $this->userService->setStep($user->telegram_id, 'entering_title');

            // 3. Xabarni o'zgartirib Sarlavha so'raymiz
            $text = $user->language == 'uz'
                ? "Kategoriya tanlandi! ✅\n\nEndi e'loningiz uchun sarlavha (nomi) yozib yuboring:"
                : "Категория выбрана! ✅\n\nНапишите заголовок (название) вашего объявления:";

            Telegram::editMessageText([
                'chat_id'    => $chatId,
                'message_id' => $messageId,
                'text'       => $text
            ]);

            // Hide the main menu bottom keyboard so UI is clean during text input
            Telegram::sendMessage([
                'chat_id'      => $chatId,
                'text'         => $user->language == 'uz'
                    ? "✏️ Sarlavhani yozing:"
                    : "✏️ Напишите заголовок:",
                'reply_markup' => json_encode(['remove_keyboard' => true])
            ]);
        } elseif ($data === 'finish_photos') {
            $this->userService->setStep($user->telegram_id, 'entering_price');
            
            $msgUz = "Rasmlar qabul qilindi. ✅\n\nEndi mahsulot narxini kiriting.\nAgarda ushbu mahsulotni ehson sifatida tekinga bermoqchi bo'lsangiz, shunchaki quyidagi tugmani bosing:";
            $msgRu = "Фото приняты. ✅\n\nТеперь введите цену продукта.\nЕсли вы хотите отдать этот продукт бесплатно в качестве пожертвования, просто нажмите кнопку ниже:";
            $msg = $user->language == 'uz' ? $msgUz : $msgRu;
            $btnFree = $user->language == 'uz' ? "🎁 Ehson (Tekin)" : "🎁 Благотворительность (Бесплатно)";

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $msg,
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [['text' => $btnFree]]
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ])
            ]);
        } elseif (str_starts_with($data, 'browse_cat_')) {
            $categoryId = (int) str_replace('browse_cat_', '', $data);

            // Save the browse category and switch step
            $this->userService->setBrowseCategory($user->telegram_id, $categoryId);
            
            // Check if user has saved location
            if ($user->latitude && $user->longitude) {
                $msg = $user->language == 'uz'
                    ? "📍 Sizning oxirgi joylashuvingiz saqlangan. Shu manzil bo'yicha yaqin atrofdagi e'lonlarni qidiraymi?"
                    : "📍 Ваше последнее местоположение сохранено. Искать ближайшие объявления по этому адресу?";
                
                $btnSaved = $user->language == 'uz' ? "✅ Ha, oxirgi manzil bo'yicha" : "✅ Да, по последнему адресу";
                $btnNew   = $user->language == 'uz' ? "🔄 Yangi manzil yuborish"  : "🔄 Отправить новый адрес";

                Telegram::editMessageText([
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                    'text' => $msg,
                    'reply_markup' => json_encode(['inline_keyboard' => [
                        [['text' => $btnSaved, 'callback_data' => "use_saved_location_" . $categoryId]],
                        [['text' => $btnNew, 'callback_data' => "request_new_location_" . $categoryId]]
                    ]])
                ]);
            } else {
                $this->requestNewLocation($chatId, $messageId, $user, $categoryId);
            }
        } elseif (str_starts_with($data, 'request_new_location_')) {
            $categoryId = (int) str_replace('request_new_location_', '', $data);
            $this->userService->setBrowseCategory($user->telegram_id, $categoryId);
            $this->requestNewLocation($chatId, $messageId, $user, $categoryId);
        } elseif (str_starts_with($data, 'use_saved_location_')) {
            $categoryId = (int) str_replace('use_saved_location_', '', $data);
            $offset = 0;
            $limit = 2;
            $lat = $user->latitude;
            $lng = $user->longitude;

            Telegram::editMessageText([
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text' => $user->language == 'uz' ? "⏳ Yuklanmoqda..." : "⏳ Загрузка..."
            ]);

            $products = $this->productRepository->getNearbyByCategory($categoryId, $lat, $lng, 50, $offset, $limit + 1);
            
            if ($products->isEmpty()) {
                $emptyMsg = $user->language == 'uz'
                    ? "🔍 Yaqin atrofda bu kategoriyadan e'lon topilmadi."
                    : "🔍 Поблизи нет объявлений в этой категории.";
                Telegram::sendMessage(['chat_id' => $chatId, 'text' => $emptyMsg]);
            } else {
                $hasNextPage = $products->count() > $limit;
                if ($hasNextPage) { $products->pop(); }

                foreach ($products as $index => $product) {
                    $keyboard = null;
                    if ($index === count($products) - 1) {
                        $buttons = [];
                        if ($hasNextPage) {
                            $nextBtn = $user->language == 'uz' ? "Keyingi ➡️" : "Далее ➡️";
                            $buttons[] = ['text' => $nextBtn, 'callback_data' => "page_nearby_{$categoryId}_" . ($offset + $limit)];
                        }
                        if (!empty($buttons)) { $keyboard = [$buttons]; }
                    }
                    $this->sendProductCard($chatId, $product, $user->language, $product->distance, $keyboard);
                }
            }
            $this->sendMainMenu($chatId, $user->language);
        } elseif (str_starts_with($data, 'browse_all_cat_') || str_starts_with($data, 'page_all_') || str_starts_with($data, 'page_nearby_')) {
            $isNearby = str_starts_with($data, 'page_nearby_');
            
            if (str_starts_with($data, 'browse_all_cat_')) {
                $categoryId = (int) str_replace('browse_all_cat_', '', $data);
                $offset = 0;
            } else {
                // Parse page_all_{cat}_{offset} or page_nearby_{cat}_{offset}
                $parts = explode('_', $data);
                // Parts: [0]=>page, [1]=>all|nearby, [2]=>cat, [3]=>offset
                $categoryId = (int) $parts[2];
                $offset = (int) $parts[3];
            }
            $limit = 2;

            Telegram::editMessageText([
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text' => $user->language == 'uz' ? "⏳ Yuklanmoqda..." : "⏳ Загрузка..."
            ]);

            if ($isNearby) {
                // If it's nearby pagination, we need lat/lng from the user's last interaction
                // User model should have latitude and longitude columns saved during `browsing_location`
                $lat = $user->latitude;
                $lng = $user->longitude;
                if (!$lat || !$lng) {
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => $user->language == 'uz' ? "Joylashuvingiz topilmadi." : "Местоположение не найдено."
                    ]);
                    return;
                }
                // Fetch limit + 1 to check if there is a next page
                $products = $this->productRepository->getNearbyByCategory($categoryId, $lat, $lng, 3, $offset, $limit + 1);
            } else {
                $products = $this->productRepository->getAllByCategory($categoryId, $offset, $limit + 1);
            }

            if ($products->isEmpty()) {
                $emptyMsg = $user->language == 'uz'
                    ? "🔍 Boshqa e'lon topilmadi."
                    : "🔍 Больше объявлений не найдено.";
                Telegram::sendMessage(['chat_id' => $chatId, 'text' => $emptyMsg]);
            } else {
                $hasNextPage = $products->count() > $limit;
                if ($hasNextPage) {
                    $products->pop(); // remove the extra item
                }

                $count = count($products);
                foreach ($products as $index => $product) {
                    $keyboard = null;
                    // Add Pagination buttons to the LAST item of the loop
                    if ($index === $count - 1) {
                        $buttons = [];
                        $prefix  = $isNearby ? "page_nearby_{$categoryId}_" : "page_all_{$categoryId}_";
                        
                        if ($offset > 0) {
                            $prevBtn = $user->language == 'uz' ? "⬅️ Oldingi" : "⬅️ Назад";
                            $buttons[] = ['text' => $prevBtn, 'callback_data' => $prefix . (max(0, $offset - $limit))];
                        }
                        if ($hasNextPage) {
                            $nextBtn = $user->language == 'uz' ? "Keyingi ➡️" : "Далее ➡️";
                            $buttons[] = ['text' => $nextBtn, 'callback_data' => $prefix . ($offset + $limit)];
                        }
                        if (!empty($buttons)) {
                            $keyboard = [$buttons];
                        }
                    }

                    $distance = $isNearby ? $product->distance : null;
                    $this->sendProductCard($chatId, $product, $user->language, $distance, $keyboard);
                }
            }
        }
    }

    protected function requestNewLocation($chatId, $messageId, $user, $categoryId): void
    {
        $this->userService->setStep($user->telegram_id, 'browsing_location');

        $msg    = $user->language == 'uz'
            ? "📍 Joylashuvingizni yuboring, yaqin atrofdagi e'lonlarni ko'rsatamiz."
            : "📍 Отправьте своё местоположение, покажем ближайшие объявления.";
        $btnLoc = $user->language == 'uz' ? "📍 Joylashuvni yuborish" : "📍 Отправить местоположение";

        Telegram::editMessageText([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $msg
        ]);

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $msg,
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => $btnLoc, 'request_location' => true]]
                ],
                'resize_keyboard'   => true,
                'one_time_keyboard' => true
            ])
        ]);
    }

    protected function sendProductCard($chatId, $product, string $lang, ?float $distanceKm = null, ?array $keyboard = null): void
    {
        $price = $product->price
            ? number_format($product->price, 0, '.', ' ') . ' so\'m'
            : ($lang == 'uz' ? 'Tekin 🎁' : 'Бесплатно 🎁');

        $caption  = "📍 <b>{$product->title}</b>\n";
        // $caption .= "––––––––\n";
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

    protected function sendMainMenu($chatId, $lang): void
    {
        $btnAdd  = $lang == 'uz' ? "📝 E'lon berish"         : "📝 Подать объявление";
        $btnView = $lang == 'uz' ? "🛍 Barcha e'lonlar"     : "🛍 Все объявления";
        $btnLang = $lang == 'uz' ? "🌐 Tilni o'zgartirish" : "🌐 Изменить язык";
        $btnMine = $lang == 'uz' ? "📋 Mening e'lonlarim" : "📋 Мои объявления";
        $text    = $lang == 'uz' ? "🏠 Asosiy menyu" : "🏠 Главное меню";

        Telegram::sendMessage([
            'chat_id'      => $chatId,
            'text'         => $text,
            'reply_markup' => json_encode([
                'keyboard' => [
                    [ ['text' => $btnAdd], ['text' => $btnView] ],
                    [ ['text' => $btnLang], ['text' => $btnMine] ]
                ],
                'resize_keyboard' => true
            ])
        ]);
    }
}
