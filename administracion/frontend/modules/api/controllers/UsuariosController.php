<?php

namespace frontend\modules\api\controllers;

use common\models\Usuarios;
use common\models\GestorUsuarios;
use common\models\UsuariosEstudio;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use common\components\EmailHelper;

class UsuariosController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bearerAuth' => [
                    'class' => OptionalBearerAuth::className(),
                    'except' => [
                        'options',
                        'existe',
                        'validar-codigo',
                        'recuperar-cuenta',
                        'alta-cliente'
                    ],
                    'actionsClient' => [
                        'set-token-app-cliente',
                        'reporte-cliente'
                    ]
                ],
            ]
        );
    }

    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $usuario = new UsuariosEstudio();
        
        $usuario->IdUsuario = Yii::$app->user->identity->id;
        
        $usuario->Dame();
        
        return $usuario;
    }
    
    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $usuario = new UsuariosEstudio();
        
        $usuario->IdUsuario = $id;
        
        $usuario->Dame();
        
        return $usuario;
    }

    public function actionReporteCliente() {
        $reporte = Yii::$app->request->post('reporte');

        EmailHelper::enviarEmail(
            'DocDoc <contacto@docdoc.com.ar>',
            'juliochaile96@gmail.com',
            'Reporte de error DocDoc! Clientes',
            'reporte',
            [
                'reporte' => $reporte
            ]
        );

        return ['Error' => null];
    }

    public function actionAltaCliente()
    {
        $usuario = new Usuarios();

        $usuario->setAttributes(Yii::$app->request->post());

        $gestor = new GestorUsuarios();

        $respuesta = $gestor->AltaCliente($usuario);

        if (substr($respuesta, 0, 2) == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $respuesta];
        }
    }
    
    public function actionCambiarPassword()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $OldPass = Yii::$app->request->post('OldPass');
        $NewPass = Yii::$app->request->post('NewPass');
        $Token = Yii::$app->request->post('Token');
        
        $usuario = new Usuarios();
        $usuario->Token = $Token;
        $usuario->DamePorToken();
        
        $resultado = $usuario->CambiarPassword($Token, $OldPass, $NewPass, 'U');
        
        if ($resultado == 'OK') {
            return ['Error' => null];
        }
        
        return ['Error' => $resultado];
    }
    
    public function actionValidarCodigo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $usuario = new Usuarios();
        
        $Codigo = Yii::$app->request->post('Codigo');
        $Usuario = Yii::$app->request->post('Usuario');
        
        $usuario->Usuario = $Usuario;
        $resultado = $usuario->ValidarCodigo($Codigo);
        
        if ($resultado == 'OK') {
            $usuario->Password = $Codigo;
            
            $login = $usuario->Login('C', $usuario->Password, Yii::$app->security->generateRandomString(300));
            
            if ($login['Mensaje'] == 'OK') {
                Yii::$app->user->login($usuario);
                
                $out = [
                        'Usuario' => [
                            'Usuario' => $usuario->Usuario,
                            'Nombres' => $usuario->Nombres,
                            'Apellidos' => $usuario->Apellidos,
                            'Token' => $usuario->Token,
                            'TokenApp' => $usuario->TokenApp,
                            'Email' => $usuario->Email,
                            'DebeCambiarPass' => $usuario->DebeCambiarPass
                        ]
                    ];
                
                return ['Error' => null, 'Mensaje' => $out];
            }
            
            return ['Error' => $login];
        }
        
        return ['Error' => $resultado];
    }
    
    public function actionListarPermisos($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $usuario = new Usuarios();
        
        $usuario->IdUsuario = $id;
        
        $usuario->Dame();
        
        $permisos = $usuario->DamePermisos();
        
        return $permisos;
    }
    
    public function actionExiste()
    {
        $usuario = new Usuarios();
        
        $usuario->setAttributes(Yii::$app->request->post());
        
        $existe = $usuario->ExisteUsuario();
        
        if ($existe == 'S') {
            $usuario->DamePorUsuario();
            
            return ['Error' => null, 'DebeCambiarPass' => $usuario->DebeCambiarPass];
        } else {
            return ['Error' => 'El usuario indicado no existe'];
        }
    }

    public function actionRecuperarCuenta()
    {
        $usuario = new Usuarios();
        $usuario->Email = Yii::$app->request->post('Email');

        $NuevaPass = Yii::$app->request->post('Pass');

        $sql = 'CALL dsp_dame_usuario_por_email( :email )';
        
        $query = Yii::$app->db->createCommand($sql);
    
        $query->bindValues([
            ':email' => $usuario->Email
        ]);

        if(array_key_exists('Mensaje', $query->queryOne())) {
            return ['Error' => $query->queryOne()['Mensaje']];
        }

        $usuario->attributes = $query->queryOne();

        $resultado = $usuario->CambiarPassword($usuario->Token, null, $NuevaPass, 'R');

        if ($resultado !== 'OK') {
            return ['Error' => $resultado];
        }

        EmailHelper::enviarEmail(
            'DocDoc <contacto@docdoc.com.ar>',
            $usuario->Email,
            'Recuperar Cuenta',
            'recuperar-cuenta',
            [
                'usuario' => $usuario->Usuario,
                'pass' => $NuevaPass
            ]
        );

        return ['Error' => null];
    }

    public function actionSetTokenApp()
    {
        $usuario = new Usuarios();
        $usuario->IdEstudio = Yii::$app->user->identity->IdEstudio;
        $usuario->IdUsuario = Yii::$app->user->identity->IdUsuario;
        $usuario->TokenApp = Yii::$app->request->post('TokenApp');

        $resultado = $usuario->SetTokenApp();

        if ($resultado !== 'OK') {
            return ['Error' => $resultado];
        } else {
            return ['Error' => null];
        }
    }

    public function actionSetTokenAppCliente()
    {
        $usuario = new Usuarios();
        $usuario->Usuario = Yii::$app->user->identity->Usuario;
        $usuario->IdUsuario = Yii::$app->user->identity->IdUsuario;
        $usuario->TokenApp = Yii::$app->request->post('TokenApp');

        $resultado = $usuario->SetTokenAppCliente();

        if ($resultado !== 'OK') {
            return ['Error' => $resultado];
        } else {
            return ['Error' => null];
        }
    }
}
