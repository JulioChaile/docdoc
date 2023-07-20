<?php
namespace common\models;

use Yii;
use yii\base\Model;

class GestorComunicados extends Model
{
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_comunicado( :titulo, :contenido, :idMultimedia, :fechaComunicado, :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $Objeto->IdEstudio,
            ':titulo' => $Objeto->Titulo,
            ':contenido' => $Objeto->Contenido,
            ':idMultimedia' => $Objeto->IdMultimedia,
            ':fechaComunicado' => $Objeto->FechaComunicado
        ]);
        
        return $query->queryScalar();
    }

    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_comunicado( :idComunicado )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idComunicado' => $Objeto->IdComunicado
        ]);
        
        return $query->queryScalar();
    }

    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_comunicado( :idComunicado, :titulo, :contenido )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idComunicado' => $Objeto->IdComunicado,
            ':titulo' => $Objeto->Titulo,
            ':contenido' => $Objeto->Contenido,
        ]);
        
        return $query->queryScalar();
    }

    public function Listar($IdEstudio, $Offset, $fechaHoy)
    {
        $sql = 'CALL dsp_listar_comunicados( :idEstudio, :offset, :fechaHoy )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio,
            ':fechaHoy' => $fechaHoy,
            ':offset' => $Offset
        ]);

        $result = $query->queryAll();
        
        return $result;
    }
}
