<?php
namespace frontend\modules\api\controllers;

use common\models\RolesTipoCaso;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class RolesTipoCasoController extends BaseController
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
        
        $rtc = new RolesTipoCaso();
        
        $rtc->IdRTC = $id;
        
        $rtc->Dame();
        
        return $rtc;
    }
}
