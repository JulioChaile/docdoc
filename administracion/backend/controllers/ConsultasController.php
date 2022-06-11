<?php
namespace backend\controllers;

use common\models\Consultas;
use backend\models\GestorConsultas;
use backend\models\GestorDifusiones;
use common\components\PermisosHelper;
use common\models\forms\BusquedaForm;
use common\models\forms\DerivarConsultaForm;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use yii\data\Pagination;

class ConsultasController extends Controller
{
    public function actionIndex()
    {
        $FechaInicio = null;
        $FechaFin = null;
        return $this->actionListar($FechaInicio, $FechaFin);
    }
    public function actionListar($FechaInicio = null, $FechaFin = null, $Cadena = '', $Estado = 'T', $IdDifusion = 0)
    {
        PermisosHelper::verificarPermiso('BuscarConsultas');
        $busqueda = new BusquedaForm();
        
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
            $FechaInicio = $busqueda->FechaInicio;
            $FechaFin = $busqueda->FechaFin;
            $Estado = $busqueda->Combo;
            $IdDifusion = $busqueda->Id;
        } else {
            $busqueda->FechaInicio = $FechaInicio;
            $busqueda->FechaFin = $FechaFin;
        }
        $gestor = new GestorConsultas();
        $query = $gestor->BuscarAvanzado($Cadena, $Estado, $FechaInicio, $FechaFin, $IdDifusion);

        $paginacion = new Pagination([
            'defaultPageSize' => 10,
            'totalCount' => count($query),
        ]);

        $consultas = array_slice ($query, $paginacion->offset, $paginacion->limit);
        
        $gestorDifusiones = new GestorDifusiones();
        $difusiones = $gestorDifusiones->Buscar();
        return $this->render('index', [
                    'models' => $consultas,
                    'paginacion' => $paginacion,
                    'busqueda' => $busqueda,
                    'difusiones' => $difusiones,
        ]);
    }
    
    public function actionModificar($id)
    {
        PermisosHelper::verificarPermiso('ModificarConsulta');
        
        if (!intval($id)) {
            throw new HttpException('422', 'La consulta indicada no es válida.');
        }
        
        $consulta = new Consultas();
        $consulta->IdConsulta = $id;
        $consulta->setScenario(Consultas::_MODIFICAR);
        if ($consulta->load(Yii::$app->request->post()) && $consulta->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorConsultas();
            $resultado = $gestor->Modificar($consulta);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $consulta->Dame();
            $gd = new GestorDifusiones();
            $difusiones = $gd->Buscar();
            return $this->renderAjax('modificar', [
                        'model' => $consulta,
                        'difusiones' => $difusiones,
            ]);
        }
    }
    
    public function actionDarBaja($id)
    {
        PermisosHelper::verificarPermiso('DarBajaConsulta');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['422' => 'La consulta indicada no es válida.'];
        }
        
        $consulta = new Consultas();
        $consulta->IdConsulta = $id;
        
        $resultado = $consulta->DarBaja();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionActivar($id)
    {
        PermisosHelper::verificarPermiso('DarBajaConsulta');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['422' => 'La consulta indicada no es válida.'];
        }
        
        $consulta = new Consultas();
        $consulta->IdConsulta = $id;
        
        $resultado = $consulta->Activar();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionDerivar($id)
    {
        PermisosHelper::verificarPermiso('DerivarConsulta');
        
        if (!intval($id)) {
            throw new HttpException('422', 'La consulta indicada no es válida.');
        }
        
        $consulta = new DerivarConsultaForm();
        $consulta->IdConsulta = $id;
        
        if ($consulta->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gest = new Consultas();
            $resultado = $gest->Derivar($consulta);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('derivar', [
                    'model' => $consulta
            ]);
        }
    }
    
    public function actionBorrar($id)
    {
        PermisosHelper::verificarPermiso('BorrarConsulta');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!intval($id)) {
            return ['error' => 'La consulta indicada no es válida'];
        }
        
        $consulta = new Consultas();
        $consulta->IdConsulta = $id;
        
        $gestor = new GestorConsultas();
        $resultado = $gestor->Borrar($consulta);
        
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
}
