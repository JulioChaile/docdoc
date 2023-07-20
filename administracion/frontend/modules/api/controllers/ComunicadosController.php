<?php

namespace frontend\modules\api\controllers;

use common\models\Comunicados;
use common\models\GestorComunicados;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;

class ComunicadosController extends BaseController
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

    public function actionEditarComunicado()
    {
        $comunicado = new Comunicados();

        $Titulo = Yii::$app->request->post('Titulo');
        $Contenido = Yii::$app->request->post('Contenido');
        $IdComunicado = Yii::$app->request->post('IdComunicado');
        
        $comunicado->Titulo = $Titulo;
        $comunicado->IdComunicado = $IdComunicado;
        $comunicado->Contenido = $Contenido;
        $comunicado->IdEstudio = Yii::$app->user->identity->IdEstudio;

        $gestor = new GestorComunicados();

        $resultado = $gestor->Modificar($comunicado);

        if ($resultado == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionAltaComunicado()
    {
        $comunicado = new Comunicados();

        $Titulo = Yii::$app->request->post('Titulo');
        $Contenido = Yii::$app->request->post('Contenido');
        $URL = Yii::$app->request->post('URL');
        $Tipo = Yii::$app->request->post('Tipo');
        $Nombre = Yii::$app->request->post('Nombre');
        
        $comunicado->Titulo = $Titulo;
        $comunicado->Contenido = $Contenido;
        $comunicado->IdEstudio = Yii::$app->user->identity->IdEstudio;

        $IdMultimedia = null;

        if (!empty($URL)) {
            $sql = "SELECT COALESCE(MAX(IdMultimedia), 0) + 1 FROM Multimedia";

            $query = Yii::$app->db->createCommand($sql);
            
            $IdMultimedia = $query->queryScalar();

            
            $sql = "INSERT INTO Multimedia VALUES (" . $IdMultimedia . ", '" . $URL . "', NOW(), '" . $Tipo . "', '" . $Nombre . "')";

            $query = Yii::$app->db->createCommand($sql);
            
            $query->execute();

            $comunicado->IdMultimedia = $IdMultimedia;
        }

        $gestor = new GestorComunicados();

        $resultado = $gestor->Alta($comunicado);

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'comunicado' => Yii::$app->request->post(),
                'IdComunicado' => substr($resultado, 2)
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionBorrar()
    {
        $comunicado = new Comunicados();

        $comunicado->IdComunicado = Yii::$app->request->post('IdComunicado');

        $gestor = new GestorComunicados;
        
        $resultado = $gestor->Borrar($comunicado);

        if ($resultado == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionListar()
    {
        $offset = Yii::$app->request->get('offset');
        $fechahoy = Yii::$app->request->get('fechahoy');
        $IdEstudio = Yii::$app->user->identity->IdEstudio;

        $gestor = new GestorComunicados();
        
        return $gestor->Listar($IdEstudio, $offset, $fechahoy);
    }
}
