<?php
namespace common\models;

use Yii;
use yii\base\Model;

class GestorComentariosCaso extends Model
{
    public function Alta($Comentario, $IdCaso)
    {
        $sql = 'CALL dsp_alta_comentario_caso( :token, :comentario, :idCaso, :idUsuario )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idUsuario' => Yii::$app->user->identity->IdUsuario,
            ':idCaso' => $IdCaso,
            ':comentario' => $Comentario
        ]);
        
        return $query->queryScalar();
    }

    public function AltaUsuarioComentario($IdComentarioCaso, $IdCaso, $IdUsuario)
    {
        $sql = 'CALL dsp_alta_usuario_comentario_caso( :token, :idComentarioCaso, :idCaso, :idUsuario )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idUsuario' => $IdUsuario,
            ':idCaso' => $IdCaso,
            ':idComentarioCaso' => $IdComentarioCaso
        ]);
        
        return $query->queryScalar();
    }

    public function ListarComentarios($IdCaso)
    {
        $sql = 'CALL dsp_listar_comentarios_caso( :idCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCaso' => $IdCaso
        ]);
        
        return $query->queryAll();
    }

    public function ListarComentariosSinLeer()
    {
        $sql = 'CALL dsp_listar_comentarios_sin_leer( :idUsuario )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idUsuario' => Yii::$app->user->identity->IdUsuario
        ]);
        
        return $query->queryAll();
    }

    public function SetFechaVistoComentario($IdCaso)
    {
        $sql = 'CALL dsp_set_fecha_visto_comentario( :token, :idCaso, :idUsuario )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idUsuario' => Yii::$app->user->identity->IdUsuario,
            ':idCaso' => $IdCaso
        ]);
        
        return $query->queryScalar();
    }
}
