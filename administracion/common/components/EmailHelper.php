<?php

namespace common\components;

use Http\Adapter\Guzzle6\Client;
use Mailgun\Mailgun;
use Yii;
use yii\web\HttpException;

class EmailHelper
{
    const DOMAIN = 'mg.docdoc.com.ar';
    const API_KEY = '2fe1bf22e2965cf934275a207e0ca51b-4a62b8e8-a84d7346';

    /**
     * Envia un email al destinatario usando la configuracion establecida en EmailHelper.
     *
     * Ejemplo: enviarEmail('Usuario <usuario@ejemplo.com>', 'Bienvenido a $EMPRESA',
     *          'bienvenida', [
     *                  'user' => 'Usuario'
     *          ]);
     *
     * @param string $from Emisor del email
     * @param string $dest Destinatario del email
     * @param string $asunto Asunto del email
     * @param string $view Vista a renderizar en @common/email
     * @param array $params Parametros que se le pasaran a la vista
     * @param array $attachment Opcional: Ruta del archivo adjunto que se desea enviar
     */
    public function enviarEmail(string $from, string $dest, string $asunto, string $view, array $params, string $attachment = null)
    {
        return self::enviarGmail($from, $dest, $asunto, $view, $params, $attachment);
    }

    public static function enviarGmail(string $from, string $dest, string $asunto, string $view, array $params, string $attachment = null)
    {
        $message_text = Yii::$app->controller->renderPartial('@common/mail/' . $view, $params);
        return GmailHelper::send($from, $dest, $asunto, $message_text);
    }

    public static function enviarYii(string $from, string $dest, string $asunto, string $view, array $params, string $attachment = null)
    {
        $exploded_from = explode('<', $from);
        $real_from = $exploded_from[1];
        $real_from = substr($real_from, 0, strlen($real_from) - 1);
        $mail = Yii::$app->mail->compose()
            ->setFrom([$real_from => $exploded_from[0]])
            ->setTo($dest)
            ->setSubject($asunto)
            ->setHtmlBody(Yii::$app->controller->renderPartial('@common/mail/' . $view, $params));
        if (isset($attachment)) {
            $mail->attach($attachment);
        }
        return $mail->send();
    }

    public static function enviarMailgun(string $from, string $dest, string $asunto, string $view, array $params, string $attachment = null)
    {
        $client = new Client();
        $mailgun = new Mailgun(EmailHelper::API_KEY, $client);
        $domain = EmailHelper::DOMAIN;
        $msg = [
            'from' => $from,
            'to' => $dest,
            'subject' => $asunto,
            'html' => Yii::$app->controller->renderPartial('@common/mail/' . $view, $params)
        ];
        if ($attachment != null) {
            $result = $mailgun->sendMessage($domain, $msg, [ 'attachment' => [$attachment] ]);
        } else {
            $result = $mailgun->sendMessage($domain, $msg);
        }
        $responseCode = $result->http_response_code;
        if ($responseCode != '200') {
            throw new HttpException($responseCode, 'Ocurrió un error al intentar enviar un correo a la dirección indicada.');
        }
    }
}
