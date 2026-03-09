<?php

return [

    'types' => ['publik', 'internal', 'rahasia'],
    
    'departments' => [
        'Rendatin',
        'Kul',
        'Komisioner',
        'Teknis',
        'SDM',
        'Parmas',
    ],

    'permissions' => [
        // User Management
        'manage users',
        'create users',
        'manage roles',
        'manage permissions',

        // User Profile Management
        'change password',
        'change name',
        'change description',
        'change photos',

        // File Management
        //'upload files',
        'edit internal posts',
        'delete internal posts',
        'create internal posts',
        
        // 'delete files',
        'download files',

        //file confidential
        'create confidential files',
        'view confidential files',
        'edit confidential files',
        'delete confidential files',
    ],

    'roles' => [
        'Superadmin',
        'staff',
        'kasubag',
        'komisioner'
    ],
    

];
