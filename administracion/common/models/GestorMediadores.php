<?php
namespace common\models;

use Yii;
use yii\base\Model;

class GestorMediadores extends Model
{
    /**
     * Permite dar de alta una mediador controlando que no exista una misma mediador ya cargada.
     * Devuelve OK + el id del caso creado o un mensaje de error en
     * Mensaje. dsp_alta_mediador
     *
     * @param Objeto
     */
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_mediador( :token, :nombre, :registro, :mp, :domicilio, :telefono, :email, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':nombre' => $Objeto->Nombre,
            ':registro' => $Objeto->Registro,
            ':mp' => $Objeto->MP,
            ':domicilio' => $Objeto->Domicilio,
            ':telefono' => $Objeto->Telefono,
            ':email' => $Objeto->Email,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id
        ]);
        
        return $query->queryScalar();
    }

    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_mediador( :token, :idMediador, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idMediador' => $Objeto->IdMediador,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id
        ]);
        
        return $query->queryScalar();
    }

    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_mediador( :token, :idMediador, :nombre, :registro, :mp, :domicilio, :telefono, :email)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idMediador' => $Objeto->IdMediador,
            ':nombre' => $Objeto->Nombre,
            ':registro' => $Objeto->Registro,
            ':mp' => $Objeto->MP,
            ':domicilio' => $Objeto->Domicilio,
            ':telefono' => $Objeto->Telefono,
            ':email' => $Objeto->Email
        ]);
        
        return $query->queryScalar();
    }

    public function Buscar($Cadena = '', $Offset = null, $Limit = null)
    {
        $sql = 'CALL dsp_buscar_mediadores( :cadena, :limit, :offset )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena,
            ':offset' => $Offset,
            ':limit' => $Limit
        ]);

        $result = $query->queryAll();
        
        return $result;
    }

    public function Contar($Cadena = '')
    {
        $sql = 'CALL dsp_contar_mediadores( :cadena )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena
        ]);

        $result = $query->queryScalar();
        
        return $result;
    }
}
