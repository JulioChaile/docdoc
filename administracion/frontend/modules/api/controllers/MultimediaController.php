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
        $urls = [];
        $names = [];
        
        foreach ($_FILES as $name => $value) {
            $uploaded = UploadedFile::getInstancesByName($name)[0];

            $file = $this->generateRandomString(32) . '.' . $uploaded->extension;

            /*
            if (empty($uploaded->tempName)) {
                return ['files' => $_FILES];
            }
            */

            $content = file_get_contents($uploaded->tempName, 'r');

            Yii::$app->bucket->escribirArchivo("estudios/$file", $content);

            $urls[] = $file;
            $names[] = $name;
        }

        return [
            'Urls' => $urls,
            'Names' => $names
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
