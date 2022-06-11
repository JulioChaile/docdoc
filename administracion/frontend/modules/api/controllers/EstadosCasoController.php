<?php

namespace frontend\modules\api\controllers;

use common\models\EstadosCaso;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class EstadosCasoController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bearerAuth' => [
                    'class' => OptionalBearerAuth::className(),
                    'except' => ['options'],
                ],
            ]
        );
    }
    
    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $estadoCaso = new EstadosCaso();
        
        $estadoCaso->IdEstadoCaso = $id;
        
        $estadoCaso->Dame();
        
        return $estadoCaso;
    }
}
