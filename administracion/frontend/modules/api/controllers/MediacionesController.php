<?php

namespace frontend\modules\api\controllers;

use common\models\GestorMediaciones;
use common\models\Mediaciones;
use common\models\GestorMediadores;
use common\components\Calendar;
use common\models\GestorEventos;
use common\models\Estudios;
use common\models\MovimientosCaso;
use common\models\Casos;
use common\components\FCMHelper;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;

class MediacionesController extends BaseController
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
        $IdMediacion = Yii::$app->request->get('id');

        $mediacion = new Mediaciones();
        $mediacion->IdMediacion = $IdMediacion;

        $mediacion->DameMediacion();

        return $mediacion;
    }

    public function actionBuscar()
    {
        $gestor = new GestorMediaciones();
        
        $respuesta = $gestor->Buscar(
            Yii::$app->user->identity->IdEstudio,
            Yii::$app->user->identity->IdUsuario,
            Yii::$app->request->get('Cadena'),
            Yii::$app->request->get('Offset'),
            Yii::$app->request->get('CausaPenal')
        );

        return $respuesta;
    }

    public function actionAlta()
    {
        $mediacion = new Mediaciones();
        
        $mediacion->setAttributes(Yii::$app->request->post());

        $gestor = new GestorMediaciones();

        $resultado = $gestor->Alta($mediacion);

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdMediacion' => substr($resultado, 2)
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionEditar()
    {
        $mediacion = new Mediaciones();
        
        $mediacion->setAttributes(Yii::$app->request->post());

        $gestor = new GestorMediaciones();

        $resultado = $gestor->Modificar($mediacion);

        if ($resultado == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionDatos()
    {
        $gestor = new GestorMediaciones();

        return $gestor->DameDatos();
    }

    public function actionMediadores()
    {
        $offset = Yii::$app->request->get('offset');
        $limit = Yii::$app->request->get('limit');
        $cadena = Yii::$app->request->get('cadena');
        $gestor = new GestorMediadores();

        return $gestor->Buscar($cadena, $offset, $limit);
    }

    public function actionFechas()
    {
        $gestor = new GestorMediaciones();

        return $gestor->ListarFechasAudiencia();
    }

    public function actionEvento() {
        $IdCalendario = Yii::$app->request->post('IdCalendario');
        $IdCalendarioAPI = Yii::$app->request->post('IdCalendarioAPI');
        $Titulo = Yii::$app->request->post('Titulo');
        $Descripcion = Yii::$app->request->post('Descripcion');
        $Comienzo = Yii::$app->request->post('Comienzo');
        $ComienzoDb = Yii::$app->request->post('ComienzoDb');
        $Fin = Yii::$app->request->post('Fin');
        $FinDb = Yii::$app->request->post('FinDb');
        $IdColor = Yii::$app->request->post('IdColor');
        $IdMediacion = Yii::$app->request->post('IdMediacion');
        $IdMovimientoCaso = Yii::$app->request->post('IdMovimientoCaso');

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
            $IdEvento = substr($resultado, 2);

            if (!empty($IdMovimientoCaso)) {
                $resultado2 = $evento->AltaEventoMovimiento($IdEvento, $IdMovimientoCaso);

                $movimiento = new MovimientosCaso();
                $movimiento->IdMovimientoCaso = $IdMovimientoCaso;
                $movimiento->Dame();

                $caso = new Casos();
                $caso->IdCaso = $movimiento->IdCaso;
                $caso->Dame();
                $personas = $caso->PersonasCaso;
                $tokens = array();
    
                foreach ($personas as $p) {
                    if (!empty($p->TokenApp)) {
                        $tokens[] = $p->TokenApp;
                    }
                }

                $fechaAudiencia = substr($ComienzoDb, 0, -3);

                if (!empty($tokens)) {
                    $respuesta = FCMHelper::enviarNotificacionPush(
                        [
                            'title' => 'Se fijó una nueva fecha de audiencia',
                            'body' => "El estudio encargado del caso {$caso->Caratula} fijó fecha de audiencia para {$fechaAudiencia}"
                        ],
                        $tokens,
                        [
                            'tipo' => 'audiencia',
                            'id' => $caso->IdCaso
                        ],
                        'mult',
                        true
                    );
                }
            }

            $mediacion = new Mediaciones();
            $mediacion->IdMediacion= $IdMediacion;
            
            $result = $mediacion->AltaEvento($IdEvento);

            if ($result == 'OK') {
                return ['Error' => null];
            } else {
                return ['Error' => $result];
            }
        } else {
            return ['Error' => $resultado];
        }
    }
}
