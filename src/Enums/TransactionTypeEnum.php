<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case PAYIN = 'payin';
    case PAYOUT = 'payout';
    case EXCHANGE_PAYOUT = 'exchange-payout';
    case EXCHANGE_PAYIN = 'exchange-payin';
}
