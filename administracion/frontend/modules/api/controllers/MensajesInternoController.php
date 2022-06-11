<?php

namespace frontend\modules\api\controllers;

use common\models\GestorMensajesInterno;
use common\components\FCMHelper;
use common\models\Casos;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;

class MensajesInternoController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bearerAuth' => [
                    'class' => OptionalBearerAuth::className(),
                    'except' => ['options'],
                    'actionsClient' => ['enviar-mensaje', 'listar-mensajes', 'nuevos-mensajes', 'update-mensajes', 'index']
                ],
            ]
        );
    }
    
    public function actionEnviarMensaje()
    {
        $Contenido = Yii::$app->request->post('Contenido');
        $IdCaso = Yii::$app->request->post('IdCaso');
        $Cliente = Yii::$app->request->post('Cliente');
        $URL = Yii::$app->request->post('URL');
        $TipoMult = Yii::$app->request->post('TipoMult');
        $IdUsuario = Yii::$app->user->identity->IdUsuario;

        $gestor = new GestorMensajesInterno;

        if (strpos($URL, 'https://io.docdoc.com.ar/api/multimedia?file=') !== false) {
            $URL = explode("=", $URL)[1];
        }

        $resultado = $gestor->AltaMensaje($Contenido, $IdCaso, $Cliente, $URL, $TipoMult, $IdUsuario);

        if ($Cliente === "N") {
            $caso = new Casos();
            $caso->IdCaso = $IdCaso;
            $caso->Dame();
            $personas = $caso->PersonasCaso;
            $tokens = array();

            foreach ($personas as $p) {
                if (!empty($p->TokenApp)) {
                    $tokens[] = $p->TokenApp;
                }
            }

            $respuesta = '';

            if (!empty($tokens)) {
                $respuesta = FCMHelper::enviarNotificacionPush(
                    [
                        'title' => 'Nuevo Mensaje',
                        'body' => "Tienes nuevos mensajes en uno de tus casos: {$caso->Caratula}."
                    ],
                    $tokens,
                    [
                        'tipo' => 'nuevoMensaje',
                        'id' => $caso->IdCaso
                    ],
                    'mult',
                    true
                );
            }
        }

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdMensajeChatInterno' => substr($resultado, 2),
                '$respuesta' => $personas
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionListarMensajes()
    {
        $IdCaso = Yii::$app->request->get('IdCaso');

        $gestor = new GestorMensajesInterno;

        return $gestor->ListarMensajes($IdCaso);
    }

    public function actionNuevosMensajes()
    {
        $IdCaso = Yii::$app->request->get('IdCaso');
        $Cliente = Yii::$app->request->get('Cliente');
        $IdUsuario = Yii::$app->user->identity->IdUsuario;

        $gestor = new GestorMensajesInterno;

        return $gestor->NuevosMensajes($IdCaso, $IdUsuario, $Cliente);
    }

    public function actionUpdateMensajes()
    {
        $IdCaso = Yii::$app->request->post('IdCaso');
        $Cliente = Yii::$app->request->post('Cliente');

        $gestor = new GestorMensajesInterno;

        $resultado = $gestor->UpdateMensajes($IdCaso, $Cliente);

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return ['Error' => $resultado];
        }
    }
}
