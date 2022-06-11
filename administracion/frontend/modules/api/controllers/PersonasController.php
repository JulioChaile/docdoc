<?php
namespace frontend\modules\api\controllers;

use common\models\Personas;
use common\models\Casos;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use yii\helpers\ArrayHelper;
use common\components\FCMHelper;
use Yii;

class PersonasController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bearerAuth' => [
                    'class' => OptionalBearerAuth::className(),
                    'except' => ['options'],
                    'actionsClient' => ['parametros']
                ],
            ]
        );
    }

    public function actionAltaTelefono($id)
    {
        $persona = new Personas();

        $persona->IdPersona = $id;

        $datos = [
            'IdPersona' => $id,
            'Detalle' => Yii::$app->request->post('Detalle'),
            'EsPrincipal' => Yii::$app->request->post('EsPrincipal'),
            'Telefono' => Yii::$app->request->post('Telefono')
        ];

        $resultado = $persona->AltaTelefonos((object) $datos);

        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionUpdate($id)
    {
        $persona = new Personas();

        $persona->IdPersona = $id;

        $datos = [
          'IdPersona' => $id,
          'Detalle' => Yii::$app->request->post('Detalle'),
          'EsPrincipal' => Yii::$app->request->post('EsPrincipal'),
          'Telefono' => Yii::$app->request->post('Telefono'),
          'TelefonoOld' => Yii::$app->request->post('TelefonoOld')
        ];
        
        $resultado = $persona->ModificarTelefono((object) $datos);

        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionEliminar($id)
    {
        $persona = new Personas();

        $persona->IdPersona = $id;

        $datos = [
            'IdPersona' => $id,
            'Telefono' => Yii::$app->request->post('Telefono')
        ];

        $resultado = $persona->BorrarTelefono((object) $datos);

        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }

    /**
     * @api {get} /personas/casos Listar Personas Casos
     * @apiName ListarPersonasCasos
     * @apiGroup Casos
     * @apiPermission logueado
     *
     * @apiParam {[]Number} IdsCasos
     *
     * @apiSuccess {[]Object} . Datos de las Personas.
     */

    public function actionPersonasCasos()
    {
        $caso = new Casos();

        $IdsCasos = json_decode(Yii::$app->request->get('IdsCasos'), true);

        $out = array();

        foreach ($IdsCasos as $IdCaso) {
            $caso->IdCaso = $IdCaso;
            $out[$IdCaso] = $caso->ListarPersonas();
        }

        return $out;
    }

    public function actionPadron($documento)
    {
        $persona = new Personas();

        $resultado = $persona->Padron($documento);

        if ($resultado['Mensaje'] != 'OK') {
            return ['Error' => $resultado['Mensaje']];
        }

        return $resultado;
    }

    public function actionParametros()
    {
        $Parametros = Yii::$app->request->post('Parametros');
        $IdCaso = Yii::$app->request->post('IdCaso');
        $IdPersona = Yii::$app->request->post('IdPersona');

        $persona = new Personas();

        $resultado = $persona->Parametros($Parametros, $IdCaso, $IdPersona);

        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionHistoriaClinica()
    {
        $HistoriaClinica = json_decode(Yii::$app->request->post('HistoriaClinica'));
        $IdCaso = Yii::$app->request->post('IdCaso');
        $IdPersona = Yii::$app->request->post('IdPersona');

        $persona = new Personas();

        if (empty($HistoriaClinica->IdHistoriaClinica)) {
            $resultado = $persona->AltaHistoriaClinica($HistoriaClinica, $IdCaso, $IdPersona);

            if (substr($resultado, 0, 2) == 'OK') {
                return [
                    'Error' => null,
                    'IdHistoriaClinica' => substr($resultado, 2)
                ];
            } else {
                return ['Error' => $resultado];
            }
        } else {
            $resultado = $persona->EditarHistoriaClinica($HistoriaClinica, $IdCaso, $IdPersona);

            if ($resultado == 'OK') {
                return ['Error' => null];
            } else {
                return ['Error' => $resultado];
            }
        }
    }

    public function actionEditarDocumentacion($id)
    {
        $personas = Yii::$app->request->post('personas');

        $primeraVez = Yii::$app->request->post('primeraVez');

        $persona = new Personas();

        $caso = new Casos();
        $caso->IdCaso = $id;
        $caso->Dame();

        $errores = array();

        foreach ($personas as $p) {
            if (!empty($p['TokenApp'])) {
                $respuesta = FCMHelper::enviarNotificacionPush(
                    [
                        'title' => 'Hay novedades en uno de tus casos',
                        'body' => "El estudio encargado del caso {$caso->Caratula} quiere que revises la documentacion requerida del mismo."
                    ],
                    $p['TokenApp'],
                    [
                        'tipo' => 'documentacion',
                        'id' => $caso->IdCaso
                    ],
                    'user',
                    true
                );
            }

            $resultado = $persona->EditarDocumentacion($p['IdPersona'], $id, $p['DocumentacionSolicitada']);

            if ($resultado !== 'OK') {
                $errores[] = $resultado;
            }
        }

        return $errores;
    }
}
