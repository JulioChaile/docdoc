<?php
namespace backend\controllers;

use common\models\forms\BusquedaForm;
use backend\models\GestorRoles;
use backend\models\Roles;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;

class RolesController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }
    
    public function actionListar($Cadena = '', $IncluyeBajas = 'N')
    {
        if (!in_array('BuscarRoles', Yii::$app->session->get('Permisos'))) {
            throw new HttpException('403', 'No se tienen los permisos necesarios para ver la página solicitada.');
        }
        
        $busqueda = new BusquedaForm();
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
            $IncluyeBajas = $busqueda->Check;
        }
        $gestor = new GestorRoles();
        $listadoRoles = $gestor->Buscar($Cadena, $IncluyeBajas);
        
        return $this->render('index', [
                    'models' => $listadoRoles,
                    'busqueda' => $busqueda,
        ]);
    }
    
    public function actionAlta()
    {
        if (!in_array('AltaRol', Yii::$app->session->get('Permisos'))) {
            throw new HttpException('403', 'No se tienen los permisos necesarios para ver la página solicitada.');
        }
        
        $rol = new Roles();
        $rol->setScenario(Roles::_ALTA);
        
        if ($rol->load(Yii::$app->request->post()) && $rol->validate()) {
            Yii::$app->response->format = 'json';
            $gestor = new GestorRoles();
            $resultado = $gestor->Alta($rol);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-rol', [
                        'model' => $rol,
                        'titulo' => 'Alta de Rol'
            ]);
        }
    }
    
    public function actionModificar($id)
    {
        if (!in_array('ModificarRol', Yii::$app->session->get('Permisos'))) {
            throw new HttpException('403', 'No se tienen los permisos necesarios para ver la página solicitada.');
        }
        
        $rol = new Roles();
        $rol->setScenario(Roles::_MODIFICAR);
        if (intval($id)) {
            $rol->IdRol = $id;
        } else {
            throw new HttpException('422', 'El rol indacdo es inválido');
        }
        
        if ($rol->load(Yii::$app->request->post()) && $rol->validate()) {
            Yii::$app->response->format = 'json';
            $gestor = new GestorRoles();
            $resultado = $gestor->Modificar($rol);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $rol->Dame();
            return $this->renderAjax('datos-rol', [
                        'model' => $rol,
                        'titulo' => 'Modificar rol',
            ]);
        }
    }
    
    public function actionActivar($id)
    {
        if (!in_array('ActivarRol', Yii::$app->session->get('Permisos'))) {
            throw new HttpException('403', 'No se tienen los permisos necesarios para ver la página solicitada.');
        }
        
        $rol = new Roles();
        if (intval($id)) {
            $rol->IdRol = $id;
        } else {
            throw new HttpException('422', 'El rol indacdo es inválido');
        }
        
        Yii::$app->response->format = 'json';
        $resultado = $rol->Activar();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionDarBaja($id)
    {
        if (!in_array('DarBajaRol', Yii::$app->session->get('Permisos'))) {
            throw new HttpException('403', 'No se tienen los permisos necesarios para ver la página solicitada.');
        }
        
        $rol = new Roles();
        if (intval($id)) {
            $rol->IdRol = $id;
        } else {
            throw new HttpException('422', 'El rol indacdo es inválido');
        }
        
        Yii::$app->response->format = 'json';
        $resultado = $rol->DarBaja();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionPermisos($id)
    {
        if (!in_array('AsignarPermisosRol', Yii::$app->session->get('Permisos'))) {
            throw new HttpException('403', 'No se tienen los permisos necesarios para ver la página solicitada.');
        }
        
        $rol = new Roles();
        if (intval($id)) {
            $rol->IdRol = $id;
        } else {
            throw new HttpException('422', 'El rol indacdo es inválido');
        }
        if (Yii::$app->request->isPost) {
            $permisos = Yii::$app->request->post('Permisos');
            $permisos = $permisos == null ? array() : $permisos;
            $permisosJson = json_encode(array_keys($permisos));
            
            $resultado = $rol->ActualizarPermisos($permisosJson);
            if ($resultado == 'OK') {
                Yii::$app->session->setFlash('info', 'Permisos modificados con éxito. Reinicie sesión.');
            } else {
                Yii::$app->session->setFlash('danger', $resultado);
            }
        }
        
        $rol->Dame();
        $permisos = $rol->ListarPermisos();

        return $this->render('permisos', [
                    'model' => $rol,
                    'permisos' => $permisos,
        ]);
    }
    
    public function actionBorrar($id)
    {
        if (!in_array('BorrarRol', Yii::$app->session->get('Permisos'))) {
            throw new HttpException('403', 'No se tienen los permisos necesarios para ver la página solicitada.');
        }
        
        $rol = new Roles();
        if (intval($id)) {
            $rol->IdRol = $id;
        } else {
            throw new HttpException('422', 'El rol indacdo es inválido');
        }
        
        Yii::$app->response->format = 'json';
        $gestor = new GestorRoles();
        $resultado = $gestor->Borrar($rol);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
}
