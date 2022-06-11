<?php
namespace frontend\modules\api\controllers;

use Exception;
use Yii;
use common\models\GestorChatApi;
use common\models\Casos;
use common\models\Contactos;
use common\models\Mediadores;
use common\components\FCMHelper;

class WebhookController extends BaseController
{
    public function actionCreate()
    {
        return $this->actionIndex();
    }

    /**
     * Recibe las notificaciones de WhatsApp y segun sean mensajes nuevos
     * o avisos de "Recibido" o "Visto, guarda el mensaje en la base de datos
     * o actualiza la fecha del mensaje enviado segun corresponda
     */
    public function actionIndex()
    {
        $notificaciones = Yii::$app->request->post();

        $gestor = new GestorChatApi;
        
        if (array_key_exists('messages', $notificaciones) && isset($notificaciones['messages'])) {
            foreach ($notificaciones['messages'] as $message) {
                $IdExternoMensaje = $message['id'];
                $IdExternoChat = $message['chatId'];
                $Contenido = $message['body'];
                $FechaEnviado = date("Y-m-d H:i:s", $message['time']);
                $FechaRecibido = date("Y-m-d H:i:s");
                $IdUsuario = $message['fromMe'] ? 1 : null; // Se guardan los mensajes que son enviados desde el celular con IdUsuario = 1
                $Tipo = $message['type'];
                $tipoChat = '';
                $tokens = [];
                $objetoExterno = [];

                $resultado = $gestor->DameChat(null, $IdExternoChat);

                Yii::info($resultado);

                if (array_key_exists('Mensaje', $resultado) && $resultado['Mensaje'] != 'OK') {
                    $resultado = $gestor->DameChatMediador(null, $IdExternoChat);
                    if (array_key_exists('Mensaje', $resultado) && $resultado['Mensaje'] != 'OK') {
                        $resultado = $gestor->DameChatContacto(null, $IdExternoChat);
                        if (array_key_exists('Mensaje', $resultado) && $resultado['Mensaje'] != 'OK') {
                            $tipoChat = 'externo';
                            $objetoExterno['IdMensajeApi'] = $IdExternoMensaje;
                            $objetoExterno['IdChatApi'] = $IdExternoChat;
                            $objetoExterno['FechaEnviado'] = $FechaEnviado;
                            $objetoExterno['FechaRecibido'] = $FechaRecibido;
                            $objetoExterno['FechaVisto'] = $IdUsuario === 1 ? $FechaRecibido : null;
                            $objetoExterno['Contenido'] = $Contenido;
                            $objetoExterno['IdUsuario'] = $IdUsuario;
                        } else {
                            $tipoChat = 'contacto';
                            $IdChat = $resultado['IdChatContacto'];
                        }
                    } else {
                        $tipoChat = 'mediador';
                        $IdChat = $resultado['IdChatMediador'];
                    }
                } else {
                    $tipoChat = 'caso';
                    $IdChat = $resultado['IdChat'];
                    $tokens = json_decode($resultado['Tokens'], true);
                }

                switch ($tipoChat) {
                    case 'caso':
                        $result = $gestor->AltaMensaje($IdExternoMensaje, $IdChat, $Contenido, $FechaEnviado, $FechaRecibido, $IdUsuario);

                        if (substr($result, 0, 2) !== 'OK') {
                            throw new Exception($result);
                        }

                        if (!empty($tokens)) {
                            FCMHelper::enviarNotificacionPush(
                                [
                                    'title' => 'Nuevo Mensaje',
                                    'body' => 'Tienes mensajes sin leer en el caso ' . $resultado['Caratula']        
                                ],
                                array_values(array_unique($tokens)),
                                [
                                    'tipo' => 'chatMensaje',
                                    'id' => $IdChat,
                                    'caratula' => $resultado['Caratula'],
                                    'telefono' => $resultado['Telefono'] 
                                ]
                            );
                        }

                        if ($Contenido === 'Video upload disabled' && $tipoChat !== 'mediador') {
                            $MensajeAutomatico = "Este es un mensaje automatico de DocDoc. Si intentó enviar un video por favor envielo como archivo para que nuestro sistema pueda guardarlo correctamente.";

                            Yii::$app->chatapi->enviarMensaje($IdChat, $IdExternoChat, $MensajeAutomatico, 1);
                        }
                        break;
                        
                    case 'mediador':
                        $result = $gestor->AltaMensajeMediador($IdExternoMensaje, $IdChat, $Contenido, $FechaEnviado, $FechaRecibido, $IdUsuario);

                        if (substr($result, 0, 2) !== 'OK') {
                            throw new Exception($result);
                        }

                        if ($Contenido === 'Video upload disabled' && $tipoChat !== 'mediador') {
                            $MensajeAutomatico = "Este es un mensaje automatico de DocDoc. Si intentó enviar un video por favor envielo como archivo para que nuestro sistema pueda guardarlo correctamente.";

                            Yii::$app->chatapi->enviarMensaje($IdChat, $IdExternoChat, $MensajeAutomatico, 1, 'mediador');
                        }
                        break;

                    case 'contacto':
                        $result = $gestor->AltaMensajeContacto($IdExternoMensaje, $IdChat, $Contenido, $FechaEnviado, $FechaRecibido, $IdUsuario);

                        if (substr($result, 0, 2) !== 'OK') {
                            throw new Exception($result);
                        }

                        if ($Contenido === 'Video upload disabled' && $tipoChat !== 'mediador') {
                            $MensajeAutomatico = "Este es un mensaje automatico de DocDoc. Si intentó enviar un video por favor envielo como archivo para que nuestro sistema pueda guardarlo correctamente.";

                            Yii::$app->chatapi->enviarMensajeContacto($IdChat, $IdExternoChat, $MensajeAutomatico, 1);
                        }
                        break;

                    case 'externo':
                        if ((substr($Contenido, 0, 7) === 'Nombre:' || substr($Contenido, 0, 9) === 'Consulta:' || substr($Contenido, 0, 13) === '!Has recibido' || substr($Contenido, 0, 38) === 'Se te asigno una nueva tarea pendiente') && $IdUsuario === 1) {
                            return;
                        }

                        $result = $gestor->AltaMensajeExterno($objetoExterno);

                        if (substr($result, 0, 2) !== 'OK') {
                            throw new Exception($result);
                        }

                        if ($Contenido === 'Video upload disabled' && $tipoChat !== 'mediador') {
                            $MensajeAutomatico = "Este es un mensaje automatico de DocDoc. Si intentó enviar un video por favor envielo como archivo para que nuestro sistema pueda guardarlo correctamente.";

                            Yii::$app->chatapi->enviarMensajeExterno($objetoExterno, $MensajeAutomatico, 1);
                        }
                        break;
                }

                if ($Tipo !== 'chat' && $Tipo !== 'call_log' && $Tipo !== 'ptt') {
                    $content = file_get_contents($Contenido, false);

                    $name = $this->generateRandomString(32);

                    $format = array_reverse(explode('.', $Contenido))[0];

                    $formatosDoc = ['doc', 'docx', 'docm', 'dot', 'dotx', 'dotm', 'odt', 'pdf'];

                    Yii::$app->bucket->escribirArchivo("estudios/$name.$format", $content);

                    $all_mimes = '{"png":["image\/png","image\/x-png"],"bmp":["image\/bmp","image\/x-bmp","image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp","image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp","application\/x-win-bitmap"],"gif":["image\/gif"],"jpeg":["image\/jpeg","image\/pjpeg"],"xspf":["application\/xspf+xml"],"vlc":["application\/videolan"],"wmv":["video\/x-ms-wmv","video\/x-ms-asf"],"au":["audio\/x-au"],"ac3":["audio\/ac3"],"flac":["audio\/x-flac"],"ogg":["audio\/ogg","video\/ogg","application\/ogg"],"oga":["audio\/ogg","video\/ogg","application\/ogg"],"kmz":["application\/vnd.google-earth.kmz"],"kml":["application\/vnd.google-earth.kml+xml"],"rtx":["text\/richtext"],"rtf":["text\/rtf"],"jar":["application\/java-archive","application\/x-java-application","application\/x-jar"],"zip":["application\/x-zip","application\/zip","application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],"7zip":["application\/x-compressed"],"xml":["application\/xml","text\/xml"],"svg":["image\/svg+xml"],"3g2":["video\/3gpp2"],"3gp":["video\/3gp","video\/3gpp"],"mp4":["video\/mp4"],"m4a":["audio\/x-m4a"],"f4v":["video\/x-f4v"],"flv":["video\/x-flv"],"webm":["video\/webm"],"aac":["audio\/x-acc"],"m4u":["application\/vnd.mpegurl"],"pdf":["application\/pdf","application\/octet-stream"],"pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],"ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office","application\/msword"],"docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],"xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],"xl":["application\/excel"],"xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel","application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],"xsl":["text\/xsl"],"mpeg":["video\/mpeg"],"mov":["video\/quicktime"],"avi":["video\/x-msvideo","video\/msvideo","video\/avi","application\/x-troff-msvideo"],"movie":["video\/x-sgi-movie"],"log":["text\/x-log"],"txt":["text\/plain"],"css":["text\/css"],"html":["text\/html"],"wav":["audio\/x-wav","audio\/wave","audio\/wav"],"xhtml":["application\/xhtml+xml"],"tar":["application\/x-tar"],"tgz":["application\/x-gzip-compressed"],"psd":["application\/x-photoshop","image\/vnd.adobe.photoshop"],"exe":["application\/x-msdownload"],"js":["application\/x-javascript"],"mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],"rar":["application\/x-rar","application\/rar","application\/x-rar-compressed"],"gzip":["application\/x-gzip"],"hqx":["application\/mac-binhex40","application\/mac-binhex","application\/x-binhex40","application\/x-mac-binhex40"],"cpt":["application\/mac-compactpro"],"bin":["application\/macbinary","application\/mac-binary","application\/x-binary","application\/x-macbinary"],"oda":["application\/oda"],"ai":["application\/postscript"],"smil":["application\/smil"],"mif":["application\/vnd.mif"],"wbxml":["application\/wbxml"],"wmlc":["application\/wmlc"],"dcr":["application\/x-director"],"dvi":["application\/x-dvi"],"gtar":["application\/x-gtar"],"php":["application\/x-httpd-php","application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],"swf":["application\/x-shockwave-flash"],"sit":["application\/x-stuffit"],"z":["application\/x-compress"],"mid":["audio\/midi"],"aif":["audio\/x-aiff","audio\/aiff"],"ram":["audio\/x-pn-realaudio"],"rpm":["audio\/x-pn-realaudio-plugin"],"ra":["audio\/x-realaudio"],"rv":["video\/vnd.rn-realvideo"],"jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],"tiff":["image\/tiff"],"eml":["message\/rfc822"],"pem":["application\/x-x509-user-cert","application\/x-pem-file"],"p10":["application\/x-pkcs10","application\/pkcs10"],"p12":["application\/x-pkcs12"],"p7a":["application\/x-pkcs7-signature"],"p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],"p7r":["application\/x-pkcs7-certreqresp"],"p7s":["application\/pkcs7-signature"],"crt":["application\/x-x509-ca-cert","application\/pkix-cert"],"crl":["application\/pkix-crl","application\/pkcs-crl"],"pgp":["application\/pgp"],"gpg":["application\/gpg-keys"],"rsa":["application\/x-pkcs7"],"ics":["text\/calendar"],"zsh":["text\/x-scriptzsh"],"cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],"wma":["audio\/x-ms-wma"],"vcf":["text\/x-vcard"],"srt":["text\/srt"],"vtt":["text\/vtt"],"ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],"csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],"json":["application\/json","text\/json"]}';
                    $all_mimes = json_decode($all_mimes,true);

                    if (array_key_exists($format, $all_mimes)) {
                        $MimeType = $all_mimes[$format][0];

                        $Tipo = explode('/', $MimeType)[0] === 'application' ? 'O' : strtoupper($MimeType)[0];
                    } else {
                        $Tipo = 'O';
                    }

                    switch ($tipoChat) {
                        case 'caso':
                            $IdCaso = $resultado['IdCaso'];

                            $caso = new Casos();
                            $caso->IdCaso = $IdCaso;

                            $Multimedia = array([
                                'URL' => $name . '.' . $format,
                                'Tipo' => $Tipo,
                                'OrigenMultimedia' => in_array(strtolower($format), $formatosDoc, true) ? 'D' : 'R',
                                'Nombre' => $name
                            ]);

                            $respuesta = $caso->AltaMultimedia($Multimedia);
        
                            if (substr($respuesta, 0, 2) !== 'OK') {
                                throw new Exception($result);
                            }
                            break;

                        case 'contacto':
                            $IdContacto = $resultado['IdContacto'];

                            $Multimedia = array([
                                'URL' => $name . '.' . $format,
                                'Tipo' => $Tipo
                            ]);

                            $contacto = new Contactos();
                            $contacto->IdContacto = $IdContacto;

                            $respuesta = $contacto->AltaMultimedia($Multimedia);
        
                            if (substr($respuesta, 0, 2) !== 'OK') {
                                throw new Exception($result);
                            }
                            break;

                        case 'mediador':
                            $IdMediador = $resultado['IdMediador'];

                            $Multimedia = array([
                                'URL' => $name . '.' . $format,
                                'Tipo' => $Tipo
                            ]);

                            $mediador = new Mediadores();
                            $mediador->IdMediador = $IdMediador;

                            $respuesta = $mediador->AltaMultimedia($Multimedia);
        
                            if (substr($respuesta, 0, 2) !== 'OK') {
                                throw new Exception($result);
                            }
                            break;
                    }
                }
            }
        }

        if (array_key_exists('ack', $notificaciones) && isset($notificaciones['ack'])) {
            foreach ($notificaciones['ack'] as $ack) {
                $IdExternoMensaje = $ack['id'];
                $FechaRecibido = null;
                $FechaVisto = null;

                if ($ack['status'] == 'delivered') {
                    $FechaRecibido = date("Y-m-d H:i:s");
                } else if ($ack['status'] == 'read') {
                    $FechaVisto = date("Y-m-d H:i:s");
                } else {
                    throw new Exception($result);
                }

                $result = $gestor->SetFechaMensaje($IdExternoMensaje, $FechaRecibido, $FechaVisto);
                $result = $gestor->SetFechaMensajeExterno($IdExternoMensaje, $FechaRecibido, $FechaVisto);

                if (substr($result, 0, 2) !== 'OK') {
                    throw new Exception($result);
                }
            }
        }

        return 'ok';
    }

    /**
     * https://stackoverflow.com/questions/4356289/php-random-string-generator
     *
     * @param length Largo del string
     */
    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
