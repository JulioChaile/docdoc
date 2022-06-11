<?php
namespace backend\controllers;

use common\models\GestorCompetencias;
use common\models\Competencias;
use common\models\forms\BusquedaForm;
use common\components\PermisosHelper;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;

class CompetenciasController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }

    public function actionAlta()
    {
        $Competencia = new Competencias();
        $Competencia->setScenario(Competencias::_ALTA);
        if ($Competencia->load(Yii::$app->request->post()) && $Competencia->validate()) {
            Yii::$app->response->format = 'json';
            
            $gestor = new GestorCompetencias();
            $resultado = $gestor->Alta($Competencia);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-competencias', [
                        'model' => $Competencia,
                        'titulo' => 'Nueva competencia'
            ]);
        }
    }

    public function actionBorrar($id)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id)) {
            return['error' => 'La competencia indicada no es válida.'];
        }
        
        $Competencia = new Competencias();
        $Competencia->IdCompetencia = $id;
        
        $gestor = new GestorCompetencias();
        $resultado = $gestor->Borrar($Competencia);
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
        
        $Competencia = new Competencias();
        $Competencia->IdCompetencia = $id;
        $Competencia->setScenario(Competencias::_MODIFICAR);
        if ($Competencia->load(Yii::$app->request->post()) && $Competencia->validate()) {
            Yii::$app->response->format = 'json';
            
            $gestor = new GestorCompetencias();
            $resultado = $gestor->Modificar($Competencia);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $Competencia->Dame();
            return $this->renderAjax('datos-competencias', [
                        'model' => $Competencia,
                        'titulo' => 'Modificar Competencia'
            ]);
        }
    }

    public function actionListar($cadena = '')
    {
        PermisosHelper::verificarPermiso('BuscarTiposCaso');
        
        $busqueda = new BusquedaForm();
        $Cadena = $cadena;
        $IncluyeBajas = 'N';
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
            $IncluyeBajas = ($busqueda->Check ?? 'N');
        }
        
        $gestor = new GestorCompetencias();
        $Competencias = $gestor->Buscar($Cadena, $IncluyeBajas);
        
        return $this->render('index', [
                'models' => $Competencias,
                'busqueda' => $busqueda
        ]);
    }

    public function actionActivar($id)
    {
        PermisosHelper::verificarPermiso('ActivarTipoCaso');
        
        Yii::$app->response->format = 'json';
        if (!intval($id)) {
            return['error' => 'El tipo de caso indicado no es válido.'];
        }
        
        $competencia = new Competencias();
        $competencia->IdCompetencia = $id;
        
        $resultado = $competencia->Activar();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionDarBaja($id)
    {
        PermisosHelper::verificarPermiso('DarBajaTipoCaso');
        
        Yii::$app->response->format = 'json';
        if (!intval($id)) {
            return['error' => 'El tipo de caso indicado no es válido.'];
        }
        
        $competencia = new Competencias();
        $competencia->IdCompetencia = $id;
        
        $resultado = $competencia->DarBaja();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }

    public function actionTiposCaso($id)
    {
        if (!intval($id)) {
            throw new HttpException('422', 'La competencia indicada no es válida.');
        }
        
        $Competencia = new Competencias();
        $Competencia->IdCompetencia = $id;
        $Competencia->Dame();
        return $this->renderAjax('datos-tipos-caso-competencias', [
                    'model' => $Competencia,
                    'titulo' => 'Tipos Casos Competencia: ' . $Competencia->Competencia
        ]);
    }

    public function actionAgregarTipoCaso($id, $idTipoCaso)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id) || !intval($idTipoCaso)) {
            return ['error' => 'La competencia indicada no es válida.'];
        }
        
        $Competencia = new Competencias();
        $Competencia->IdCompetencia = $id;
        $mensaje = $Competencia->AgregarTipoCaso($idTipoCaso);
        if ($mensaje != 'OK') {
            return ['error' => $mensaje];
        }

        return ['error' => null];
    }

    public function actionQuitarTipoCaso($id, $idTipoCaso)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id) || !intval($idTipoCaso)) {
            return ['error' => 'La competencia indicada no es válida.'];
        }
        
        $Competencia = new Competencias();
        $Competencia->IdCompetencia = $id;
        $mensaje = $Competencia->BorrarTipoCaso($idTipoCaso);
        if ($mensaje != 'OK') {
            return ['error' => $mensaje];
        }

        return ['error' => null];
    }
}
