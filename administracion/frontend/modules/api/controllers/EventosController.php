<?php
namespace frontend\modules\api\controllers;

use common\components\Calendar;
use common\models\GestorEventos;
use common\models\Estudios;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;

class EventosController extends BaseController
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

    public function actionCreate() {
        $IdCalendario = Yii::$app->request->post('IdCalendario');
        $IdCalendarioAPI = Yii::$app->request->post('IdCalendarioAPI');
        $Titulo = Yii::$app->request->post('Titulo');
        $Descripcion = Yii::$app->request->post('Descripcion');
        $Comienzo = Yii::$app->request->post('Comienzo');
        $ComienzoDb = Yii::$app->request->post('ComienzoDb');
        $Fin = Yii::$app->request->post('Fin');
        $FinDb = Yii::$app->request->post('FinDb');
        $IdColor = Yii::$app->request->post('IdColor');

        $estudio = new Estudios;
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;

        $usuarios = $estudio->BuscarUsuarios();

        $Invitados = array();

        foreach ($usuarios as $user) {
            $Invitados[] = $user['Email'];
        }

        $Calendar = new Calendar('contacto@docdoc.com.ar');

        $respuesta = $Calendar->insertEvent($Titulo, $Descripcion, $Invitados, $Comienzo, $Fin, $IdCalendarioAPI, $IdColor, '');

        if (array_key_exists('Error', $respuesta)) {
            return $respuesta;
        }

        $evento = new GestorEventos();

        $resultado = $evento->AltaEvento($IdCalendario, $respuesta['Id'], $Titulo, $Descripcion, $ComienzoDb, $FinDb, $IdColor);

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdEvento' => substr($resultado, 2)
            ];
        } else {
            return ['Error' => $resultado];
        }
    }
}