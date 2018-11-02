<?php

declare(strict_types=1);

use Tychovbh\ResourceMapper\RawResource;

// Here you can see some example mapping configuration replace these for your own.

return [
    'user' => [
        'id' => 'RawID',
        'company' => 'RawCompany.RawName',
        'title' => function (RawResource $resource) {
            return ucfirst($resource->get('RawTitle'));
        },
        'fullname' => function (RawResource $resource) {
            return $resource->join(' ', 'RawFirstname', 'RawSuffix', 'RawLastname');
        },
    ],
    'company_user' => [
        'user' => function (RawResource $resource) {
            return $this->config('user')->map($resource->get('RawCompany.RawUser'));
        }
    ],
    'users' => [
        'items' => function (RawResource $resource) {
            return $this->config('user')->mapCollection($resource->get('RawItems'));
        }
    ]
    // Add more mapping config here
];
