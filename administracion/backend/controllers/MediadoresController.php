<?php
namespace backend\controllers;

use common\models\GestorMediadores;
use common\models\Mediadores;
use common\models\forms\BusquedaForm;
use common\components\PermisosHelper;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;

class MediadoresController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }

    public function actionAlta()
    {
        $Mediador = new Mediadores();
        $Mediador->setScenario(Mediadores::_ALTA);
        if ($Mediador->load(Yii::$app->request->post()) && $Mediador->validate()) {
            Yii::$app->response->format = 'json';
            
            $gestor = new GestorMediadores();
            $resultado = $gestor->Alta($Mediador);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-mediadores', [
                        'model' => $Mediador,
                        'titulo' => 'Nueva mediador'
            ]);
        }
    }

    public function actionBorrar($id)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id)) {
            return['error' => 'El mediador indicado no es válido.'];
        }
        
        $Mediador = new Mediadores();
        $Mediador->IdMediador = $id;
        
        $gestor = new GestorMediadores();
        $resultado = $gestor->Borrar($Mediador);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }

    public function actionModificar($id)
    {
        if (!intval($id)) {
            throw new HttpException('422', 'El mediador indicado no es válido.');
        }
        
        $Mediador = new Mediadores();
        $Mediador->IdMediador = $id;
        $Mediador->setScenario(Mediadores::_MODIFICAR);
        if ($Mediador->load(Yii::$app->request->post()) && $Mediador->validate()) {
            Yii::$app->response->format = 'json';
            
            $gestor = new GestorMediadores();
            $resultado = $gestor->Modificar($Mediador);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $Mediador->Dame();
            return $this->renderAjax('datos-mediadores', [
                        'model' => $Mediador,
                        'titulo' => 'Modificar Mediador'
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
        
        $gestor = new GestorMediadores();
        $Mediadores = $gestor->Buscar($Cadena);
        
        return $this->render('index', [
                'models' => $Mediadores,
                'busqueda' => $busqueda
        ]);
    }
}
