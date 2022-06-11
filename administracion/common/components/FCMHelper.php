<?php

namespace common\components;

use GuzzleHttp\Client;
use common\models\Estudios;

class FCMHelper
{
    const API_KEY_APP = 'AAAAdWUed5o:APA91bG3FbMcZ8-FjX97mGDtPvC63TXN4CAkIzWUSOquAnG3Adh9Wc7229nRM8YmxUymvV15DG5AHVmBCOEmGIRE1AOr_nz5_16sqx7GyNEEov7kVyGtEFU01aUZo_SJcv2McaxMtxHp';
    const API_KEY_CLIENTES = 'AAAAZLQztuU:APA91bGsVvTPRgdvaJn4R4_JmxfOhYcSwGR55JD49cJAsMKAz6klWLDfsWA0pMU8NpyLLek8LVJNKZnM8RdIJKfdLYsD8MKqQdgVoVNMJGlrLB6BUkgW4rHZBjKJcYIxhl-RzI_7GV5y';

    public static function enviarNotificacionPush($notificacion, $to, $data = [], $toTipo = 'mult', $cliente = false)
    {
        $client = new Client();

        $json = array();

        if (!empty($notificacion)) {
            $notificacion['click_action'] = 'FCM_PLUGIN_ACTIVITY';
            $json['notification'] = $notificacion;
        }

        switch ($toTipo) {
            case 'mult':
                $json['registration_ids'] = $to;
                break;

            case 'topic':
                $json['to'] = "/topics/" . $to;
                break;

            case 'user':
                $json['to'] = $to;
                break;
        }

        if (!empty($data)) {
            $json['data'] = $data;
        }

        $api_key = $cliente
            ? FCMHelper::API_KEY_CLIENTES
            : FCMHelper::API_KEY_APP;

        $response = $client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'key=' . $api_key
            ],
            'json' => $json
        ]);

        return json_decode($response->getBody(), true);
    }

    public static function notificacionPorEstudio($IdEstudio, $notificacion, $data = [])
    {
        $Estudio = new Estudios;
        $Estudio->IdEstudio = $IdEstudio;

        $usuarios = $Estudio->BuscarUsuarios();
        $tokens = array();

        if (empty($usuarios)) {
            return ['Error' => null];
        } else {
            foreach ($usuarios as $usuario) {
                $cadete = $usuario['Observaciones'] === 'cadete' || $usuario['Observaciones'] === 'Cadete';

                if (!$cadete && !empty($usuario['TokenApp'])) {
                    $tokens[] = $usuario['TokenApp'];
                }
            }

            return FCMHelper::enviarNotificacionPush($notificacion, $tokens, $data);
        }
    }
}