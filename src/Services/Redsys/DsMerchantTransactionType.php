<?php

namespace Orumad\LaravelRedsys\Services\Redsys;

abstract class DsMerchantTransactionType
{
    public const AUTHORIZATION = '0';
    public const PREAUTHORIZATION = '1';
    public const CONFIRM_PREAUTHORIZATION = '2';
    public const AUTOMATIC_REFUND = '3';
    public const RECURRING_TRANSACTION = '5';
    public const SUCCESSIVE_TRANSACTION = '6';
    public const PRE_AUTHENTICATION = '7';
    public const CONFIRM_PRE_AUTHENTICATION = '8';
    public const CANCEL_PREAUTHORIZATION = '9';
    public const DEFERRED_AUTHORIZATION = 'O';
    public const CONFIRM_DEFERRED_AUTHORIZATION = 'P';
    public const CANCEL_DEFERRED_AUTHORIZATION = 'Q';
    public const DEFERRED_INITIAL_FEE = 'R';
    public const DEFERRED_SUCCESSIVE_FEE = 'S';
}
