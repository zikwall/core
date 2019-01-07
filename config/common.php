<?php
return [
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationNamespaces' => [
                'zikwall\encore\modules\core\migrations',
            ],
        ],
    ],
];