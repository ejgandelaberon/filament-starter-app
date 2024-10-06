<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;

return [
    [
        'name' => 'John Doe',
        'position' => 'Software Engineer',
        'salary' => '$100,000',
        'start_date' => [
            'display' => 'Mon 25th Jul 11',
            'timestamp' => Carbon::parse('Mon 25th Jul 11')->timestamp,
        ],
        'office' => 'New York',
        'extn' => '5421',
    ],
    [
        'name' => 'Jane Doe',
        'position' => 'Product Manager',
        'salary' => '$120,000',
        'start_date' => [
            'display' => 'Tue 26th Jul 11',
            'timestamp' => Carbon::parse('Tue 26th Jul 11')->timestamp,
        ],
        'office' => 'San Francisco',
        'extn' => '8422',
    ],
    [
        'name' => 'John Smith',
        'position' => 'Quality Assurance',
        'salary' => '$90,000',
        'start_date' => [
            'display' => 'Wed 27th Jul 11',
            'timestamp' => Carbon::parse('Wed 27th Jul 11')->timestamp,
        ],
        'office' => 'Los Angeles',
        'extn' => '7321',
    ],
];
