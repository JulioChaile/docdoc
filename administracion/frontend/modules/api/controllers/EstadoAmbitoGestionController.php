<?php

namespace frontend\modules\api\controllers;

use common\models\GestorEstadoAmbitoGestion;
use common\models\EstadoAmbitoGestion;
use common\models\Juzgados;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class EstadoAmbitoGestionController extends BaseController
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

    const TTL_EstadoAmbitoGestion = 60;
    
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $consulta = function () {
            $gestor = new GestorEstadoAmbitoGestion();
            $EstadoAmbitoGestion = $gestor->Buscar();
            return $EstadoAmbitoGestion;
        };

        $key = serialize([self::class]);
        
        return Yii::$app->cache->getOrSet($key, $consulta, self::TTL_EstadoAmbitoGestion);
    }

    public function actionEstadosJuzgado($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $juzgado = new Juzgados();
        
        $juzgado->IdJuzgado = $id;
        
        $juzgado->Dame();
        
        return $juzgado;
    }
}
