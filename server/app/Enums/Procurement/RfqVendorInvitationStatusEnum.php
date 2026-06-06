<?php

namespace App\Enums\Procurement;

enum RfqVendorInvitationStatusEnum: string
{
    case INVITED = 'invited';
    case VIEWED = 'viewed';
    case SUBMITTED = 'submitted';
}
