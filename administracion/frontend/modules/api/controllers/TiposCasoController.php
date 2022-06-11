<?php
namespace frontend\modules\api\controllers;

use common\models\GestorTiposCaso;
use common\models\TiposCaso;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class TiposCasoController extends BaseController
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
        
        $gestor = new GestorTiposCaso();
        
        $Cadena = Yii::$app->request->get('Cadena');
        
        $IncluyeBajas = Yii::$app->request->get('IncluyeBajas');
        
        $tiposCaso = $gestor->Buscar($Cadena, $IncluyeBajas);
        
        return $tiposCaso;
    }
    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $tipoCaso = new TiposCaso();
        
        $tipoCaso->IdTipoCaso = $id;
        
        $tipoCaso->Dame();
        
        return $tipoCaso;
    }
    
    public function actionRoles($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $tipoCaso = new TiposCaso();
        
        $tipoCaso->IdTipoCaso = $id;
        
        $roles = $tipoCaso->ListarRoles();
        
        return $roles;
    }

    public function actionJuzgados($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $tipoCaso = new TiposCaso();
        
        $tipoCaso->IdTipoCaso = $id;

        $juzgados = $tipoCaso->ListarJuzgados($id);

        return $juzgados;
    }
}
