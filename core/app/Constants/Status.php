<?php

namespace App\Constants;

class Status
{
    const ENABLE  = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO  = 0;

    const VERIFIED   = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS  = 1;
    const PAYMENT_PENDING  = 2;
    const PAYMENT_REJECT   = 3;

    const TICKET_OPEN   = 0;
    const TICKET_ANSWER = 1;
    const TICKET_REPLY  = 2;
    const TICKET_CLOSE  = 3;

    const KYC_UNVERIFIED = 0;
    const KYC_PENDING    = 2;
    const KYC_VERIFIED   = 1;

    const PRIORITY_LOW    = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH   = 3;

    const USER_ACTIVE = 1;
    const USER_BAN    = 0;

    const ORDER_PENDING   = 0;
    const ORDER_CONFIRMED = 1;
    const ORDER_SHIPPED   = 2;
    const ORDER_DELIVERED = 3;
    const ORDER_OUT_FOR_DELIVERY = 4;
    const ORDER_DELIVERY_FAILED  = 5;
    const ORDER_RETURNED         = 6;
    const ORDER_PROCESSING = 7;
    const ORDER_PACKAGING  = 8;
    const ORDER_CANCEL    = 9;

    const PAYMENT_ONLINE  = 1;
    const PAYMENT_OFFLINE = 2;

    /** COD charge: flat amount (1) or percentage (2) */
    const COD_CHARGE_FLAT     = 1;
    const COD_CHARGE_PERCENT  = 2;

    const DISCOUNT_FIXED      = 1;
    const DISCOUNT_PERCENTAGE = 0;

    const ORDER_PAYMENT_PENDING = 0;
    const ORDER_PAYMENT_SUCCESS = 1;
    const ORDER_PAYMENT_CANCEL  = 9;
}
