<?php

namespace frontend\modules\api\controllers;

use common\models\Casos;
use common\models\Objetivos;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;

class ObjetivosController extends BaseController
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

    const TTL_OBJETIVOS = 10;
    
    public function actionIndex()
    {
        $IdsCaso = json_decode(Yii::$app->request->get('IdsCaso'));

        $consulta = function () use ($IdsCaso) {
            $objetivos = array();

            $caso = new Casos();

            foreach ($IdsCaso as $IdCaso) {
                $caso->IdCaso = $IdCaso;
                $objetivos[$IdCaso] = $caso->ListarObjetivos();
            }

            return $objetivos;
        };

        $key = serialize([self::class, $IdsCaso]);
                
        return Yii::$app->cache->getOrSet($key, $consulta, self::TTL_OBJETIVOS);
    }
    
    public function actionListar()
    {
        $IdsCaso = json_decode(Yii::$app->request->post('IdsCaso'));

        $consulta = function () use ($IdsCaso) {
            $objetivos = array();

            $caso = new Casos();

            foreach ($IdsCaso as $IdCaso) {
                $caso->IdCaso = $IdCaso;
                $objetivos[$IdCaso] = $caso->ListarObjetivos();
            }

            return $objetivos;
        };

        $key = serialize([self::class, $IdsCaso]);
                
        return Yii::$app->cache->getOrSet($key, $consulta, self::TTL_OBJETIVOS);
    }
    
    public function actionCreate()
    {
        $objetivo = new Objetivos();
        
        $objetivo->setAttributes(Yii::$app->request->post());
        
        $caso = new Casos();
        
        $caso->IdCaso = Yii::$app->request->post('IdCaso');
        
        $resultado = $caso->AltaObjetivo($objetivo);
        if (substr($resultado, 0, 2) == 'OK') {
            return ['Error' => null, 'IdObjetivo' => intval(substr($resultado, 2))];
        } else {
            return ['Error' => $resultado];
        }
    }
    
    public function actionUpdate($id)
    {
        $objetivo = new Objetivos();
        
        $objetivo->IdObjetivo = $id;
        
        $objetivo->setAttributes(Yii::$app->request->getBodyParams());

        Yii::info($objetivo);
        
        $caso = new Casos();
        
        $resultado = $caso->ModificarObjetivo($objetivo);
        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }
    
    public function actionView($id)
    {
        $objetivo = new Objetivos();
        
        $objetivo->IdObjetivo = $id;
        
        $objetivo->Dame();
        
        return $objetivo;
    }
    
    public function actionDelete($id)
    {
        $objetivo = new Objetivos();
        
        $objetivo->IdObjetivo = $id;
        
        $caso = new Casos();
        
        $resultado = $caso->BorrarObjetivo($objetivo);
        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }
}
