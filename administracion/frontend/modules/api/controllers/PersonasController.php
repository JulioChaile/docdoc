<?php
namespace frontend\modules\api\controllers;

use common\models\Personas;
use common\models\Casos;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use yii\helpers\ArrayHelper;
use common\components\FCMHelper;
use Yii;

class PersonasController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bearerAuth' => [
                    'class' => OptionalBearerAuth::className(),
                    'except' => ['options'],
                    'actionsClient' => ['parametros']
                ],
            ]
        );
    }

    public function actionAltaTelefono($id)
    {
        $persona = new Personas();

        $persona->IdPersona = $id;

        $datos = [
            'IdPersona' => $id,
            'Detalle' => Yii::$app->request->post('Detalle'),
            'EsPrincipal' => Yii::$app->request->post('EsPrincipal'),
            'Telefono' => Yii::$app->request->post('Telefono')
        ];

        $resultado = $persona->AltaTelefonos((object) $datos);

        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionUpdate($id)
    {
        $persona = new Personas();

        $persona->IdPersona = $id;

        $datos = [
          'IdPersona' => $id,
          'Detalle' => Yii::$app->request->post('Detalle'),
          'EsPrincipal' => Yii::$app->request->post('EsPrincipal'),
          'Telefono' => Yii::$app->request->post('Telefono'),
          'TelefonoOld' => Yii::$app->request->post('TelefonoOld')
        ];
        
        $resultado = $persona->ModificarTelefono((object) $datos);

        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionEliminar($id)
    {
        $persona = new Personas();

        $persona->IdPersona = $id;

        $datos = [
            'IdPersona' => $id,
            'Telefono' => Yii::$app->request->post('Telefono')
        ];

        $resultado = $persona->BorrarTelefono((object) $datos);

        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }

    /**
     * @api {get} /personas/casos Listar Personas Casos
     * @apiName ListarPersonasCasos
     * @apiGroup Casos
     * @apiPermission logueado
     *
     * @apiParam {[]Number} IdsCasos
     *
     * @apiSuccess {[]Object} . Datos de las Personas.
     */

    public function actionPersonasCasos()
    {
        $caso = new Casos();

        $IdsCasos = json_decode(Yii::$app->request->post('IdsCasos'), true);

        $out = array();

        foreach ($IdsCasos as $IdCaso) {
            $caso->IdCaso = $IdCaso;
            $out[$IdCaso] = $caso->ListarPersonas();
        }

        return $out;
    }

    public function actionPadron($documento)
    {
        $persona = new Personas();

        $resultado = $persona->Padron($documento);

        if ($resultado['Mensaje'] != 'OK') {
            return ['Error' => $resultado['Mensaje']];
        }

        return $resultado;
    }

    public function actionParametros()
    {
        $Parametros = Yii::$app->request->post('Parametros');
        $IdCaso = Yii::$app->request->post('IdCaso');
        $IdPersona = Yii::$app->request->post('IdPersona');

        $persona = new Personas();

        $resultado = $persona->Parametros($Parametros, $IdCaso, $IdPersona);

        if ($resultado == 'OK') {
            return ['Error' => null];
        } else {
            return ['Error' => $resultado];
        }
    }

    public function actionHistoriaClinica()
    {
        $HistoriaClinica = json_decode(Yii::$app->request->post('HistoriaClinica'));
        $IdCaso = Yii::$app->request->post('IdCaso');
        $IdPersona = Yii::$app->request->post('IdPersona');

        $persona = new Personas();

        if (empty($HistoriaClinica->IdHistoriaClinica)) {
            $resultado = $persona->AltaHistoriaClinica($HistoriaClinica, $IdCaso, $IdPersona);

            if (substr($resultado, 0, 2) == 'OK') {
                return [
                    'Error' => null,
                    'IdHistoriaClinica' => substr($resultado, 2)
                ];
            } else {
                return ['Error' => $resultado];
            }
        } else {
            $resultado = $persona->EditarHistoriaClinica($HistoriaClinica, $IdCaso, $IdPersona);

            if ($resultado == 'OK') {
                return ['Error' => null];
            } else {
                return ['Error' => $resultado];
            }
        }
    }

    public function actionEditarDocumentacion($id)
    {
        $personas = Yii::$app->request->post('personas');

        $primeraVez = Yii::$app->request->post('primeraVez');

        $persona = new Personas();

        $caso = new Casos();
        $caso->IdCaso = $id;
        $caso->Dame();

        $errores = array();

        $count = 0;

        foreach ($personas as $p) {
            if (!empty($p['TokenApp'])) {
                $respuesta = FCMHelper::enviarNotificacionPush(
                    [
                        'title' => 'Hay novedades en uno de tus casos',
                        'body' => "El estudio encargado del caso {$caso->Caratula} quiere que revises la documentacion requerida del mismo."
                    ],
                    $p['TokenApp'],
                    [
                        'tipo' => 'documentacion',
                        'id' => $caso->IdCaso
                    ],
                    'user',
                    true
                );
            }

            $resultado = $persona->EditarDocumentacion($p['IdPersona'], $id, $p['DocumentacionSolicitada']);

            if ($resultado !== 'OK') {
                $errores[] = $resultado;
            }
        }

        $sql2 = 'SELECT pc.DocumentacionSolicitada FROM PersonasCaso pc INNER JOIN Personas p USING(IdPersona) WHERE pc.DocumentacionSolicitada IS NOT NULL AND pc.IdCaso = ' . $id;
            
        $query2 = Yii::$app->db->createCommand($sql2);
        
        $docs = $query2->queryAll();

        $count = 0;

        foreach ($docs as $doc) {
            $d = $doc;

            if (!empty($d)) {
                foreach ($d as $item) {
                    Yii::info($item);

                    $ff = json_decode($item, true);

                    foreach ($ff as $a) {
                        if (!$a['Estado']) {
                            $count = $count + 1;
                        }
                    }
                }
            }
        }

        if ($count === 0) {
            $sql3 = "UPDATE RecordatorioDocumentacion SET Activa = 'N' WHERE IdCaso = " . $id;

            $query3 = Yii::$app->db->createCommand($sql3);
            
            $query3->execute();
        } else {
            $sql4 = "SELECT * FROM RecordatorioDocumentacion WHERE Activa = 'N' AND IdCaso = " . $id;

            $query4 = Yii::$app->db->createCommand($sql4);
            
            $recs = $query4->queryAll();

            if (!empty($recs)) {
                $sql5 = "UPDATE RecordatorioDocumentacion SET Activa = 'S', FechaLimite = DATE(DATE_ADD(FechaLimite, interval 10 day)), Frecuencia = 2, UltimoRecordatorio = DATE(NOW()) WHERE IdCaso = " . $id;

                $query5 = Yii::$app->db->createCommand($sql5);
                
                $query5->execute();

                $sql6 = "SELECT DATE(DATE_ADD(NOW(), INTERVAL 10 DAY))";

                $query6 = Yii::$app->db->createCommand($sql6);
                
                $FechaLimite = $query6->queryScalar();

                if (!empty($caso->IdChat)) {
                    $sql2 = 'SELECT CONCAT(p.Apellidos, " ", p.Nombres) Persona, pc.DocumentacionSolicitada, pc.EsPrincipal FROM PersonasCaso pc INNER JOIN Personas p USING(IdPersona) WHERE pc.DocumentacionSolicitada IS NOT NULL AND pc.IdCaso = ' . $id;
                    
                    $query2 = Yii::$app->db->createCommand($sql2);
                    
                    $personas = $query2->queryAll();
    
                    $principal = '';
                    $listado = '';
                    $fecha = $FechaLimite;
                    $dias = '10';
    
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
                                    [ 'type' => 'text', 'text' => $dias . '' ],
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
    
                    return $respuestaChat;
                }
            } else {
                $sql14 = "SELECT * FROM RecordatorioDocumentacion WHERE IdCaso = " . $id;

                $query14 = Yii::$app->db->createCommand($sql14);
                
                $recs = $query14->queryAll();

                if (empty($recs)) {
                    $sql6 = "INSERT INTO RecordatorioDocumentacion VALUES (0, DATE(DATE_ADD(DATE(NOW()), interval 10 day)), 2, 'S', DATE(NOW()))";

                    $query6 = Yii::$app->db->createCommand($sql6);
                    
                    $query6->execute();

                    if (!empty($caso->IdChat)) {
                        $sql2 = 'SELECT CONCAT(p.Apellidos, " ", p.Nombres) Persona, pc.DocumentacionSolicitada, pc.EsPrincipal FROM PersonasCaso pc INNER JOIN Personas p USING(IdPersona) WHERE pc.DocumentacionSolicitada IS NOT NULL AND pc.IdCaso = ' . $id;
                        
                        $query2 = Yii::$app->db->createCommand($sql2);
                        
                        $personas = $query2->queryAll();

                        $sql16 = "SELECT DATE(DATE_ADD(NOW(), INTERVAL 10 DAY))";

                        $query16 = Yii::$app->db->createCommand($sql16);
                        
                        $FechaLimite = $query16->queryScalar();
        
                        $principal = '';
                        $listado = '';
                        $fecha = $FechaLimite;
                        $dias = '10';
        
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
                                        [ 'type' => 'text', 'text' => $dias . '' ],
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
        
                        return $respuestaChat;
                    }
                }
            }
        }

        return $errores;
    }
}
