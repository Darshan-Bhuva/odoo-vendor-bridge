<?php

namespace App\Enums\Procurement;

enum PurchaseOrderStatusEnum: string
{
    case DRAFT = 'draft';
    case GENERATED = 'generated';
    case SENT = 'sent';
}
