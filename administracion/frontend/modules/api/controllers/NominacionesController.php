<?php

namespace frontend\modules\api\controllers;

use backend\models\Nominaciones;
use common\models\Juzgados;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class NominacionesController extends BaseController
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

    const TTL_NOMINACIONES = 60;
    
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $IdsJuzgado = json_decode(Yii::$app->request->get('IdsJuzgado'));
        
        $Cadena = Yii::$app->request->get('Cadena');
        
        $IncluyeBajas= Yii::$app->request->get('IncluyeBajas');

        $consulta = function () use ($IdsJuzgado, $Cadena, $IncluyeBajas) {
            $juzgado = new Juzgados();

            $nominaciones = array();

            foreach ($IdsJuzgado as $IdJuzgado) {
                $juzgado->IdJuzgado = $IdJuzgado;
            
                $nominaciones[$IdJuzgado] = $juzgado->BuscarNominaciones($Cadena, $IncluyeBajas);
            }
            
            return $nominaciones;
        };

        $key = serialize([self::class, $IdsJuzgado, $Cadena, $IncluyeBajas]);

        return Yii::$app->cache->getOrSet($key, $consulta, self::TTL_NOMINACIONES);
    }
    
    public function actionView($id)
    {
        $nominacion = new Nominaciones();
        
        $nominacion->IdNominacion = $id;
        
        $nominacion->Dame();
        
        return $nominacion;
    }
}
