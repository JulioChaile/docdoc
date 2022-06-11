<?php

namespace frontend\modules\api\controllers;

use common\models\GestorPlantillas;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;

class PlantillasController extends BaseController
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
        $IdEstudio = Yii::$app->request->get('IdEstudio');

        $gestor = new GestorPlantillas();

        return [
            'Plantillas' => $gestor->ListarPlantillas($IdEstudio),
            'Carpetas' => $gestor->ListarCarpetas($IdEstudio)
        ];

        // return $gestor->ListarPlantillas($IdEstudio);
    }

    public function actionAlta()
    {
        $IdEstudio = Yii::$app->request->post('IdEstudio');
        $Nombre = Yii::$app->request->post('Nombre');
        $Plantilla = Yii::$app->request->post('Plantilla');
        $Actores = Yii::$app->request->post('Actores');
        $Demandados = Yii::$app->request->post('Demandados');
        $IdCarpetaPadre = Yii::$app->request->post('IdCarpetaPadre');

        if (empty($IdCarpetaPadre)) {
            $IdCarpetaPadre = null;
        }

        $gestor = new GestorPlantillas();

        $resultado = $gestor->AltaPlantilla($IdEstudio, $Nombre, $Plantilla, $Actores, $Demandados, $IdCarpetaPadre);

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdPlantilla' => substr($resultado, 2)
            ];
        } else {
            return [
                'Error' => $resultado
            ];
        }
    }

    public function actionAltaCarpeta()
    {
        $IdEstudio = Yii::$app->request->post('IdEstudio');
        $Nombre = Yii::$app->request->post('Nombre');
        $IdCarpetaPadre = Yii::$app->request->post('IdCarpetaPadre');

        if (empty($IdCarpetaPadre)) {
            $IdCarpetaPadre = null;
        }

        $gestor = new GestorPlantillas();

        $resultado = $gestor->AltaCarpeta($IdEstudio, $Nombre, $IdCarpetaPadre);

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdCarpetaPlantilla' => substr($resultado, 2)
            ];
        } else {
            return [
                'Error' => $resultado
            ];
        }
    }

    public function actionEliminarCarpeta()
    {
        $IdCarpeta = Yii::$app->request->post('id');

        $gestor = new GestorPlantillas();

        $resultado = $gestor->EliminarCarpeta($IdCarpeta);

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

    public function actionEliminar()
    {
        $IdPlantilla = Yii::$app->request->post('id');

        $gestor = new GestorPlantillas();

        $resultado = $gestor->EliminarPlantilla($IdPlantilla);

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

    public function actionEditar()
    {
        $IdEstudio = Yii::$app->request->post('IdEstudio');
        $IdPlantilla = Yii::$app->request->post('IdPlantilla');
        $Nombre = Yii::$app->request->post('Nombre');
        $Plantilla = Yii::$app->request->post('Plantilla');
        $Actores = Yii::$app->request->post('Actores');
        $Demandados = Yii::$app->request->post('Demandados');

        $gestor = new GestorPlantillas();

        $resultado = $gestor->ModificarPlantilla($IdEstudio, $IdPlantilla, $Nombre, $Plantilla, $Actores, $Demandados);

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

    public function actionEditarCarpeta()
    {
        $IdEstudio = Yii::$app->request->post('IdEstudio');
        $IdCarpetaPlantilla = Yii::$app->request->post('IdCarpetaPlantilla');
        $Nombre = Yii::$app->request->post('Nombre');

        $gestor = new GestorPlantillas();

        $resultado = $gestor->ModificarCarpetaPlantilla($IdEstudio, $IdCarpetaPlantilla, $Nombre);

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

    public function actionMoverElementos()
    {
        $IdCarpetaPadre = Yii::$app->request->post('IdCarpetaPadre');
        $Plantillas = Yii::$app->request->post('Plantillas');
        $Carpetas = Yii::$app->request->post('Carpetas');

        if (empty($IdCarpetaPadre)) {
            $IdCarpetaPadre = null;
        }

        foreach ($Plantillas as $p) {
            $sql = 'UPDATE  PlantillasEstudio
                    SET     IdCarpetaPadre = :idCarpetaPadre
                    WHERE   `IdPlantilla` = :id';

            $query = Yii::$app->db->createCommand($sql);

            $query->bindValues([
                ':idCarpetaPadre' => $IdCarpetaPadre,
                ':id' => $p
            ]);

            $query->execute();
        }

        foreach ($Carpetas as $p) {
            $sql = 'UPDATE  CarpetasPlantillasEstudio
                    SET     IdCarpetaPadre = :idCarpetaPadre
                    WHERE   `IdCarpetaPlantilla` = :id';

            $query = Yii::$app->db->createCommand($sql);

            $query->bindValues([
                ':idCarpetaPadre' => $IdCarpetaPadre,
                ':id' => $p
            ]);

            $query->execute();
        }

        return ['Error' => null];
    }
}
