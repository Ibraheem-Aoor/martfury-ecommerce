<?php

return [
    [
        'name' => 'Marketplaces',
        'flag' => 'marketplace.index',
    ],

    [
        'name' => 'Stores',
        'flag' => 'marketplace.store.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'marketplace.store.create',
        'parent_flag' => 'marketplace.store.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'marketplace.store.edit',
        'parent_flag' => 'marketplace.store.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'marketplace.store.destroy',
        'parent_flag' => 'marketplace.store.index',
    ],
    [
        'name'        => 'View',
        'flag'        => 'marketplace.store.view',
        'parent_flag' => 'marketplace.store.index',
    ],
    [
        'name'        => 'Update balance',
        'flag'        => 'marketplace.store.revenue.create',
        'parent_flag' => 'marketplace.store.index',
    ],

    [
        'name' => 'Withdrawals',
        'flag' => 'marketplace.withdrawal.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'marketplace.withdrawal.edit',
        'parent_flag' => 'marketplace.withdrawal.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'marketplace.withdrawal.destroy',
        'parent_flag' => 'marketplace.withdrawal.index',
    ],

    [
        'name' => 'Unverified vendors',
        'flag' => 'marketplace.unverified-vendors.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'marketplace.unverified-vendors.edit',
        'parent_flag' => 'marketplace.unverified-vendors.index',
    ],
];
