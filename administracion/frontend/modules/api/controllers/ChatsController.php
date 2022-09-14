<?php
namespace frontend\modules\api\controllers;

use common\components\ChatApi;
use common\models\GestorChatApi;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;

class ChatsController extends BaseController
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

    // Permite crear un nuevo chat
    public function actionCreate()
    {
        $idCaso = Yii::$app->request->post('IdCaso');
        $idPersona = Yii::$app->request->post('IdPersona');
        $telefono = Yii::$app->request->post('Telefono');
        $idMediador = Yii::$app->request->post('IdMediador');
        $idContacto = Yii::$app->request->post('IdContacto');
        if ($idMediador == 0 || $idMediador == '' || $idMediador == 'null') {
            $idMediador = null;
        }
        if ($idContacto == 0 || $idContacto == '' || $idContacto == 'null') {
            $idContacto = null;
        }

        return empty($idContacto)
            ? Yii::$app->chatapi->nuevoChat($telefono, $idCaso, $idPersona, $idMediador)
            : Yii::$app->chatapi->nuevoChatContacto($telefono, $idContacto);
    }

    // Permite actualizar el IdUltimoMensajeLeido de un Chat
    public function actionUpdate($id)
    {
        $gestor = new GestorChatApi();

        $idUltimoMensaje = Yii::$app->request->post('IdUltimoLeido');
        $mediador = Yii::$app->request->post('mediador');
        $contacto = Yii::$app->request->post('contacto');
        if ($mediador == 0 || $mediador == '' || $mediador == 'null') {
            $mediador = null;
        }
        if ($contacto == 0 || $contacto == '' || $contacto == 'null') {
            $contacto = null;
        }

        $respuesta = empty($mediador)
            ? (
                empty($contacto)
                    ? $gestor->ModificarUltMsjLeido($id, $idUltimoMensaje)
                    : $gestor->ModificarUltMsjLeidoContacto($id, $idUltimoMensaje)
            )
            : $gestor->ModificarUltMsjLeidoMediador($id, $idUltimoMensaje);

            if ($respuesta == 'OK') {
                return [
                  'Error' => null
                ];
            } else {
                return ['Error' => $respuesta];
            }
    }

    // Permite obtener los datos de un chat
    public function actionView($id)
    {
        $mediador = Yii::$app->request->get('mediador');
        $contacto = Yii::$app->request->get('contacto');
        if ($mediador == 0 || $mediador == '' || $mediador == 'null') {
            $mediador = null;
        }
        if ($contacto == 0 || $contacto == '' || $contacto == 'null') {
            $contacto = null;
        }

        $gestor = new GestorChatApi();

        $respuesta = empty($mediador)
            ? (
                empty($contacto)
                ? $gestor->DameChat($id, null)
                : $gestor->DameChatContacto($id, null)
            )
            : $gestor->DameChatMediador($id, null);

        if (array_key_exists('Mensaje', $respuesta)) {
            return ['Error' => $respuesta['Mensaje']];
        }

        return $respuesta;
    }

    // Permite cambiar el telefono de un chat
    public function actionUpdateTelefono($id)
    {
        $Telefono = Yii::$app->request->post('Telefono');
        $IdPersona = Yii::$app->request->post('IdPersona');

        $phone = Yii::$app->chatapi->readChatUrl($Telefono);

        if ($phone === 'not exists') {
            return ['Error' => 'Number not exists'];
        }

        $IdExternoChat = $phone.'@c.us';

        $gestor = new GestorChatApi();

        $respuesta = $gestor->ModificarTelefono($id, $Telefono, $IdExternoChat, $IdPersona);

        if (substr($respuesta, 0, 2) == 'OK') {
            return [
              'Error' => null,
              'Telefono' => substr($respuesta, 2)
            ];
        } else {
            return ['Error' => $respuesta];
        }
    }

    public function actionReemplazarCaso()
    {
        $IdExternoChat = Yii::$app->request->post('IdExternoChat');
        $IdCaso = Yii::$app->request->post('IdCaso');
        $IdPersona = Yii::$app->request->post('IdPersona');

        $gestor = new GestorChatApi();

        $respuesta = $gestor->ReemplazarCaso($IdExternoChat, $IdCaso, $IdPersona);

        if (substr($respuesta, 0, 2) == 'OK') {
            return [
              'Error' => null,
              'IdChat' => substr($respuesta, 2)
            ];
        } else {
            return ['Error' => $respuesta];
        }
    }
}
