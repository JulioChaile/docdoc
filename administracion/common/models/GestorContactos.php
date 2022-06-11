<?php
namespace common\models;

use Yii;
use yii\base\Model;

class GestorContactos extends Model
{
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_contacto_estudio( :token, :idEstudio, :nombre, :apellidos, :telefono, :email, :tipo )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idEstudio' => $Objeto->IdEstudio,
            ':nombre' => $Objeto->Nombres,
            ':apellidos' => $Objeto->Apellidos,
            ':telefono' => $Objeto->Telefono,
            ':email' => $Objeto->Email,
            ':tipo' => $Objeto->Tipo
        ]);
        
        return $query->queryScalar();
    }

    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_contacto_estudio( :token, :idContacto, :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idEstudio' => $Objeto->IdEstudio,
            ':idContacto' => $Objeto->IdContacto,
        ]);
        
        return $query->queryScalar();
    }

    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_contacto_estudio( :token, :idContacto, :idEstudio, :nombre, :apellidos, :telefono, :email, :tipo)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idEstudio' => $Objeto->IdEstudio,
            ':idContacto' => $Objeto->IdContacto,
            ':nombre' => $Objeto->Nombres,
            ':apellidos' => $Objeto->Apellidos,
            ':telefono' => $Objeto->Telefono,
            ':email' => $Objeto->Email,
            ':tipo' => $Objeto->Tipo
        ]);
        
        return $query->queryScalar();
    }

    public function Buscar($IdEstudio = 0, $Cadena = '', $Tipo = '', $Limit = '', $Offset = '')
    {
        $sql = 'CALL dsp_buscar_contactos_estudio( :cadena, :idEstudio, :tipo, :limit, :offset )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio,
            ':cadena' => $Cadena,
            ':tipo' => $Tipo,
            ':limit' => $Limit,
            ':offset' => $Offset
        ]);

        $result = $query->queryAll();
        
        return $result;
    }

    public function Contar($IdEstudio = 0, $Cadena = '', $Tipo = '')
    {
        $sql = 'CALL dsp_contar_contactos_estudio( :cadena, :idEstudio, :tipo )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio,
            ':cadena' => $Cadena,
            ':tipo' => $Tipo
        ]);

        $result = $query->queryScalar();
        
        return $result;
    }
}
