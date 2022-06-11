<?php

namespace frontend\modules\api\controllers;

use common\models\Contactos;
use common\models\GestorContactos;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;

class ContactosController extends BaseController
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
        $IdContacto = Yii::$app->request->get('id');

        $contacto = new Contactos();
        $contacto->IdContacto = $IdContacto;

        $contacto->Dame();

        return $contacto;
    }

    public function actionEditarContacto()
    {
        $contacto = new Contactos();
        
        $contacto->setAttributes(Yii::$app->request->post());
        $contacto->IdEstudio = Yii::$app->user->identity->IdEstudio;

        $gestor = new GestorContactos;

        $resultado = $gestor->Modificar($contacto);

        if ($resultado == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionAltaContacto()
    {
        $contacto = new Contactos();
        
        $contacto->setAttributes(Yii::$app->request->post());
        $contacto->IdEstudio = Yii::$app->user->identity->IdEstudio;

        $gestor = new GestorContactos;

        $resultado = $gestor->Alta($contacto);

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdContacto' => substr($resultado, 2)
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionContarContactos()
    {
        $Tipo = Yii::$app->request->get('Tipo');
        $Cadena = Yii::$app->request->get('Cadena');
        $IdEstudio = Yii::$app->user->identity->IdEstudio;

        $gestor = new GestorContactos;

        return $gestor->Contar($IdEstudio, $Cadena, $Tipo);
    }

    public function actionBorrarContacto()
    {
        $contacto = new Contactos();

        $contacto->IdEstudio = Yii::$app->user->identity->IdEstudio;
        $contacto->IdContacto = Yii::$app->request->post('IdContacto');

        $gestor = new GestorContactos;
        
        $resultado = $gestor->Borrar($contacto);

        if ($resultado == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionBuscarContacto()
    {
        $cadena = Yii::$app->request->get('cadena');
        $tipo = Yii::$app->request->get('tipo');
        $limit = Yii::$app->request->get('limit');
        $offset = Yii::$app->request->get('offset');
        $IdEstudio = Yii::$app->user->identity->IdEstudio;

        $gestor = new GestorContactos;
        
        return $gestor->Buscar($IdEstudio, $cadena, $tipo, $limit, $offset);
    }

    public function actionAltaMultimedia()
    {
        $IdContacto = Yii::$app->request->post('IdContacto');
        $Multimedia = Yii::$app->request->post('Multimedia');

        $Contacto = new Contactos();
        $Contacto->IdContacto = $IdContacto;

        $resultado = $Contacto->AltaMultimedia($Multimedia);

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return [
                'Error' => $resultado
            ];
        }
    }
}
