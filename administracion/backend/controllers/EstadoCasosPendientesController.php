<?php
namespace backend\controllers;

use common\models\GestorEstadoCasosPendientes;
use common\models\GestorCasosPendientes;
use common\models\EstadoCasosPendientes;
use common\models\forms\BusquedaForm;
use common\components\PermisosHelper;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;

class EstadoCasosPendientesController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }

    public function actionAlta()
    {
        $EstadoCasoPendiente = new EstadoCasosPendientes();
        $EstadoCasoPendiente->setScenario(EstadoCasosPendientes::_ALTA);
        if ($EstadoCasoPendiente->load(Yii::$app->request->post()) && $EstadoCasoPendiente->validate()) {
            Yii::$app->response->format = 'json';
            
            $gestor = new GestorEstadoCasosPendientes();
            $resultado = $gestor->Alta($EstadoCasoPendiente);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-estado-casos-pendientes', [
                        'model' => $EstadoCasoPendiente,
                        'titulo' => 'Nuevo Estado de Casos Pendientes'
            ]);
        }
    }

    public function actionBorrar($id)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id)) {
            return['error' => 'La EstadoCasoPendiente indicada no es válida.'];
        }
        
        $EstadoCasoPendiente = new EstadoCasosPendientes();
        $EstadoCasoPendiente->IdEstadoCasoPendiente = $id;
        
        $gestor = new GestorEstadoCasosPendientes();
        $resultado = $gestor->Borrar($EstadoCasoPendiente);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }

    public function actionModificar($id)
    {
        if (!intval($id)) {
            throw new HttpException('422', 'El estado indicado no es válido.');
        }
        
        $EstadoCasoPendiente = new EstadoCasosPendientes();
        $EstadoCasoPendiente->IdEstadoCasoPendiente = $id;
        $EstadoCasoPendiente->setScenario(EstadoCasosPendientes::_MODIFICAR);
        if ($EstadoCasoPendiente->load(Yii::$app->request->post()) && $EstadoCasoPendiente->validate()) {
            Yii::$app->response->format = 'json';
            
            $gestor = new GestorEstadoCasosPendientes();
            $resultado = $gestor->Modificar($EstadoCasoPendiente);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $EstadoCasoPendiente->Dame();
            return $this->renderAjax('datos-estado-casos-pendientes', [
                        'model' => $EstadoCasoPendiente,
                        'titulo' => 'Modificar Estado de Casos Pendientes'
            ]);
        }
    }

    public function actionListar()
    {
        PermisosHelper::verificarPermiso('BuscarTiposCaso');
        
        $gestor = new GestorCasosPendientes();
        $EstadoCasoPendiente = $gestor->ListarEstados();
        
        return $this->render('index', [
                'models' => $EstadoCasoPendiente
        ]);
    }
}
