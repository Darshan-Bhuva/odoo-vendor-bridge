<?php

namespace App\Enums\Procurement;

enum InvoiceStatusEnum: string
{
    case GENERATED = 'generated';
    case EMAILED = 'emailed';
    case PAID = 'paid';
}
