<?php
namespace common\models;

use Yii;
use yii\base\Model;

class MovimientosCaso extends Model
{
    public $IdMovimientoCaso;
    public $IdCaso;
    public $IdTipoMov;
    public $IdUsuarioCaso;
    public $IdResponsable;
    public $Detalle;
    public $FechaAlta;
    public $FechaEsperada;
    public $FechaRealizado;
    public $Cuaderno;
    public $Escrito;
    public $Color;

    // Otros
    public $Multimedia;
    public $IdObjetivo;
    public $Objetivo;
    
    const ESTADOS = [
        'A' => 'Activo',
        'B' => 'Archivado'
    ];

    public function rules()
    {
        return [
            [$this->attributes(), 'safe']
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'IdCaso' => 'Caso',
            'IdTipoMov' => 'Tipo de movimiento',
            'IdUsuarioCaso' => 'Usuario',
            'IdResponsable' => 'Responsable',
        ];
    }

    /**
     * Permite instanciar un movimiento de caso desde la base de datos.
     * dsp_dame_movimiento_caso
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_movimiento_caso( :idMovCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idMovCaso' => $this->IdMovimientoCaso
        ]);
        
        $this->attributes = $query->queryOne();
    }

    /**
     * Permite marcar un movimiento de caso como realizado. Devuelve OK o un mensaje
     * de error en Mensaje. dsp_realizar_movimiento_caso
     */
    public function Realizar()
    {
        $sql = 'CALL dsp_realizar_movimiento_caso( :token, :idMovCaso,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idMovCaso' => $this->IdMovimientoCaso,
        ]);
        
        if ($query->queryScalar() == 'OK') {
            return $query->queryScalar();
        } else {
            return json_encode($query->queryOne());
        }
    }

    /**
     * Permite marcar un movimiento de caso como realizado. Devuelve OK o un mensaje
     * de error en Mensaje. dsp_desrealizar_movimiento_caso
     */
    public function Desrealizar()
    {
        $sql = 'CALL dsp_desrealizar_movimiento_caso( :token, :idMovCaso,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idMovCaso' => $this->IdMovimientoCaso,
        ]);
        
        return $query->queryScalar();
    }

    public function AltaRecordatorio($IdMovimientoCaso, $Frecuencia)
    {
        $sql = 'CALL dsp_alta_recordatorio_mov( :idMovimientoCaso, :frecuencia )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idMovimientoCaso' => $IdMovimientoCaso,
            ':frecuencia' => $Frecuencia
        ]);
        
        return $query->queryScalar();
    }
}
