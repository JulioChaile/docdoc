<?php
namespace frontend\modules\api\controllers;

use GuzzleHttp\Client;
use common\models\UsuariosEstudio;
use common\models\Usuarios;
use common\components\FCMHelper;
use common\components\EmailHelper;
use Yii;

/**
 * @apiDefine logueado Requiere autenticación 
 * Este método sólo puede ser ejecutado por usuarios autenticados,
 * por lo tanto se debe indicar un header Authorization: Bearer {Token}
 */
class LoginController extends BaseController
{
    public function actionCreate()
    {
        $usuario = new UsuariosEstudio();
        $usuario->setScenario(Usuarios::_LOGIN);
        $usuario->Usuario = Yii::$app->request->post('Usuario');
        $usuario->Password = Yii::$app->request->post('Password');
        $InApp = Yii::$app->request->post('InApp');
        $cliente = Yii::$app->request->post('Cliente');
        
        if ($usuario->validate()) {
            $app = empty($cliente) ? 'C' : 'D';

            $login = $usuario->Login($app, $usuario->Password, Yii::$app->security->generateRandomString(300));

            if ($login['Mensaje']== 'OK') {
                Yii::$app->user->login($usuario);

                $usuario->Dame();

                $cadete = $usuario->Observaciones === 'cadete' || $usuario->Observaciones === 'Cadete';

                $out = [
                        'Usuario' => [
                            'Usuario' => $usuario->Usuario,
                            'Nombres' => $usuario->Nombres,
                            'Apellidos' => $usuario->Apellidos,
                            'Token' => $usuario->Token,
                            'TokenApp' => $usuario->TokenApp,
                            'Email' => $usuario->Email,
                            'DebeCambiarPass' => $usuario->DebeCambiarPass,
                            'IdEstudio' => $usuario->IdEstudio,
                            'IdUsuario' => $usuario->IdUsuario
                        ]
                    ];
                    

                if ($usuario->Usuario !== 'devpva' && $usuario->Usuario !== 'JulioChaile') {
                    EmailHelper::enviarEmail(
                        'DocDoc <contacto@docdoc.com.ar>',
                        'estudiopva@gmail.com',
                        'Nuevo Login',
                        'reporte',
                        [
                            'reporte' => 'El usuario ' . $usuario->Apellidos . ', ' . $usuario->Nombres . ' acaba de iniciar sesion.'
                        ]
                    );
                }

                if ($cadete) {
                    $out['Usuario']['IdRol'] = 5; // En realidad no importa que vaya aca, la idea es que no sea indefinido nada mas
                }

                if (!empty($usuario->TokenApp)) {
                    $notificacion = [
                        'title' => 'Login',
                        'body' => 'Su sesión en la aplicación ha finalizado, se inició sesión en otro dispositivo.'
                    ];

                    $data = [
                        'tipo' => 'login',
                        'inApp' => empty($InApp)
                            ? ''
                            : 1
                    ];

                    $to = $usuario->TokenApp;

                    $respuesta = FCMHelper::enviarNotificacionPush($notificacion, $to, $data, 'user');

                    return [
                        'Error' => null,
                        'Mensaje' => $out,
                        'responseNot' => $respuesta
                    ];
                }

                return [
                    'Error' => null,
                    'Mensaje' => $out
                ];
            } else {
                Yii::$app->response->setStatusCode(422);
                return ['Error' => $login['Mensaje']];
            }
        }
        
        Yii::$app->response->setStatusCode(400);
        return ['Error' => 'Los parámetros de usuario no son válidos.'];
    }
}
