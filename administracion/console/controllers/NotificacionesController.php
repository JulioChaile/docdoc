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

        $this->recordatoriosDoc();
        $this->recordatoriosMov();
        $this->notificacionesAudiencias();

        // En caso de error -> return ExitCode::UNSPECIFIED_ERROR;
        return ExitCode::OK;
    }

    public function actionTest()
    {
        return $this->notificacionesAudiencias();
    }

    public function recordatoriosDoc()
    {
        $sql = 'SELECT *, CONCAT(TIMESTAMPDIFF(DAY, DATE(NOW()), DATE(FechaLimite))) Dias FROM RecordatorioDocumentacion WHERE Activa = "S" AND DATE(NOW()) = DATE(DATE_ADD(UltimoRecordatorio, INTERVAL Frecuencia DAY))';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $recordatorios = $query->queryAll();

        foreach ($recordatorios as $r) {
            Yii::info($r);
            $IdCaso = $r['IdCaso'];
            $caso = new Casos;
            $caso->IdCaso = $IdCaso;
            $caso->Dame(5, 'N');

            if (!empty($caso->IdChat)) {
                $sql2 = 'SELECT CONCAT(p.Apellidos, " ", p.Nombres) Persona, pc.DocumentacionSolicitada, pc.EsPrincipal FROM PersonasCaso pc INNER JOIN Personas p USING(IdPersona) WHERE pc.DocumentacionSolicitada IS NOT NULL AND pc.IdCaso = ' . $IdCaso;
            
                $query2 = Yii::$app->db->createCommand($sql2);
                
                $personas = $query2->queryAll();

                $principal = '';
                $listado = '';
                $fecha = $r['FechaLimite'];
                $dias = $r['Dias'];

                foreach ($personas as $p) {
                    if ($p['EsPrincipal'] === 'S') {
                        $principal = $p['Persona'];
                    }

                    $doc = json_decode($p['DocumentacionSolicitada']);

                    if (!empty($doc)) {
                        $doc = array_filter($doc, function ($d) {
                            return !$d->Estado;
                        });

                        if (!empty($doc)) {
                            $listado = $listado . ', ' . $p['Persona'] . ': ';

                            foreach ($doc as $d) {
                                $listado = $listado . ' - ' . $d->Doc;
                            }
                        }
                    }
                }

                $listado = $listado . '.';

                $Contenido = $principal . " te recuerdo que hasta las fecha " . $fecha . " podes completar los requisitos que necesitamos para gestionar tu caso. Es decir faltan " . $dias . " dias. Esta faltando: " . $listado . " Si ya enviaste la documentación solicitada indicanos cual asi lo registramos";

                $Objeto = [
                    'chatId' => $caso->IdExternoChat,
                    'template' => 'recordatorio_doc',
                    'language' => [
                        'policy' => 'deterministic',
                        'code' => 'es'
                    ],
                    'namespace' => 'ed2267b7_c376_4b90_90ae_233fb7734eb9',
                    'params' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                [ 'type' => 'text', 'text' => $principal ],
                                [ 'type' => 'text', 'text' => $fecha ],
                                [ 'type' => 'text', 'text' => $dias ],
                                [ 'type' => 'text', 'text' => $listado ]
                            ]
                        ]
                    ]
                ];

                $respuestaChat = Yii::$app->chatapi->enviarTemplate(
                    $caso->IdChat,
                    $Contenido,
                    1,
                    $Objeto,
                    null
                );

                $sql3 = "UPDATE RecordatorioDocumentacion SET UltimoRecordatorio = DATE(NOW()) WHERE IdCaso = " . $IdCaso;

                $query3 = Yii::$app->db->createCommand($sql3);
                
                $query3->execute();
            }
        }
    }

    public function recordatoriosMov()
    {
        $sql = 'SELECT m.Detalle, c.IdCaso, r.IdRecordatorioMovimiento FROM RecordatorioMovimiento r INNER JOIN MovimientosCaso m ON r.IdMovimientoCaso = m.IdMovimientoCaso INNER JOIN Casos c ON c.IdCaso = m.IdCaso WHERE DATE(NOW()) <= DATE(m.FechaEsperada) AND DATE(NOW()) = DATE(DATE_ADD(UltimoRecordatorio, INTERVAL Frecuencia DAY))';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $recordatorios = $query->queryAll();

        foreach ($recordatorios as $r) {
            Yii::info($r);
            $IdCaso = $r['IdCaso'];
            $caso = new Casos;
            $caso->IdCaso = $IdCaso;
            $caso->Dame(5, 'N');

            if (!empty($caso->IdChat)) {
                $Contenido = "Te contamos que estamos trabajando en tu caso. Gestion de hoy: " . $r['Detalle'];

                $Objeto = [
                    'chatId' => $caso->IdExternoChat,
                    'template' => 'recordatorio_mov',
                    'language' => [
                        'policy' => 'deterministic',
                        'code' => 'es'
                    ],
                    'namespace' => 'ed2267b7_c376_4b90_90ae_233fb7734eb9',
                    'params' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                [ 'type' => 'text', 'text' => $r['Detalle'] ]
                            ]
                        ]
                    ]
                ];

                $respuestaChat = Yii::$app->chatapi->enviarTemplate(
                    $caso->IdChat,
                    $Contenido,
                    1,
                    $Objeto,
                    null
                );

                $sql3 = "UPDATE RecordatorioMovimiento SET UltimoRecordatorio = DATE(NOW()) WHERE IdRecordatorioMovimiento = " . $r["IdRecordatorioMovimiento"];

                $query3 = Yii::$app->db->createCommand($sql3);
                
                $query3->execute();
            }
        }
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
                    $n['IdChat'],
                    $Contenido,
                    1,
                    $Objeto,
                    null
                );
            }
        }
    }
}
