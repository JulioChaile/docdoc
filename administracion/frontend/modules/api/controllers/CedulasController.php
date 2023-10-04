<?php
namespace frontend\modules\api\controllers;

use common\models\Casos;
use frontend\modules\api\filters\auth\OptionalBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class CedulasController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bearerAuth' => [
                    'class' => OptionalBearerAuth::className(),
                    'except' => ['options'],
                ],
            ]
        );
    }
    
    public function actionAltaCedulas()
    {
        $cedulas = json_decode(Yii::$app->request->post('cedulas'), true);

        $IdEstudio = Yii::$app->user->identity->IdEstudio;

        $Cedulas = [];

        foreach ($cedulas as $c) {
            $Fecha = $c[0];
            $NroExpediente = $c[1];
            $NroExpedienteFormat = explode('-', $c[1])[0];
            $Descripcion = $c[2];
            $TipoEscrito = $c[3];
            $Nominacion = $c[4];

            $sql =  "SELECT IdCaso FROM Casos WHERE NroExpediente = '" . $NroExpedienteFormat . "' LIMIT 1";
        
            $query = Yii::$app->db->createCommand($sql);

            $IdCaso = $query->queryScalar();

            if (empty($IdCaso)) {
                $IdCaso = null;
            }

            $sql2 =  "SELECT MAX(IdCorrelativo) FROM Cedulas WHERE FechaCedula = '" . $Fecha . "' LIMIT 1";
        
            $query2 = Yii::$app->db->createCommand($sql2);

            $IdCorrelativo = $query2->queryScalar();

            if (empty($IdCorrelativo)) {
                $IdCorrelativo = 1;
            } else {
                $IdCorrelativo = $IdCorrelativo + 1;
            }

            $sql3 = "INSERT INTO Cedulas VALUES (0," . $IdCorrelativo . ", '" . $Fecha . "', '" . $NroExpediente . "', '" . $Descripcion . "', '" . $TipoEscrito . "', '" . $Nominacion . "', " . ($IdCaso ? $IdCaso : 'NULL') . ", NULL, 'N', " . $IdEstudio . ")";

            $query3 = Yii::$app->db->createCommand($sql3);
            
            $query3->execute();

            $sql8 = "SELECT MAX(IdCedula) FROM Cedulas";

            $query8 = Yii::$app->db->createCommand($sql8);
            
            $IdCedula = $query8->queryScalar();

            $EstadoAmbitoGestion = '';
            $Caratula = '';

            if (!empty($IdCaso)) {
                $sql4 = 'CALL dsp_alta_movimiento_caso( :token, :idCaso, :idTipoMov,'
                . ' :idResponsable, :detalle, :fechaEsperada, :cuaderno, :escrito,'
                . ' :color, :multimedia, :fechaAlta, :cliente, :objetivo, :IP, :userAgent, :app )';
        
                $query4 = Yii::$app->db->createCommand($sql4);
                
                $query4->bindValues([
                    ':token' => Yii::$app->user->identity->Token,
                    ':IP' => Yii::$app->request->userIP,
                    ':userAgent' => Yii::$app->request->userAgent,
                    ':app' => Yii::$app->id,
                    ':idCaso' => $IdCaso,
                    ':idTipoMov' => 15,
                    ':idResponsable' => Yii::$app->user->identity->IdUsuario,
                    ':detalle' => $Descripcion,
                    ':fechaEsperada' => null,
                    ':cuaderno' => '',
                    ':escrito' => '',
                    ':color' => 'grey',
                    ':multimedia' => null,
                    ':fechaAlta' => date('Y-m-d'),
                    ':cliente' => '',
                    ':objetivo' => ''
                ]);

                $respuesta = $query4->queryScalar();
                
                if (substr($respuesta, 0, 2) == 'OK') {
                    $IdMovimientoCaso = substr($respuesta, 2);
                    $IdObjetivo = '';

                    $sql15 = 'SELECT IdObjetivo FROM Objetivos WHERE Objetivo = "ULTIMA NOTIFICACION" AND IdCaso = ' . $IdCaso;
            
                    $query15 = Yii::$app->db->createCommand($sql15);
                    
                    $IdObjetivo = $query15->queryScalar();

                    if (empty($IdObjetivo)) {
                        $sql555 = 'SELECT IdTipoMov FROM TiposMovimiento WHERE TipoMovimiento = "Gestión oficina" AND IdEstudio = ' . $IdEstudio;
                
                        $query555 = Yii::$app->db->createCommand($sql555);
                        
                        $IdTipoMov = $query555->queryScalar();

                        $sql5 = 'SELECT COALESCE(MAX(IdObjetivo),0) + 1 FROM Objetivos';
                
                        $query5 = Yii::$app->db->createCommand($sql5);
                        
                        $IdObjetivo = $query5->queryScalar();

                        $sql6 = 'INSERT INTO Objetivos VALUES(' . $IdObjetivo . ', ' . $IdCaso . ', "ULTIMA NOTIFICACION", NOW(), ' . $IdTipoMov . ', "warning")';
                
                        $query6 = Yii::$app->db->createCommand($sql6);
                        
                        $query6->execute();
                    }

                    $sql7 = "INSERT INTO MovimientosObjetivo VALUES (" . $IdObjetivo . ", " . $IdMovimientoCaso . ")";
            
                    $query7 = Yii::$app->db->createCommand($sql7);
                    
                    $query7->execute();
                }

                $casos = new Casos();
                $casos->IdCaso = $IdCaso;
                $casos->Dame();

                $EstadoAmbitoGestion = $casos->EstadoAmbitoGestion;
                $Caratula = $casos->Caratula;
            }

            $Cedulas[] = [
                'IdCedula' => $IdCedula,
                'IdCorrelativo' => $IdCorrelativo,
                'FechaCedula' => $Fecha,
                'NroExpediente' => $NroExpediente,
                'Descripcion' => $Descripcion,
                'TipoEscrito' => $TipoEscrito,
                'Nominacion' => $Nominacion,
                'IdCaso' => $IdCaso,
                'IdUsuario' => null,
                'Check' => 'N',
                'IdEstudio' => $IdEstudio,
                'EstadoAmbitoGestion' => $EstadoAmbitoGestion,
                'Caratula' => $Caratula
            ];
        }

        return $Cedulas;
    }

    public function actionListar ()
    {
        $Fecha = Yii::$app->request->get('Fecha');
        $nroExp = Yii::$app->request->get('nroExp');
        $IdEstudio = Yii::$app->user->identity->IdEstudio;

        $sql = "SELECT c.*, cs.Caratula, eag.EstadoAmbitoGestion, u.Usuario, jea.Orden" .
            " FROM Cedulas c" .
            " LEFT JOIN Casos cs ON c.IdCaso = cs.IdCaso" .
            " LEFT JOIN EstadoAmbitoGestion eag ON eag.IdEstadoAmbitoGestion = cs.IdEstadoAmbitoGestion" .
            " LEFT JOIN JuzgadosEstadosAmbitos jea ON jea.IdEstadoAmbitoGestion = eag.IdEstadoAmbitoGestion AND cs.IdJuzgado = jea.IdJuzgado" .
            " LEFT JOIN Usuarios u ON c.IdUsuario = u.IdUsuario" .
            " WHERE c.IdEstudio = " . $IdEstudio .
            ($Fecha === '' ? '' : " AND (c.FechaCedula = '" . $Fecha . "')") .
            " AND (c.NroExpediente LIKE '%" . $nroExp . "%')";

        $query = Yii::$app->db->createCommand($sql);
        
        return $query->queryAll();
    }

    public function actionFinalizar ()
    {
        $Ids = json_decode(Yii::$app->request->post('Ids'), true);
        $IdUsuario = Yii::$app->user->identity->IdUsuario;
        $chats = [];

        foreach ($Ids as $id) {
            $sql = "UPDATE Cedulas c SET c.Check = 'S', c.IdUsuario = " . $IdUsuario . " WHERE c.IdCedula = " . $id;
            
            $query = Yii::$app->db->createCommand($sql);
            
            $query->execute();

            $sql2 = "SELECT IdCaso FROM Cedulas WHERE IdCedula = " . $id;
            
            $query2 = Yii::$app->db->createCommand($sql2);
            
            $IdCaso = $query2->queryScalar();

            if (!empty($IdCaso)) {
                $caso = new Casos;
                $caso->IdCaso = $IdCaso;
                $caso->Dame();

                if (!empty($caso->IdChat)) {
                    $sql3 = "SELECT * FROM Cedulas WHERE IdCedula = " . $id;
            
                    $query3 = Yii::$app->db->createCommand($sql3);
                    
                    $Cedula = $query3->queryOne();

                    $sql4 = "SELECT Estudio FROM Estudios WHERE IdEstudio = " . Yii::$app->user->identity->IdEstudio;
            
                    $query4 = Yii::$app->db->createCommand($sql4);
                    
                    $Estudio = $query4->queryScalar();

                    $Contenido = "Hola como estas, estamos trabajando en tu caso, en el dia de hoy el juzgado nos informa el siguiente decreto " . $Cedula['Descripcion'] . ". Solo queremos que sepas que estamos en movimiento, no te preocupes si no entendes lo que significa porque a veces son cuestiones muy técnicas y para eso estamos nosotros! que tengas un hermoso dia te deseamos desde " . $Estudio;

                    $Objeto = [
                        'chatId' => $caso->IdExternoChat,
                        'template' => 'cedula_check',
                        'language' => [
                            'policy' => 'deterministic',
                            'code' => 'es_AR'
                        ],
                        'namespace' => 'ed2267b7_c376_4b90_90ae_233fb7734eb9',
                        'params' => [
                            [
                                'type' => 'body',
                                'parameters' => [
                                    [ 'type' => 'text', 'text' => $Cedula['Descripcion'] ],
                                    [ 'type' => 'text', 'text' => $Estudio ]
                                ]
                            ]
                        ]
                    ];

                    $chats[] = Yii::$app->chatapi->enviarTemplate(
                        $caso->IdChat,
                        $Contenido,
                        1,
                        $Objeto,
                        null
                    );
                }
            }
        }

        return $chats;
    }
}
