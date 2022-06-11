<?php
namespace common\models;

use Yii;
use yii\base\Model;

class GestorMensajesInterno extends Model
{
    public function AltaMensaje($Contenido, $IdCaso, $Cliente, $URL, $TipoMult, $IdUsuario)
    {
        $sql = 'CALL dsp_alta_mensaje_chat_interno( :idCaso, :contenido, :idUsuario, :cliente, :URLMult,'
                . ' :tipoMult )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCaso' => $IdCaso,
            ':contenido' => $Contenido,
            ':idUsuario' => $IdUsuario,
            ':cliente' => $Cliente,
            ':URLMult' => $URL,
            ':tipoMult' => $TipoMult
        ]);
        
        return $query->queryScalar();
    }

    public function ListarMensajes($IdCaso)
    {
        $sql = 'CALL dsp_buscar_mensajes_chat_interno( :idCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCaso' => $IdCaso
        ]);
        
        return $query->queryAll();
    }

    public function NuevosMensajes($IdCaso, $IdUsuario, $Cliente)
    {
        $sql = 'CALL dsp_buscar_nuevos_mensajes_interno( :idCaso, :idUsuario, :cliente )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCaso' => $IdCaso,
            ':idUsuario' => $IdUsuario,
            ':cliente' => $Cliente
        ]);
        
        return $query->queryAll();
    }

    public function UpdateMensajes($IdCaso, $Cliente)
    {
        $sql = 'CALL dsp_set_fechavisto_mensajes_internos( :idCaso, :cliente )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCaso' => $IdCaso,
            ':cliente' => $Cliente
        ]);
        
        return $query->queryScalar();
    }
}