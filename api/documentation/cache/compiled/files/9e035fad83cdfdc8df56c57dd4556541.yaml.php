<?php
return [
    '@class' => 'Grav\\Common\\File\\CompiledYamlFile',
    'filename' => 'user/plugins/bootstrapper/blueprints.yaml',
    'modified' => 1436533044,
    'data' => [
        'name' => 'Bootstrapper',
        'version' => '1.0.1',
        'description' => 'Loads the Boostrap Framework v3.3.2 assets for any plugin/theme that needs it',
        'icon' => 'bold',
        'author' => [
            'name' => 'Team Grav',
            'email' => 'devs@getgrav.org',
            'url' => 'http://getgrav.org'
        ],
        'homepage' => 'https://github.com/getgrav/grav-plugin-bootstrapper',
        'demo' => 'http://demo.getgrav.org/bootstrap-skeleton/',
        'keywords' => 'bootstrap, css, plugin, framework',
        'bugs' => 'https://github.com/getgrav/grav-plugin-bootstrapper/issues',
        'license' => 'MIT',
        'form' => [
            'validation' => 'strict',
            'fields' => [
                'enabled' => [
                    'type' => 'toggle',
                    'label' => 'Plugin status',
                    'highlight' => 1,
                    'default' => 0,
                    'options' => [
                        1 => 'Enabled',
                        0 => 'Disabled'
                    ],
                    'validate' => [
                        'type' => 'bool'
                    ]
                ]
            ]
        ]
    ]
];
