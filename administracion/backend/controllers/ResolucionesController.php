<?php
namespace backend\controllers;

use common\models\GestorResoluciones;
use common\models\Resoluciones;
use common\models\forms\BusquedaForm;
use common\components\PermisosHelper;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;

class ResolucionesController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }

    public function actionAlta()
    {
        $Resolucion = new Resoluciones();
        $Resolucion->setScenario(Resoluciones::_ALTA);
        if ($Resolucion->load(Yii::$app->request->post()) && $Resolucion->validate()) {
            Yii::$app->response->format = 'json';
            
            $gestor = new GestorResoluciones();
            $resultado = $gestor->Alta($Resolucion);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $Resolucion->FechaResolucion = date('Y-m-d');

            Yii::info($Resolucion->FechaResolucion);

            return $this->renderAjax('datos-resoluciones', [
                        'model' => $Resolucion,
                        'titulo' => 'Nueva resolucion'
            ]);
        }
    }

    public function actionBorrar($id)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id)) {
            return['error' => 'La resolucion indicada no es válida.'];
        }
        
        $Resolucion = new Resoluciones();
        $Resolucion->IdResolucionSMVM = $id;
        
        $gestor = new GestorResoluciones();
        $resultado = $gestor->Borrar($Resolucion);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }

    public function actionModificar($id)
    {
        if (!intval($id)) {
            throw new HttpException('422', 'El tipo de caso indicado no es válido.');
        }
        
        $Resolucion = new Resoluciones();
        $Resolucion->IdResolucionSMVM = $id;
        $Resolucion->setScenario(Resoluciones::_MODIFICAR);
        if ($Resolucion->load(Yii::$app->request->post()) && $Resolucion->validate()) {
            Yii::$app->response->format = 'json';
            
            $gestor = new GestorResoluciones();
            $resultado = $gestor->Modificar($Resolucion);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $Resolucion->Dame();
            return $this->renderAjax('datos-resoluciones', [
                        'model' => $Resolucion,
                        'titulo' => 'Modificar Resolucion'
            ]);
        }
    }

    public function actionListar($cadena = '')
    {
        PermisosHelper::verificarPermiso('BuscarTiposCaso');
        
        $busqueda = new BusquedaForm();
        $Cadena = $cadena;
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
        }
        
        $gestor = new GestorResoluciones();
        $Resoluciones = $gestor->Buscar($Cadena);
        
        return $this->render('index', [
                'models' => $Resoluciones,
                'busqueda' => $busqueda
        ]);
    }
}
