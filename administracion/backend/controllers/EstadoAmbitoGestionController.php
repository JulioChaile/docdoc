<?php
namespace backend\controllers;

use common\models\GestorEstadoAmbitoGestion;
use common\models\EstadoAmbitoGestion;
use common\models\forms\BusquedaForm;
use common\components\PermisosHelper;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;

class EstadoAmbitoGestionController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }

    public function actionAlta()
    {
        $EstadoAmbitoGestion = new EstadoAmbitoGestion();
        $EstadoAmbitoGestion->setScenario(EstadoAmbitoGestion::_ALTA);
        if ($EstadoAmbitoGestion->load(Yii::$app->request->post()) && $EstadoAmbitoGestion->validate()) {
            Yii::$app->response->format = 'json';
            
            $gestor = new GestorEstadoAmbitoGestion();
            $resultado = $gestor->Alta($EstadoAmbitoGestion);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-estado-ambito-gestion', [
                        'model' => $EstadoAmbitoGestion,
                        'titulo' => 'Nueva Estado de Ambito de Gestion'
            ]);
        }
    }

    public function actionBorrar($id)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id)) {
            return['error' => 'La EstadoAmbitoGestion indicada no es válida.'];
        }
        
        $EstadoAmbitoGestion = new EstadoAmbitoGestion();
        $EstadoAmbitoGestion->IdEstadoAmbitoGestion = $id;
        
        $gestor = new GestorEstadoAmbitoGestion();
        $resultado = $gestor->Borrar($EstadoAmbitoGestion);
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
        
        $EstadoAmbitoGestion = new EstadoAmbitoGestion();
        $EstadoAmbitoGestion->IdEstadoAmbitoGestion = $id;
        $EstadoAmbitoGestion->setScenario(EstadoAmbitoGestion::_MODIFICAR);
        if ($EstadoAmbitoGestion->load(Yii::$app->request->post()) && $EstadoAmbitoGestion->validate()) {
            Yii::$app->response->format = 'json';
            
            $gestor = new GestorEstadoAmbitoGestion();
            $resultado = $gestor->Modificar($EstadoAmbitoGestion);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $EstadoAmbitoGestion->Dame();
            return $this->renderAjax('datos-estado-ambito-gestion', [
                        'model' => $EstadoAmbitoGestion,
                        'titulo' => 'Modificar Estado de Ambito de Gestion'
            ]);
        }
    }

    public function actionModificarMensaje($id)
    {
        if (!intval($id)) {
            throw new HttpException('422', 'El estado indicado no es válido.');
        }
        
        $EstadoAmbitoGestion = new EstadoAmbitoGestion();
        $EstadoAmbitoGestion->IdEstadoAmbitoGestion = $id;
        if ($EstadoAmbitoGestion->load(Yii::$app->request->post())) {
            Yii::$app->response->format = 'json';
            
            $gestor = new GestorEstadoAmbitoGestion();
            $resultado = $gestor->ModificarMensaje($EstadoAmbitoGestion);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $EstadoAmbitoGestion->Dame();
            return $this->renderAjax('datos-mensaje', [
                        'model' => $EstadoAmbitoGestion,
                        'titulo' => 'Modificar Mensaje'
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
        
        $gestor = new GestorEstadoAmbitoGestion();
        $EstadoAmbitoGestion = $gestor->Buscar($Cadena);
        
        return $this->render('index', [
                'models' => $EstadoAmbitoGestion,
                'busqueda' => $busqueda
        ]);
    }
}
