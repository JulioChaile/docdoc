<?php
namespace frontend\modules\api\controllers;

use common\models\Casos;
use common\models\Usuarios;
use common\models\Estudios;
use common\models\UsuariosCaso;
use common\models\TiposCaso;
use common\components\EmailHelper;
use common\components\ChatApi;
use common\components\FechaHelper;
use common\models\GestorCasos;
use common\components\FCMHelper;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class CasosController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bearerAuth' => [
                    'class' => OptionalBearerAuth::className(),
                    'except' => ['options'],
                    'actionsClient' => ['buscar-cliente', 'movimientos-clientes', 'eventos-clientes', 'listar-carpetas', 'opciones-parametros']
                ],
            ]
        );
    }

    const LIMIT_CASOS = 10;
    
    public function actionIndex()
    {
        $gestor = new GestorCasos();
        
        $respuesta = $gestor->BuscarAvanzado(
            Yii::$app->user->identity->IdEstudio,
            Yii::$app->user->identity->IdUsuario,
            Yii::$app->request->get('IdNominacion'),
            Yii::$app->request->get('IdEstadoCaso'),
            Yii::$app->request->get('FechaInicio'),
            Yii::$app->request->get('FechaFin'),
            Yii::$app->request->get('Tipo'),
            Yii::$app->request->get('Cadena'),
            Yii::$app->request->get('IdTipoCaso'),
            Yii::$app->request->get('Estado')
        );

        $Offset = intval(Yii::$app->request->get('Offset'));
        
        usort($respuesta, function ($a, $b) {
            return strcmp($b["FechaAlta"], $a["FechaAlta"]);
        });
        
        return array_slice($respuesta, intval($Offset), self::LIMIT_CASOS);
        // return $Offset == count($casos) ? null : $casos;
    }

    public function actionBuscar()
    {
        $gestor = new GestorCasos();
        
        $respuesta = $gestor->Buscar(
            Yii::$app->user->identity->IdEstudio,
            Yii::$app->user->identity->IdUsuario,
            Yii::$app->request->get('Tipo'),
            Yii::$app->request->get('Cadena'),
            Yii::$app->request->get('Offset'),
            Yii::$app->request->get('Orden'),
            Yii::$app->request->get('Limit')
        );

        return $respuesta;
    }

    public function actionBuscarJudiciales()
    {
        $gestor = new GestorCasos();
        
        $casos = $gestor->Buscar(
            Yii::$app->user->identity->IdEstudio,
            Yii::$app->user->identity->IdUsuario,
            'T',
            '',
            0,
            'fecha',
            999999
        );

        $sql = 'SELECT C.*, U.Apellidos, U.Nombres FROM JudicialesC C INNER JOIN Usuarios U USING(IdUsuario)';
        $sql2 = 'SELECT * FROM JudicialesI';
        
        $query = Yii::$app->db->createCommand($sql);
        $query2 = Yii::$app->db->createCommand($sql2);
        
        $JudicialesC = $query->queryAll();
        $JudicialesI = $query2->queryAll();

        $CasosJudiciales = [];

        foreach ($casos as $v) {        
            if (($v['IdJuzgado'] === '1' || $v['IdJuzgado'] === '6' || $v['IdJuzgado'] === '7' || $v['IdJuzgado'] === '11') && $v['Estado'] !== 'R' && $v['IdEstadoAmbitoGestion'] !== '31' && $v['IdEstadoAmbitoGestion'] !== '5' && $v['IdEstadoAmbitoGestion'] !== '61' && $v['IdEstadoAmbitoGestion'] !== '2' && $v['IdEstadoAmbitoGestion'] !== '7') {
                $CasosJudiciales[] = $v;
            }
        }

        return [
            'JudicialesC' => $JudicialesC,
            'JudicialesI' => $JudicialesI,
            'CasosJudiciales' => $CasosJudiciales
        ];
    }

    public function actionFinalizarCasos()
    {
        $Cantidad = Yii::$app->request->post('Cantidad');
        $IdEstadoAmbitoGestion = Yii::$app->request->post('IdEstadoAmbitoGestion');
        $Estado = Yii::$app->request->post('Estado');
        $Casos = Yii::$app->request->post('Casos');
        $IdUsuario = Yii::$app->user->identity->IdUsuario;

        $gestor = new GestorCasos();

        $respuestaC = $gestor->AltaJudicialesC($Cantidad, $IdEstadoAmbitoGestion, $IdUsuario);

        if (substr($respuestaC, 0, 2) == 'OK') {
            $IdJudicialesC = substr($respuestaC, 2);
            $respuesta = [];
            $msj = [];

            foreach ($Casos as $key => $c) {
                $respuesta[] = $gestor->AltaJudicialesI($IdJudicialesC, $c['IdCaso']);

                $caso = new Casos;
                $caso->IdCaso = $c['IdCaso'];

                if (!empty($caso->IdExternoChat)) {
                    $Mensaje = 'Hola como estas , te cuento que en el dia de la fecha hemos controlado tu caso , el que se encuentra en la etapa ' . $Estado . ' hace ' . $c['Dias'] . ' dias. Si no entiendes bien que significa ese estado no te hagas problema, por ahí son cuestiones técnicas que no hace falta que comprendas, lo importante es que sepas que tu caso está siendo controlado periódicamente y que estamos avanzando. Te mando saludos y que tengas un lindo día!';

                    $msj[] = Yii::$app->chatapi->enviarMensaje($caso->IdChat, $caso->IdExternoChat, $Mensaje, $IdUsuario, null);
                }
            }

            return [ 'IdJudicialesC' => $IdJudicialesC, 'respuestaI' => $respuesta, 'msj' => $msj ];
        } else {
            return ['Error' => $respuestaC];
        }
    }

    public function actionBuscarCliente()
    {
        $gestor = new GestorCasos();

        $cadena = Yii::$app->request->get('Cadena');
        
        $respuesta = $gestor->Buscar(0, 0, 'C', $cadena, 0);

        $array = array();

        foreach ($respuesta as $c) {
            $array[] =[
                'IdCaso' => $c['IdCaso'],
                'Caratula' => $c['Caratula'],
                'Estudios' => $c['Estudios'],
                'PersonasCaso' => json_decode($c['PersonasCaso'], true)
            ];
        }

        return $array;
    }

    public function actionNumeroCasos()
    {
        $IdEstudio = Yii::$app->user->identity->IdEstudio;

        $gestor = new GestorCasos();

        $respuesta = $gestor->NumeroCasos($IdEstudio);

        return $respuesta;
    }
    
    /**
     * @api {post} /casos Alta de Caso
     * @apiName AltaCaso
     * @apiGroup Casos
     * @apiPermission logueado
     *
     * @apiParam {Number} IdJuzgado
     * @apiParam {Number} IdNominacion
     * @apiParam {Number} IdTipoCaso
     * @apiParam {Number} IdEstadoCaso
     * @apiParam {Number} IdOrigen
     * @apiParam {String} Caratula
     * @apiParam {String} NroExpediente
     * @apiParam {String} Carpeta
     * @apiParam {String} Observaciones
     * @apiParam {[]Object} PersonasCaso
     *
     * @apiError {String} Error Mensaje de error.
     */
    public function actionCreate()
    {
        $caso = new Casos();
        
        $caso->setAttributes(Yii::$app->request->post());
        
        $gestor = new GestorCasos();
        
        $resultado = $gestor->Alta($caso);
        
        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdCaso' => substr($resultado, 2)
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    /** Test para wordpress */
    public function actionWordpressCreate()
    {
        // Yii::app() -> runController('login/create');

        $caso = new Casos();
        
        $caso->setAttributes(Yii::$app->request->post());

        $token = Yii::$app->request->post('token');
        
        $gestor = new GestorCasos();
        
        $resultado = $gestor->WordpressAlta($caso, $token);
        
        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdCaso' => substr($resultado, 2)
            ];
        } else {
            return ['Error' => $resultado];
        }
    }
    
    /**
     * @api {get} /casos/:id Dame Caso
     * @apiName DameCaso
     * @apiGroup Casos
     * @apiPermission logueado
     *
     * @apiSuccess {Number} IdCaso
     * @apiSuccess {Number} IdJuzgado
     * @apiSuccess {Number} IdNominacion
     * @apiSuccess {Number} IdTipoCaso
     * @apiSuccess {Number} IdEstadoCaso
     * @apiSuccess {Number} IdOrigen
     * @apiSuccess {String} Caratula
     * @apiSuccess {String} NroExpediente
     * @apiSuccess {String} Carpeta
     * @apiSuccess {String} FechaAlta
     * @apiSuccess {String} Observaciones
     * @apiSuccess {String} FechaUltVisita
     * @apiSuccess {String} Estado
     */
    public function actionView($id)
    {
        $movs = Yii::$app->request->get('movs');

        $caso = new Casos();
        
        $caso->IdCaso = $id;

        if(empty($movs)) {
            $movs = 'S';
        }
        
        $caso->Dame('', $movs);
        
        return $caso;
    }
    
    /**
     * @api {get} /casos/movimientos/:id Listar Movimientos Caso
     * @apiName MovimientosCaso
     * @apiGroup Casos
     * @apiPermission logueado
     *
     * @apiSuccess {[]Object} - Listado de Movimientos
     */
    public function actionMovimientos($id, $Offset = 0, $Cadena = '', $Color = '', $Usuarios = '[]', $Tipos = '[]', $IdUsuarioGestion = 0, $Tareas = 0, $Limit = 30)
    {
        $caso = new Casos();
        
        $caso->IdCaso = $id;
        
        return $caso->ListarMovimientos($Cadena, $Offset, $Limit, $Color, $Usuarios, $Tipos, $IdUsuarioGestion, $Tareas);
    }

    public function actionEventosClientes()
    {
        $usuario = Yii::$app->request->get('usuario');
        $caso = new Casos();
        
        return $caso->ListarEventosClientes($usuario);
    }

    public function actionMovimientosClientes($id)
    {
        $caso = new Casos();
        
        $caso->IdCaso = $id;
        
        return $caso->ListarMovimientosClientes();
    }

    public function actionMovimientosSinRealizar($id)
    {
        $caso = new Casos();
        
        $caso->IdCaso = $id;
        
        return $caso->ListarMovimientosSinRealizar();
    }

    public function actionMovimientosRealizados($id)
    {
        $caso = new Casos();
        
        $caso->IdCaso = $id;
        
        return $caso->ListarMovimientosRealizados();
    }
    
    public function actionPrevisualizarBorrado($id)
    {
        $gestor = new GestorCasos();
        
        $previsualizacion = $gestor->PrevisualizarBorrado($id);
        
        return $previsualizacion;
    }
    
    public function actionDelete($id)
    {
        $caso = new Casos();
        
        $caso->IdCaso = $id;
        
        $gestor = new GestorCasos();
        
        $resultado = $gestor->Borrar($caso);
        
        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }
    
    public function actionUpdate($id)
    {
        $caso = new Casos();
        
        $caso->IdCaso = $id;
        
        $caso->setAttributes(Yii::$app->request->getBodyParams());
        
        $gestor = new GestorCasos();
        
        $resultado = $gestor->Modificar($caso);
        
        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }
    
    public function actionBaja($id)
    {
        $caso = new Casos();
        
        $caso->IdCaso = $id;
        
        $resultado = $caso->Baja();
        
        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }
    
    public function actionArchivar($id)
    {
        $caso = new Casos();
        
        $caso->IdCaso = $id;

        $Estado = Yii::$app->request->post('Estado');
        
        $resultado = $caso->Archivar($Estado);
        
        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionUsuarios()
    {
        $gestor = new GestorCasos();

        $IdUsuario = Yii::$app->request->post('IdUsuario');
        $IdCaso = Yii::$app->request->post('IdCaso');
        $Permiso = Yii::$app->request->post('Permiso');

        $resultado = $gestor->ModificarPermisoUsuarioCaso($IdUsuario, $IdCaso, $Permiso);

        if (substr($resultado, 0, 2) != 'OK') {
            return ['Error' => $resultado];
        }

        return ['IdUsuarioCaso' => substr($resultado, 2)];
    }

    public function actionEliminarUsuario()
    {
        $gestor = new GestorCasos();

        $IdUsuario = Yii::$app->request->post('IdUsuario');
        $IdCaso = Yii::$app->request->post('IdCaso');

        $resultado = $gestor->EliminarUsuarioCaso($IdUsuario, $IdCaso);

        if ($resultado != 'OK') {
            return ['Error' => $resultado];
        }

        return $resultado;
    }

    /**
     * @api {get} /casos/persona/:id Dame Persona Caso
     * @apiName DamePersonaCaso
     * @apiGroup Casos
     * @apiPermission logueado
     *
     * @apiParam {Number} idPersona
     *
     * @apiSuccess {Object} . Datos de la Persona.
     */
    public function actionPersonas($id)
    {
        $caso = new Casos();

        $caso->IdCaso = $id;

        return $caso->ListarPersonas();
    }
    
    public function actionPersona($id, $idPersona)
    {
        $caso = new Casos();

        $caso->IdCaso = $id;

        return $caso->DamePersona($idPersona);
    }

    public function actionAltaPersona($id)
    {
        $Persona = json_decode(Yii::$app->request->post('Persona'), true);

        $caso = new Casos();

        $caso->IdCaso = $id;

        $respuesta = $caso->AltaPersonas($Persona);

        if (substr($respuesta, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdPersona' => substr($respuesta, 2)
            ];
        } else {
            return ['Error' => $respuesta];
        }
    }

    public function actionBorrarPersona($id, $idPersona)
    {
        $caso = new Casos();

        $caso->IdCaso = $id;

        $respuesta = $caso->BorrarPersona($idPersona);

        if ($respuesta == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return ['Error' => $respuesta];
        }
    }

    private function generateRandomString($length = 256)
    {
        $characters = '123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @api {post} /casos/compartir Compartir Caso
     * @apiName CompartirCaso
     * @apiGroup Casos
     * @apiPermission logueado
     *
     * @apiParam {String} Email Email de a quién se desea compartir.
     * @apiParam {Number} IdCaso Caso a compartir.
     *
     * @apiError {String} Error Mensaje de error.
     */
    public function actionCompartir()
    {
        $Email = Yii::$app->request->post('Email');
        $Telefono = Yii::$app->request->post('Telefono');
        $IdCaso = Yii::$app->request->post('IdCaso');
        $Token = $this->generateRandomString(256);

        $usuario = new Usuarios;
        $usuario->Email = $Email;
        $usuario->TelefonoUsuario = $Telefono;

        if (empty($Email)) {
            $usuario->DamePorTelefono();
        } else {
            $usuario->DamePorEmail();
        }

        $IdUsuario = Yii::$app->user->identity->IdUsuario;
        $IdEstudio = Yii::$app->user->identity->IdEstudio;

        $usuarioOrigen = new Usuarios;
        $usuarioOrigen->IdUsuario = $IdUsuario;
        $usuarioOrigen->Dame();


        $gestor = new GestorCasos;

        if (isset($usuario->IdUsuario)) {
            // Compartir el caso con un usuario registrado
            $caso = new Casos;
            $caso->IdCaso = $IdCaso;
            $resultado = $caso->Compartir($usuario);
            if ($resultado != 'OK') {
                return ['Error' => $resultado];
            }

            // Obtengo el estudio del usuario
            $usuario->DamePorToken();

            // Guardo la compartición en la db
            $gestor->RegistrarComparticion([
                'IdCaso' => $IdCaso,
                'Email' => $Email,
                'Token' => $Token,
                'FechaEnviado' => FechaHelper::datetimeActualMysql(true),
                'FechaRecibido' => FechaHelper::datetimeActualMysql(true),
                'IdUsuarioOrigen' => $IdUsuario,
                'IdUsuarioDestino' => $usuario->IdUsuario,
                'IdEstudioDestino' => $usuario->IdEstudio,
                'IdEstudioOrigen' => $IdEstudio
            ]);

            $caso->Dame();

            $tipoCaso = new TiposCaso;
            $tipoCaso->IdTipoCaso = $caso->IdTipoCaso;
            $tipoCaso->Dame();
            
            if (!empty($Email)) {
                EmailHelper::enviarEmail(
                    'DocDoc <contacto@docdoc.com.ar>',
                    $Email,
                    'Nuevo caso en DocDoc!',
                    'nuevo-caso-docdoc',
                    [
                        'usuario' => $usuarioOrigen,
                        'caso' => $caso,
                        'tipoCaso' => $tipoCaso
                    ]
                );
            }

            if (!empty($Telefono)) {
                $Contenido = $usuarioOrigen->Apellidos . ', ' . $usuarioOrigen->Nombres . ' te invitó a formar parte de un caso! ' .
                            $usuarioOrigen->Nombres . ', quiere compartir el caso ' . $caso->Caratula . ' de ' . $tipoCaso->TipoCaso . 'con vos.';

                $respuestaMensaje = Yii::$app->chatapi->mensajeComun($Telefono, $Contenido);

                if ($respuestaMensaje['Error'] !== null) {
                    return [
                        'Error' => 'Telefono: ' . $respuestaMensaje['Error']
                    ];
                }

                $Contenido = 'Podes ver el caso en https://app.docdoc.com.ar/';

                $respuestaMensaje = Yii::$app->chatapi->mensajeComun($Telefono, $Contenido);

                $Contenido = 'Podés comunicarte con ' . $usuarioOrigen->Nombres . ', a través de su email ' . $usuarioOrigen->Email .
                '. Si no recuerdas tu usuario o contraseña podes hacer click en "Recuperar cuenta" para generar una nueva contraseña y recuperar tu usuario.';

                $respuestaMensaje = Yii::$app->chatapi->mensajeComun($Telefono, $Contenido);

                $Contenido = 'Estos mensajes fueron enviados automáticamente por DocDoc! Abogados Online, por favor no conteste directamente.';

                $respuestaMensaje = Yii::$app->chatapi->mensajeComun($Telefono, $Contenido);
            }

            $ContenidoMensajeGlobal = 'Se compartio el caso ' . $caso->Caratula . ' en su estudio. Para verlo ingrese en https://app.docdoc.com.ar/. Recuerda que si olvidaste tus datos de inicio de sesion puedes hacer click en "Recuperar cuenta" e ingresar tu mail y una nueva contraseña';

            $respuestaMensajeEstudio = Yii::$app->chatapi->mensajeGlobal($usuario->IdEstudio, $ContenidoMensajeGlobal);

            $respuestaNotiPush = FCMHelper::notificacionPorEstudio(
                $usuario->IdEstudio,
                [
                    'title' => 'Nuevo caso compartido: ' . $caso->Caratula,
                    'body' => 'Se compartio un nuevo caso en su estudio, toque para ir a verlo.'
                ],
                [
                    'tipo' => 'casoCompartido',
                    'id' => $IdCaso
                ]
            );

            return [
                'Error' => null,
                'responseNot' => $respuestaNotiPush
            ];
        }

        // Generar invitación a un usuario nuevo

        $usuarioCaso = new UsuariosCaso;
        $usuarioCaso->IdUsuario = $IdUsuario;
        $usuarioCaso->IdCaso = $IdCaso;
        $usuarioCaso->Dame();

        if (!isset($usuarioCaso->IdUsuarioCaso) || $usuarioCaso->Permiso != 'A') {
            return ['Error' => 'Usted no tiene permiso para compartir este caso.'];
        }

        $caso = new Casos;
        $caso->IdCaso = $IdCaso;
        $caso->Dame();

        $tipoCaso = new TiposCaso;
        $tipoCaso->IdTipoCaso = $caso->IdTipoCaso;
        $tipoCaso->Dame();

        $usuario->IdUsuario = $IdUsuario;
        $usuario->Dame();

        // Guardo la compartición en la db
        $comparticion = $gestor->RegistrarComparticion([
            'IdCaso' => $IdCaso,
            'Email' => $Email,
            'Token' => $Token,
            'FechaEnviado' => FechaHelper::datetimeActualMysql(true),
            'FechaRecibido' => null,
            'IdUsuarioOrigen' => $IdUsuario,
            'IdUsuarioDestino' => null,
            'IdEstudioDestino' => null,
            'IdEstudioOrigen' => $IdEstudio
        ]);

        if (substr($comparticion, 0, 2) != 'OK') {
            return ['Error' => $comparticion];
        }

        $IdComparticion = substr($comparticion, 2);

        $stored = Yii::$app->cache->set($Token, [
            'IdUsuario' => $IdUsuario,
            'Email' => $Email,
            'IdCaso' => $IdCaso,
            'IdComparticion' => $IdComparticion
        ], 3600 * 24 * 7); // Expira en 7 días

        if (!$stored) {
            return ['Error' => 'Ocurrió un error interno en el servidor. Contáctese con la administración.'];
        }

        if (!empty($Email)) {
            EmailHelper::enviarEmail(
                'DocDoc <contacto@docdoc.com.ar>',
                $Email,
                'Invitación a DocDoc! Abogados Online',
                'invitacion-docdoc',
                [
                    'token' => $Token,
                    'caso' => $caso,
                    'usuario' => $usuarioOrigen,
                    'tipoCaso' => $tipoCaso
                ]
            );
        }

        if (!empty($Telefono)) {
            $Contenido = $usuarioOrigen->Apellidos . ', ' . $usuarioOrigen->Nombres . ' te invitó a DocDoc! ' .
                        $usuario->Nombres . ', quiere compartir el caso ' . $caso->Caratula . 'de ' . $tipoCaso->TipoCaso . 'con vos.';

            $respuestaMensaje = Yii::$app->chatapi->mensajeComun($Telefono, $Contenido);

            if ($respuestaMensaje['Error'] !== null) {
                return [
                    'Error' => 'Telefono: ' . $respuestaMensaje['Error']
                ];
            }

            $Contenido = 'Para acceder a la plataforma podes registrarte en https://app.docdoc.com.ar/registrar/' . $Token;

            $respuestaMensaje = Yii::$app->chatapi->mensajeComun($Telefono, $Contenido);

            $Contenido = 'Podés comunicarte con ' . $usuarioOrigen->Nombres . ', a través de su email ' . $usuarioOrigen->Email .
            '. Recordá que esta invitación tiene duración de 1 semana, si no la aceptas dentro de los 7 días deberás esperar a recibir una nueva.';

            $respuestaMensaje = Yii::$app->chatapi->mensajeComun($Telefono, $Contenido);

            $Contenido = 'Estos mensajes fueron enviados automáticamente por DocDoc! Abogados Online, por favor no lo contestes directamente.';

            $respuestaMensaje = Yii::$app->chatapi->mensajeComun($Telefono, $Contenido);
        }

        return ['Error' => null];
    }

    /**
     * @api {post} /casos/compartidos Listar Casos Compartidos
     * @apiName CasosCompartidos
     * @apiGroup Casos
     * @apiPermission logueado
     *
     * @apiError {String} Error Mensaje de error.
     */
    public function actionCompartidos()
    {
        return Yii::$app->user->identity->ListarCasosCompartidos();
    }

    public function actionCompartirPorEstudio($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $IdsEstudios = json_decode(Yii::$app->request->post('IdsEstudios'));
        $IdCaso = $id;

        $IdUsuario = Yii::$app->user->identity->IdUsuario;
        $IdEstudioOrigen = Yii::$app->user->identity->IdEstudio;

        $caso = new Casos;
        $caso->IdCaso = $IdCaso;

        $caso->Dame();

        $tipoCaso = new TiposCaso;
        $tipoCaso->IdTipoCaso = $caso->IdTipoCaso;
        $tipoCaso->Dame();

        $gestor = new GestorCasos;

        $errores = array();

        foreach ($IdsEstudios as $IdEstudio) {
            $resultado = $caso->CompartirPorEstudio($IdEstudio);
            if ($resultado != 'OK') {
                $errores[$IdEstudio] = 'Compartir: ' . $resultado;
            } else {
                $comparticion = $gestor->RegistrarComparticion([
                    'IdCaso' => $IdCaso,
                    'Email' => null,
                    'Token' => $this->generateRandomString(256),
                    'FechaEnviado' => FechaHelper::datetimeActualMysql(true),
                    'FechaRecibido' => FechaHelper::datetimeActualMysql(true),
                    'IdUsuarioOrigen' => $IdUsuario,
                    'IdUsuarioDestino' => null,
                    'IdEstudioDestino' => $IdEstudio,
                    'IdEstudioOrigen' => $IdEstudioOrigen
                ]);
        
                if (substr($comparticion, 0, 2) != 'OK') {
                    $errores[$IdEstudio] = 'Registrar: ' . $comparticion;
                }

                $Estudio = new Estudios;
                $Estudio->IdEstudio = $IdEstudio;

                $usuarios = $Estudio->BuscarUsuarios();

                $usuarioOrigen = new Usuarios;
                $usuarioOrigen->IdUsuario = $IdUsuario;
                $usuarioOrigen->Dame();

                if (empty($usuarios)) {
                    $errores[$IdEstudio] = 'Enviar mail: el estudio no tiene usuarios asociados';
                } else {
                    foreach ($usuarios as $usuario) {
                        $user = new Usuarios;

                        $user->Email = $usuario['Email'];

                        EmailHelper::enviarEmail(
                            'DocDoc <contacto@docdoc.com.ar>',
                            $user->Email,
                            'Nuevo caso en DocDoc!',
                            'nuevo-caso-docdoc',
                            [
                                'usuario' => $usuarioOrigen,
                                'caso' => $caso,
                                'tipoCaso' => $tipoCaso
                            ]
                        );
                    }
                }

                $Contenido = 'Se compartio el caso ' . $caso->Caratula . ' en su estudio. Para verlo ingrese en https://app.docdoc.com.ar/. Recuerda que si olvidaste tus datos de inicio de sesion puedes hacer click en "Recuperar cuenta" e ingresar tu mail y una nueva';

                $respuestaMensajeEstudio = Yii::$app->chatapi->mensajeGlobal($IdEstudio, $Contenido);
            }
        }

        return $errores;
    }

    public function actionEliminarComparticion($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $IdsEstudios = json_decode(Yii::$app->request->post('IdsEstudios'));
        $IdCaso = $id;

        $gestor = new GestorCasos;

        $errores = array();

        foreach ($IdsEstudios as $IdEstudio) {
            $respuesta = $gestor->EliminarComparticion($IdCaso, $IdEstudio);

            if ($respuesta !== 'OK') {
                $errores[$IdEstudio] = $respuesta;
            }
        }

        return $errores;
    }

    public function actionOpcionesParametros()
    {
        $gestor = new GestorCasos;

        return $gestor->OpcionesParametros();
    }

    public function actionEditarOpcionesParametros()
    {
        $IdOpcionesParametrosCaso = Yii::$app->request->post('id');
        $Opciones = Yii::$app->request->post('opciones');

        $gestor = new GestorCasos;

        $resultado = $gestor->EditarOpcionesParametros($IdOpcionesParametrosCaso, $Opciones);

        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionBuscarContactoParametros()
    {
        $cadena = Yii::$app->request->get('cadena');
        $tipo = Yii::$app->request->get('tipo');
        $limit = Yii::$app->request->get('limit');
        $offset = Yii::$app->request->get('offset');

        $gestor = new GestorCasos;

        switch ($tipo) {
            case 'C':
                return $gestor->BuscarComisaria($cadena, $offset, $limit);

            case 'CM':
                return $gestor->BuscarCentroMedico($cadena, $offset, $limit);

            case 'CS':
                return $gestor->BuscarCiaSeguro($cadena, $offset, $limit);
        }
    }

    public function actionAltaContactoParametros()
    {
        $opcion = Yii::$app->request->post('opcion');
        $telefono = Yii::$app->request->post('telefono');
        $direccion = Yii::$app->request->post('direccion');
        $tipo = Yii::$app->request->post('tipo');

        $gestor = new GestorCasos;

        switch ($tipo) {
            case 'C':
                $resultado = $gestor->AltaComisaria($opcion);

                if (substr($resultado, 0, 2) == 'OK') {
                    return [
                        'Error' => null,
                        'IdComisaria' => substr($resultado, 2)
                    ];
                } else {
                    return ['Error' => $resultado];
                }

            case 'CM':
                $resultado = $gestor->AltaCentroMedico($opcion);

                if (substr($resultado, 0, 2) == 'OK') {
                    return [
                        'Error' => null,
                        'IdCentroMedico' => substr($resultado, 2)
                    ];
                } else {
                    return ['Error' => $resultado];
                }

            case 'CS':
                $resultado = $gestor->AltaCiaSeguro($opcion, $telefono, $direccion);

                if (substr($resultado, 0, 2) == 'OK') {
                    return [
                        'Error' => null,
                        'IdCiaSeguro' => substr($resultado, 2)
                    ];
                } else {
                    return ['Error' => $resultado];
                }
        }
    }

    public function actionParametros()
    {
        $Parametros = Yii::$app->request->post('Parametros');
        $IdCaso = Yii::$app->request->post('IdCaso');

        $caso = new Casos();

        $resultado = $caso->Parametros($Parametros, $IdCaso);

        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionCausaPenal()
    {
        $CausaPenal = json_decode(Yii::$app->request->post('CausaPenal'));
        $IdCaso = Yii::$app->request->post('IdCaso');

        $caso = new Casos();

        if (empty($CausaPenal->IdCausaPenalCaso)) {
            $resultado = $caso->AltaCausaPenal($CausaPenal, $IdCaso);

            if (substr($resultado, 0, 2) == 'OK') {
                return [
                    'Error' => null,
                    'IdCausaPenalCaso' => substr($resultado, 2)
                ];
            } else {
                return ['Error' => $resultado];
            }
        } else {
            $resultado = $caso->EditarCausaPenal($CausaPenal, $IdCaso);

            if ($resultado == 'OK') {
                return ['Error' => null];
            } else {
                return ['Error' => $resultado];
            }
        }
    }

    public function actionAltaCarpeta()
    {
        $Nombre = Yii::$app->request->post('Nombre');
        $IdCaso = Yii::$app->request->post('IdCaso');

        $caso = new Casos();

        $resultado = $caso->AltaCarpeta($IdCaso, $Nombre);

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdCarpetaCaso' => substr($resultado, 2)
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionEditarCarpeta()
    {
        $Nombre = Yii::$app->request->post('Nombre');
        $IdCaso = Yii::$app->request->post('IdCaso');
        $IdCarpetaCaso = Yii::$app->request->post('IdCarpetaCaso');

        $caso = new Casos();

        $resultado = $caso->EditarCarpeta($IdCarpetaCaso, $IdCaso, $Nombre);

        if ($resultado== 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionBorrarCarpeta()
    {
        $IdCarpetaCaso = Yii::$app->request->post('IdCarpetaCaso');

        $caso = new Casos();

        $resultado = $caso->BorrarCarpeta($IdCarpetaCaso);

        if ($resultado == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionListarCarpetas()
    {
        $IdCaso = Yii::$app->request->get('IdCaso');

        $caso = new Casos();

        return $caso->ListarCarpetas($IdCaso);
    }

    public function actionListarEtiquetas()
    {
        $IdEstudio = Yii::$app->user->identity->IdEstudio;

        $caso = new Casos();

        return $caso->ListarEtiquetas($IdEstudio);
    }

    public function actionAltaEtiqueta()
    {
        $Etiqueta = Yii::$app->request->post('Etiqueta');
        $IdCaso = Yii::$app->request->post('IdCaso');
        $IdEstudio = Yii::$app->user->identity->IdEstudio;

        $caso = new Casos();

        $resultado = $caso->AltaEtiqueta($IdCaso, $IdEstudio, $Etiqueta);

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdEtiquetaCaso' => substr($resultado, 2)
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionBorrarEtiqueta()
    {
        $IdEtiquetaCaso = Yii::$app->request->post('IdEtiquetaCaso');

        $caso = new Casos();

        $resultado = $caso->BorrarEtiqueta($IdEtiquetaCaso);

        if ($resultado == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return ['Error' => $resultado];
        }
    }
}
