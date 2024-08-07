<?php

namespace App\Gateway;

use App\Enums\Currency;
use App\Exceptions\UnknownExchangeRate;

class ExchangeRateGateway
{
    private const EXCHANGE_RATES = [
        Currency::CHF->value => [
            Currency::EUR->value => '1.1',
        ],
    ];

    public function getExchangeRate(Currency $from, Currency $to): string
    {
        /*return self::EXCHANGE_RATES[$from->value][$to->value] ??
            throw new UnknownExchangeRate($from, $to);*/
        return '1.1';
    }
}
