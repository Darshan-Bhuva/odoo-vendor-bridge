<?php

namespace App\Enums\Procurement;

enum QuotationStatusEnum: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case SELECTED = 'selected';
    case REJECTED = 'rejected';
}
