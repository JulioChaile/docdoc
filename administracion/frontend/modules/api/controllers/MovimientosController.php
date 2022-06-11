<?php
namespace frontend\modules\api\controllers;

use common\components\Calendar;
use common\models\Estudios;
use common\models\Casos;
use common\models\GestorCasos;
use common\models\GestorEventos;
use common\models\MovimientosCaso;
use common\components\ChatApi;
use common\models\UsuariosEstudio;
use common\models\Objetivos;
use common\components\FCMHelper;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;

class MovimientosController extends BaseController
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
    
    
    public function actionCreate()
    {
        $caso = new Casos();
        
        $caso->setAttributes(Yii::$app->request->post());
        
        $movimiento = new MovimientosCaso();
        
        $movimiento->setAttributes(Yii::$app->request->post());

        $Cliente = Yii::$app->request->post('Cliente');

        $resultado = $caso->AltaMovimiento($movimiento, $Cliente);
        if (substr($resultado, 0, 2) == 'OK') {
            if ($movimiento->Escrito === 'dWz6H78mpQ') {
                $usuario = new UsuariosEstudio;
                $usuario->IdUsuario = $movimiento->IdResponsable;
                $usuario->Dame();

                $caso->Dame();

                if (isset($usuario->TelefonoUsuario) && !empty($usuario->TelefonoUsuario)) {
                    $Contenido = "Se te asigno una nueva tarea pendiente\nCaso: " . $caso->Caratula . "\nMovimiento: " . $movimiento->Detalle;

                    $respuestaMensaje = Yii::$app->chatapi->mensajeComun($usuario->TelefonoUsuario, $Contenido);
                }
            }

            if ($Cliente === 'S') {
                $caso->Dame();
                $personas = $caso->PersonasCaso;
                $tokens = array();

                foreach ($personas as $p) {
                    if (!empty($p->TokenApp)) {
                        $tokens[] = $p->TokenApp;
                    }
                }
                if (!empty($tokens)) {
                    $respuesta = FCMHelper::enviarNotificacionPush(
                        [
                            'title' => 'Hay novedades en uno de tus casos',
                            'body' => "El estudio encargado del caso {$caso->Caratula} tiene novedades para vos"
                        ],
                        $tokens,
                        [
                            'tipo' => 'nuevoMovimiento',
                            'id' => $caso->IdCaso,
                            'caratula' => $caso->Caratula
                        ],
                        'mult',
                        true
                    );
                }
            }

            return [
                'Error' => null,
                'IdMovimientoCaso' => substr($resultado, 2)
            ];
        } else {
            return ['Error' => $resultado];
        }
    }
    
    public function actionDelete($id)
    {
        $movimiento = new MovimientosCaso();
        
        $movimiento->IdMovimientoCaso = $id;
        
        $caso = new Casos();
                
        $resultado = $caso->BorrarMovimiento($movimiento);
        
        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }
    
    public function actionUpdate($id)
    {
        $movimiento = new MovimientosCaso();
        
        $movimiento->IdMovimientoCaso = $id;
        
        $movimiento->setAttributes(Yii::$app->request->getBodyParams());
        
        $caso = new Casos();
        
        $resultado = $caso->ModificarMovimiento($movimiento);
        
        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }
    
    public function actionRealizar($id)
    {
        $movimiento = new MovimientosCaso();
        
        $movimiento->IdMovimientoCaso = $id;
        
        $resultado = $movimiento->Realizar();
        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }
    
    public function actionDesrealizar($id)
    {
        $movimiento = new MovimientosCaso();
        
        $movimiento->IdMovimientoCaso = $id;
        
        $resultado = $movimiento->Desrealizar();
        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }
    
    public function actionView($id)
    {
        $movimiento = new MovimientosCaso();
        
        $movimiento->IdMovimientoCaso = $id;
        
        $movimiento->Dame();
        
        return $movimiento;
    }
    
    public function actionAsociarObjetivo($id, $idObjetivo)
    {
        $objetivo = new Objetivos();
        $objetivo->IdObjetivo = $idObjetivo;

        $movimiento = new MovimientosCaso();
        $movimiento->IdMovimientoCaso = $id;
        $movimiento->Color = Yii::$app->request->post('Color');
        
        $resultado = $objetivo->AltaMovimientoCaso($movimiento);
        
        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }
    
    public function actionDesasociarObjetivo($id, $idObjetivo)
    {
        $objetivo = new Objetivos();
        
        $objetivo->IdObjetivo = $idObjetivo;
        
        $resultado = $objetivo->BorrarMovimientoCaso($id);
        
        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }
    
    public function actionVistaTribunales()
    {
        $IdEstudio = Yii::$app->user->identity->IdEstudio;
      
        $gestor = new GestorCasos();
        $resultado = $gestor->FiltrarMovimientosTribunales($IdEstudio);
        
        return $resultado;
    }

    public function actionMovimientosHome()
    {
        $IdEstudio = Yii::$app->user->identity->IdEstudio;
      
        $gestor = new GestorCasos();
        $resultado = $gestor->MovimientosDelDia($IdEstudio);
        
        return $resultado;
    }

    public function actionCrearEvento()
    {
        $IdCalendario = Yii::$app->request->post('IdCalendario');
        $IdCalendarioAPI = Yii::$app->request->post('IdCalendarioAPI');
        $IdMovimientoCaso = Yii::$app->request->post('IdMovimientoCaso');
        $Titulo = Yii::$app->request->post('Titulo');
        $Descripcion = Yii::$app->request->post('Descripcion');
        $Comienzo = Yii::$app->request->post('Comienzo');
        $ComienzoDb = Yii::$app->request->post('ComienzoDb');
        $Fin = Yii::$app->request->post('Fin');
        $FinDb = Yii::$app->request->post('FinDb');
        $IdColor = Yii::$app->request->post('IdColor');

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

            $resultado = $evento->AltaEventoMovimiento($IdEvento, $IdMovimientoCaso);

            if (substr($resultado, 0, 2) == 'OK') {
                return ['Error' => null];
            } else {
                return ['Error' => $resultado];
            }
        } else {
            return ['Error' => $resultado];
        }
    }
}
