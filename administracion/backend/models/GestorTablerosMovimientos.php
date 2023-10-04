<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 18-May-2018 18:19:28
 */
class GestorTablerosMovimientos extends Model
{
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_tablero_movimientos( :token, :idTipoMov, :orden, :idEstudio,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idTipoMov' => $Objeto->IdTipoMov,
            ':orden' => $Objeto->Orden,
            ':idEstudio' => $Objeto->IdEstudio,
        ]);
        
        return $query->queryScalar();
    }

    public function ModificarOrden($Objeto)
    {
        $sql = 'CALL dsp_modificar_orden_tablero_movimientos( :token, :idTipoMovimientoTablero, :idEstudio, :orden,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idTipoMovimientoTablero' => $Objeto->IdTipoMovimientoTablero,
            ':idEstudio' => $Objeto->IdEstudio,
            ':orden' => $Objeto->Orden,
        ]);
        
        return $query->queryScalar();
    }
    
    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_tablero_movimientos( :token, :idTipoMovimientoTablero, :idEstudio,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idTipoMovimientoTablero' => $Objeto->IdTipoMovimientoTablero,
            ':idEstudio' => $Objeto->IdEstudio,
        ]);
        
        return $query->queryScalar();
    }
}
