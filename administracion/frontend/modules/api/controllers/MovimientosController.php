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

        $Acciones = json_decode(Yii::$app->request->post('Acciones'), true);
        
        $movimiento = new MovimientosCaso();
        
        $movimiento->setAttributes(Yii::$app->request->post());

        $Cliente = Yii::$app->request->post('Cliente');

        $resultado = $caso->AltaMovimiento($movimiento, $Cliente);
        if (substr($resultado, 0, 2) == 'OK') {
            if (!empty($Acciones)) {
                $IdMovimientoCaso = substr($resultado, 2);
                $IdUsuario = Yii::$app->user->identity->IdUsuario;

                foreach ($Acciones as $a) {
                    $sql = "INSERT INTO MovimientosAcciones VALUES (0, " . $IdMovimientoCaso . ", '" . $a['Accion'] . "', DATE(NOW()), " . $IdUsuario . " )";

                    $query = Yii::$app->db->createCommand($sql);
                    
                    $query->execute();
                }
            }

            if ($movimiento->Escrito === 'dWz6H78mpQ') {
                $usuario = new UsuariosEstudio;
                $usuario->IdUsuario = $movimiento->IdResponsable;
                $usuario->Dame();

                $caso->Dame();

                if (isset($usuario->TelefonoUsuario) && !empty($usuario->TelefonoUsuario)) {
                    $sql4 = "SELECT Accion FROM MovimientosAcciones WHERE IdMovimientoCaso = " . $IdMovimientoCaso . " ORDER BY IdMovimientoAccion DESC";

                    $query4 = Yii::$app->db->createCommand($sql4);
                    
                    $accion = $query4->queryScalar();

                    if (empty($accion)) {
                        $accion = '';
                    }

                    $Contenido = "Se te asigno una nueva tarea pendiente\nCaso: " . $caso->Caratula . "\nMovimiento: " . $movimiento->Detalle . ', ' . $accion;

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
                'IdMovimientoCaso' => substr($resultado, 2),
                'Acciones' => $Acciones
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

        $Acciones = json_decode(Yii::$app->request->getBodyParam('Acciones'), true);
        
        $caso = new Casos();
        
        $resultado = $caso->ModificarMovimiento($movimiento);
        
        if ($resultado == 'OK') {
            if (!empty($Acciones)) {
                $IdMovimientoCaso = $id;
                $IdUsuario = Yii::$app->user->identity->IdUsuario;

                foreach ($Acciones as $a) {
                    $sql = "INSERT INTO MovimientosAcciones VALUES (0, " . $IdMovimientoCaso . ", '" . $a['Accion'] . "', DATE(NOW()), " . $IdUsuario . " )";

                    $query = Yii::$app->db->createCommand($sql);
                    
                    $query->execute();
                }
            }
            if ($movimiento->Escrito === 'dWz6H78mpQ') {
                $usuario = new UsuariosEstudio;
                $usuario->IdUsuario = $movimiento->IdResponsable;
                $usuario->Dame();
                $movimiento->Dame();
                $caso->IdCaso = $movimiento->IdCaso;

                $caso->Dame();

                if (isset($usuario->TelefonoUsuario) && !empty($usuario->TelefonoUsuario)) {
                    $sql4 = "SELECT Accion FROM MovimientosAcciones WHERE IdMovimientoCaso = " . $id . " ORDER BY IdMovimientoAccion DESC";

                    $query4 = Yii::$app->db->createCommand($sql4);
                    
                    $accion = $query4->queryScalar();

                    if (empty($accion)) {
                        $accion = '';
                    }

                    $Contenido = "Se te asigno una nueva tarea pendiente\nCaso: " . $caso->Caratula . "\nMovimiento: " . $movimiento->Detalle . ', ' . $accion;

                    $respuestaMensaje = Yii::$app->chatapi->mensajeComun($usuario->TelefonoUsuario, $Contenido);

                    // return ['Error' => null, 'r' => $respuestaMensaje];
                }
            }
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
    
    public function actionEditarAccion()
    {
        $Accion = Yii::$app->request->post('accion');
        $IdMovimientoAccion = Yii::$app->request->post('id');

        $sql = "UPDATE MovimientosAcciones SET Accion = '" . $Accion . "' WHERE IdMovimientoAccion = " . $IdMovimientoAccion;

        $query = Yii::$app->db->createCommand($sql);
        
        $query->execute();

        return ['Error' => null];
    }
    
    public function actionBorrarAccion()
    {
        $IdMovimientoAccion = Yii::$app->request->post('id');

        $sql = "DELETE FROM MovimientosAcciones WHERE IdMovimientoAccion = " . $IdMovimientoAccion;

        $query = Yii::$app->db->createCommand($sql);
        
        $query->execute();
        
        return ['Error' => null];
    }
    
    public function actionEliminarRecordatorio()
    {
        $id = Yii::$app->request->post('id');

        $sql = "DELETE FROM RecordatorioMovimiento WHERE IdMovimientoCaso = " . $id;

        $query = Yii::$app->db->createCommand($sql);
        
        $query->execute();
        
        return ['Error' => null];
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

    public function actionAltaRecordatorio()
    {
        $IdMovimientoCaso = Yii::$app->request->post('IdMovimientoCaso');
        $Frecuencia = Yii::$app->request->post('Frecuencia');

        if ($Frecuencia === 0 || $Frecuencia === '0') {
            return [
                'Error' => null
            ];
        }

        $movimiento = new MovimientosCaso();

        $resultado = $movimiento->AltaRecordatorio($IdMovimientoCaso, $Frecuencia);

        if ($resultado == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionDuplicar()
    {
        $IdMovimientoCaso = Yii::$app->request->post('IdMovimientoCaso');
        $IdObjetivo = Yii::$app->request->post('IdObjetivo');

        $sql = 'CALL dsp_duplicar_movimiento( :IdMovimientoCaso, :IdObjetivo )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':IdMovimientoCaso' => $IdMovimientoCaso,
            ':IdObjetivo' => $IdObjetivo
        ]);

        $resultado = $query->queryScalar();

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdMovimientoCaso' => substr($resultado, 2)
            ];
        } else {
            return ['Error' => $resultado];
        }
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
