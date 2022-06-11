<?php

namespace frontend\modules\api\controllers;

use common\models\GestorJurisdicciones;
use common\models\Jurisdicciones;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class JurisdiccionesController extends BaseController
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
        
        $gestor = new GestorJurisdicciones();
        
        $Cadena = Yii::$app->request->get('Cadena');
        
        $IncluyeBajas = Yii::$app->request->get('IncluyeBajas');
        
        $jurisdicciones = $gestor->Buscar($Cadena, $IncluyeBajas);
        
        return $jurisdicciones;
    }
    
    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $jurisdiccion = new Jurisdicciones();
        
        $jurisdiccion->IdJurisdiccion = $id;
        
        $jurisdiccion->Dame();
        
        return $jurisdiccion;
    }
}
