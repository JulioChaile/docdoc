<?php
namespace backend\controllers;

use common\components\PermisosHelper;
use backend\models\CiasSeguro;
use common\models\forms\BusquedaForm;
use common\models\GestorCasos;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class CiasSeguroController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }
    
    public function actionListar($Cadena = '')
    {        
        $gestor = new GestorCasos();
        $ciasseguro = $gestor->BuscarCiaSeguro($Cadena, 0, 10000);
        
        return $this->render('index', [
                    'models' => $ciasseguro
        ]);
    }
    
    public function actionAlta()
    {
        $cia = new CiasSeguro();
        $cia->setScenario(CiasSeguro::_ALTA);
        if ($cia->load(Yii::$app->request->post()) && $cia->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorCasos();
            $resultado = $gestor->AltaCiaSeguro($cia->CiaSeguro, $cia->Telefono, $cia->Direccion);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-cia', [
                        'model' => $cia,
                        'titulo' => 'Nueva compañia'
            ]);
        }
    }
    
    public function actionModificar($id)
    {
        if (!intval($id)) {
            throw new HttpException('422', 'La compañia indicada no es válida.');
        }
        
        $cia = new CiasSeguro();
        $cia->IdCiaSeguro = $id;
        $cia->setScenario(CiasSeguro::_MODIFICAR);
        if ($cia->load(Yii::$app->request->post()) && $cia->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorCasos();
            $resultado = $gestor->ModificarCiaSeguro($cia);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $cia->Dame();
            return $this->renderAjax('datos-cia', [
                        'model' => $cia,
                        'titulo' => 'Modificar compañia'
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
