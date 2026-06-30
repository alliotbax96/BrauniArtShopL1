<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Validator;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('yandex', \SocialiteProviders\Yandex\Provider::class);
            $event->extendSocialite('vkontakte', \SocialiteProviders\VKID\Provider::class);
        });

        Validator::extend('unique_phone', function ($attribute, $value, $parameters, $validator) {
            // Нормализуем номер: убираем всё, кроме цифр
            $normalizedPhone = preg_replace('![^0-9]+!', '', $value);

            // Проверяем уникальность нормализованного номера
            return !\App\Models\User::where('phone', $normalizedPhone)->exists();
        });
    }
}
