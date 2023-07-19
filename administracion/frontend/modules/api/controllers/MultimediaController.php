<?php
namespace frontend\modules\api\controllers;

use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\web\UploadedFile;
use common\components\EmailHelper;

class MultimediaController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bearerAuth' => [
                    'class' => OptionalBearerAuth::className(),
                    'except' => ['index', 'options'],
                    'actionsClient' => ['create']
                ],
            ]
        );
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
    
    public function actionCreate()
    {
        Yii::info($_FILES);

        $urls = [];
        $names = [];

        $upload_max_filesize = ini_get('upload_max_filesize');
        $post_max_size = ini_get('post_max_size');
        Yii::info($upload_max_filesize);
        Yii::info($post_max_size);
        
        foreach ($_FILES as $name => $value) {
            if ($_FILES[$name]['error'] !== UPLOAD_ERR_OK) {
                $error = $_FILES[$name]['error'];
                // Manejar el error de subida del archivo, por ejemplo:
                switch ($error) {
                    case UPLOAD_ERR_INI_SIZE:
                        // El archivo excede la directiva upload_max_filesize en php.ini
                        Yii::info('El archivo excede el tamaño máximo permitido.');
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        // El archivo excede el tamaño máximo especificado en el formulario
                        Yii::info('El archivo excede el tamaño máximo permitido en el formulario.');
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        // El archivo fue subido parcialmente
                        Yii::info('El archivo fue subido parcialmente. Por favor, inténtalo nuevamente.');
                        break;
                    // Otros casos de error según la documentación de PHP: https://www.php.net/manual/en/features.file-upload.errors.php
                    default:
                        Yii::info('Ocurrió un error al subir el archivo. Por favor, inténtalo nuevamente.');
                        break;
                }
            }

            $uploaded = UploadedFile::getInstancesByName($name)[0];

            $file = $this->generateRandomString(32) . '.' . $uploaded->extension;

            if (empty($uploaded->tempName)) {
                return [
                    'files' => $_FILES,
                    'value' => $value,
                    'name' => $name,
                    'uploaded' => $uploaded,
                    'uploadMaxSize' => $upload_max_filesize,
                    'postMaxSize' => $post_max_size
                ];
            }

            $content = file_get_contents($uploaded->tempName, 'r');

            Yii::$app->bucket->escribirArchivo("estudios/$file", $content);

            $urls[] = $file;
            $names[] = $name;
        }

        return [
            'files' => $_FILES,
            'upload_max_filesize' => $upload_max_filesize,
            'post_max_size' => $post_max_size,
            'Urls' => $urls,
            'Names' => $names
        ];
    }
    
    public function actionLink()
    {
        $extension = Yii::$app->request->post('extension');

        $file = $this->generateRandomString(32) . '.' . $extension;

        return [
            'url' => Yii::$app->bucket->getLink("estudios/$file"),
            'file' => $file
        ];
    }
    
    public function actionSubirImg()
    {
        $img = Yii::$app->request->post('img');
        $IdCaso = Yii::$app->request->post('IdCaso');

        $file = $this->generateRandomString(32) . '.' . 'png';

        $content = @file_get_contents($img, false);

        Yii::$app->bucket->escribirArchivo("estudios/$file", $content);

        $sql = 'INSERT INTO FotosCaso (FotoCaso, IdCaso) VALUES ("' . $file .'",' . $IdCaso . ')';

        $query = Yii::$app->db->createCommand($sql);

        $query->execute();

        return [
            'name' => $file
        ];
    }
    
    public function actionReemplazarImg()
    {
        $img = Yii::$app->request->post('img');
        $IdMultimedia = Yii::$app->request->post('IdMultimedia');

        $file = $this->generateRandomString(32) . '.' . 'png';

        $content = @file_get_contents($img, false);

        Yii::$app->bucket->escribirArchivo("estudios/$file", $content);

        $sql = 'UPDATE Multimedia SET URL = "' . $file . '" WHERE IdMultimedia = ' . $IdMultimedia;

        $query = Yii::$app->db->createCommand($sql);

        $query->execute();

        return [
            'name' => $file
        ];
    }
    
    public function actionIndex()
    {
        $download = Yii::$app->request->get('download');
        $file = Yii::$app->request->get('file');
        $name = Yii::$app->request->get('name');

        if (!isset($file)) {
            throw new HttpException('404', 'File not found');
        }

        $fileContent = Yii::$app->bucket->leerArchivo("estudios/$file");

        if (!empty($name)) {
            $file = $name . '.' . array_reverse(explode('.', $file))[0];
        }

        if ($download === 'true') {
            return Yii::$app->response->sendContentAsFile($fileContent, $file);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;

        return $fileContent;
    }

    public function actionDelete($id)
    {
        $file = Yii::$app->request->getBodyParam('file');
        // TODO: controlar que el usuario tenga permisos
        Yii::$app->bucket->borrarArchivo("estudios/$file");

        return ['Error' => null];
    }
}
