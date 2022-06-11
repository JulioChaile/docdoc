<?php

namespace frontend\modules\api\controllers;

use common\models\Casos;
use common\components\FPDF;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use common\components\EmailHelper;

class MultimediaCasoController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bearerAuth' => [
                    'class' => OptionalBearerAuth::className(),
                    'except' => ['generar-pdf', 'options'],
                    'actionsClient' => ['index', 'alta']
                ],
            ]
        );
    }
    
    public function actionIndex()
    {
        $IdCaso = Yii::$app->request->get('IdCaso');

        $caso = new Casos();
        $caso->IdCaso = $IdCaso;

        return $caso->ListarMultimedia();
    }

    public function actionAlta()
    {
        $IdCaso = Yii::$app->request->post('IdCaso');
        $Multimedia = Yii::$app->request->post('Multimedia');

        $caso = new Casos();
        $caso->IdCaso = $IdCaso;

        $resultado = $caso->AltaMultimedia($Multimedia);

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return [
                'Error' => $resultado
            ];
        }
    }

    public function actionEditarNombre()
    {
        $IdCaso = Yii::$app->request->post('IdCaso');
        $Nombre = Yii::$app->request->post('Nombre');
        $IdMultimedia = Yii::$app->request->post('IdMultimedia');

        $caso = new Casos();
        $caso->IdCaso = $IdCaso;

        $resultado = $caso->EditarNombreMultimedia($IdMultimedia, $Nombre);

        if (substr($resultado, 0, 2) == 'OK') {
            return [
                'Error' => null
            ];
        } else {
            return [
                'Error' => $resultado
            ];
        }
    }

    public function actionEliminar()
    {
        $IdCaso = Yii::$app->request->post('IdCaso');
        $Multimedia = json_decode(Yii::$app->request->post('Multimedia'), true);

        $caso = new Casos();
        $caso->IdCaso = $IdCaso;

        $resultado = $caso->EliminarMultimedia($Multimedia);

        if (substr($resultado, 0, 2) == 'OK') {
            foreach ($Multimedia as $m) {
                Yii::$app->bucket->borrarArchivo("estudios/" . $m['URL']);
            }
            
            return [
                'Error' => null
            ];
        } else {
            return [
                'Error' => $resultado
            ];
        }
    }

    public function actionEnviarEmail()
    {
        $Email = Yii::$app->request->post('Email');
        $Asunto = Yii::$app->request->post('Asunto');
        $Contenido = Yii::$app->request->post('Contenido');
        $Multimedia = json_decode(Yii::$app->request->post('Multimedia'), true);
        $ContenidoPDF = Yii::$app->request->post('ContenidoPDF');

        $links = array();
        $MultimediaPDF = array();
        $check = false;

        foreach ($Multimedia as $m) {
            if ($m['Tipo'] === 'I') {
                $check = true;
                $MultimediaPDF[] = $m;
            } else {
                $links[] = $m['URL'];
            }
        }

        if ($check) {
            $content = $this->generarPDF($MultimediaPDF, $ContenidoPDF);

            $name = $this->generateRandomString(32);

            Yii::$app->bucket->escribirArchivo("estudios/$name.pdf", $content);

            $links[] = $name . '.pdf';
        }

        EmailHelper::enviarEmail(
            'DocDoc <contacto@docdoc.com.ar>',
            $Email,
            $Asunto,
            'enviar-archivo',
            [
                'contenido' => $Contenido,
                'links' => $links
            ]
        );

        return ['Error' => null];
    }

    public function actionGenerarPdf()
    {
        $Multimedia = json_decode(Yii::$app->request->get('Multimedia'), true);
        $ContenidoPDF = Yii::$app->request->get('ContenidoPDF');

        $content = $this->generarPDF($Multimedia, $ContenidoPDF);

        return Yii::$app->response->sendContentAsFile($content, 'doc.pdf');
    }

    public function actionMover()
    {
        $ids = Yii::$app->request->post('ids');
        $IdCarpetaCaso = Yii::$app->request->post('IdCarpetaCaso');

        $ids = json_decode($ids, true);
        $errores = array();

        $caso = new Casos();

        foreach ($ids as $IdMultimedia) {
            $resultado = $caso->MoverMultimedia($IdMultimedia, $IdCarpetaCaso);
            if ($resultado !== 'OK') {
                $errores[] = $IdMultimedia;
            }
        }

        return $errores;
    }

    public function actionDocumento()
    {
        $IdCaso = Yii::$app->request->post('IdCaso');
        $Doc = Yii::$app->request->post('Doc');

        $content = @file_get_contents($Doc, false);

        $name = $this->generateRandomString(32);

        Yii::$app->bucket->escribirArchivo("estudios/$name.doc", $content);

        $caso = new Casos();
        $caso->IdCaso = $IdCaso;

        $Objeto = array([
            'URL' => $name . '.doc',
            'Nombre' => $name,
            'Tipo' => 'D',
            'OrigenMultimedia' => 'C'
        ]);

        $respuesta = $caso->AltaMultimedia($Objeto);

        if (substr($respuesta, 0, 2) !== 'OK') {
            return [
                'Error' => $respuesta
            ];
        } else {
            return [
                'Error' => null
            ];
        }
    }

    public function actionDescargaMultiple()
    {
        $Archivos = Yii::$app->request->post('archivos');

        $zipFilename = md5("file-" . time()) . ".zip";

        $zipPath = Yii::$app->basePath . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . $zipFilename;

        $zip = new \ZipArchive();

        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $ruta = "https://io.docdoc.com.ar/api/multimedia?file=";

        foreach ($Archivos as $a) {
            $zip->addFile($ruta . $a, $a);
        }

        $zip->close();

        Yii::$app->response->sendFile($zipPath);
    }

    public function actionMasivo() {

        $array = Yii::$app->request->post('array');

        /*
        $array = '[
            {
              "IdCaso": 394,
              "Contenido": "https://s3.eu-central-1.wasabisys.com/incoming-chat-api/2020/10/19/153725/2e730526-c698-42fe-90d9-bf7d4bcfdb39.jpeg"
            },
            {
              "IdCaso": 394,
              "Contenido": "https://s3.eu-central-1.wasabisys.com/incoming-chat-api/2020/10/19/153725/eca2c5ec-b16c-45aa-b933-eee7c0ed00c6.jpeg"
            },
            {
              "IdCaso": 423,
              "Contenido": "https://s3.eu-central-1.wasabisys.com/incoming-chat-api/2020/10/21/153725/6d293f24-da25-4012-9b23-ad74cf9c1425.oga"
            }
           ]';
        */

        $Multimedia = json_decode($array, true);

        $errores = array();

        foreach ($Multimedia as $m) {
            $content = @file_get_contents($m['Contenido'], false);

            $name = $this->generateRandomString(32);

            $format = array_reverse(explode('.', $m['Contenido']))[0];

            Yii::$app->bucket->escribirArchivo("estudios/$name.$format", $content);

            $all_mimes = '{"png":["image\/png","image\/x-png"],"bmp":["image\/bmp","image\/x-bmp","image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp","image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp","application\/x-win-bitmap"],"gif":["image\/gif"],"jpeg":["image\/jpeg","image\/pjpeg"],"xspf":["application\/xspf+xml"],"vlc":["application\/videolan"],"wmv":["video\/x-ms-wmv","video\/x-ms-asf"],"au":["audio\/x-au"],"ac3":["audio\/ac3"],"flac":["audio\/x-flac"],"ogg":["audio\/ogg","video\/ogg","application\/ogg"],"kmz":["application\/vnd.google-earth.kmz"],"kml":["application\/vnd.google-earth.kml+xml"],"rtx":["text\/richtext"],"rtf":["text\/rtf"],"jar":["application\/java-archive","application\/x-java-application","application\/x-jar"],"zip":["application\/x-zip","application\/zip","application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],"7zip":["application\/x-compressed"],"xml":["application\/xml","text\/xml"],"svg":["image\/svg+xml"],"3g2":["video\/3gpp2"],"3gp":["video\/3gp","video\/3gpp"],"mp4":["video\/mp4"],"m4a":["audio\/x-m4a"],"f4v":["video\/x-f4v"],"flv":["video\/x-flv"],"webm":["video\/webm"],"aac":["audio\/x-acc"],"m4u":["application\/vnd.mpegurl"],"pdf":["application\/pdf","application\/octet-stream"],"pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],"ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office","application\/msword"],"docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],"xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],"xl":["application\/excel"],"xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel","application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],"xsl":["text\/xsl"],"mpeg":["video\/mpeg"],"mov":["video\/quicktime"],"avi":["video\/x-msvideo","video\/msvideo","video\/avi","application\/x-troff-msvideo"],"movie":["video\/x-sgi-movie"],"log":["text\/x-log"],"txt":["text\/plain"],"css":["text\/css"],"html":["text\/html"],"wav":["audio\/x-wav","audio\/wave","audio\/wav"],"xhtml":["application\/xhtml+xml"],"tar":["application\/x-tar"],"tgz":["application\/x-gzip-compressed"],"psd":["application\/x-photoshop","image\/vnd.adobe.photoshop"],"exe":["application\/x-msdownload"],"js":["application\/x-javascript"],"mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],"rar":["application\/x-rar","application\/rar","application\/x-rar-compressed"],"gzip":["application\/x-gzip"],"hqx":["application\/mac-binhex40","application\/mac-binhex","application\/x-binhex40","application\/x-mac-binhex40"],"cpt":["application\/mac-compactpro"],"bin":["application\/macbinary","application\/mac-binary","application\/x-binary","application\/x-macbinary"],"oda":["application\/oda"],"ai":["application\/postscript"],"smil":["application\/smil"],"mif":["application\/vnd.mif"],"wbxml":["application\/wbxml"],"wmlc":["application\/wmlc"],"dcr":["application\/x-director"],"dvi":["application\/x-dvi"],"gtar":["application\/x-gtar"],"php":["application\/x-httpd-php","application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],"swf":["application\/x-shockwave-flash"],"sit":["application\/x-stuffit"],"z":["application\/x-compress"],"mid":["audio\/midi"],"aif":["audio\/x-aiff","audio\/aiff"],"ram":["audio\/x-pn-realaudio"],"rpm":["audio\/x-pn-realaudio-plugin"],"ra":["audio\/x-realaudio"],"rv":["video\/vnd.rn-realvideo"],"jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],"tiff":["image\/tiff"],"eml":["message\/rfc822"],"pem":["application\/x-x509-user-cert","application\/x-pem-file"],"p10":["application\/x-pkcs10","application\/pkcs10"],"p12":["application\/x-pkcs12"],"p7a":["application\/x-pkcs7-signature"],"p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],"p7r":["application\/x-pkcs7-certreqresp"],"p7s":["application\/pkcs7-signature"],"crt":["application\/x-x509-ca-cert","application\/pkix-cert"],"crl":["application\/pkix-crl","application\/pkcs-crl"],"pgp":["application\/pgp"],"gpg":["application\/gpg-keys"],"rsa":["application\/x-pkcs7"],"ics":["text\/calendar"],"zsh":["text\/x-scriptzsh"],"cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],"wma":["audio\/x-ms-wma"],"vcf":["text\/x-vcard"],"srt":["text\/srt"],"vtt":["text\/vtt"],"ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],"csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],"json":["application\/json","text\/json"]}';
            $all_mimes = json_decode($all_mimes,true);

            if (array_key_exists($format, $all_mimes)) {
                $MimeType = $all_mimes[$format][0];

                $Tipo = explode('/', $MimeType)[0] === 'application' ? 'O' : strtoupper($MimeType)[0];
            } else {
                $Tipo = 'O';
            }

            $IdCaso = $m['IdCaso'];

            $caso = new Casos();
            $caso->IdCaso = $IdCaso;

            $Objeto = array([
                'URL' => $name . '.' . $format,
                'Tipo' => $Tipo,
                'OrigenMultimedia' => 'R'
            ]);

            $respuesta = $caso->AltaMultimedia($Objeto);

            if (substr($respuesta, 0, 2) !== 'OK') {
                $errores[$IdCaso] = $respuesta;
            }
        }

        return empty($errores) ? ['Error' => null] : $errores;
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

    private function generarPDF($Multimedia, $ContenidoPDF = '')
    {
        $pdf = new FPDF('P', 'cm');
        $pdf->AddPage();
        $pdf->SetFont('Arial','',14);
        $wPage = $pdf->GetPageWidth();
        $hPage = $pdf->GetPageHeight();

        if (!empty($ContenidoPDF)) {
            $ContenidoPDF = str_replace('<br>', "\n", $ContenidoPDF);
            $pdf->MultiCell(0, .5, $ContenidoPDF);
            $pdf->Ln(2);
        }

        $pdf->SetAutoPageBreak(true, 0);

        foreach ($Multimedia as $m) {
            $type = array_reverse(explode('.', $m['URL']))[0];
            $info = array();

            if ($type === 'jpg' || $type === 'jpeg') {
                $info = $pdf->_parsejpg('https://io.docdoc.com.ar/api/multimedia?file=' . $m['URL']);
            } else {
                $info = $pdf->_parsepng('https://io.docdoc.com.ar/api/multimedia?file=' . $m['URL']);
            }
                
            $wImage = $wPage <= $info['w']/38
                ? $wPage - 1
                : 0;

            $hImage = $hPage - 2 <= $info['h']/38
                ? $hPage - 3
                : 0;

            if (array_key_exists('Titulo', $m) && !empty($m['Titulo'])) {
                $pdf->SetFont('','B',16);
                $pdf->Cell(0, 0, $m['Titulo'], 0, 1);
                $pdf->Ln(1);
            }

            if (array_key_exists('Descripcion', $m) && !empty($m['Descripcion'])) {
                $m['Descripcion'] = str_replace('<br>', "\n", $m['Descripcion']);
                $pdf->SetFont('','',14);
                $pdf->MultiCell(0, .5, $m['Descripcion'], 0, 1);
                $pdf->Ln(1);
            }

            $pdf->Image('https://io.docdoc.com.ar/api/multimedia?file=' . $m['URL'], 0, null, $wImage, $hImage, $type);
            $pdf->Ln(1);
        }

        return $pdf->Output('S');
    }
}
