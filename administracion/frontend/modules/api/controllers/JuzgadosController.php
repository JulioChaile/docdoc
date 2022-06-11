<?php

namespace frontend\modules\api\controllers;

use common\models\GestorJuzgados;
use common\models\Juzgados;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class JuzgadosController extends BaseController
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

    const TTL_JUZGADOS = 60;
    
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $IdJurisdiccion = Yii::$app->request->get('IdJurisdiccion');
        
        $Cadena = Yii::$app->request->get('Cadena');
        
        $IncluyeBajas= Yii::$app->request->get('IncluyeBajas');
        
        $consulta = function () use ($IdJurisdiccion, $Cadena, $IncluyeBajas) {
            $gestor = new GestorJuzgados();
            $IdJurisdiccion = 1;
            $juzgados = $gestor->Buscar($IdJurisdiccion, $Cadena, $IncluyeBajas);
            return $juzgados;
        };

        $key = serialize([self::class, $IdJurisdiccion, $Cadena, $IncluyeBajas]);
        
        return Yii::$app->cache->getOrSet($key, $consulta, self::TTL_JUZGADOS);
    }
    
    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $juzgado = new Juzgados();
        
        $juzgado->IdJuzgado = $id;
        
        $juzgado->Dame();
        
        return $juzgado;
    }

    public function actionEstados()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $juzgado = new Juzgados();
        
        return $juzgado->Estados();
    }
}
