<?php

namespace App\Enum;

enum VolunteeringTransitions: string
{
    case Approve = 'approve';
    case Activate = 'activate';
    case Complete = 'complete';
    case Cancel = 'cancel';
}
