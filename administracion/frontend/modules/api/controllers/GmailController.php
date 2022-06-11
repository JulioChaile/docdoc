<?php
namespace frontend\modules\api\controllers;

use Yii;
use GuzzleHttp\Client;
use yii\web\HttpException;

class GmailController extends BaseController
{
    /**
     * @api {post} /send Enviar Email
     * @apiName Send
     * @apiGroup Gmail
     * 
     * @apiParam {String} token Token de autenticación.
     * @apiParam {String} sender
     * @apiParam {String} to
     * @apiParam {String} subject
     * @apiParam {String} message_text Cuerpo del mensaje.
     *
     * @apiSuccess {String} id Id de mensaje
     * @apiSuccess {[]String} labelIds Ids de labels
     * @apiSuccess {String} threadId Id de Thread
     * @apiError {String} error Mensaje de error.
     */
    public function actionCreate()
    {
        $client = new Client([
            'base_uri' => 'http://localhost:8056'
        ]);

        try {
            $response = $client->request('POST', '/send', [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => Yii::$app->request->post()
            ]);
            $jsonResp = json_decode($response->getBody(), true);
            return $jsonResp;
        } catch (\Exception $e) {
            throw new HttpException(500, "Error gmail-api");
        }
    }
}
