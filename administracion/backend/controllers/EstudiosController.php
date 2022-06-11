<?php

namespace backend\controllers;

use backend\models\GestorEstudios;
use backend\models\GestorOrigenes;
use backend\models\RolesEstudio;
use common\models\UsuariosEstudio;
use common\models\CalendariosEstudio;
use common\models\UsuariosAcl;
use common\components\Calendar;
use common\components\EmailHelper;
use common\components\PermisosHelper;
use common\models\Empresa;
use common\models\EstadosCaso;
use common\models\ObjetivosEstudio;
use common\models\Cuadernos;
use common\models\MensajesEstudio;
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
        return $this->actionListar();
    }
    
    public function actionListar($Cadena = '', $IncluyeBajas = 'N')
    {
        PermisosHelper::verificarPermiso('BuscarEstudios');
        
        $busqueda = new BusquedaForm();
        
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
            $IncluyeBajas = $busqueda->Check;
        }
        
        $gestor = new GestorEstudios();
        
        $estudios = $gestor->Buscar($Cadena, $IncluyeBajas);
        
        return $this->render('index', [
                'models' => $estudios,
                'busqueda' => $busqueda
        ]);
    }
    
    public function actionAlta()
    {
        PermisosHelper::verificarPermiso('AltaEstudio');
        
        $estudio = new Estudios();
        $estudio->setScenario(Estudios::_ALTA);
        if ($estudio->load(Yii::$app->request->post()) && $estudio->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $gestor = new GestorEstudios();
            $resultado = $gestor->Alta($estudio);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-estudio', [
                        'model' => $estudio,
                        'titulo' => 'Nuevo estudio jurídico'
            ]);
        }
    }
    
    public function actionActivar($id)
    {
        PermisosHelper::verificarPermiso('ActivarEstudio');
                
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            ['error' => 'El estudio indicado no es válido.'];
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
        
        $resultado = $estudio->Activar();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionDarBaja($id)
    {
        PermisosHelper::verificarPermiso('DarBajaEstudio');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            ['error' => 'El estudio indicado no es válido.'];
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
        
        $resultado = $estudio->DarBaja();
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionBorrar($id)
    {
        PermisosHelper::verificarPermiso('BorrarEstudio');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            ['error' => 'El estudio indicado no es válido.'];
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
        
        $gestor = new GestorEstudios();
        $resultado = $gestor->Borrar($estudio);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionAutocompletar($id = 0, $cadena = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if ($id != 0) {
            $estudio = new Estudios();
            
            $estudio->IdEstudio = $id;
            
            $estudio->Dame();
            
            $out = [
                'id' => $estudio->IdEstudio,
                'text' => $estudio->Estudio
            ];
        } else {
            $gestor = new GestorEstudios();
            
            $estudios = $gestor->Buscar($cadena);
            
            $out = array();
            
            foreach ($estudios as $estudio) {
                $out[] = [
                    'id' => $estudio['IdEstudio'],
                    'text' => $estudio['Estudio']
                ];
            }
        }
        return $out;
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
    
    public function actionUsuarios($id = 0, $Cadena = '', $IncluyeBajas = 'N')
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
        $estudio->IdEstudio = $id;
        $estudio->Dame();
        $usuarios = $estudio->BuscarUsuarios($Cadena, $IncluyeBajas);
        
        return $this->render('usuarios', [
                'models' => $usuarios,
                'busqueda' => $busqueda,
                'estudio' => $estudio
        ]);
    }
    
    public function actionAltaUsuario($id, $cadete = '')
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
            
            if ($usuario->Observaciones !== 'cadete') {
                $pass = rand(pow(10, 4), pow(10, 5)-1);
                $usuario->Password = $pass;
            }
            
            $resultado = $estudio->AltaUsuario($usuario);
            if (substr($resultado, 0, 2) == 'OK') {
                if ($usuario->Observaciones !== 'cadete') {
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
                }
                
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $roles = $estudio->ListarRoles();
            if (!empty($cadete)) {
                $usuario->IdRolEstudio = $roles[0]['IdRolEstudio'];
                $usuario->Observaciones = 'cadete';
            }
            return $this->renderAjax('datos-usuario', [
                        'model' => $usuario,
                        'roles' => $roles,
                        'cadete' => $cadete
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
    
    public function actionEstudio($id)
    {
        if (!intval($id)) {
            throw new HttpException('422', 'El estudio indicado no es válido.');
        }
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
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
        
        return $this->render('estudio', [
                    'estudio' => $estudio,
        ]);
    }
    
    public function actionOrigenes($id)
    {
        $estudio = new Estudios();
        
        $estudio->IdEstudio = $id;
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
        $origen->IdEstudio = $id;
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
    
    public function actionRoles($id)
    {
        PermisosHelper::verificarPermiso('ListarRolesEstudio');
        
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
        
        $roles = $estudio->ListarRoles();
        
        return $this->render('roles', [
                    'estudio' => $estudio,
                    'models' => $roles,
        ]);
    }
    
    public function actionAltaRol($id)
    {
        PermisosHelper::verificarPermiso('AltaRolEstudio');
        
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $rolEstudio = new RolesEstudio();
        if ($rolEstudio->load(Yii::$app->request->post()) && $rolEstudio->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $estudio = new Estudios();
            $estudio->IdEstudio = $id;
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
    
    public function actionEstadosCaso($id)
    {
        PermisosHelper::verificarPermiso('BuscarEstadosCaso');
        
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
        $estudio->Dame();
        
        $estadosCaso = $estudio->ListarEstadosCaso();
        return $this->render('estados-caso', [
                    'estudio' => $estudio,
                    'estadosCaso' => $estadosCaso,
        ]);
    }
    
    public function actionAltaEstadoCaso($id)
    {
        PermisosHelper::verificarPermiso('AltaEstadoCaso');
        
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $estadoCaso = new EstadosCaso();
        $estadoCaso->setScenario(EstadosCaso::_ALTA);
        $estadoCaso->IdEstudio = $id;
        if ($estadoCaso->load(Yii::$app->request->post()) && $estadoCaso->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $estudio = new Estudios();
            $estudio->IdEstudio = $id;
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

    public function actionObjetivos($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
        $estudio->Dame();
        
        $objetivos = $estudio->ListarObjetivos();
        return $this->render('objetivos', [
                    'estudio' => $estudio,
                    'objetivos' => $objetivos,
        ]);
    }
    
    public function actionAltaObjetivo($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $Objetivo = new ObjetivosEstudio();
        $Objetivo->setScenario(ObjetivosEstudio::_ALTA);
        $Objetivo->IdEstudio = $id;
        if ($Objetivo->load(Yii::$app->request->post()) && $Objetivo->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $estudio = new Estudios();
            $estudio->IdEstudio = $id;
            $resultado = $estudio->AltaObjetivo($Objetivo);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-objetivos', [
                        'objetivo' => $Objetivo,
                        'titulo' => 'Nuevo Objetivo'
            ]);
        }
    }
    
    public function actionModificarObjetivo($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estado de caso indicado no es válido.');
        }
        
        $Objetivo = new ObjetivosEstudio();
        $Objetivo->setScenario(ObjetivosEstudio::_MODIFICAR);
        $Objetivo->IdObjetivoEstudio = $id;
        if ($Objetivo->load(Yii::$app->request->post()) && $Objetivo->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $estudio = new Estudios();
            $resultado = $estudio->ModificarObjetivo($Objetivo);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $Objetivo->Dame();
            return $this->renderAjax('datos-objetivos', [
                        'objetivo' => $Objetivo,
                        'titulo' => 'Modificar Objetivo'
            ]);
        }
    }
    
    public function actionBorrarObjetivo($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'El estado de caso indicado no es válido.'];
        }
        
        $Objetivo = new ObjetivosEstudio();
        $Objetivo->IdObjetivoEstudio= $id;
        $estudio = new Estudios();
        $resultado = $estudio->BorrarObjetivo($Objetivo);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }

    public function actionCuadernos($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
        $estudio->Dame();
        
        $cuadernos = $estudio->ListarCuadernos();
        return $this->render('cuadernos', [
                    'estudio' => $estudio,
                    'cuadernos' => $cuadernos,
        ]);
    }
    
    public function actionAltaCuaderno($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $Cuaderno = new Cuadernos();
        $Cuaderno->setScenario(Cuadernos::_ALTA);
        $Cuaderno->IdEstudio = $id;
        if ($Cuaderno->load(Yii::$app->request->post()) && $Cuaderno->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $estudio = new Estudios();
            $estudio->IdEstudio = $id;
            $resultado = $estudio->AltaCuaderno($Cuaderno);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-cuadernos', [
                        'cuaderno' => $Cuaderno,
                        'titulo' => 'Nuevo Cuaderno'
            ]);
        }
    }
    
    public function actionModificarCuaderno($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estado de caso indicado no es válido.');
        }
        
        $Cuaderno = new Cuadernos();
        $Cuaderno->setScenario(Cuadernos::_MODIFICAR);
        $Cuaderno->IdCuaderno = $id;
        if ($Cuaderno->load(Yii::$app->request->post()) && $Cuaderno->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $estudio = new Estudios();
            $resultado = $estudio->ModificarCuaderno($Cuaderno);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $Cuaderno->Dame();
            return $this->renderAjax('datos-cuadernos', [
                        'cuaderno' => $Cuaderno,
                        'titulo' => 'Modificar Cuaderno'
            ]);
        }
    }
    
    public function actionBorrarCuaderno($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'El estado de caso indicado no es válido.'];
        }
        
        $Cuaderno = new Cuadernos();
        $Cuaderno->IdCuaderno = $id;
        $estudio = new Estudios();
        $resultado = $estudio->BorrarCuaderno($Cuaderno);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }

    public function actionMensajes($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
        $estudio->Dame();
        
        $mensajes = $estudio->ListarMensajes();
        return $this->render('mensajes', [
                    'estudio' => $estudio,
                    'mensajes' => $mensajes,
        ]);
    }
    
    public function actionAltaMensaje($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $Mensaje = new MensajesEstudio();
        $Mensaje->setScenario(MensajesEstudio::_ALTA);
        $Mensaje->IdEstudio = $id;
        if ($Mensaje->load(Yii::$app->request->post()) && $Mensaje->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $estudio = new Estudios();
            $estudio->IdEstudio = $id;
            $resultado = $estudio->AltaMensaje($Mensaje);
            if (substr($resultado, 0, 2) == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-mensajes', [
                        'mensaje' => $Mensaje,
                        'titulo' => 'Nuevo Mensaje'
            ]);
        }
    }
    
    public function actionModificarMensaje($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estado de caso indicado no es válido.');
        }
        
        $Mensaje = new MensajesEstudio();
        $Mensaje->setScenario(MensajesEstudio::_MODIFICAR);
        $Mensaje->IdMensajeEstudio = $id;
        if ($Mensaje->load(Yii::$app->request->post()) && $Mensaje->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $estudio = new Estudios();
            $resultado = $estudio->ModificarMensaje($Mensaje);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $Mensaje->Dame();
            return $this->renderAjax('datos-mensajes', [
                        'mensaje' => $Mensaje,
                        'titulo' => 'Modificar Mensaje'
            ]);
        }
    }
    
    public function actionBorrarMensaje($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'El estado de caso indicado no es válido.'];
        }
        
        $Mensaje = new MensajesEstudio();
        $Mensaje->IdMensajeEstudio= $id;
        $estudio = new Estudios();
        $resultado = $estudio->BorrarMensaje($Mensaje);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
    
    public function actionTiposMovimiento($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido');
        }
        
        $busqueda = new BusquedaForm();
        if ($busqueda->load(Yii::$app->request->post()) && $busqueda->validate()) {
            $Cadena = $busqueda->Cadena;
            $Categoria = $busqueda->Combo;
        } else {
            $Cadena = '';
            $Categoria = 'T';
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
        $estudio->Dame();
        
        $tipos = $estudio->BuscarTiposMovimiento($Cadena, $Categoria);
        
        return $this->render('tipos-movimiento', [
                    'estudio' => $estudio,
                    'models' => $tipos,
                    'busqueda' => $busqueda
        ]);
    }
    
    public function actionAltaTipoMovimiento($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido');
        }
        
        $tipo = new TiposMovimiento();
        $tipo->IdEstudio = $id;
        if ($tipo->load(Yii::$app->request->post()) && $tipo->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $estudio = new Estudios();
            $estudio->IdEstudio = $id;
            
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
    
    public function actionParametros($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
        $estudio->Dame();
        
        $parametros = $estudio->ListarParametros();
        
        return $this->render('parametros', [
                    'estudio' => $estudio,
                    'models' => $parametros,
        ]);
    }
    
    public function actionModificarParametro($id = 0, $parametro = null)
    {
        PermisosHelper::verificarPermiso('ModificarParametro');
        
        if (!intval($id) || is_null($parametro)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
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

    public function actionEventos($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $estudio = new Estudios();
        $estudio->IdEstudio = $id;
        $estudio->Dame();
        
        $calendario = $estudio->ListarCalendarios();

        !empty($calendario) ? $eventos = $estudio->BuscarEventos($calendario['IdCalendario'], '') : $eventos = array();

        $colores = [
            'calendar' => [ 
                '1' => '#ac725e',
                '2' => '#d06b64',
                '3' => '#f83a22',
                '4' => '#fa573c',
                '5' => '#ff7537',
                '6' => '#ffad46',
                '7' => '#42d692',
                '8' => '#16a765',
                '9' => '#7bd148',
                '10' => '#b3dc6c',
                '11' => '#fbe983',
                '12' => '#fad165',
                '13' => '#92e1c0',
                '14' => '#9fe1e7',
                '15' => '#9fc6e7',
                '16' => '#4986e7',
                '17' => '#9a9cff',
                '18' => '#b99aff',
                '19' => '#c2c2c2',
                '20' => '#cabdbf',
                '21' => '#cca6ac',
                '22' => '#f691b2',
                '23' => '#cd74e6',
                '24' => '#a47ae2'
            ],
            'event' => [
                '1' => '#a4bdfc',
                '2' => '#7ae7bf',
                '3' => '#dbadff',
                '4' => '#ff887c',
                '5' => '#fbd75b',
                '6' => '#ffb878',
                '7' => '#46d6db',
                '8' => '#e1e1e1',
                '9' => '#5484ed',
                '10' => '#51b749',
                '11' => '#dc2127'
            ]
        ];
            
        return $this->render('calendarios', [
                    'estudio' => $estudio,
                    'calendario' => $calendario,
                    'eventos' => $eventos,
                    'colores' => $colores
        ]);
    }
    
    public function actionAltaCalendario($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estudio indicado no es válido.');
        }
        
        $Calendario = new CalendariosEstudio();
        $Calendario->setScenario(CalendariosEstudio::_ALTA);
        $Calendario->IdEstudio = $id;

        $colores = [ 
            '1' => '#ac725e',
            '2' => '#d06b64',
            '3' => '#f83a22',
            '4' => '#fa573c',
            '5' => '#ff7537',
            '6' => '#ffad46',
            '7' => '#42d692',
            '8' => '#16a765',
            '9' => '#7bd148',
            '10' => '#b3dc6c',
            '11' => '#fbe983',
            '12' => '#fad165',
            '13' => '#92e1c0',
            '14' => '#9fe1e7',
            '15' => '#9fc6e7',
            '16' => '#4986e7',
            '17' => '#9a9cff',
            '18' => '#b99aff',
            '19' => '#c2c2c2',
            '20' => '#cabdbf',
            '21' => '#cca6ac',
            '22' => '#f691b2',
            '23' => '#cd74e6',
            '24' => '#a47ae2'
        ];

        if ($Calendario->load(Yii::$app->request->post()) && $Calendario->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $estudio = new Estudios();
            $estudio->IdEstudio = $id;

            $Calendar = new Calendar();
            $respuestaApi = $Calendar->insertCalendar($Calendario->Titulo, $Calendario->Descripcion, '', '');

            if (isset($respuestaApi['Error'])) {
                return ['error' => $respuestaApi['Error']];
            }

            $Calendario->IdCalendarioAPI = $respuestaApi;

            $Calendar->insertAcl($Calendario->IdCalendarioAPI, 'contacto@docdoc.com.ar', 'user', 'owner');

            $resultado = $estudio->AltaCalendario($Calendario);

            if (substr($resultado, 0, 2) == 'OK') {

                $usuarios = $estudio->BuscarUsuarios();

                foreach ($usuarios as $user) {
                    $respuestaApi = $Calendar->insertAcl($Calendario->IdCalendarioAPI, $user['Email']);

                    if (!isset($respuestaApi['Error'])) {
                        $usuarioAcl = new UsuariosAcl;
                        $usuarioAcl->IdACLAPI = $respuestaApi;
                        $usuarioAcl->IdCalendario = substr($resultado, 2);
                        $usuarioAcl->IdUsuario = $user['IdUsuario'];
                        $usuarioAcl->Rol = 'W';


                        $respuesta = $estudio->AltaUsuarioAcl($usuarioAcl);

                        if (substr($respuesta, 0, 2) !== 'OK') {
                            return ['error' => $respuesta];
                        }
                    }
                }

                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            return $this->renderAjax('datos-calendario', [
                        'model' => $Calendario,
                        'colores' => $colores
            ]);
        }
    }
    
    public function actionModificarCalendario($id)
    {
        if (!intval($id)) {
            throw new HttpException(422, 'El estado de caso indicado no es válido.');
        }
        
        $Mensaje = new MensajesEstudio();
        $Mensaje->setScenario(MensajesEstudio::_MODIFICAR);
        $Mensaje->IdMensajeEstudio = $id;
        if ($Mensaje->load(Yii::$app->request->post()) && $Mensaje->validate()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $estudio = new Estudios();
            $resultado = $estudio->ModificarMensaje($Mensaje);
            if ($resultado == 'OK') {
                return ['error' => null];
            } else {
                return ['error' => $resultado];
            }
        } else {
            $Mensaje->Dame();
            return $this->renderAjax('datos-mensajes', [
                        'mensaje' => $Mensaje,
                        'titulo' => 'Modificar Mensaje'
            ]);
        }
    }
    
    public function actionBorrarCalendario($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!intval($id)) {
            return ['error' => 'El estado de caso indicado no es válido.'];
        }
        
        $Mensaje = new MensajesEstudio();
        $Mensaje->IdMensajeEstudio= $id;
        $estudio = new Estudios();
        $resultado = $estudio->BorrarMensaje($Mensaje);
        if ($resultado == 'OK') {
            return ['error' => null];
        } else {
            return ['error' => $resultado];
        }
    }
}
