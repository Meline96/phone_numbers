<?php
namespace App\Providers;

use App\Services\PhoneNumbers\PhoneNumbers;
use App\Services\PhoneNumbers\Contracts\PhoneNumbers as PhoneNumbersInterface;
use Illuminate\Support\ServiceProvider;

class PhoneNumbersServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            PhoneNumbersInterface::class,
            PhoneNumbers::class
        );
    }
}
