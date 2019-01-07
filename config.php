<?php

use zikwall\encore\modules\core\Events;

return [
    'id' => 'core',
    'class' => \zikwall\encore\modules\core\Module::className(),
    'isCoreModule' => true,
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['127.0.0.1', '::1'],
            'generators' => [
                'module' => [
                    'class' => 'zikwall\encore\modules\core\modules\developer\Generator',
                    'templates' => [
                        'core' => '@zikwall/encore/modules/core/modules/developer/default',
                    ]
                ]
            ],
        ],
        'developer' => [
            'class' => 'zikwall\encore\modules\core\modules\developer\Module'
        ]
    ],
    'events' => [

    ],
    'urlManagerRules' => [
        'error' => 'core/error/index',
        'default' => 'core/default/home',
        'developer' => 'core/developer/index'
    ],
];
?>