<?php
namespace common\models;

use Yii;
use yii\base\Model;

class GestorEventos extends Model
{
    public function AltaEvento($IdCalendario, $IdEventoAPI, $Titulo, $Descripcion, $Comienzo, $Fin, $IdColor)
    {
        $sql = 'CALL dsp_alta_evento( :token, :idEventoAPI, :idCalendario, :titulo, :descripcion,'
                . ' :comienzo, :fin, :idColor )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idEventoAPI' => $IdEventoAPI,
            ':idCalendario' => $IdCalendario,
            ':titulo' => $Titulo,
            ':descripcion' => $Descripcion,
            ':comienzo' => $Comienzo,
            ':fin' => $Fin,
            ':idColor' => $IdColor
        ]);
        
        return $query->queryScalar();
    }

    public function AltaEventoMovimiento($IdEvento, $IdMovimientoCaso)
    {
        $sql = 'CALL dsp_alta_evento_movimiento( :token, :idEvento, :idMovimientoCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idEvento' => $IdEvento,
            ':idMovimientoCaso' => $IdMovimientoCaso
        ]);
        
        return $query->queryScalar();
    }
}