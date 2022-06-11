<?php
namespace backend\controllers;

use common\components\PermisosHelper;
use common\models\EstadosCaso;
use common\models\forms\BusquedaForm;
use backend\models\GestorEstadosCaso;
use backend\models\SubEstadosCaso;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class EstadosCasoController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }
    
    public function actionListar($Cadena = '', $IncluyeBajas = 'N')
    {
        PermisosHelper::verificarPermiso('BuscarEstadosCaso');
        
        $busqueda = new BusquedaForm();
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
            $IncluyeBajas = $busqueda->Check;
        }
        
        $gestor = new GestorEstadosCaso();
        $estadosCaso = $gestor->Buscar($Cadena, $IncluyeBajas);
        
        return $this->render('index', [
                    'models' => $estadosCaso,
                    'busqueda' => $busqueda
        ]);
    }
    
    public function actionAlta()
    {
        PermisosHelper::verificarPermiso('AltaEstadoCaso');
        
        $estado = new EstadosCaso();
        $estado->setScenario(EstadosCaso::_ALTA);
        if ($estado->load(Yii::$app->request->post()) && $estado->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorEstadosCaso();
            $resultado = $gestor->Alta($estado);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-estados-caso', [
                        'model' => $estado,
                        'titulo' => 'Nuevo estado de caso'
            ]);
        }
    }
    
    public function actionModificar($id)
    {
        PermisosHelper::verificarPermiso('ModificarEstadoCaso');
        
        if (!intval($id)) {
            throw new HttpException('422', 'El estado de caso indicado no es válido.');
        }
        
        $estado = new EstadosCaso();
        $estado->IdEstadoCaso = $id;
        $estado->setScenario(EstadosCaso::_MODIFICAR);
        if ($estado->load(Yii::$app->request->post()) && $estado->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorEstadosCaso();
            $resultado = $gestor->Modificar($estado);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $estado->Dame();
            return $this->renderAjax('datos-estados-caso', [
                        'model' => $estado,
                        'titulo' => 'Modificar estado de caso'
            ]);
        }
    }
    
    public function actionBorrar($id)
    {
        PermisosHelper::verificarPermiso('BorrarEstadoCaso');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'El estado de caso indicado no es válido.'];
        }
        
        $estado = new EstadosCaso();
        $estado->IdEstadoCaso = $id;
        
        $gestor = new GestorEstadosCaso();
        $resultado = $gestor->Borrar($estado);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionDarBaja($id)
    {
        PermisosHelper::verificarPermiso('DarBajaEstadoCaso');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'El estado de caso indicado no es válido.'];
        }
        
        $estado = new EstadosCaso();
        $estado->IdEstadoCaso = $id;
        
        $resultado = $estado->DarBaja();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionActivar($id)
    {
        PermisosHelper::verificarPermiso('ActivarEstadoCaso');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'El estado de caso indicado no es válido.'];
        }
        
        $estado = new EstadosCaso();
        $estado->IdEstadoCaso = $id;
        
        $resultado = $estado->Activar();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
}
