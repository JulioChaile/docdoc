<?php

namespace common\components;

use GuzzleHttp\Client;
use yii\web\HttpException;
use Yii;

class GmailHelper
{
    public static function send($sender, $to, $subject, $message_text)
    {
        $client = new Client([
            'base_uri' => 'https://io.docdoc.com.ar'
        ]);
        
        try {
            $response = $client->request('POST', '/api/gmail', [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'sender' => $sender,
                    'to' => $to,
                    'subject' => $subject,
                    'message_text' => $message_text,
                    'token' => 'AIzaSyDjyytt5GAgQoLohIeGaY5dJiHhoKu6ih0'
                ]
            ]);
            $jsonResp = json_decode($response->getBody(), true);
            if (array_key_exists('error', $jsonResp)) {
                Yii::error($jsonResp['error']);
                // throw new HttpException(500, 'Ocurrió un error al intentar enviar un correo a la dirección indicada.');
            }
            return $jsonResp;
        } catch (\Exception $e) {
            // throw new HttpException(503, $e->getMessage());
        }
    }
}
