<?php

namespace frontend\modules\api\controllers;

use common\models\Empresa;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class EmpresaController extends BaseController
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
    
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $empresa = new Empresa();
        
        $empresa->DameParametro(Yii::$app->request->get('Parametro'));
        
        return $empresa;
    }

    public function actionPadron()
    {
        $cadena = Yii::$app->request->get('cadena');

        $empresa = new Empresa();

        return $empresa->BuscarPadron('T', $cadena);
    }
}
