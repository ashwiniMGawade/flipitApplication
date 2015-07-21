<?php
return [
    '@class' => 'Grav\\Common\\File\\CompiledYamlFile',
    'filename' => 'system/config/site.yaml',
    'modified' => 1437388288,
    'data' => [
        'title' => 'Flipit',
        'author' => [
            'name' => 'Flipit',
            'email' => 'developer@flipit.com'
        ],
        'taxonomies' => [
            0 => 'category',
            1 => 'tag'
        ],
        'blog' => [
            'route' => '/blog'
        ],
        'metadata' => [
            'description' => 'Flipit'
        ],
        'summary' => [
            'enabled' => true,
            'format' => 'short',
            'size' => 300,
            'delimiter' => '==='
        ]
    ]
];
