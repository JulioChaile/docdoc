<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$config = [
    'id' => 'app-administracion',
    'name' => 'DocDoc IO',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
    'bootstrap' => ['frontend\modules\api\Bootstrap', 'log'],
    'modules' => [
        'api' => [
            'class' => 'frontend\modules\api\Api',
        ],
    ],
    'timeZone' => 'America/Argentina/Tucuman',
    'language' => 'es',
    'components' => [
        'request' => [
            'csrfParam' => 'admin_csrf',
            'class' => 'yii\web\Request',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        
        'user' => [
            'identityClass' => 'common\models\Usuarios',
            'loginUrl' => '/site/index',
//            'authTimeout' => 60 * 60,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'session' => [
            'name' => 'DDSESSID',
            'timeout' => 60 * 60,
            'cookieParams' => [
                'secure' => true
            ]
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller>/<id:\d+>' => '<controller>',
                '<controller>/<action>/<id:\d+>' => '<controller>/<action>',
                '<controller>/index' => '<controller>',
            ],
        ],
    ],
    'as access' => [
        'class' => 'yii\filters\AccessControl',
        'rules' => [
            [
                'allow' => true,
                'controllers' => ['site'],
                'actions' => ['index'],
                'roles' => ['?'],
            ],
            /**
             *  Debug
             */
            [
                'allow' => true,
                'controllers' => ['debug', 'default', 'debug/default'],
            ],
            
            /**
             *  Usuarios logueados que no deben cambiar contraseña, están activos
             *  y tienen Token bueno
             */
            [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function () {
                    $usuario = Yii::$app->user->identity;
                    $token = Yii::$app->session->get('Token');
                    Yii::info('match callback');
                    return $usuario->DebeCambiarPass == 'N' && $usuario->Estado == 'A' && $usuario->Token == $token;
                },
            ],
        ],
        // Función que se ejecuta cuando el request es denegado.
        'denyCallback' => function ($rule, $action) {
            if (!Yii::$app->user->isGuest) {
                if (Yii::$app->user->identity->DebeCambiarPass == 'S') {
                    //Redirect
                    Yii::$app->user->returnUrl = Yii::$app->request->referrer;
                    return $action->controller->redirect('/usuarios/cambiar-password');
                } else {
                    Yii::$app->user->logout();
                    Yii::$app->session->setFlash('danger', 'Ocurrió un problema con su sesión.');
                    Yii::$app->user->returnUrl = Yii::$app->request->referrer;
                    return $action->controller->redirect(Yii::$app->user->loginUrl);
                }
            }
            return $action->controller->redirect(Yii::$app->user->loginUrl);
        },
    ],
    'params' => $params,
];
        
return $config;
