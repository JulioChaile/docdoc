<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use common\models\Casos;
use common\components\EmailHelper;
use common\components\FCMHelper;

class NotificacionesController extends Controller
{

    /**
     * Permite notificar al responsable o creador del movimiento de Gestion Estudio,
     * que el mismo se vención, enviándole un mail.
     */
    public function actionMovimientosVencidosGestionEstudio()
    {
        $casos = new Casos;
        $notificaciones = $casos->ListarNotificacionesGestionEstudio();

        foreach ($notificaciones as $notificacion) {
            $email = $notificacion['Email'];
            $movimientos = json_decode($notificacion['Movimientos']);

            EmailHelper::enviarEmail(
                'DocDoc <contacto@docdoc.com.ar>',
                $email,
                'Movimientos a punto a vencerse',
                'movimiento-vencido',
                [
                    'movimientos' => $movimientos
                ]
            );
        }

        $this->notificacionesAudiencias();

        // En caso de error -> return ExitCode::UNSPECIFIED_ERROR;
        return ExitCode::OK;
    }

    public function actionTest()
    {
        return $this->notificacionesAudiencias();
    }

    private function notificacionesAudiencias()
    {
        $sql = 'CALL dsp_listar_eventos_notificaciones()';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $notificaciones = $query->queryAll();

        foreach ($notificaciones as $n) {
            $personas = json_decode($n['PersonasCaso'], true);
            $tokens = array();

            foreach ($personas as $p) {
                if (!empty($p['TokenApp']) && $p['Observaciones'] === 'Actor') {
                    $tokens[] = $p['TokenApp'];
                }
            }

            $caratula = $n['Caratula'];
            $detalle = $n['Detalle'];
            $idCaso = $n['IdCaso'];
            $idChat = $n['IdChat'];
            $idExternoChat = $n['IdExternoChat'];
            $dias = intval($n['Dias']) === 1 ? "{$n['Dias']} día" : "{$n['Dias']} días";
            $fecha = implode(
                '/',
                array_reverse(
                    explode(
                        '-',
                        explode(
                            ' ',
                            $n['FechaEvento']
                        )[0]
                    )
                )
            );
            $hora = substr(
                explode(
                    ' ',
                    $n['FechaEvento']
                )[1],
                0,
                -3
            );

            if (!empty($tokens)) {
                try {
                    $respuesta = FCMHelper::enviarNotificacionPush(
                        [
                            'title' => 'Recordatorio',
                            'body' => "Te recordamos que tu audiencia en el caso {$caratula} será en {$dias}, es decir el día {$fecha} a las {$hora} hs.\nDetalles: {$detalle}."
                        ],
                        $tokens,
                        [
                            'tipo' => 'audiencia',
                            'id' => $idCaso
                        ],
                        'mult',
                        true
                    );
                } catch (Throwable $e) {}
            }

            if (!empty($n['IdChat'])) {
                $Contenido = "Te recordamos que tu audiencia en el caso {$n['Caratula']} será en {$dias}, es decir el día {$fecha} a las {$hora} hs.\nDetalles: {$n['Detalle']}\n\nRecordá que podes bajar la app para nuestros clientes y estar al tanto de todas las novedades:\nhttps://play.google.com/store/apps/details?id=com.docdoc_clientes.app";

                $Objeto = [
                    'chatId' => $idExternoChat,
                    'template' => 'recordatorio_audiencia',
                    'language' => [
                        'policy' => 'deterministic',
                        'code' => 'es'
                    ],
                    'namespace' => 'ed2267b7_c376_4b90_90ae_233fb7734eb9',
                    'params' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                [ 'type' => 'text', 'text' => $n['Caratula'] ],
                                [ 'type' => 'text', 'text' => $dias ],
                                [ 'type' => 'text', 'text' => $fecha ],
                                [ 'type' => 'text', 'text' => $hora ],
                                [ 'type' => 'text', 'text' => $n['Detalle'] ],
                            ]
                        ]
                    ]
                ];

                $respuestaChat = Yii::$app->chatapi->enviarTemplate(
                    $idExternoChat,
                    $Contenido,
                    1,
                    $Objeto,
                    null
                );
            }
        }
    }
}
