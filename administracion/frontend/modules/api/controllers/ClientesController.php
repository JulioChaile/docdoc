<?php
namespace frontend\modules\api\controllers;

use common\models\Casos;
use common\models\Usuarios;
use common\models\Estudios;
use backend\models\GestorEstudios;
use common\models\GestorCasos;
use common\components\EmailHelper;
use common\components\FechaHelper;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;

class ClientesController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bearerAuth' => [
                    'class' => OptionalBearerAuth::className(),
                    'except' => ['create', 'index', 'options'],
                ],
            ]
        );
    }
    
    /**
     * @api {get} /clientes Datos de una invitación
     * @apiName InvitacionCliente
     * @apiGroup Clientes
     *
     * @apiParam {String} Token Token de invitación.
     *
     * @apiSuccess {Object} Usuario Datos del Usuario que compartió el caso.
     * @apiSuccess {Object} Caso Datos del Caso a compartir.
     * @apiSuccess {String} Email Email del nuevo Cliente.
     * @apiError {String} Error Mensaje de error.
     */
    public function actionIndex()
    {
        $Token = Yii::$app->request->get('Token');

        $data = Yii::$app->cache->get($Token);

        if ($data === false) {
            return ['Error' => 'Para registrarse debe tener una invitación válida.'];
        }

        $caso = new Casos;
        $caso->IdCaso = $data['IdCaso'];
        $caso->Dame('', 'N', 'S');

        $usuario = new Usuarios;
        $usuario->IdUsuario = $data['IdUsuario'];
        $usuario->Dame();

        return [
            'Usuario' => [
                'Usuario' => $usuario->Usuario,
                'Nombres' => $usuario->Nombres,
                'Apellidos' => $usuario->Apellidos,
                'Email' => $usuario->Email
            ],
            'Caso' => $caso,
            'Email' => $data['Email']
        ];
    }

    /**
     * @api {post} /clientes Alta de cliente
     * @apiName AltaCliente
     * @apiGroup Clientes
     *
     * @apiParam {String} Token Token de invitación.
     * @apiParam {String} Estudio Nombre del Estudio.
     * @apiParam {Number} IdCiudad Ciudad del Estudio.
     * @apiParam {String} Domicilio Domicilio del Estudio.
     * @apiParam {String} Telefono Teléfono del Estudio.
     * @apiParam {String} Nombres Nombres del Usuario.
     * @apiParam {String} Apellidos Apellidos del Usuario.
     * @apiParam {String} Usuario Nombre de usuario.
     * @apiParam {String} Password Password del usuario.
     * @apiParam {String} Email Email del usuario.
     *
     * @apiSuccess {Object} Usuario Datos del Usuario.
     * @apiSuccess {String} Usuario.Usuario  Nombre de Usuario.
     * @apiSuccess {String} Usuario.Nombres  Nombres del Usuario.
     * @apiSuccess {String} Usuario.Apellidos  Apellidos del Usuario.
     * @apiSuccess {String} Usuario.Email  Email del Usuario.
     * @apiSuccess {String} Usuario.Token  Token del Usuario.
     * @apiSuccess {String} Usuario.DebeCambiarPass  Flag DebeCambiarPass [S|N].
     * @apiError {String} Error Mensaje de error.
     */
    public function actionCreate()
    {
        $Token = Yii::$app->request->post('Token');

        $data = Yii::$app->cache->get($Token);

        if ($data === false) {
            return ['Error' => 'Para registrarse debe tener una invitación válida.'];
        }

        $usuario = new Usuarios;
        $usuario->setAttributes(Yii::$app->request->post());

        $estudio = new Estudios;
        $estudio->setAttributes(Yii::$app->request->post());

        $estudios = $estudio->ListarEstudios();

        $caso = new Casos;
        $caso->IdCaso = $data['IdCaso'];

        $gestor = new GestorEstudios;
        $resultado = $gestor->AltaClienteInvitacion($estudio, $usuario, $caso);

        if (substr($resultado, 0, 2) != 'OK') {
            return ['Error' => $resultado];
        }

        Yii::$app->cache->delete($Token);

        $usuario->IdUsuario = substr($resultado, 2);
        $usuario->Dame();
        // Obtengo el estudio
        $usuario->DamePorToken();

        $gestorCasos = new GestorCasos;
        $gestorCasos->RegistrarComparticion([
            'IdCaso' => null,
            'Email' => null,
            'Token' => null,
            'FechaEnviado' => null,
            'FechaRecibido' => FechaHelper::datetimeActualMysql(true),
            'IdUsuarioOrigen' => null,
            'IdUsuarioDestino' => $usuario->IdUsuario,
            'IdEstudioDestino' => $usuario->IdEstudio,
            'IdEstudioOrigen' => null
        ], $data['IdComparticion']);

        $out = [
            'Usuario' => [
                'Usuario' => $usuario->Usuario,
                'Nombres' => $usuario->Nombres,
                'Apellidos' => $usuario->Apellidos,
                'Token' => $usuario->Token,
                'Email' => $usuario->Email,
                'DebeCambiarPass' => $usuario->DebeCambiarPass
            ]
        ];

        foreach ($estudios as $estudioMail) {
            $Estudio = new Estudios();
            $Estudio->IdEstudio = $estudioMail['IdEstudio'];
            $Estudio->Dame();
    
            $usuarios = $Estudio->BuscarUsuarios();
    
            foreach ($usuarios as $usuarioMail) {
                EmailHelper::enviarEmail(
                    'DocDoc <contacto@docdoc.com.ar>',
                    $usuarioMail['Email'],
                    'Nueva estudio en DocDoc!',
                    'nuevo-estudio',
                    [
                        'usuarioNuevo' => $usuario,
                        'usuarioMail' => $usuarioMail,
                        'estudioNuevo' => $estudio,
                        'estudioMail' => $estudioMail
                    ]
                );
            }
        }

        return ['Error' => null, 'Mensaje' => $out];
    }
}
