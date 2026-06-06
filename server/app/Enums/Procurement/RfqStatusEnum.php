<?php

namespace App\Enums\Procurement;

enum RfqStatusEnum: string
{
    case DRAFT = 'draft';
    case OPEN = 'open';
    case CLOSED = 'closed';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
