<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Entity\Volunteering;
use App\Enum\VolunteeringStatus;
use App\Enum\VolunteeringTransitions;

return App::config([
    'framework' => [
        'workflows' => [
            'volunteering_status' => [
                'type' => 'state_machine',
                'audit_trail' => [
                    'enabled' => true,
                ],
                'marking_store' => [
                    'type' => 'method',
                    'property' => 'status',
                ],
                'supports' => [Volunteering::class],
                'initial_marking' => VolunteeringStatus::Pending,
                'places' => VolunteeringStatus::cases(),
                'transitions' => [
                    VolunteeringTransitions::Approve->value => [
                        'from' => VolunteeringStatus::Pending,
                        'to' => VolunteeringStatus::Approved,
                        'metadata' => [
                            'label' => 'Approve Volunteer',
                            'description' => 'Confirm the volunteer is eligible',
                            'required_role' => 'ROLE_ORGANIZER',
                        ]
                    ],
                    VolunteeringTransitions::Activate->value => [
                        'from' => VolunteeringStatus::Approved,
                        'to' => VolunteeringStatus::Active,
                    ],
                    VolunteeringTransitions::Complete->value => [
                        'from' => VolunteeringStatus::Active,
                        'to' => VolunteeringStatus::Completed
                    ],
                    VolunteeringTransitions::Cancel->value => [
                        'from' => [
                            VolunteeringStatus::Pending,
                            VolunteeringStatus::Approved,
                            VolunteeringStatus::Active,
                        ],
                        'to' => VolunteeringStatus::Cancelled,
                    ],
                ]
            ]
        ]
    ]
]);
