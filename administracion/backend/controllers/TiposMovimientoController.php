<?php
namespace backend\controllers;

use common\components\PermisosHelper;
use common\models\forms\BusquedaForm;
use backend\models\GestorTiposMovimiento;
use common\models\TiposMovimiento;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class TiposMovimientoController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }
    
    public function actionListar($Cadena = '', $Categoria = 'T')
    {
        PermisosHelper::verificarPermiso('BuscarTiposMovimiento');
        
        $busqueda = new BusquedaForm();
        
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
            $Categoria = $busqueda->Combo;
        }
        
        $gestor = new GestorTiposMovimiento();
        $tiposMovimiento = $gestor->Buscar($Cadena, $Categoria);
        
        return $this->render('index', [
                'models' => $tiposMovimiento,
                'busqueda' => $busqueda
        ]);
    }
    
    public function actionAlta()
    {
        PermisosHelper::verificarPermiso('AltaTipoMovimiento');
        
        $tipoMovimiento = new TiposMovimiento();
        $tipoMovimiento->setScenario(TiposMovimiento::_ALTA);
        if ($tipoMovimiento->load(Yii::$app->request->post()) && $tipoMovimiento->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorTiposMovimiento();
            $resultado = $gestor->Alta($tipoMovimiento);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-tipos-movimiento', [
                        'model' => $tipoMovimiento,
                        'titulo' => 'Nuevo tipo de movimiento'
            ]);
        }
    }
    
    public function actionModificar($id)
    {
        PermisosHelper::verificarPermiso('ModificarTipoMovimiento');
        
        if (!intval($id)) {
            throw new HttpException('422', 'El tipo de movimiento indicado no es válido.');
        }
        
        $tipoMovimiento = new TiposMovimiento();
        $tipoMovimiento->IdTipoMov = $id;
        $tipoMovimiento->setScenario(TiposMovimiento::_MODIFICAR);
        if ($tipoMovimiento->load(Yii::$app->request->post()) && $tipoMovimiento->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorTiposMovimiento();
            $resultado = $gestor->Modificar($tipoMovimiento);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $tipoMovimiento->Dame();
            return $this->renderAjax('datos-tipos-movimiento', [
                        'model' => $tipoMovimiento,
                        'titulo' => 'Modificar tipo de movimiento'
            ]);
        }
    }
    
    public function actionBorrar($id)
    {
        PermisosHelper::verificarPermiso('BorrarTipoMovimiento');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!intval($id)) {
            return['error' => 'El tipo de movimiento indicado no es válido.'];
        }
        
        $tipoMovimiento = new TiposMovimiento();
        $tipoMovimiento->IdTipoMov = $id;
        
        $gestor = new GestorTiposMovimiento();
        $resultado = $gestor->Borrar($tipoMovimiento);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
}
