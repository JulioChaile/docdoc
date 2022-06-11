<?php

namespace backend\controllers;

use common\components\EmailHelper;
use common\components\PermisosHelper;
use common\models\Empresa;
use common\models\forms\BusquedaForm;
use common\models\forms\CambiarPasswordForm;
use backend\models\GestorRoles;
use common\models\GestorUsuarios;
use common\models\Usuarios;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\HttpException;

class UsuariosController extends Controller
{
    public function actionIndex()
    {
        return $this->actionListar();
    }
    
    public function actionListar($Tipo = 'T', $Cadena = '', $IncluyeBajas = 'N')
    {
        if (!in_array('BuscarUsuarios', Yii::$app->session->get('Permisos'))) {
            throw new HttpException('403', 'No se tienen los permisos necesarios para ver la página solicitada.');
        }
        
        $busqueda = new BusquedaForm();
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
            $IncluyeBajas = $busqueda->Check;
            $Tipo = $busqueda->Combo;
        }
        
        $gestor = new GestorUsuarios();
        $usuarios = $gestor->Buscar($Tipo, $Cadena, $IncluyeBajas);
        
        return $this->render('index', [
                    'models' => $usuarios,
                    'busqueda' => $busqueda,
        ]);
    }
    
    public function actionAlta()
    {
        if (!in_array('AltaUsuario', Yii::$app->session->get('Permisos'))) {
            throw new HttpException('403', 'No se tienen los permisos necesarios para ver la página solicitada.');
        }
        
        $usuario = new Usuarios();
        $usuario->setScenario(Usuarios::_ALTA);
        if ($usuario->load(Yii::$app->request->post()) && $usuario->validate()) {
            Yii::$app->response->format = 'json';
            
            $pass = rand(0, 9999);
            $usuario->Password = $pass;
            $gestor = new GestorUsuarios();
            $resultado = $gestor->Alta($usuario);
            if (substr($resultado, 0, 2) == 'OK') {
                //$htmlBody = $this->renderPartial('@app/mail/alta-usuario', ['usuario' => $usuario->Usuario, 'password' => $pass]);
                EmailHelper::enviarEmail(
                    'info@docdoc.com.ar', 
                    $usuario->Email, 
                    'Bienvenido a Doc Doc', 
                    'alta-usuario', 
                    [
                        'usuario' => $usuario->Usuario, 
                        'password' => $pass
                    ]
                );
                
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $gestorRoles = new GestorRoles();
            $listadoRoles = $gestorRoles->Buscar();
            return $this->renderAjax('datos-usuario', [
                'model' => $usuario,
                'titulo' => 'Alta de usuario',
                'listadoRoles' => $listadoRoles,
            ]);
        }
    }
    
    public function actionModificar($id)
    {
        if (!in_array('ModificarUsuario', Yii::$app->session->get('Permisos'))) {
            throw new HttpException('403', 'No se tienen los permisos necesarios para ver la página solicitada.');
        }
        
        $usuario = new Usuarios();
        $usuario->setScenario(Usuarios::_MODIFICAR);
        if (intval($id)) {
            $usuario->IdUsuario = $id;
        } else {
            throw new HttpException('422', 'El usuario indicado es inválido.');
        }
        
        if ($usuario->load(Yii::$app->request->post()) && $usuario->validate()) {
            Yii::$app->response->format = 'json';
            
            $usuario->IdRol = null;
            $gestor = new GestorUsuarios();
            $resultado = $gestor->Modificar($usuario);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $usuario->Dame();
            $gestorRoles = new GestorRoles();
            $roles = $gestorRoles->Buscar();
            return $this->renderAjax('datos-usuario', [
                        'model' => $usuario,
                        'listadoRoles' => $roles,
                        'titulo' => 'Modificar usuario'
            ]);
        }
    }
    
    public function actionBorrar($id)
    {
        if (!in_array('BorrarUsuario', Yii::$app->session->get('Permisos'))) {
            throw new HttpException('403', 'No se tienen los permisos necesarios para ver la página solicitada.');
        }
        
        $usuario = new Usuarios();
        if (intval($id)) {
            $usuario->IdUsuario = $id;
        } else {
            throw new HttpException('422', 'El usuario indicado es inválido.');
        }
        
        Yii::$app->response->format = 'json';
        $gestor = new GestorUsuarios();
        $resultado = $gestor->Borrar($usuario);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionActivar($id)
    {
        if (!in_array('ActivarUsuario', Yii::$app->session->get('Permisos'))) {
            throw new HttpException('403', 'No se tienen los permisos necesarios para ver la página solicitada.');
        }
        
        $usuario = new Usuarios();
        if (intval($id)) {
            $usuario->IdUsuario = $id;
        } else {
            throw new HttpException('422', 'El usuario indicado es inválido.');
        }
        
        Yii::$app->response->format = 'json';
        $resultado = $usuario->Activar();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionDarBaja($id)
    {
        if (!in_array('DarBajaUsuario', Yii::$app->session->get('Permisos'))) {
            throw new HttpException('403', 'No se tienen los permisos necesarios para ver la página solicitada.');
        }
        
        $usuario = new Usuarios();
        if (intval($id)) {
            $usuario->IdUsuario = $id;
        } else {
            throw new HttpException('422', 'El usuario indicado es inválido.');
        }
        
        Yii::$app->response->format = 'json';
        $resultado = $usuario->DarBaja();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionLogin()
    {
        // Si ya estoy logueado redirecciona al home
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        // Guardo también en la sesión los parámetros de Empresa
        $empresa = new Empresa();
        Yii::$app->session->open();
        Yii::info(Yii::$app->user->returnUrl);
        $this->layout = 'login';

        $usuario = new Usuarios();
        $usuario->setScenario(Usuarios::_LOGIN);

        if ($usuario->load(Yii::$app->request->post()) && $usuario->validate()) {
            $login = $usuario->Login('A', $usuario->Password, Yii::$app->security->generateRandomString(300));

            if ($login['Mensaje'] == 'OK') {
                Yii::$app->user->login($usuario);
                Yii::$app->session->set('Token', $usuario->Token);
                Yii::$app->session->set('Parametros', ArrayHelper::map($empresa->DameDatos(), 'Parametro', 'Valor'));

                PermisosHelper::guardarPermisosSesion($usuario->DamePermisos());

                // El usuario debe modificar el password
                if ($usuario->DebeCambiarPass == 'S') {
                    Yii::$app->session->setFlash('info', 'Debe modificar su contraseña antes de ingresar.');
                    return $this->redirect('/usuarios/cambiar-password');
                } else {
                    return $this->redirect(Yii::$app->user->returnUrl);
                }
            } else {
                $usuario->Password = null;
                Yii::$app->session->setFlash('danger', $login['Mensaje']);
            }
        }
        Yii::$app->session->set('Parametros', ArrayHelper::map($empresa->DameDatos(), 'Parametro', 'Valor'));

        return $this->render('login', [
                    'model' => $usuario,
        ]);
    }
    
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
    
    public function actionCambiarPassword()
    {
        $form = new CambiarPasswordForm();

        $this->layout = 'login';

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $usuario = Yii::$app->user->identity;

            $mensaje = $usuario->CambiarPassword($usuario->Token, $form->Anterior, $form->Password_repeat);

            if ($mensaje == 'OK') {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('success', 'La contraseña fue modificada.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('danger', $mensaje);
                return $this->render('password', [
                            'model' => $form,
                ]);
            }
        } else {
            return $this->render('password', [
                        'model' => $form,
            ]);
        }
    }
}
