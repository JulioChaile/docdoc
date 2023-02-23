<?php
namespace frontend\modules\api\controllers;

use common\models\Origenes;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use yii\helpers\ArrayHelper;
use Yii;

class OrigenesController extends BaseController
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
        // Yii::$app->response->format = Response::FORMAT_JSON;

        Yii::$app->response->format = 'json';

        $IdEstudio = Yii::$app->user->identity->IdEstudio;

        $origenes = new Origenes();

        return $origenes -> ListarOrigenes($IdEstudio);
    }

    public function actionView($id)
    {
        $origen = new Origenes();
        
        $origen->IdOrigen = $id;
        
        $origen->Dame();
        
        return $origen;
    }
}
