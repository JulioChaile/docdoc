<?php
namespace backend\controllers;

use backend\models\GestorOrigenes;
use common\models\Origenes;
use common\models\forms\BusquedaForm;
use common\components\PermisosHelper;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;

class OrigenesController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }

    public function actionAlta()
    {
        $Origenes = new Origenes();
        $Origenes->setScenario(Origenes::_ALTA);
        if ($Origenes->load(Yii::$app->request->post()) && $Origenes->validate()) {
            Yii::$app->response->format = 'json';
            
            $gestor = new GestorOrigenes();
            $resultado = $gestor->Alta($Origenes);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-origenes', [
                        'model' => $Origenes,
                        'titulo' => 'Nuevo Origen'
            ]);
        }
    }

    public function actionBorrar($id)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id)) {
            return['error' => 'El origen indicada no es válida.'];
        }
        
        $Origenes = new Origenes();
        $Origenes->IdOrigen = $id;
        
        $gestor = new GestorOrigenes();
        $resultado = $gestor->Borrar($Origenes);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }

    public function actionModificar($id)
    {
        if (!intval($id)) {
            throw new HttpException('422', 'El origen indicado no es válido.');
        }
        
        $Origenes = new Origenes();
        $Origenes->IdOrigen = $id;
        $Origenes->setScenario(Origenes::_MODIFICAR);
        if ($Origenes->load(Yii::$app->request->post()) && $Origenes->validate()) {
            Yii::$app->response->format = 'json';
            
            $gestor = new GestorOrigenes();
            $resultado = $gestor->Modificar($Origenes);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $Origenes->Dame();
            return $this->renderAjax('datos-origenes', [
                        'model' => $Origenes,
                        'titulo' => 'Modificar Origen'
            ]);
        }
    }

    public function actionListar()
    {
        PermisosHelper::verificarPermiso('BuscarTiposCaso');

        $busqueda = new BusquedaForm();
        
        $gestor = new Origenes();
        $Origenes = $gestor->ListarOrigenes();
        
        return $this->render('index', [
                'models' => $Origenes,
                'busqueda' => $busqueda
        ]);
    }
}
