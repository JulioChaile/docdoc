<?php

namespace estudios\controllers;

use yii\web\Controller;
use const YII_ENV_TEST;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    
    public function actionError()
    {
        return $this->render('error');
    }
    public function actionIndex()
    {
        return $this->render('index');
    }
}
