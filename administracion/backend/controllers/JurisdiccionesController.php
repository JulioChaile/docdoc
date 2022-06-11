<?php
namespace backend\controllers;

use common\components\PermisosHelper;
use common\models\forms\BusquedaForm;
use common\models\GestorJurisdicciones;
use common\models\Jurisdicciones;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class JurisdiccionesController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }
    
    public function actionListar($Cadena = '', $IncluyeBajas = 'N')
    {
        PermisosHelper::verificarPermiso('BuscarJurisdicciones');
        $busqueda = new BusquedaForm();
        
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
            $IncluyeBajas = $busqueda->Check;
        }
        
        $gestor = new GestorJurisdicciones();
        $jurisdicciones = $gestor->Buscar($Cadena, $IncluyeBajas);
        
        return $this->render('index', [
                    'models' => $jurisdicciones,
                    'busqueda' => $busqueda,
        ]);
    }
    
    public function actionAlta()
    {
        PermisosHelper::verificarPermiso('AltaJurisdiccion');
        
        $jurisdiccion = new Jurisdicciones();
        $jurisdiccion->setScenario(Jurisdicciones::_ALTA);
        if ($jurisdiccion->load(Yii::$app->request->post()) && $jurisdiccion->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorJurisdicciones();
            $resultado = $gestor->Alta($jurisdiccion);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-jurisdicciones', [
                        'model' => $jurisdiccion,
                        'titulo' => 'Nueva jurisdicción'
            ]);
        }
    }
    
    public function actionModificar($id)
    {
        PermisosHelper::verificarPermiso('ModificarJurisdiccion');
        
        if (!intval($id)) {
            throw new HttpException('422', 'La jurisdicción indicada no es válida.');
        }
        
        $jurisdiccion = new Jurisdicciones();
        $jurisdiccion->IdJurisdiccion = $id;
        $jurisdiccion->setScenario(Jurisdicciones::_MODIFICAR);
        if ($jurisdiccion->load(Yii::$app->request->post()) && $jurisdiccion->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorJurisdicciones();
            $resultado = $gestor->Modificar($jurisdiccion);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $jurisdiccion->Dame();
            return $this->renderAjax('datos-jurisdicciones', [
                        'model' => $jurisdiccion,
                        'titulo' => 'Modificar jurisdicción'
            ]);
        }
    }
    
    public function actionBorrar($id)
    {
        PermisosHelper::verificarPermiso('BorrarJurisdiccion');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return['error' => 'La jurisdicción indicada no es válida.'];
        }
        
        $jurisdiccion = new Jurisdicciones();
        $jurisdiccion->IdJurisdiccion = $id;
        
        $gestor = new GestorJurisdicciones();
        $resultado = $gestor->Borrar($jurisdiccion);
        
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionActivar($id)
    {
        PermisosHelper::verificarPermiso('ActivarJurisdiccion');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return['error' => 'La jurisdicción indicada no es válida.'];
        }
        
        $jurisdiccion = new Jurisdicciones();
        $jurisdiccion->IdJurisdiccion = $id;
        
        $resultado = $jurisdiccion->Activar();
        
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionDarBaja($id)
    {
        PermisosHelper::verificarPermiso('DarBajaJurisdiccion');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return['error' => 'La jurisdicción indicada no es válida.'];
        }
        
        $jurisdiccion = new Jurisdicciones();
        $jurisdiccion->IdJurisdiccion = $id;
        
        $resultado = $jurisdiccion->DarBaja();
        
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
}
