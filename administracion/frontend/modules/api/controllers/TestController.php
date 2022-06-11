<?php
namespace frontend\modules\api\controllers;

use frontend\modules\api\filters\auth\OptionalBearerAuth;
use console\controllers\NotificacionesController;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class TestController extends BaseController
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

    public function actionTest ()
    {
        $consoleController = new NotificacionesController('notificaciones', Yii::$app);
        return $consoleController->runAction('test');
    }

    public function actionIndex ()
    {
        $offset = Yii::$app->request->get('offset');

        $sql = 'SELECT 	`ID-CASO`, DOMICILIO, domicilioLatitud, domicilioLongitud
                FROM 	casos
                WHERE	ESTADO != 4 AND
                        (domicilioLatitud IS NULL OR domicilioLatitud = "") AND
                        CIUDAD LIKE "%san miguel%" AND
                        DOMICILIO IS NOT NULL AND
                        DOMICILIO != ""
                LIMIT   :offset, 100';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':offset' => intval($offset)
        ]);
        
        return $query->queryAll();
    }

    public function actionUpdate ()
    {
        $id = Yii::$app->request->post('id');
        $latitud = Yii::$app->request->post('latitud');
        $longitud = Yii::$app->request->post('longitud');

        $sql = 'UPDATE  casos
                SET     domicilioLatitud = :latitud,
                        domicilioLongitud = :longitud
                WHERE   `ID-CASO` = :id';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':latitud' => $latitud,
            ':longitud' => $longitud,
            ':id' => $id
        ]);

        return $query->execute();
    }
}
