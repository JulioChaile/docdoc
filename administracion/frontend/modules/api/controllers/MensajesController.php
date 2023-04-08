<?php

namespace frontend\modules\api\controllers;

use common\components\ChatApi;
use common\models\GestorChatApi;
use common\models\Casos;
use common\models\Contactos;
use common\models\Mediadores;
use common\models\GestorMensajesInterno;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;

class MensajesController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bearerAuth' => [
                    'class' => OptionalBearerAuth::className(),
                    'except' => ['options', 'mensaje-global-template'],
                ],
            ]
        );
    }
    
    public function actionEnviarMensaje()
    {
        $IdChat = Yii::$app->request->post('IdChat');
        $Contenido = Yii::$app->request->post('Contenido');
        $IdCaso = Yii::$app->request->post('IdCaso');
        $mediador = Yii::$app->request->post('mediador');
        $contacto = Yii::$app->request->post('contacto');
        if ($mediador === 0 || $mediador === '' || $mediador === 'null') {
            $mediador = null;
        }
        if ($contacto === 0 || $contacto === '' || $contacto === 'null') {
            $contacto = null;
        }
        $Multimedia = json_decode(Yii::$app->request->post('Multimedia'), true);
        $IdUsuario = Yii::$app->user->identity->IdUsuario;

        if (!empty($Contenido)) {
            $Contenido = str_replace('<br>', "\n", $Contenido);
        }

        $gestor = new GestorChatApi;
        $chat = empty($mediador)
            ? (empty($contacto)
                ? $gestor->DameChat($IdChat)
                : $gestor->DameChatContacto($IdChat))
            : $gestor->DameChatMediador($IdChat);

        if (array_key_exists('Mensaje', $chat) && $chat['Mensaje'] != 'OK') {
            return ['Error' => $chat['Mensaje']];
        }
        $IdExternoChat = $chat['IdExternoChat'];

        if (empty($Multimedia)) {
            $respuestaEnviarMensaje = empty($contacto)
                ? Yii::$app->chatapi->enviarMensaje($IdChat, $IdExternoChat, $Contenido, $IdUsuario, $mediador)
                : Yii::$app->chatapi->enviarMensajeContacto($IdChat, $IdExternoChat, $Contenido, $IdUsuario);
        } else {
            $URL = 'https://io.docdoc.com.ar/api/multimedia?file=' . $Multimedia[0]['URL'];

            if (empty($contacto) && empty($mediador)) {
                $respuestaEnviarMensaje = Yii::$app->chatapi->enviarArchivo($IdChat, $IdExternoChat, $URL, $IdUsuario, $Contenido, 'caso');

                $caso = new Casos();
                $caso->IdCaso = $IdCaso;

                if (array_key_exists('Tipo', $Multimedia[0])) {
                    $resultado = $caso->AltaMultimedia($Multimedia);
                }
            } else if(empty($mediador)) {
                $respuestaEnviarMensaje = Yii::$app->chatapi->enviarArchivo($IdChat, $IdExternoChat, $URL, $IdUsuario, $Contenido, 'contacto');

                $contacto = new Contactos();
                $contacto->IdContacto = $chat['IdContacto'];

                $resultado = $contacto->AltaMultimedia($Multimedia);
            } else {
                $respuestaEnviarMensaje = Yii::$app->chatapi->enviarArchivo($IdChat, $IdExternoChat, $URL, $IdUsuario, $Contenido, 'mediador');

                $mediador = new Mediadores();
                $mediador->IdMediador = $chat['IdMediador'];

                $resultado = $mediador->AltaMultimedia($Multimedia);
            }
        }

        return $respuestaEnviarMensaje;
    }
    
    public function actionEnviarTemplate()
    {
        $IdChat = Yii::$app->request->post('IdChat');
        $Contenido = Yii::$app->request->post('Contenido');
        $Objeto = Yii::$app->request->post('Objeto');
        $mediador = Yii::$app->request->post('mediador');
        $contacto = Yii::$app->request->post('contacto');
        if ($mediador === 0 || $mediador === '' || $mediador === 'null') {
            $mediador = null;
        }
        if ($contacto === 0 || $contacto === '' || $contacto === 'null') {
            $contacto = null;
        }

        $IdUsuario = Yii::$app->user->identity->IdUsuario;

        if (!empty($Contenido)) {
            $Contenido = str_replace('<br>', "\n", $Contenido);
        }

        $gestor = new GestorChatApi;
        $chat = empty($mediador)
            ? (empty($contacto)
                ? $gestor->DameChat($IdChat)
                : $gestor->DameChatContacto($IdChat))
            : $gestor->DameChatMediador($IdChat);

        if (array_key_exists('Mensaje', $chat) && $chat['Mensaje'] != 'OK') {
            return ['Error' => $chat['Mensaje']];
        }
        $IdExternoChat = $chat['IdExternoChat'];

        $Objeto['chatId'] = $IdExternoChat;

        $respuestaEnviarMensaje = empty($contacto)
            ? Yii::$app->chatapi->enviarTemplate($IdChat, $Contenido, $IdUsuario, $Objeto, $mediador)
            : Yii::$app->chatapi->enviarTemplateContacto($IdChat, $IdExternoChat, $Contenido, $IdUsuario, $Objeto);

        return $respuestaEnviarMensaje;
    }
    
    public function actionEnviarMensajeExterno()
    {
        $IdChatApi = Yii::$app->request->post('IdChatApi');
        $Contenido = Yii::$app->request->post('Contenido');
        $Multimedia = json_decode(Yii::$app->request->post('Multimedia'), true);
        $IdUsuario = Yii::$app->user->identity->IdUsuario;

        if (!empty($Contenido)) {
            $Contenido = str_replace('<br>', "\n", $Contenido);
        }

        if (true) {
            $respuestaEnviarMensaje = Yii::$app->chatapi->enviarMensajeExterno($IdChatApi, $Contenido, $IdUsuario);
        } else {
            //$URL = 'https://io.docdoc.com.ar/api/multimedia?file=' . $Multimedia[0]['URL'];

            //if (empty($contacto) && empty($mediador)) {
            //    $respuestaEnviarMensaje = Yii::$app->chatapi->enviarArchivo($IdChat, $IdExternoChat, $URL, $IdUsuario, $Contenido, 'caso');

            //    $caso = new Casos();
            //    $caso->IdCaso = $IdCaso;

            //    $resultado = $caso->AltaMultimedia($Multimedia);
            //} else if(empty($mediador)) {
            //    $respuestaEnviarMensaje = Yii::$app->chatapi->enviarArchivo($IdChat, $IdExternoChat, $URL, $IdUsuario, $Contenido, 'contacto');

            //    $contacto = new Contactos();
            //    $contacto->IdContacto = $chat['IdContacto'];

            //    $resultado = $contacto->AltaMultimedia($Multimedia);
            //} else {
            //    $respuestaEnviarMensaje = Yii::$app->chatapi->enviarArchivo($IdChat, $IdExternoChat, $URL, $IdUsuario, $Contenido, 'mediador');

            //    $mediador = new Mediadores();
            //    $mediador->IdMediador = $chat['IdMediador'];

            //    $resultado = $mediador->AltaMultimedia($Multimedia);
            //}
        }

        return $respuestaEnviarMensaje;
    }
    
    public function actionEnviarTemplateExterno()
    {
        $IdChatApi = Yii::$app->request->post('IdChatApi');
        $Contenido = Yii::$app->request->post('Contenido');
        $Objeto = Yii::$app->request->post('Objeto');
        $Multimedia = json_decode(Yii::$app->request->post('Multimedia'), true);
        $IdUsuario = Yii::$app->user->identity->IdUsuario;

        if (!empty($Contenido)) {
            $Contenido = str_replace('<br>', "\n", $Contenido);
        }

        if (true) {
            $respuestaEnviarMensaje = Yii::$app->chatapi->enviarTemplateExterno($IdChatApi, $Contenido, $IdUsuario, $Objeto);
        } else {
            //$URL = 'https://io.docdoc.com.ar/api/multimedia?file=' . $Multimedia[0]['URL'];

            //if (empty($contacto) && empty($mediador)) {
            //    $respuestaEnviarMensaje = Yii::$app->chatapi->enviarArchivo($IdChat, $IdExternoChat, $URL, $IdUsuario, $Contenido, 'caso');

            //    $caso = new Casos();
            //    $caso->IdCaso = $IdCaso;

            //    $resultado = $caso->AltaMultimedia($Multimedia);
            //} else if(empty($mediador)) {
            //    $respuestaEnviarMensaje = Yii::$app->chatapi->enviarArchivo($IdChat, $IdExternoChat, $URL, $IdUsuario, $Contenido, 'contacto');

            //    $contacto = new Contactos();
            //    $contacto->IdContacto = $chat['IdContacto'];

            //    $resultado = $contacto->AltaMultimedia($Multimedia);
            //} else {
            //    $respuestaEnviarMensaje = Yii::$app->chatapi->enviarArchivo($IdChat, $IdExternoChat, $URL, $IdUsuario, $Contenido, 'mediador');

            //    $mediador = new Mediadores();
            //    $mediador->IdMediador = $chat['IdMediador'];

            //    $resultado = $mediador->AltaMultimedia($Multimedia);
            //}
        }

        return $respuestaEnviarMensaje;
    }

    public function actionListarMensajes($id)
    {
        $limit = Yii::$app->request->get('Limit');
        $idUltimoMensaje = Yii::$app->request->get('IdUltimoMensaje');
        $mediador = Yii::$app->request->get('mediador');
        $contacto = Yii::$app->request->get('contacto');
        if ($idUltimoMensaje === 0 || $idUltimoMensaje === '' || $idUltimoMensaje === 'null') {
            $idUltimoMensaje = null;
        }
        if ($mediador === 0 || $mediador === '' || $mediador === 'null') {
            $mediador = null;
        }
        if ($contacto === 0 || $contacto === '' || $contacto === 'null') {
            $contacto = null;
        }

        $gestor = new GestorChatApi;
        return empty($mediador)
            ? (empty($contacto)
                ? $gestor->ListarMensajes($id, $idUltimoMensaje, $limit)
                : $gestor->ListarMensajesContacto($id, $idUltimoMensaje, $limit))
            : $gestor->ListarMensajesMediador($id, $idUltimoMensaje, $limit);
    }

    public function actionListarMensajesExterno()
    {
        $id = Yii::$app->request->get('IdChatApi');
        $gestor = new GestorChatApi;
        return $gestor->ListarMensajesExterno($id);
    }

    public function actionListarChatsExterno()
    {
        $Palabra = Yii::$app->request->get('palabra');
        $gestor = new GestorChatApi;
        return $gestor->ListarChatsExterno($Palabra);
    }

    public function actionNuevosMensajes()
    {
        $IdCaso = Yii::$app->request->get('IdCaso');
        $Cliente = Yii::$app->request->get('Cliente');
        $idUsuario = Yii::$app->user->identity->IdUsuario;

        $gestor = new GestorChatApi;
        $gestormensajesinternos = new GestorMensajesInterno;

        return [
            "Caso" => $gestor->NuevosMensajes($idUsuario),
            "Mediador" => $gestor->NuevosMensajesMediador($idUsuario),
            "Contacto" => $gestor->NuevosMensajesContacto($idUsuario),
            "Interno" => $gestormensajesinternos->NuevosMensajes($IdCaso, $idUsuario, $Cliente),
            "Externo" => $gestor->NuevosMensajesExterno() 
        ];
    }

    public function actionNuevosMensajesExterno()
    {
        $gestor = new GestorChatApi;

        return $gestor->NuevosMensajesExterno();
    }

    public function actionMensajeComun()
    {
        $Telefono = Yii::$app->request->post('telefono');
        $Contenido = Yii::$app->request->post('contenido');

        return Yii::$app->chatapi->mensajeComun($Telefono, $Contenido);
    }

    public function actionMensajeGlobalTemplate()
    {
        $IdsChat = json_decode(Yii::$app->request->post('IdsChat'), true);
        $NuevosChats = json_decode(Yii::$app->request->post('NuevosChats'), true);
        $Contenido = Yii::$app->request->post('Contenido');
        $Objeto = Yii::$app->request->post('Objeto');

        $IdUsuario = Yii::$app->user->identity->IdUsuario;

        $errores = array();

        if (!empty($NuevosChats)) {
            foreach ($NuevosChats as $key => $n) {
                $Telefono = $n['Telefono'];
                $IdCaso = $n['IdCaso'];
                $IdPersona = $n['IdPersona'];
                
                $respuestaCrearChat = Yii::$app->chatapi->nuevoChat($Telefono, $IdCaso, $IdPersona);

                if ($respuestaCrearChat['Error'] === null && array_key_exists('IdChat', $respuestaCrearChat)) {
                    $IdsChat[] = [
                        'IdChat' => $respuestaCrearChat['IdChat'],
                        'IdCaso' => $IdCaso
                    ];
                } else {
                    $errores[$IdCaso] = $respuestaCrearChat['Error'] ? $respuestaCrearChat['Error'] : 'Ya existe un chat con este numero';
                }
            }
        }

        if (!empty($IdsChat)) {
            foreach ($IdsChat as $key => $n) {
                $IdChat = $n['IdChat'];
                $IdCaso = $n['IdCaso'];

                $gestor = new GestorChatApi;
                $chat = $gestor->DameChat($IdChat);
                if (array_key_exists('Mensaje', $chat) && $chat['Mensaje'] != 'OK') {
                    $errores[$IdCaso] = $chat['Mensaje'];
                } else {
                    $IdExternoChat = $chat['IdExternoChat'];
                    $Objeto['chatId'] = $IdExternoChat;

                    $respuestaEnviarMensaje = Yii::$app->chatapi->enviarTemplate($IdChat, $Contenido, $IdUsuario, $Objeto, null);

                    if ($respuestaEnviarMensaje['Error'] !== null) {
                        $errores[$IdCaso] = $respuestaEnviarMensaje['Error'];
                    } else {
                        $IdUltMsjLeido = $respuestaEnviarMensaje['IdMensaje'];

                        $respuestaActualizarUltMsjLeido = $gestor->ModificarUltMsjLeido($IdChat, $IdUltMsjLeido);
                    }
                }
            }
        }

        return $errores;
    }

    public function actionMensajeGlobal()
    {
        $IdsChat = json_decode(Yii::$app->request->post('IdsChat'), true);
        $NuevosChats = json_decode(Yii::$app->request->post('NuevosChats'), true);
        $Multimedia = json_decode(Yii::$app->request->post('Multimedia'), true);
        $Contenido = Yii::$app->request->post('Contenido');

        $IdUsuario = Yii::$app->user->identity->IdUsuario;

        $errores = array();

        if (!empty($NuevosChats)) {
            foreach ($NuevosChats as $key => $n) {
                $Telefono = $n['Telefono'];
                $IdCaso = $n['IdCaso'];
                $IdPersona = $n['IdPersona'];
                
                $respuestaCrearChat = Yii::$app->chatapi->nuevoChat($Telefono, $IdCaso, $IdPersona);

                if ($respuestaCrearChat['Error'] === null && array_key_exists('IdChat', $respuestaCrearChat)) {
                    $IdsChat[] = [
                        'IdChat' => $respuestaCrearChat['IdChat'],
                        'IdCaso' => $IdCaso
                    ];
                } else {
                    $errores[$IdCaso] = $respuestaCrearChat['Error'] ? $respuestaCrearChat['Error'] : 'Ya existe un chat con este numero';
                }
            }
        }

        if (!empty($IdsChat)) {
            foreach ($IdsChat as $key => $n) {
                $IdChat = $n['IdChat'];
                $IdCaso = $n['IdCaso'];

                $gestor = new GestorChatApi;
                $chat = $gestor->DameChat($IdChat);
                if (array_key_exists('Mensaje', $chat) && $chat['Mensaje'] != 'OK') {
                    $errores[$IdCaso] = $chat['Mensaje'];
                } else {
                    $IdExternoChat = $chat['IdExternoChat'];

                    if (empty($Multimedia)) {
                        $respuestaEnviarMensaje = Yii::$app->chatapi->enviarMensaje($IdChat, $IdExternoChat, $Contenido, $IdUsuario);
                    } else {
                        $URL = 'https://io.docdoc.com.ar/api/multimedia?file=' . $Multimedia[0]['URL'];

                        $respuestaEnviarMensaje = Yii::$app->chatapi->enviarArchivo($IdChat, $IdExternoChat, $URL, $IdUsuario, $Contenido);
                    }

                    if ($respuestaEnviarMensaje['Error'] !== null) {
                        $errores[$IdCaso] = $respuestaEnviarMensaje['Error'];
                    } else {
                        $IdUltMsjLeido = $respuestaEnviarMensaje['IdMensaje'];

                        $respuestaActualizarUltMsjLeido = $gestor->ModificarUltMsjLeido($IdChat, $IdUltMsjLeido);

                        if (!empty($Multimedia)) {
                            $caso = new Casos();
                            $caso->IdCaso = $IdCaso;
                            
                            $Multimedia[0]['Nombre'] = $Multimedia[0]['URL'];
                            $resultado = $caso->AltaMultimedia($Multimedia);
                        }
                    }
                }
            }
        }

        return $errores;
    }
}
