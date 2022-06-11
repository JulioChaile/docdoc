<?php

namespace estudios\controllers;

use backend\models\GestorEstudios;
use backend\models\GestorOrigenes;
use backend\models\RolesEstudio;
use common\models\UsuariosEstudio;
use common\components\EmailHelper;
use common\components\PermisosHelper;
use common\models\Empresa;
use common\models\EstadosCaso;
use common\models\Estudios;
use common\models\forms\BusquedaForm;
use common\models\Origenes;
use common\models\TiposMovimiento;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class EstudiosController extends Controller
{
    public function actionIndex()
    {
        $estudio = new Estudios();
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        if ($estudio->load(Yii::$app->request->post()) && $estudio->validate()) {
            $gestor = new GestorEstudios();
            $resultado = $gestor->Modificar($estudio);
            
            if ($resultado == 'OK') {
                Yii::$app->session->setFlash('success', 'Los datos del estudio se actualizaron correctamente.');
            } else {
                Yii::$app->session->setFlash('danger', $resultado);
            }
        }
        $estudio->Dame();
        
        return $this->render('index', [
                    'estudio' => $estudio,
        ]);
    }
  
    public function actionAutocompletarUsuarios($id = 0, $idEstudio = 0, $cadena = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if ($id != 0 && $idEstudio != 0) {
            $usuario = new UsuariosEstudio();
            
            $usuario->IdUsuario = $id;
            $usuario->IdEstudio = $idEstudio;
            
            $usuario->Dame();
            
            $out = [
                'id' => $usuario->IdUsuario,
                'text' => $usuario->Apellidos.', '.$usuario->Nombres. ' ('.$usuario->Usuario.')'
            ];
        } else {
            $estudio = new Estudios();
            
            $estudio->IdEstudio = $idEstudio;
            
            $usuarios = $estudio->BuscarUsuarios($cadena);
            
            $out = array();
            
            foreach ($usuarios as $usuario) {
                $out[] = [
                    'id' => $usuario['IdUsuario'],
                    'text' => $usuario['Apellidos'].', '.$usuario['Nombres'].' ('.$usuario['Usuario'].')'
                ];
            }
        }
        return $out;
    }
    
    public function actionUsuarios($Cadena = '', $IncluyeBajas = 'N')
    {
        PermisosHelper::verificarPermiso('BuscarUsuariosEstudio');
        
        $busqueda = new BusquedaForm();
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
            $IncluyeBajas = $busqueda->Check;
        } else {
            $busqueda->Cadena = $Cadena;
            $busqueda->Check = $IncluyeBajas;
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        $estudio->Dame();
        $usuarios = $estudio->BuscarUsuarios($Cadena, $IncluyeBajas);
        
        return $this->render('usuarios', [
                'models' => $usuarios,
                'busqueda' => $busqueda,
                'estudio' => $estudio
        ]);
    }
    
    public function actionAltaUsuario($id)
    {
        PermisosHelper::verificarPermiso('AltaUsuarioEstudio');
        
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
        $usuario = new UsuariosEstudio();
        $usuario->IdEstudio = $id;
        if ($usuario->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $pass = rand(pow(10, 4), pow(10, 5)-1);
            $usuario->Password = $pass;
            
            $resultado = $estudio->AltaUsuario($usuario);
            if (substr($resultado, 0, 2) == 'OK') {
                $estudio->Dame();
                
                EmailHelper::enviarEmail(
                    'DocDoc <contacto@docdoc.com.ar>',
                    $usuario->Email,
                    'Bienvenido a Doc Doc',
                    'alta-usuario-estudio',
                    [
                        'usuario' => $usuario->Usuario,
                        'password' => $pass,
                        'estudio' => $estudio->Estudio
                    ]
                );
                
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $roles = $estudio->ListarRoles();
            return $this->renderAjax('datos-usuario', [
                        'model' => $usuario,
                        'roles' => $roles,
            ]);
        }
    }
    
    public function actionBorrarUsuario($id, $idUsuario)
    {
        PermisosHelper::verificarPermiso('BorrarUsuarioEstudio');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id) || !intval($idUsuario)) {
            return ['error' => 'Los parámetros son incorrectos'];
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
        $usuario = new UsuariosEstudio();
        $usuario->IdEstudio = $id;
        $usuario->IdUsuario = $idUsuario;
        
        $resultado = $estudio->BorrarUsuario($usuario);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionActivarUsuario($id, $idUsuario)
    {
        PermisosHelper::verificarPermiso('ActivarUsuarioEstudio');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id) || !intval($idUsuario)) {
            return ['error' => 'Los parámetros son incorrectos'];
        }
        
        $usuario = new UsuariosEstudio();
        $usuario->IdEstudio = $id;
        $usuario->IdUsuario = $idUsuario;
        
        $resultado = $usuario->Activar();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionDarBajaUsuario($id, $idUsuario)
    {
        PermisosHelper::verificarPermiso('DarBajaUsuarioEstudio');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id) || !intval($idUsuario)) {
            return ['error' => 'Los parámetros son incorrectos'];
        }
        
        $usuario = new UsuariosEstudio();
        $usuario->IdEstudio = $id;
        $usuario->IdUsuario = $idUsuario;
        
        $resultado = $usuario->DarBaja();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionOrigenes()
    {
        $estudio = new Estudios();
        
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        $estudio->Dame();
        $origenes = $estudio->ListarOrigenes();
        
        return $this->render('origenes', [
                'models' => $origenes,
                'estudio' => $estudio,
        ]);
    }
    
    public function actionModificarOrigen($id)
    {
        if (!intval($id)) {
            throw new HttpException('422', 'El origen indicado no es válido.');
        }
        
        $origen = new Origenes();
        $origen->IdOrigen = $id;
        $origen->setScenario(Origenes::_MODIFICAR);
        if ($origen->load(Yii::$app->request->post()) && $origen->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorOrigenes();
            $resultado = $gestor->Modificar($origen);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $origen->Dame();
            return $this->renderAjax('datos-origen', [
                        'model' => $origen,
                        'titulo' => 'Modificar origen'
            ]);
        }
    }
    
    public function actionAltaOrigen($id)
    {
        if (!intval($id)) {
            throw new HttpException('422', 'El origen indicado no es válido.');
        }
        
        $origen = new Origenes();
        $origen->IdEstudio = Yii::$app->user->identity->IdEstudio;
        $origen->setScenario(Origenes::_ALTA);
        if ($origen->load(Yii::$app->request->post()) && $origen->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorOrigenes();
            $resultado = $gestor->Alta($origen);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-origen', [
                        'model' => $origen,
                        'titulo' => 'Nuevo origen'
            ]);
        }
    }
    
    public function actionBorrarOrigen($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            throw new HttpException('422', 'El origen indicado no es válido.');
        }
        
        $origen = new Origenes();
        $origen->IdOrigen = $id;
        
        $gestor = new GestorOrigenes();
        $resultado = $gestor->Borrar($origen);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionRoles()
    {
        PermisosHelper::verificarPermiso('ListarRolesEstudio');
        
        $estudio = new Estudios();
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        
        $roles = $estudio->ListarRoles();
        
        return $this->render('roles', [
                    'estudio' => $estudio,
                    'models' => $roles,
        ]);
    }
    
    public function actionAltaRol()
    {
        PermisosHelper::verificarPermiso('AltaRolEstudio');
        
        
        $rolEstudio = new RolesEstudio();
        if ($rolEstudio->load(Yii::$app->request->post()) && $rolEstudio->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $estudio = new Estudios();
            $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
            $resultado = $estudio->AltaRol($rolEstudio);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-rol-estudio', [
                        'model' => $rolEstudio
            ]);
        }
    }
    
    public function actionBorrarRol($id)
    {
        PermisosHelper::verificarPermiso('BorrarRolEstudio');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return[ 'error' => 'El rol de estudio indicado no es válido.'];
        }
        
        $estudio = new Estudios();
        $rol = new RolesEstudio();
        $rol->IdRolEstudio = $id;
        $resultado = $estudio->BorrarRol($rol);
        
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionEstadosCaso()
    {
        PermisosHelper::verificarPermiso('BuscarEstadosCaso');
        
        $estudio = new Estudios();
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        $estudio->Dame();
        
        $estadosCaso = $estudio->ListarEstadosCaso();
        return $this->render('estados-caso', [
                    'estudio' => $estudio,
                    'estadosCaso' => $estadosCaso,
        ]);
    }
    
    public function actionAltaEstadoCaso()
    {
        PermisosHelper::verificarPermiso('AltaEstadoCaso');
        
        $estadoCaso = new EstadosCaso();
        $estadoCaso->setScenario(EstadosCaso::_ALTA);
        $estadoCaso->IdEstudio = Yii::$app->user->identity->IdEstudio;
        if ($estadoCaso->load(Yii::$app->request->post()) && $estadoCaso->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $estudio = new Estudios();
            $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
            $resultado = $estudio->AltaEstadoCaso($estadoCaso);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-estados-caso', [
                        'estadoCaso' => $estadoCaso,
                        'titulo' => 'Nuevo Estado de Caso'
            ]);
        }
    }
    
    public function actionModificarEstadoCaso($id)
    {
        PermisosHelper::verificarPermiso('ModificarEstadoCaso');
        
        if (!intval($id)) {
            throw new HttpException(422, 'El estado de caso indicado no es válido.');
        }
        
        
        $estadoCaso = new EstadosCaso();
        $estadoCaso->setScenario(EstadosCaso::_MODIFICAR);
        $estadoCaso->IdEstadoCaso = $id;
        if ($estadoCaso->load(Yii::$app->request->post()) && $estadoCaso->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $estudio = new Estudios();
            $resultado = $estudio->ModificarEstadoCaso($estadoCaso);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $estadoCaso->Dame();
            return $this->renderAjax('datos-estados-caso', [
                        'estadoCaso' => $estadoCaso,
                        'titulo' => 'Modificar Estado de Caso'
            ]);
        }
    }
    
    public function actionBorrarEstadoCaso($id)
    {
        PermisosHelper::verificarPermiso('BorrarEstadoCaso');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'El estado de caso indicado no es válido.'];
        }
        
        $estadoCaso = new EstadosCaso();
        $estadoCaso->IdEstadoCaso = $id;
        $estudio = new Estudios();
        $resultado = $estudio->BorrarEstadoCaso($estadoCaso);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionDarBajaEstadoCaso($id)
    {
        PermisosHelper::verificarPermiso('DarBajaEstadoCaso');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'El estado de caso indicado no es válido.'];
        }
        
        $estadoCaso = new EstadosCaso();
        $estadoCaso->IdEstadoCaso = $id;
        $resultado = $estadoCaso->DarBaja();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionActivarEstadoCaso($id)
    {
        PermisosHelper::verificarPermiso('ActivarEstadoCaso');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'El estado de caso indicado no es válido.'];
        }
        
        $estadoCaso = new EstadosCaso();
        $estadoCaso->IdEstadoCaso = $id;
        $resultado = $estadoCaso->Activar();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionTiposMovimiento()
    {
        $busqueda = new BusquedaForm();
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
            $Categoria = $busqueda->Combo;
        } else {
            $Cadena = '';
            $Categoria = 'T';
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        $estudio->Dame();
        
        $tipos = $estudio->BuscarTiposMovimiento($Cadena, $Categoria);
        
        return $this->render('tipos-movimiento', [
                    'estudio' => $estudio,
                    'models' => $tipos,
                    'busqueda' => $busqueda
        ]);
    }
    
    public function actionAltaTipoMovimiento()
    {
        $tipo = new TiposMovimiento();
        $tipo->IdEstudio = Yii::$app->user->identity->IdEstudio;
        if ($tipo->load(Yii::$app->request->post()) && $tipo->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $estudio = new Estudios();
            $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
            
            $resultado = $estudio->AltaTipoMovimiento($tipo);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-tipos-movimiento', [
                    'model' => $tipo,
                    'titulo' => 'Nuevo Tipo de Movimiento'
            ]);
        }
    }
    
    public function actionModificarTipoMovimiento($id)
    {
        $tipo = new TiposMovimiento();
        $tipo->IdTipoMov = $id;
        if ($tipo->load(Yii::$app->request->post()) && $tipo->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $estudio = new Estudios();
            $resultado = $estudio->ModificarTipoMovimiento($tipo);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $tipo->Dame();
            return $this->renderAjax('datos-tipos-movimiento', [
                    'model' => $tipo,
                    'titulo' => 'Modificar Tipo de Movimiento'
            ]);
        }
    }
    
    public function actionBorrarTipoMovimiento($id)
    {
        PermisosHelper::verificarPermiso('BorrarTipoMovimiento');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!intval($id)) {
            return['error' => 'El tipo de movimiento indicado no es válido.'];
        }
        
        $tipoMovimiento = new TiposMovimiento();
        $tipoMovimiento->IdTipoMov = $id;
        
        $estudio = new Estudios();
        $resultado = $estudio->BorrarTipoMovimiento($tipoMovimiento);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionParametros()
    {
        $estudio = new Estudios();
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        $estudio->Dame();
        
        $parametros = $estudio->ListarParametros();
        
        return $this->render('parametros', [
                    'estudio' => $estudio,
                    'models' => $parametros,
        ]);
    }
    
    public function actionModificarParametro($parametro = null)
    {
        PermisosHelper::verificarPermiso('ModificarParametro');
        
        if (is_null($parametro)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        $estudio->Dame();
        
        $empresa = new Empresa();
        $empresa->setScenario(Empresa::SCENARIO_EDITAR);
        $empresa->Parametro = $parametro;
        if ($empresa->load(Yii::$app->request->post()) && $empresa->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $resultado = $estudio->CambiarParametro($empresa->Parametro, $empresa->Valor);
            
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $empresa->DameParametro($parametro);
            return $this->renderAjax('cambiar-parametro', [
                        'empresa' => $empresa,
                        'estudio' => $estudio,
            ]);
        }
    }
}
