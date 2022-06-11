<?php
namespace frontend\modules\api\controllers;

use common\models\Consultas;
use backend\models\GestorConsultas;
use common\models\Estudios;
use common\components\EmailHelper;
use common\models\Casos;
use common\components\ChatApi;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;

class ConsultasController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bearerAuth' => [
                    'class' => OptionalBearerAuth::className(),
                    'except' => ['create', 'options'],
                ],
            ]
        );
    }
    public function actionCreate()
    {
        $consulta = new Consultas();

        $consulta->setAttributes(Yii::$app->request->post());

        $gestor_consulta = new GestorConsultas();

        $resultado = $gestor_consulta -> Alta($consulta);

        if (array_key_exists('Exito', $resultado) && $resultado['Exito'] == 'OK') {

            $respuestaNuevoChat = Yii::$app->chatapi->nuevoChat($consulta->Telefono, $resultado['IdCaso'], $resultado['IdPersona']);

            if ($respuestaNuevoChat['Error'] == null && array_key_exists('IdChat', $respuestaNuevoChat)) {
                $Contenido = 'Hola, gracias por consultar a DocDoc! Por esta vía te contactará un abogado de la plataforma para ayudarte con tu caso.';

                $respuestaEnviarMensaje = Yii::$app->chatapi->enviarMensaje($respuestaNuevoChat['IdChat'], $respuestaNuevoChat['IdExternoChat'], $Contenido, 1);
            }

            $Estudio = new Estudios;
            $Estudio->IdEstudio = 5;

            $usuarios = $Estudio->BuscarUsuarios();

            $caso = new Casos;
            $caso->IdCaso = $resultado['IdCaso'];

            $caso->Dame(5);

            if (empty($usuarios)) {
                return ['Error' => null];
            } else {
                foreach ($usuarios as $usuario) {
                    $cadete = $usuario['Observaciones'] === 'cadete' || $usuario['Observaciones'] === 'Cadete';

                    if (!$cadete) {
                        EmailHelper::enviarEmail(
                            'DocDoc <contacto@docdoc.com.ar>',
                            $usuario['Email'],
                            'Nueva consulta en DocDoc!',
                            'nueva-consulta-docdoc',
                            [
                                'caso' => $caso,
                                'consulta' => $consulta
                            ]
                        );
                    }
                }
            }

            $Contenido1 = '!Has recibido una nueva consulta en DocDoc! Puedes ver esta consulta como un nuevo caso en https://app.docdoc.com.ar/. Recuerda que si olvidaste tus datos de inicio de sesion puedes hacer click en "Recuperar cuenta" e ingresar tu mail y una nueva contraseña.';

            $respuestaMensajeEstudio1 = Yii::$app->chatapi->mensajeGlobal(5, $Contenido1);

            $Contenido2 = 'Nombre: ' . $consulta->Apynom;

            $respuestaMensajeEstudio2 = Yii::$app->chatapi->mensajeGlobal(5, $Contenido2);

            $Contenido3 = 'Consulta: ' . $consulta->Texto;

            $respuestaMensajeEstudio3 = Yii::$app->chatapi->mensajeGlobal(5, $Contenido3);

            return [
                'Error' => null,
                'IdCaso' => $caso->IdCaso,
                'IdPersona' => $resultado['IdPersona']
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionIndex()
    {
        $estudio = new Estudios();
        
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        
        $consultas = $estudio->BuscarConsultas();
        
        return $consultas;
    }
    
    public function actionView($id)
    {
        $consulta = new Consultas();
        
        $consulta->IdConsulta = $id;
        
        $consulta->Dame();
        
        return $consulta;
    }
    
    public function actionAceptarDerivacion($id)
    {
        $estudio = new Estudios();
        
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        
        $consulta = new Consultas();
        $consulta->IdDerivacionConsulta = $id;
        $resultado = $estudio->AceptarDerivacionConsulta($consulta);
        
        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }
    
    public function actionRechazarDerivacion($id)
    {
        $estudio = new Estudios();
        
        $estudio->IdEstudio = Yii::$app->user->identity->IdEstudio;
        
        $consulta = new Consultas();
        $consulta->IdDerivacionConsulta = $id;
        
        $resultado = $estudio->RechazarDerivacionConsulta($consulta);
        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }
}
