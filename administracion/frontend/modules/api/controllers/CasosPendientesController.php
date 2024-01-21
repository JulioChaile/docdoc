<?php
namespace frontend\modules\api\controllers;

use common\models\GestorCasosPendientes;
use common\models\GestorCasos;
use common\models\CasosPendientes;
use common\models\Personas;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class CasosPendientesController extends BaseController
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
        $gestor = new GestorCasosPendientes();
        
        $respuesta = $gestor->Buscar(
            Yii::$app->user->identity->IdEstudio,
            Yii::$app->request->get('Cadena'),
            Yii::$app->request->get('Documento'),
            Yii::$app->request->get('Telefono'),
            Yii::$app->request->get('Offset'),
            Yii::$app->request->get('Limit'),
            Yii::$app->request->get('Visitado'),
            Yii::$app->request->get('Estados'),
            Yii::$app->request->get('FechasAlta'),
            Yii::$app->request->get('FechasVisitado'),
            Yii::$app->request->get('Cadete'),
            Yii::$app->request->get('Finalizo'),
            Yii::$app->request->get('Origenes'),
            Yii::$app->request->get('Fecha'),
            Yii::$app->request->get('FechaDesde'),
            Yii::$app->request->get('FechaHasta')
        );

        return $respuesta;
    }

    public function actionBuscarCaso() {
        $Documento = Yii::$app->request->get('Documento');

        $gestorCP = new GestorCasosPendientes();
        $gestorC = new GestorCasos();
        $persona = new Personas();

        $casosPendientes = $gestorCP->Buscar(
            Yii::$app->user->identity->IdEstudio,
            '',
            $Documento,
            0
        );

        $casos = $gestorC->Buscar(
            Yii::$app->user->identity->IdEstudio,
            Yii::$app->user->identity->IdUsuario,
            'P',
            $Documento,
            0
        );

        $padron = $persona->Padron($Documento);

        return [
            'CasosPendientes' => $casosPendientes,
            'CasosActivos' => $casos,
            'PersonaPadron' => $padron
        ];
    }

    public function actionAlta()
    {
        $casos = json_decode(Yii::$app->request->post('casos'), true);

        $CasosPendientes = new CasosPendientes();

        $gestor = new GestorCasosPendientes();

        $errores = array();

        foreach ($casos as $c) {
            $CasosPendientes->setAttributes($c, false);

            $CasosPendientes->IdEstudio = Yii::$app->user->identity->IdEstudio;

            $respuesta = $gestor->Alta($CasosPendientes);

            if ($respuesta !== 'OK') {
                $errores[] = [
                    'persona' => $c['Apellidos'] . ', ' . $c['Nombres'],
                    'error' => $respuesta
                ];
            }
        }

        return $errores;
    }

    public function actionAltaCasoActivo()
    {
        $caso = json_decode(Yii::$app->request->post('caso'), true);

        $CasosPendientes = new CasosPendientes();

        $gestor = new GestorCasosPendientes();

        $CasosPendientes->setAttributes($caso, false);

        $CasosPendientes->IdEstudio = Yii::$app->user->identity->IdEstudio;

        $respuesta = $gestor->AltaActivo($CasosPendientes);

        if (substr($respuesta, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdCaso' => substr($respuesta, 2)
            ];
        } else {
            return ['Error' => $respuesta];
        }
    }

    public function actionEditar()
    {
        $caso = json_decode(Yii::$app->request->post('caso'), true);

        $CasosPendientes = new CasosPendientes();
        $gestor = new GestorCasosPendientes();

        $CasosPendientes->setAttributes($caso, false);
        $CasosPendientes->IdEstudio = Yii::$app->user->identity->IdEstudio;

        $respuesta = $gestor->Editar($CasosPendientes);

        if ($respuesta !== 'OK') {
            return [
                'Error' => $respuesta
            ];
        } else {
            return [
                'Error' => null
            ];
        }
    }

    public function actionEliminar()
    {
        $caso = new CasosPendientes();

        $caso->IdEstudio = Yii::$app->user->identity->IdEstudio;
        $caso->IdCasoPendiente = Yii::$app->request->post('id');
        $caso->IdCaso = Yii::$app->request->post('idCaso');

        $gestor = new GestorCasosPendientes;
        $gestorC = new GestorCasos();
        
        $resultado = $gestor->Borrar($caso);
        $resultadoC = $gestorC->Borrar($caso);

        if ($resultado !== 'OK') {
            return [
                'Error' => $resultado
            ];
        } else if ($resultadoC !== 'OK') {
            return ['Error' => $resultado];
        } else {
            return ['Error' => null];
        }
    }

    public function actionActivarCaso()
    {
        $caso = new CasosPendientes();

        $caso->IdEstudio = Yii::$app->user->identity->IdEstudio;
        $caso->IdCasoPendiente = Yii::$app->request->post('id');

        $gestor = new GestorCasosPendientes;
        $resultado = $gestor->Borrar($caso);

        if ($resultado !== 'OK') {
            return [
                'Error' => $resultado
            ];
        } else {
            return ['Error' => null];
        }
    }

    public function actionEstados()
    {
        $gestor = new GestorCasosPendientes();

        return $gestor->ListarEstados();
    }

    public function actionSimilitudes()
    {
        $Latitud = Yii::$app->request->get('Latitud');
        $Longitud = Yii::$app->request->get('Longitud');
        $Distancia = Yii::$app->request->get('Distancia');
        $Domicilio = Yii::$app->request->get('Domicilio');


        $gestor = new GestorCasosPendientes();

        $Ubicaciones = $gestor->UbicacionesCercanas($Latitud, $Longitud, $Distancia);
        $Domicilios = $gestor->UbicacionesSimilares($Domicilio);

        $sql = 'SELECT	p.*, tp.Telefono
                FROM    Personas p
                INNER JOIN TelefonosPersona tp USING(IdPersona)
                WHERE   MATCH(p.Domicilio) AGAINST(:domicilio) AND tp.EsPrincipal = "S";';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':domicilio' => $Domicilio
        ]);

        $DomiciliosPersonas = $query->queryAll();

        return [
            'Ubicaciones' => $Ubicaciones,
            'Domicilios' => $Domicilios,
            'DomiciliosPersonas' => $DomiciliosPersonas
        ];
    }
}
