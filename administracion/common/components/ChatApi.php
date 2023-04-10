<?php

namespace common\components;

use Exception;
use Yii;
use yii\base\Component;
use common\models\GestorChatApi;
use common\models\Estudios;

class ChatApi extends Component
{
    public $Token;
    public $InstanceId;
    public $ServerId;

    /**
     * Inicializa el cliente de Chat Api
     *
     * @param type $config Array de configuraciones
     * @throws Exception
     */
    public function __construct($config = [])
    {
        foreach ($config as $property => $value) {
            if (!$this->hasProperty($property)) {
                throw new Exception("La propiedad {$property} no está definida");
            }
            if (is_null($value)) {
                throw new Exception("La propiedad {$property} no puede ser nula.");
            }
            $this->{$property} = $value;
        }

        parent::__construct($config);
    }

    /**
     * Permite enviar un mensaje de texto a través de ChatApi.
     * Retorna IdMensaje de la base de datos
     *
     * @param IdChat Id del chat en la base de datos
     * @param IdExternoChat Id de ChatApi del chat
     * @param Contenido Contenido del mensaje
     * @param IdUsuario Id del usuario que envia el mensaje
     */
    public function enviarMensaje($IdChat, $IdExternoChat, $Contenido, $IdUsuario, $mediador = null)
    {
        $data = [
            'chatId' => $IdExternoChat, // Receivers phone
            'body'  => $Contenido, // Message
        ];
        $json = json_encode($data); // Encode data to JSON
        // URL for request POST /message
        $url = $this->sendUrl();
        // Make a POST request
        $options = stream_context_create(['http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => $json
            ]
        ]);

        $FechaEnviado = date("Y-m-d H:i:s");

        $gestor = new GestorChatApi;
        $resultado = empty($mediador)
            ? $gestor->AltaMensaje(null, $IdChat, $Contenido, $FechaEnviado, null, $IdUsuario)
            : $gestor->AltaMensajeMediador(null, $IdChat, $Contenido, $FechaEnviado, null, $IdUsuario);

        if (substr($resultado, 0, 2) == 'OK') {
            $IdMensaje = substr($resultado, 2);
        } else {
            return ['Error' => $resultado];
        }

        // Send a request
        $result = @file_get_contents($url, false, $options);

        Yii::info($result);

        $respuesta = json_decode($result, true);

        if (!array_key_exists('sent', $respuesta) || !isset($respuesta['sent']) || !$respuesta['sent']) {
            return ['Error' => $respuesta['message'], 'respuesta' => $respuesta];
        }

        $IdExternoMensaje = $respuesta['id'];

        $resultadoSet = empty($mediador)
            ? $gestor->SetIdExternoMensaje($IdMensaje, $IdExternoMensaje)
            : $gestor->SetIdExternoMensajeMediador($IdMensaje, $IdExternoMensaje);

        if (substr($resultadoSet, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdMensaje' => $IdMensaje,
                'respuesta' => $respuesta
            ];
        } else {
            return ['Error' => $resultadoSet];
        }
    }

    public function enviarTemplate($IdChat, $Contenido, $IdUsuario, $Objeto, $mediador = null)
    {
        // Make a POST request
        $options = stream_context_create(['http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => json_encode($Objeto)
            ]
        ]);

        $FechaEnviado = date("Y-m-d H:i:s");

        $gestor = new GestorChatApi;
        $resultado = empty($mediador)
        ? $gestor->AltaMensaje(null, $IdChat, $Contenido, $FechaEnviado, null, $IdUsuario)
        : $gestor->AltaMensajeMediador(null, $IdChat, $Contenido, $FechaEnviado, null, $IdUsuario);

        if (substr($resultado, 0, 2) == 'OK') {
            $IdMensaje = substr($resultado, 2);
        } else {
            return ['Error' => $resultado];
        }

        $url = "https://api.1msg.io/153725/sendTemplate?token=67aqw7sghzkatk34";

        // Send a request
        $result = @file_get_contents($url, false, $options);

        $respuesta = json_decode($result, true);

        if ($respuesta === null) {
            $respuesta = [];
        }

        if (!array_key_exists('sent', $respuesta) || !isset($respuesta['sent']) || !$respuesta['sent']) {
            return ['Error' => isset($respuesta['message']) ? $respuesta['message'] : 'Error inesperado', 'respuesta' => $respuesta];
        }

        $IdExternoMensaje = $respuesta['id'];

        $resultadoSet = empty($mediador)
        ? $gestor->SetIdExternoMensaje($IdMensaje, $IdExternoMensaje)
        : $gestor->SetIdExternoMensajeMediador($IdMensaje, $IdExternoMensaje);

        if (substr($resultadoSet, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdMensaje' => $IdMensaje,
                'respuesta' => $respuesta,
                'objeto' => $Objeto
            ];
        } else {
            return ['Error' => $resultadoSet];
        }
    }

    public function enviarMensajeExterno($IdChatApi, $Contenido, $IdUsuario)
    {
        $data = [
            'chatId' => $IdChatApi, // Receivers phone
            'body'  => $Contenido, // Message
        ];
        $json = json_encode($data); // Encode data to JSON
        // URL for request POST /message
        $url = $this->sendUrl();
        // Make a POST request
        $options = stream_context_create(['http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => $json
            ]
        ]);

        $FechaEnviado = date("Y-m-d H:i:s");

        $Objeto = [
            'IdChatApi' => $IdChatApi,
            'IdMensajeApi' => null,
            'Contenido' => $Contenido,
            'FechaEnviado' => $FechaEnviado,
            'FechaRecibido' => null,
            'FechaVisto' => null,
            'IdUsuario' => $IdUsuario
        ];

        $gestor = new GestorChatApi;
        $resultado = $gestor->AltaMensajeExterno($Objeto);

        if (substr($resultado, 0, 2) == 'OK') {
            $IdMensajeExterno = substr($resultado, 2);
        } else {
            return ['Error' => $resultado];
        }

        // Send a request
        $result = @file_get_contents($url, false, $options);

        $respuesta = json_decode($result, true);

        if (!array_key_exists('sent', $respuesta) || !isset($respuesta['sent']) || !$respuesta['sent']) {
            return ['Error' => $respuesta['message'], 'respuesta' => $respuesta];
        }

        $IdMensajeApi = $respuesta['id'];

        $resultadoSet = $gestor->SetIdMensajeApiExterno($IdMensajeExterno, $IdMensajeApi);

        if (substr($resultadoSet, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdMensajeExterno' => $IdMensajeExterno,
                'respuesta' => $respuesta
            ];
        } else {
            return ['Error' => $resultadoSet];
        }
    }

    public function enviarTemplateExterno($IdChatApi, $Contenido, $IdUsuario, $post)
    {
        // URL for request POST /message
        $url =  "https://api.1msg.io/153725/sendTemplate?token=67aqw7sghzkatk34";
        // Make a POST request
        $options = stream_context_create(['http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => json_encode($post)
            ]
        ]);

        $FechaEnviado = date("Y-m-d H:i:s");

        $Objeto = [
            'IdChatApi' => $IdChatApi,
            'IdMensajeApi' => null,
            'Contenido' => $Contenido,
            'FechaEnviado' => $FechaEnviado,
            'FechaRecibido' => null,
            'FechaVisto' => null,
            'IdUsuario' => $IdUsuario
        ];

        $gestor = new GestorChatApi;
        $resultado = $gestor->AltaMensajeExterno($Objeto);

        if (substr($resultado, 0, 2) == 'OK') {
            $IdMensajeExterno = substr($resultado, 2);
        } else {
            return ['Error' => $resultado];
        }

        // Send a request
        $result = @file_get_contents($url, false, $options);

        $respuesta = json_decode($result, true);

        if (!array_key_exists('sent', $respuesta) || !isset($respuesta['sent']) || !$respuesta['sent']) {
            return ['Error' => $respuesta['message'], 'respuesta' => $respuesta];
        }

        $IdMensajeApi = $respuesta['id'];

        $resultadoSet = $gestor->SetIdMensajeApiExterno($IdMensajeExterno, $IdMensajeApi);

        if (substr($resultadoSet, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdMensajeExterno' => $IdMensajeExterno,
                'respuesta' => $respuesta
            ];
        } else {
            return ['Error' => $resultadoSet];
        }
    }

    /**
     * Permite enviar un mensaje de texto a través de ChatApi.
     * Retorna IdMensaje de la base de datos
     *
     * @param IdChat Id del chat en la base de datos
     * @param IdExternoChat Id de ChatApi del chat
     * @param Contenido Contenido del mensaje
     * @param IdUsuario Id del usuario que envia el mensaje
     */
    public function enviarMensajeContacto($IdChat, $IdExternoChat, $Contenido, $IdUsuario)
    {
        $data = [
            'chatId' => $IdExternoChat, // Receivers phone
            'body'  => $Contenido, // Message
        ];
        $json = json_encode($data); // Encode data to JSON
        // URL for request POST /message
        $url = $this->sendUrl();
        // Make a POST request
        $options = stream_context_create(['http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => $json
            ]
        ]);

        $FechaEnviado = date("Y-m-d H:i:s");

        $gestor = new GestorChatApi;
        $resultado = $gestor->AltaMensajeContacto(null, $IdChat, $Contenido, $FechaEnviado, null, $IdUsuario);

        if (substr($resultado, 0, 2) == 'OK') {
            $IdMensaje = substr($resultado, 2);
        } else {
            return ['Error' => $resultado];
        }

        // Send a request
        $result = @file_get_contents($url, false, $options);

        $respuesta = json_decode($result, true);

        if (!$respuesta['sent']) {
            return ['Error' => $respuesta['message']];
        }

        $IdExternoMensaje = $respuesta['id'];

        $resultadoSet = $gestor->SetIdExternoMensajeContacto($IdMensaje, $IdExternoMensaje);

        if (substr($resultadoSet, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdMensaje' => $IdMensaje
            ];
        } else {
            return ['Error' => $resultadoSet];
        }
    }

    public function enviarTemplateContacto($IdChat, $Contenido, $IdUsuario, $Objeto)
    {
        $url = "https://api.1msg.io/153725/sendTemplate?token=67aqw7sghzkatk34";
        // Make a POST request
        $options = stream_context_create(['http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => $Objeto
            ]
        ]);

        $FechaEnviado = date("Y-m-d H:i:s");

        $gestor = new GestorChatApi;
        $resultado = $gestor->AltaMensajeContacto(null, $IdChat, $Contenido, $FechaEnviado, null, $IdUsuario);

        if (substr($resultado, 0, 2) == 'OK') {
            $IdMensaje = substr($resultado, 2);
        } else {
            return ['Error' => $resultado];
        }

        // Send a request
        $result = @file_get_contents($url, false, $options);

        $respuesta = json_decode($result, true);

        if (!$respuesta['sent']) {
            return ['Error' => $respuesta['message']];
        }

        $IdExternoMensaje = $respuesta['id'];

        $resultadoSet = $gestor->SetIdExternoMensajeContacto($IdMensaje, $IdExternoMensaje);

        if (substr($resultadoSet, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdMensaje' => $IdMensaje
            ];
        } else {
            return ['Error' => $resultadoSet];
        }
    }

    /**
     * Permite enviar un mensaje de texto con un archivo a través de ChatApi.
     * Retorna IdMensaje de la base de datos
     *
     * @param IdChat Id del chat en la base de datos
     * @param IdExternoChat Id de ChatApi del chat
     * @param Contenido Contenido del mensaje
     * @param Archivo Archivo del mensaje
     * @param IdUsuario Id del usuario que envia el mensaje
     */
    public function enviarArchivo($IdChat, $IdExternoChat, $Archivo, $IdUsuario, $Contenido = '', $tipo = 'caso')
    {
        $format = strtolower(array_reverse(explode('.', $Archivo))[0]);
        $name = explode('=', array_reverse(explode('.', $Archivo))[1])[1];
        $file = $name . '.' . $format;
        $fileContent = Yii::$app->bucket->leerArchivo("estudios/$file");

        $all_mimes = '{"png":["image\/png","image\/x-png"],"bmp":["image\/bmp","image\/x-bmp","image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp","image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp","application\/x-win-bitmap"],"gif":["image\/gif"],"jpg":["image\/jpg","image\/pjpg"],"jpeg":["image\/jpeg","image\/pjpeg"],"xspf":["application\/xspf+xml"],"vlc":["application\/videolan"],"wmv":["video\/x-ms-wmv","video\/x-ms-asf"],"au":["audio\/x-au"],"ac3":["audio\/ac3"],"flac":["audio\/x-flac"],"ogg":["audio\/ogg","video\/ogg","application\/ogg"],"oga":["audio\/ogg","video\/ogg","application\/ogg"],"kmz":["application\/vnd.google-earth.kmz"],"kml":["application\/vnd.google-earth.kml+xml"],"rtx":["text\/richtext"],"rtf":["text\/rtf"],"jar":["application\/java-archive","application\/x-java-application","application\/x-jar"],"zip":["application\/x-zip","application\/zip","application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],"7zip":["application\/x-compressed"],"xml":["application\/xml","text\/xml"],"svg":["image\/svg+xml"],"3g2":["video\/3gpp2"],"3gp":["video\/3gp","video\/3gpp"],"mp4":["video\/mp4"],"m4a":["audio\/x-m4a"],"f4v":["video\/x-f4v"],"flv":["video\/x-flv"],"webm":["video\/webm"],"aac":["audio\/x-acc"],"m4u":["application\/vnd.mpegurl"],"pdf":["application\/pdf","application\/octet-stream"],"pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],"ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office","application\/msword"],"docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],"xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],"xl":["application\/excel"],"xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel","application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],"xsl":["text\/xsl"],"mpeg":["video\/mpeg"],"mov":["video\/quicktime"],"avi":["video\/x-msvideo","video\/msvideo","video\/avi","application\/x-troff-msvideo"],"movie":["video\/x-sgi-movie"],"log":["text\/x-log"],"txt":["text\/plain"],"css":["text\/css"],"html":["text\/html"],"wav":["audio\/x-wav","audio\/wave","audio\/wav"],"xhtml":["application\/xhtml+xml"],"tar":["application\/x-tar"],"tgz":["application\/x-gzip-compressed"],"psd":["application\/x-photoshop","image\/vnd.adobe.photoshop"],"exe":["application\/x-msdownload"],"js":["application\/x-javascript"],"mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],"rar":["application\/x-rar","application\/rar","application\/x-rar-compressed"],"gzip":["application\/x-gzip"],"hqx":["application\/mac-binhex40","application\/mac-binhex","application\/x-binhex40","application\/x-mac-binhex40"],"cpt":["application\/mac-compactpro"],"bin":["application\/macbinary","application\/mac-binary","application\/x-binary","application\/x-macbinary"],"oda":["application\/oda"],"ai":["application\/postscript"],"smil":["application\/smil"],"mif":["application\/vnd.mif"],"wbxml":["application\/wbxml"],"wmlc":["application\/wmlc"],"dcr":["application\/x-director"],"dvi":["application\/x-dvi"],"gtar":["application\/x-gtar"],"php":["application\/x-httpd-php","application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],"swf":["application\/x-shockwave-flash"],"sit":["application\/x-stuffit"],"z":["application\/x-compress"],"mid":["audio\/midi"],"aif":["audio\/x-aiff","audio\/aiff"],"ram":["audio\/x-pn-realaudio"],"rpm":["audio\/x-pn-realaudio-plugin"],"ra":["audio\/x-realaudio"],"rv":["video\/vnd.rn-realvideo"],"jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],"tiff":["image\/tiff"],"eml":["message\/rfc822"],"pem":["application\/x-x509-user-cert","application\/x-pem-file"],"p10":["application\/x-pkcs10","application\/pkcs10"],"p12":["application\/x-pkcs12"],"p7a":["application\/x-pkcs7-signature"],"p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],"p7r":["application\/x-pkcs7-certreqresp"],"p7s":["application\/pkcs7-signature"],"crt":["application\/x-x509-ca-cert","application\/pkix-cert"],"crl":["application\/pkix-crl","application\/pkcs-crl"],"pgp":["application\/pgp"],"gpg":["application\/gpg-keys"],"rsa":["application\/x-pkcs7"],"ics":["text\/calendar"],"zsh":["text\/x-scriptzsh"],"cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],"wma":["audio\/x-ms-wma"],"vcf":["text\/x-vcard"],"srt":["text\/srt"],"vtt":["text\/vtt"],"ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],"csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],"json":["application\/json","text\/json"]}';
        $all_mimes = json_decode($all_mimes,true);

        if (array_key_exists($format, $all_mimes)) {
            $MimeType = $all_mimes[$format][0];
        } else {
            return [
                'Error' => 'Formato no soportado. Informe al administrador.'
            ];
        }

        $body = 'data:' . $MimeType . ';base64,' . base64_encode($fileContent);

        $data = [
            'chatId' => $IdExternoChat, // Receivers phone
            'body'  => $body,
            'filename' => $name . '.' . $format,
            'caption' => $Contenido
        ];
        $json = json_encode($data); // Encode data to JSON
        // URL for request POST /message
        $url = $this->sendFileUrl();
        // Make a POST request
        $options = stream_context_create(['http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => $json
            ]
        ]);

        $FechaEnviado = date("Y-m-d H:i:s");

        $gestor = new GestorChatApi;

        switch ($tipo) {
            case 'caso':
                $resultadoArchivo = $gestor->AltaMensaje(null, $IdChat, $Archivo, $FechaEnviado, null, $IdUsuario);
                $resultado = $gestor->AltaMensaje(null, $IdChat, $Contenido === '' ? 'Archivo enviado' : $Contenido, $FechaEnviado, null, $IdUsuario);
                break;

            case 'contacto':
                $resultadoArchivo = $gestor->AltaMensajeContacto(null, $IdChat, $Archivo, $FechaEnviado, null, $IdUsuario);
                $resultado = $gestor->AltaMensajeContacto(null, $IdChat, $Contenido === '' ? 'Archivo enviado' : $Contenido, $FechaEnviado, null, $IdUsuario);
                break;

            case 'mediador':
                $resultadoArchivo = $gestor->AltaMensajeMediador(null, $IdChat, $Archivo, $FechaEnviado, null, $IdUsuario);
                $resultado = $gestor->AltaMensajeMediador(null, $IdChat, $Contenido === '' ? 'Archivo enviado' : $Contenido, $FechaEnviado, null, $IdUsuario);
                break;
        }

        if (substr($resultado, 0, 2) == 'OK') {
            $IdMensaje = substr($resultado, 2);
        } else {
            return ['Error' => $resultado];
        }

        // Send a request
        $result = @file_get_contents($url, false, $options);

        $respuesta = json_decode($result, true);

        if (!$respuesta['sent']) {
            return ['Error' => $respuesta['message']];
        }

        $IdExternoMensaje = $respuesta['id'];

        switch ($tipo) {
            case 'caso':
                $resultadoSet = $gestor->SetIdExternoMensaje($IdMensaje, $IdExternoMensaje);
                break;

            case 'contacto':
                $resultadoSet = $gestor->SetIdExternoMensajeContacto($IdMensaje, $IdExternoMensaje);
                break;

            case 'mediador':
                $resultadoSet = $gestor->SetIdExternoMensajeMediador($IdMensaje, $IdExternoMensaje);
                break;
        }

        if (substr($resultadoSet, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdMensaje' => $IdMensaje
            ];
        } else {
            return ['Error' => $resultadoSet];
        }
    }

    /**
     * Obtiene el ChatId desde ChatApi de un nuevo chat y lo da
     * de alta en la base de datos
     * Retorna IdChat de la base de datos
     *
     * @param Telefono Telefono de la persona del chat
     * @param IdCaso Id del caso sobre el cual se inicia el chat
     * @param IdPersona Id de la persona propietaria del telefono
     */
    public function nuevoChat($Telefono, $IdCaso, $IdPersona, $IdMediador = null)
    {
        $phone = $this->readChatUrl($Telefono);

        if ($phone === 'not exists') {
            return ['Error' => 'Number not exists'];
        }

        $IdExternoChat = $phone.'@c.us';

        $respuesta = '';

        $gestor = new GestorChatApi;

        if (empty($IdMediador)) {
            $resultado = $gestor->AltaChat($IdExternoChat, $IdCaso, $IdPersona, $Telefono);
        } else {
            $resultado = $gestor->AltaChatMediador($IdExternoChat, $IdMediador, $Telefono);

            if (substr($resultado, 0, 2) !== 'OK') {
                return ['Error' => $resultado];
            }

            $urlHistorial = $this->messageHistoryUrl($IdExternoChat);

            $result = @file_get_contents($urlHistorial);

            $respuesta = json_decode($result, true);

            if (!empty($respuesta['messages'])) {
                $Mensajes = array_reverse($respuesta['messages']);

                $resultAltaMensaje = '';

                foreach ($Mensajes as $Mensaje) {
                    $IdExternoMensaje = $Mensaje['id'];
                    $Contenido = $Mensaje['body'];
                    $FechaEnviado = date("Y-m-d H:i:s", $Mensaje['time']);
                    $FechaRecibido = date("Y-m-d H:i:s", $Mensaje['time']);
                    $IdUsuario = $Mensaje['fromMe'] ? 1 : null; // Se guardan los mensajes que son enviados desde el celular con IdUsuario = 1
                    $Tipo = $Mensaje['type'];

                    $IdChat = substr($resultado, 2);

                    $resultAltaMensaje = $gestor->AltaMensajeMediador($IdExternoMensaje, $IdChat, $Contenido, $FechaEnviado, $FechaRecibido, $IdUsuario);

                    /*
                    if ($Tipo !== 'chat' && $Tipo !== 'call_log' && $Tipo !== 'ptt') {
                        $content = file_get_contents($Contenido, false);

                        $name = $this->generateRandomString(32);

                        $format = array_reverse(explode('.', $Contenido))[0];

                        Yii::$app->bucket->escribirArchivo("estudios/$name.$format", $content);

                        $all_mimes = '{"png":["image\/png","image\/x-png"],"bmp":["image\/bmp","image\/x-bmp","image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp","image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp","application\/x-win-bitmap"],"gif":["image\/gif"],"jpeg":["image\/jpeg","image\/pjpeg"],"xspf":["application\/xspf+xml"],"vlc":["application\/videolan"],"wmv":["video\/x-ms-wmv","video\/x-ms-asf"],"au":["audio\/x-au"],"ac3":["audio\/ac3"],"flac":["audio\/x-flac"],"ogg":["audio\/ogg","video\/ogg","application\/ogg"],"oga":["audio\/ogg","video\/ogg","application\/ogg"],"kmz":["application\/vnd.google-earth.kmz"],"kml":["application\/vnd.google-earth.kml+xml"],"rtx":["text\/richtext"],"rtf":["text\/rtf"],"jar":["application\/java-archive","application\/x-java-application","application\/x-jar"],"zip":["application\/x-zip","application\/zip","application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],"7zip":["application\/x-compressed"],"xml":["application\/xml","text\/xml"],"svg":["image\/svg+xml"],"3g2":["video\/3gpp2"],"3gp":["video\/3gp","video\/3gpp"],"mp4":["video\/mp4"],"m4a":["audio\/x-m4a"],"f4v":["video\/x-f4v"],"flv":["video\/x-flv"],"webm":["video\/webm"],"aac":["audio\/x-acc"],"m4u":["application\/vnd.mpegurl"],"pdf":["application\/pdf","application\/octet-stream"],"pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],"ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office","application\/msword"],"docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],"xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],"xl":["application\/excel"],"xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel","application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],"xsl":["text\/xsl"],"mpeg":["video\/mpeg"],"mov":["video\/quicktime"],"avi":["video\/x-msvideo","video\/msvideo","video\/avi","application\/x-troff-msvideo"],"movie":["video\/x-sgi-movie"],"log":["text\/x-log"],"txt":["text\/plain"],"css":["text\/css"],"html":["text\/html"],"wav":["audio\/x-wav","audio\/wave","audio\/wav"],"xhtml":["application\/xhtml+xml"],"tar":["application\/x-tar"],"tgz":["application\/x-gzip-compressed"],"psd":["application\/x-photoshop","image\/vnd.adobe.photoshop"],"exe":["application\/x-msdownload"],"js":["application\/x-javascript"],"mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],"rar":["application\/x-rar","application\/rar","application\/x-rar-compressed"],"gzip":["application\/x-gzip"],"hqx":["application\/mac-binhex40","application\/mac-binhex","application\/x-binhex40","application\/x-mac-binhex40"],"cpt":["application\/mac-compactpro"],"bin":["application\/macbinary","application\/mac-binary","application\/x-binary","application\/x-macbinary"],"oda":["application\/oda"],"ai":["application\/postscript"],"smil":["application\/smil"],"mif":["application\/vnd.mif"],"wbxml":["application\/wbxml"],"wmlc":["application\/wmlc"],"dcr":["application\/x-director"],"dvi":["application\/x-dvi"],"gtar":["application\/x-gtar"],"php":["application\/x-httpd-php","application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],"swf":["application\/x-shockwave-flash"],"sit":["application\/x-stuffit"],"z":["application\/x-compress"],"mid":["audio\/midi"],"aif":["audio\/x-aiff","audio\/aiff"],"ram":["audio\/x-pn-realaudio"],"rpm":["audio\/x-pn-realaudio-plugin"],"ra":["audio\/x-realaudio"],"rv":["video\/vnd.rn-realvideo"],"jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],"tiff":["image\/tiff"],"eml":["message\/rfc822"],"pem":["application\/x-x509-user-cert","application\/x-pem-file"],"p10":["application\/x-pkcs10","application\/pkcs10"],"p12":["application\/x-pkcs12"],"p7a":["application\/x-pkcs7-signature"],"p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],"p7r":["application\/x-pkcs7-certreqresp"],"p7s":["application\/pkcs7-signature"],"crt":["application\/x-x509-ca-cert","application\/pkix-cert"],"crl":["application\/pkix-crl","application\/pkcs-crl"],"pgp":["application\/pgp"],"gpg":["application\/gpg-keys"],"rsa":["application\/x-pkcs7"],"ics":["text\/calendar"],"zsh":["text\/x-scriptzsh"],"cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],"wma":["audio\/x-ms-wma"],"vcf":["text\/x-vcard"],"srt":["text\/srt"],"vtt":["text\/vtt"],"ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],"csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],"json":["application\/json","text\/json"]}';
                        $all_mimes = json_decode($all_mimes,true);

                        if (array_key_exists($format, $all_mimes)) {
                            $MimeType = $all_mimes[$format][0];

                            $Tipo = explode('/', $MimeType)[0] === 'application' ? 'O' : strtoupper($MimeType)[0];
                        } else {
                            $Tipo = 'O';
                        }

                        $IdCaso = $resultado['IdCaso'];

                        $caso = new Casos();
                        $caso->IdCaso = $IdCaso;

                        $Multimedia = array([
                            'URL' => $name . '.' . $format,
                            'Tipo' => $Tipo,
                            'OrigenMultimedia' => 'R'
                        ]);

                        $respuesta = $caso->AltaMultimedia($Multimedia);

                        if (substr($respuesta, 0, 2) !== 'OK') {
                            throw new Exception($result);
                        }
                    }
                    */
                }
                
                if (substr($resultAltaMensaje, 0, 2) === 'OK') {
                    $gestor->ModificarUltMsjLeidoMediador($IdChat, substr($resultAltaMensaje, 2));
                }
            }
        }
        

        switch (substr($resultado, 0, 2)) {
            case 'OK':
                return [
                    'Error' => null,
                    'IdChat' => substr($resultado, 2),
                    'IdExternoChat' => $IdExternoChat
                ];
                break;
            
            case 'EX':
                return [
                    'Error' => null,
                    'Caratula' => substr($resultado, 2),
                    'IdExternoChat' => $IdExternoChat
                ];
                break;
                
            default:
                return ['Error' => $resultado, 'p' => [$IdExternoChat, $IdCaso, $IdPersona, $Telefono], 'respuesta' => $respuesta];
                break;
        }
    }

    /**
     * Obtiene el ChatId desde ChatApi de un nuevo chat y lo da
     * de alta en la base de datos
     * Retorna IdChat de la base de datos
     *
     * @param Telefono Telefono de la persona del chat
     * @param IdContacto
     */
    public function nuevoChatContacto($Telefono, $IdContacto)
    {
        $phone = $this->readChatUrl($Telefono);

        if ($phone === 'not exists') {
            return ['Error' => 'Number not exists'];
        }

        $IdExternoChat = $phone.'@c.us';

        $gestor = new GestorChatApi;
        $resultado = $gestor->AltaChatContacto($IdExternoChat, $IdContacto, $Telefono);

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null,
                'IdChat' => substr($resultado, 2),
                'IdExternoChat' => $IdExternoChat
            ];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function mensajeComun($Telefono, $Contenido) {
        $resultado = str_replace(' ', '', str_replace('-', '', $Telefono)); // Quito espacios y guiones que pudiera tener el numero de telefono

        $phone = '';

        // Verifico si tiene codigo de pais de Argentina
        if (substr($resultado, 0, 2) == '54') {
            $phone = $resultado;
        } elseif (substr($resultado, 0, 1) == '+') {
            $phone = substr($resultado, 1);
        } else {
            $phone = '54' . $resultado;
        }

        // Agrego el 9 del numero de WhatsApp
        if (substr($phone, 2, 1) != '9') {
            $phone = '549' . substr($phone, 2);
        }

        // Verifico si el numero existe
        if (strlen($phone) < 10) {
            return [
                'Error' => 'No existe el numero ingresado.'
            ];
        } else {
            $check = $this->checkPhone($phone);

            if ($check == 'not exists') {
                return [
                    'Error' => 'No existe el numero ingresado.'
                ];
            } else {
                $data = [
                    'phone' => $phone, // Receivers phone
                    'body'  => $Contenido, // Message
                ];
                $json = json_encode($data); // Encode data to JSON
                // URL for request POST /message
                $url = $this->sendUrl();
                // Make a POST request
                $options = stream_context_create(['http' => [
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/json',
                        'content' => $json
                    ]
                ]);

                // Send a request
                $result = @file_get_contents($url, false, $options);

                $respuesta = json_decode($result, true);

                if (true) {
                    return [
                        'Error' => $respuesta['message'],
                        'r' => $respuesta
                    ];
                }
                return [
                    'Error' => null
                ];
            }
        }
    }

    public function mensajeGlobal($IdEstudio, $Contenido)
    {
        $Estudio = new Estudios;
        $Estudio->IdEstudio = $IdEstudio;

        $usuarios = $Estudio->BuscarUsuarios();

        $errores = array();

        if (empty($usuarios)) {
            return ['Error' => 'No existen usuarios en este estudio.'];
        } else {
            foreach ($usuarios as $usuario) {
                    if (isset($usuario['Telefono'])) {
                    $Telefono = $usuario['Telefono'];
                    $IdUsuario = $usuario['IdUsuario'];

                    $resultado = str_replace(' ', '', str_replace('-', '', $Telefono)); // Quito espacios y guiones que pudiera tener el numero de telefono

                    // Verifico si tiene codigo de pais de Argentina
                    if (substr($resultado, 0, 2) == '54') {
                        $phone = $resultado;
                    } elseif (substr($resultado, 0, 1) == '+') {
                        $phone = substr($resultado, 1);
                    } else {
                        $phone = '54' . $resultado;
                    }

                    // Agrego el 9 del numero de WhatsApp
                    if (substr($phone, 2, 1) != '9') {
                        $phone = '549' . substr($phone, 2);
                    }

                    // Verifico si el numero existe
                    if (strlen($phone) < 10) {
                        $errores[$IdUsuario] = 'No existe el numero ingresado.';
                    } else {
                        $check = $this->checkPhone($phone);

                        if ($check == 'not exists') {
                            $errores[$IdUsuario] = 'No existe el numero ingresado.';
                        } else {
                            $data = [
                                'phone' => $phone, // Receivers phone
                                'body'  => $Contenido, // Message
                            ];
                            $json = json_encode($data); // Encode data to JSON
                            // URL for request POST /message
                            $url = $this->sendUrl();
                            // Make a POST request
                            $options = stream_context_create(['http' => [
                                    'method'  => 'POST',
                                    'header'  => 'Content-type: application/json',
                                    'content' => $json
                                ]
                            ]);

                            // Send a request
                            $result = @file_get_contents($url, false, $options);

                            $respuesta = json_decode($result, true);

                            if (!$respuesta['sent']) {
                                $errores[$IdUsuario] = $respuesta['message'];
                            }
                        }
                    }
                }
            }
        }

        return $errores;
    }

    /**
     * Devulve la Url para enviar mensajes
     */
    public function sendUrl()
    {
        $url = "https://api.1msg.io/153725/message?token=67aqw7sghzkatk34";

        Yii::info($url, 'send');

        return $url;
    }

    /**
     * Devulve la Url para enviar archivos
     */
    public function sendFileUrl()
    {
        $url = "https://api.1msg.io/153725/sendFile?token=67aqw7sghzkatk34";

        Yii::info($url, 'send');

        return $url;
    }

    /**
     * Devulve la Url para pedir el historial de mensajes
     */
    public function messageHistoryUrl($chatId, $count = 20, $page = 0)
    {
        $url = "https://api.1msg.io/153725/messagesHistory?page={$page}&count={$count}&chatId={$chatId}&token=67aqw7sghzkatk34";

        Yii::info($url, 'messageHistory');

        return $url;
    }

    /**
     * Devuelva le Url para pedir los ultimos mensajes en el chat
     *
     * @param Numero Telefono del chat que se quiere ver (supone que son de argentina)
     */
    public function readChatUrl($Numero)
    {
        $resultado = str_replace(' ', '', str_replace('-', '', $Numero)); // Quito espacios y guiones que pudiera tener el numero de telefono

        // Verifico si tiene codigo de pais de Argentina
        if (substr($resultado, 0, 2) == '54') {
            $phone = $resultado;
        } elseif (substr($resultado, 0, 1) == '+') {
            $phone = substr($resultado, 1);
        } else {
            $phone = '54' . $resultado;
        }

        // Agrego el 9 del numero de WhatsApp
        if (substr($phone, 2, 1) != '9') {
            $phone = '549' . substr($phone, 2);
        }

        // Verifico si el numero existe
        if (strlen($phone) < 10) {
            return 'not exists';
        }

        $check = $this->checkPhone($phone);

        if ($check == 'not exists') {
            return $check;
        }

        $url = "https://api.1msg.io/153725/readChat?token=67aqw7sghzkatk34&phone={$phone}";

        Yii::info($url, 'readChat');

        return $phone;
    }

    public function checkPhone($phone)
    {
        $url = "https://api.1msg.io/153725/checkPhone?token=67aqw7sghzkatk34&phone={$phone}";

        Yii::info($url, 'checkPhone');

        $result = @file_get_contents($url);

        $respuesta = json_decode($result, true);

        // return $respuesta['result'];

        return '';
    }
}
