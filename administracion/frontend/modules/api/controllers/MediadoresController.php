<?php

namespace frontend\modules\api\controllers;

use common\models\Mediadores;
use common\models\GestorMediadores;
use common\models\GestorChatApi;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;

class MediadoresController extends BaseController
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
        $IdMediador = Yii::$app->request->get('id');

        $mediador = new Mediadores();
        $mediador->IdMediador = $IdMediador;

        $mediador->Dame();

        return $mediador;
    }

    public function actionAltaContacto()
    {
        $Nombres = Yii::$app->request->post('Nombres');
        $Registro = Yii::$app->request->post('Registro');
        $MP = Yii::$app->request->post('MP');
        $Telefono = Yii::$app->request->post('Telefono');
        $Email = Yii::$app->request->post('Email');

        $mediador = new Mediadores();
        $mediador->Nombre = $Nombres;
        $mediador->Registro = $Registro;
        $mediador->MP = $MP;
        $mediador->Telefono = $Telefono;
        $mediador->Email = $Email;

        $gestor = new GestorMediadores;

        $resultado = $gestor->Alta($mediador);

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdMediador' => substr($resultado, 2)
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionEditarContacto()
    {
        $Nombres = Yii::$app->request->post('Nombres');
        $Registro = Yii::$app->request->post('Registro');
        $MP = Yii::$app->request->post('MP');
        $Telefono = Yii::$app->request->post('Telefono');
        $Email = Yii::$app->request->post('Email');
        $IdMediador = Yii::$app->request->post('id');

        $mediador = new Mediadores();
        $mediador->IdMediador = $IdMediador;

        $mediador->Dame();

        $mediador->Nombre = empty($Nombres) ? $mediador->Nombre : $Nombre;
        $mediador->Registro = empty($Registro) ? $mediador->Registro : $Registro;
        $mediador->MP = empty($MP) ? $mediador->MP : $MP;
        $mediador->Email = $Email;

        if ($mediador->Telefono !== $Telefono && !empty($mediador->IdChatMediador)) {
            $url = Yii::$app->chatapi->readChatUrl($Telefono);

            if ($url == 'not exists') {
                return ['Error' => 'No existe el número ingresado.'];
            }

            $result = @file_get_contents($url);

            $respuesta = json_decode($result, true);

            if ($respuesta !== null && !array_key_exists('chatId', $respuesta)) {
                return ['Error' => $respuesta['message']];
            }

            $IdExternoChat = $respuesta['chatId'];

            $gestorChatApi = new GestorChatApi();

            $resultado = $gestorChatApi->ModificarTelefonoMediador($mediador->IdChatMediador, $Telefono, $IdExternoChat);

            if (substr($resultado, 0, 2) !== 'OK') {
                return ['Error' => $resultado];
            }
        }

        $mediador->Telefono = $Telefono;

        $gestor = new GestorMediadores;

        $resultado = $gestor->Modificar($mediador);

        if ($resultado == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionBorrarContacto()
    {
        $mediador = new Mediadores();

        $mediador->IdMediador = Yii::$app->request->post('IdMediador');

        $gestor = new GestorMediadores;
        
        $resultado = $gestor->Borrar($mediador);

        if ($resultado == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionContarMediadores()
    {
        $Cadena = Yii::$app->request->get('Cadena');

        $gestor = new GestorMediadores;

        return $gestor->Contar($Cadena);
    }
}
