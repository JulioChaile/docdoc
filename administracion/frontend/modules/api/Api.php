<?php

namespace frontend\modules\api;

use Yii;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\filters\RateLimiter;
use common\models\IPRateLimiter;

class Api extends Module
{
    public $controllerNamespace = 'frontend\modules\api\controllers';

    public function init()
    {
        Yii::$app->id = 'frtnd-docdoc-api';
        Yii::$app->user->loginUrl = null;
        Yii::$app->errorHandler->errorAction = null;
        
        Yii::$app->attachBehavior('access', [
            'class' => AccessControl::className(),
            'rules' => [
                    [
                    'allow' => true,
                    'roles' => ['?', '@'],
                ],
            ],
        ]);
        
        parent::init();
    }

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => ['https://app.docdoc.com.ar', 'https://notificaciones.docdoc.com.ar', 'http://localhost:8080', 'http://localhost:8000', 'https://www.docdoc.com.ar', 'https://192.168.100.11:8080', 'http://192.168.100.11:8080'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Request-Method' => ['GET', 'HEAD', 'DELETE', 'POST', 'PATCH', 'OPTIONS', 'PUT'],
                    'Access-Control-Request-Headers' => ['Authorization', 'Content-Type'],
                    'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
                ],
            ],
            'rateLimiterConBaneo' => [
                'class' => RateLimiter::className(),
                'user' => new IPRateLimiter(1000, true)
            ],
            'rateLimiter' => [
                'class' => RateLimiter::className(),
                'user' => new IPRateLimiter
            ]
        ];
    }
}
