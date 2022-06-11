<?php
namespace frontend\modules\api\controllers;

use common\models\GestorCompetencias;
use common\models\Competencias;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class CompetenciasController extends BaseController
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
        
        $gestor = new GestorCompetencias();
        
        $Cadena = Yii::$app->request->get('Cadena');
        
        $IncluyeBajas = Yii::$app->request->get('IncluyeBajas');
        
        $competencias = $gestor->Buscar($Cadena, $IncluyeBajas);
        
        return $competencias;
    }

    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $competencia = new Competencias();
        
        $competencia->IdCompetencia = $id;
        
        $competencia->Dame();
        
        return $competencia;
    }
}
