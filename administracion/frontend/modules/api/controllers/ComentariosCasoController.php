<?php
namespace frontend\modules\api\controllers;

use common\models\GestorComentariosCaso;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class ComentariosCasoController extends BaseController
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
        $gestor = new GestorComentariosCaso();
        
        $IdCaso = Yii::$app->request->get('IdCaso');

        return $gestor->ListarComentarios($IdCaso);
    }

    public function actionAlta()
    {
        $IdsUsuarios = json_decode(Yii::$app->request->post('IdsUsuarios'), true);
        $Comentario = Yii::$app->request->post('Comentario');
        $IdCaso = Yii::$app->request->post('IdCaso');

        $gestor = new GestorComentariosCaso();

        $respuestaComentario = $gestor->Alta($Comentario, $IdCaso);

        $errores = array();

        if (substr($respuestaComentario, 0, 2) == 'OK') {
            $IdComentarioCaso = substr($respuestaComentario, 2);

            foreach ($IdsUsuarios as $id) {
                $respuestaUsuario = $gestor->AltaUsuarioComentario($IdComentarioCaso, $IdCaso, $id);

                if ($respuestaUsuario !== 'OK') {
                    $errores[] = $id;
                }
            }
        } else {
            return ['Error' => $respuestaComentario];
        }

        return $errores;
    }

    public function actionComentariosSinLeer()
    {
        $gestor = new GestorComentariosCaso();

        return $gestor->ListarComentariosSinLeer();
    }

    public function actionComentarioVisto()
    {
        $IdCaso = Yii::$app->request->post('IdCaso');

        $gestor = new GestorComentariosCaso();

        $respuesta = $gestor->SetFechaVistoComentario($IdCaso);

        if ($respuesta !== 'OK') {
            return [
                'Error' => $respuesta
            ];
        } else {
            return ['Error' => null];
        }
    }
}
