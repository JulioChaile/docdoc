<?php

namespace frontend\modules\api\controllers;

use yii\web\Response;
use Yii;

class NotificacionesController extends BaseController
{
    public function actionIndex($Fecha = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($Fecha != '') {
            $date = getdate(strtotime($Fecha));
        } else {
            $date = getdate();
        }

        $dia = $date['mday'];
        if (strlen($dia) == 1) {
            $dia = '0' . $dia;
        }
        $mes = $date['mon'];
        if (strlen($mes) == 1) {
            $mes = '0' . $mes;
        }
        $anio = $date['year'];
        $key = "http://www.colegioabogadostuc.org.ar/aplicativos/notificaciones/Notif%20$dia-$mes-$anio.txt";
        Yii::info($key, 'Datos de notificaciones');
        $contenido = Yii::$app->cache->get($key);

        if ($contenido === false) {
            try {
                $contenido = file_get_contents($key);
            } catch (\Exception $e) {
                return ['Error' => 'No se pudieron obtener las últimas notificaciones. Intente de nuevo más tarde.'];
            }
            if (strlen($contenido) == 0) {
                return ['Error' => 'No se pudieron obtener las últimas notificaciones. Intente de nuevo más tarde.'];
            } else {
                Yii::$app->cache->set($key, $contenido, 0);
                return ['Error' => null, 'Mensaje' => utf8_encode($contenido)];
            }
        }
        
        return ['Error' => null, 'Mensaje' => utf8_encode($contenido)];
    }
}
