<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 07-Nov-2018 17:17:48
 */
class Objetivos extends Model
{
    public $IdObjetivo;
    public $IdCaso;
    public $Objetivo;
    public $FechaAlta;
    
    //Derivados
    public $MovimientosCaso;

    public function rules()
    {
        return [
            [$this->attributes(), 'safe']
        ];
    }

    /**
     * Permite instanciar un objetivo desde la base de datos. dsp_dame_objetivo
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_objetivo( :idObjetivo )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idObjetivo' => $this->IdObjetivo,
        ]);
        
        $this->attributes = $query->queryOne();
        $this->MovimientosCaso = json_decode($this->MovimientosCaso);
    }

    /**
     * Permite asociar un movimiento de caso a un objetivo siempre que el movimiento
     * no se encuentre asociado ya a un objetivo. Controla que el movimiento exista.
     * Devuelve OK o el mensaje de error. dsp_alta_movimientocaso_objetivo
     *
     * @param IdMovimientoCaso
     */
    public function AltaMovimientoCaso($Objeto)
    {
        $sql = 'CALL dsp_alta_movimientocaso_objetivo( :token, :idObjetivo,'
                . ' :idMovimientoCaso, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idObjetivo' => $this->IdObjetivo,
            ':idMovimientoCaso' => $Objeto->IdMovimientoCaso,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite borrar un movimiento de un caso de un objetivo. Controla que el
     * movimiento exista y este asociado al objetivo. Devuelve OK o el mensaje de
     * error. dsp_borrar_movimientocaso_objetivo
     *
     * @param IdMovimientoCaso
     */
    public function BorrarMovimientoCaso($IdMovimientoCaso)
    {
        $sql = 'CALL dsp_borrar_movimientocaso_objetivo( :token, :idObjetivo,'
                . ' :idMovimientoCaso, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idObjetivo' => $this->IdObjetivo,
            ':idMovimientoCaso' => $IdMovimientoCaso,
        ]);
        
        return $query->queryScalar();
    }
}
