<?php

namespace backend\controllers;

use common\components\PermisosHelper;
use common\models\forms\BusquedaForm;
use common\models\GestorTiposCaso;
use common\models\RolesTipoCaso;
use common\models\TiposCaso;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class TiposCasoController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }
    
    public function actionListar()
    {
        PermisosHelper::verificarPermiso('BuscarTiposCaso');
        
        $busqueda = new BusquedaForm();
        $Cadena = '';
        $IncluyeBajas = 'N';
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
            $IncluyeBajas = $busqueda->Check;
        }
        
        $gestor = new GestorTiposCaso();
        $tiposCaso = $gestor->Buscar($Cadena, $IncluyeBajas);
        
        return $this->render('index', [
                'models' => $tiposCaso,
                'busqueda' => $busqueda
        ]);
    }
    
    public function actionAlta()
    {
        PermisosHelper::verificarPermiso('AltaTipoCaso');
        
        $tipoCaso = new TiposCaso();
        $tipoCaso->setScenario(TiposCaso::_ALTA);
        $json = json_encode($tipoCaso);
        if ($tipoCaso->load(Yii::$app->request->post()) && $tipoCaso->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorTiposCaso();
            $resultado = $gestor->Alta($tipoCaso);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-tipos-caso', [
                        'model' => $tipoCaso,
                        'titulo' => 'Nuevo tipo de caso'
            ]);
        }
    }
    
    public function actionModificar($id)
    {
        PermisosHelper::verificarPermiso('ModificarTipoCaso');
        
        if (!intval($id)) {
            throw new HttpException('422', 'El tipo de caso indicado no es válido.');
        }
        
        $tipoCaso = new TiposCaso();
        $tipoCaso->IdTipoCaso = $id;
        $tipoCaso->setScenario(TiposCaso::_MODIFICAR);
        if ($tipoCaso->load(Yii::$app->request->post()) && $tipoCaso->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorTiposCaso();
            $resultado = $gestor->Modificar($tipoCaso);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $tipoCaso->Dame();
            return $this->renderAjax('datos-tipos-caso', [
                        'model' => $tipoCaso,
                        'titulo' => 'Modificar tipo de caso'
            ]);
        }
    }
    
    public function actionBorrar($id)
    {
        PermisosHelper::verificarPermiso('BorrarTipoCaso');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!intval($id)) {
            return['error' => 'El tipo de caso indicado no es válido.'];
        }
        
        $tipoCaso = new TiposCaso();
        $tipoCaso->IdTipoCaso = $id;
        
        $gestor = new GestorTiposCaso();
        $resultado = $gestor->Borrar($tipoCaso);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionActivar($id)
    {
        PermisosHelper::verificarPermiso('ActivarTipoCaso');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!intval($id)) {
            return['error' => 'El tipo de caso indicado no es válido.'];
        }
        
        $tipoCaso = new TiposCaso();
        $tipoCaso->IdTipoCaso = $id;
        
        $resultado = $tipoCaso->Activar();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionDarBaja($id)
    {
        PermisosHelper::verificarPermiso('DarBajaTipoCaso');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!intval($id)) {
            return['error' => 'El tipo de caso indicado no es válido.'];
        }
        
        $tipoCaso = new TiposCaso();
        $tipoCaso->IdTipoCaso = $id;
        
        $resultado = $tipoCaso->DarBaja();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionRoles($id)
    {
        PermisosHelper::verificarPermiso('BuscarTiposCaso');
        
        $tipoCaso = new TiposCaso();
        $tipoCaso->IdTipoCaso = $id;
        $tipoCaso->Dame();
        $roles = $tipoCaso->ListarRoles();
        
        return $this->render('roles', [
                    'models' => $roles,
                    'tipoCaso' => $tipoCaso
        ]);
    }
    
    public function actionAltaRol($id)
    {
        PermisosHelper::verificarPermiso('AltaTipoCaso');
        
        if (!intval($id)) {
            throw new HttpException('422', 'El tipo de caso indicado no es válido.');
        }
        
        $rtc = new RolesTipoCaso();
        $rtc->IdTipoCaso = $id;
        $tipoCaso = new TiposCaso();
        $tipoCaso->IdTipoCaso = $id;
        if ($rtc->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($rtc->Parametros) {
                $array = array();
                foreach ($rtc->Parametros as $par) {
                    $array[] = [
                        'Descripcion' => $par['Descripcion'],
                        'Parametro' => $par['Parametro'],
                        'Obligatorio' => !array_key_exists('Obligatorio', $par) ? false : $par['Obligatorio'],
                        'TipoDato' => $par['TipoDato']
                    ];
                }
                $rtc->Parametros = json_encode($array);
            }
            $resultado = $tipoCaso->AltaRol($rtc);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-roles-tipo-caso', [
                        'model' => $rtc,
                        'titulo' => 'Nuevo Rol'
            ]);
        }
    }
    
    public function actionModificarRol($id)
    {
        PermisosHelper::verificarPermiso('ModificarTipoCaso');
        
        if (!intval($id)) {
            throw new HttpException('422', 'El tipo de caso indicado no es válido.');
        }
        
        $rtc = new RolesTipoCaso();
        $rtc->IdRTC = $id;
        if ($rtc->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($rtc->Parametros) {
                $array = array();
                foreach ($rtc->Parametros as $par) {
                    $array[] = [
                        'Descripcion' => $par['Descripcion'],
                        'Parametro' => $par['Parametro'],
                        'Obligatorio' => !array_key_exists('Obligatorio', $par) ? false : $par['Obligatorio'],
                        'TipoDato' => $par['TipoDato']
                    ];
                }
                $rtc->Parametros = json_encode($array);
            }
            $tipoCaso = new TiposCaso();
            $resultado = $tipoCaso->ModificarRol($rtc);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $rtc->Dame();
            return $this->renderAjax('datos-roles-tipo-caso', [
                        'model' => $rtc,
                        'titulo' => 'Modificar Rol'
            ]);
        }
    }
    
    public function actionBorrarRol($id)
    {
        PermisosHelper::verificarPermiso('BorrarTipoCaso');
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return['error' => 'El tipo de caso indicado no es válido.'];
        }
        
        $rtc = new RolesTipoCaso();
        $rtc->IdRTC = $id;
        $tipoCaso = new TiposCaso();
        
        $resultado = $tipoCaso->BorrarRol($rtc);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }

    public function actionAgregarJuzgado($id, $idJuzgado, $idCompetencia)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id) || !intval($idJuzgado) || !intval($idCompetencia)) {
            return ['error' => 'La competencia indicada no es válida.'];
        }
        
        $TipoCaso = new TiposCaso();
        $TipoCaso->IdTipoCaso = $id;
        $mensaje = $TipoCaso->AgregarJuzgado($idJuzgado, $idCompetencia);
        if ($mensaje != 'OK') {
            return ['error' => $mensaje];
        }

        return ['error' => null];
    }

    public function actionQuitarJuzgado($id, $idJuzgado, $idCompetencia)
    {
        Yii::$app->response->format = 'json';
        if (!intval($id) || !intval($idJuzgado) || !intval($idCompetencia)) {
            return ['error' => 'La competencia indicada no es válida.'];
        }
        
        $TipoCaso = new TiposCaso();
        $TipoCaso->IdTipoCaso = $id;
        $mensaje = $TipoCaso->QuitarJuzgado($idJuzgado, $idCompetencia);
        if ($mensaje != 'OK') {
            return ['error' => $mensaje];
        }

        return ['error' => null];
    }
    
    public function actionJuzgados($id)
    {
        if (!intval($id)) {
            throw new HttpException('422', 'La competencia indicada no es válida.');
        }
        
        $TipoCaso = new TiposCaso();
        $TipoCaso->IdTipoCaso = $id;
        $TipoCaso->ListarJuzgados();
        return $this->renderAjax('juzgados', [
                    'model' => $TipoCaso,
                    'titulo' => 'Juzgados Tipos de Caso: ' . $TipoCaso->TipoCaso
        ]);
    }
}
