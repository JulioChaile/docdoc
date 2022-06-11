<?php
namespace backend\controllers;

use common\components\PermisosHelper;
use backend\models\Difusiones;
use common\models\forms\BusquedaForm;
use backend\models\GestorDifusiones;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class DifusionesController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }
    
    public function actionListar($Cadena = '')
    {
        PermisosHelper::verificarPermiso('BuscarDifusiones');
        
        $busqueda = new BusquedaForm();
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
        }
        
        $gestor = new GestorDifusiones();
        $difusiones = $gestor->Buscar($Cadena);
        
        return $this->render('index', [
                    'models' => $difusiones,
                    'busqueda' => $busqueda
        ]);
    }
    
    public function actionAlta()
    {
        PermisosHelper::verificarPermiso('AltaDifusion');
        
        $difusion = new Difusiones();
        $difusion->setScenario(Difusiones::_ALTA);
        if ($difusion->load(Yii::$app->request->post()) && $difusion->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorDifusiones();
            $resultado = $gestor->Alta($difusion);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-difusion', [
                        'model' => $difusion,
                        'titulo' => 'Nueva difusión'
            ]);
        }
    }
    
    public function actionModificar($id)
    {
        PermisosHelper::verificarPermiso('ModificarDifusion');
        
        if (!intval($id)) {
            throw new HttpException('422', 'La difusión indicada no es válida.');
        }
        
        $difusion = new Difusiones();
        $difusion->IdDifusion = $id;
        $difusion->setScenario(Difusiones::_MODIFICAR);
        if ($difusion->load(Yii::$app->request->post()) && $difusion->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorDifusiones();
            $resultado = $gestor->Modificar($difusion);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $difusion->Dame();
            return $this->renderAjax('datos-difusion', [
                        'model' => $difusion,
                        'titulo' => 'Modificar difusión'
            ]);
        }
    }
    
    public function actionBorrar($id)
    {
        PermisosHelper::verificarPermiso('BorrarDifusion');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!intval($id)) {
            return['error' => 'La difusión indicada no es válida.'];
        }
        
        $difusion = new Difusiones();
        $difusion->IdDifusion = $id;
        
        $gestor = new GestorDifusiones();
        $resultado = $gestor->Borrar($difusion);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
}
