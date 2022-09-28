<?php
namespace common\models;

use Yii;
use yii\base\Model;

class GestorResoluciones extends Model
{
    /**
     * Permite dar de alta una resolucion controlando que no exista una misma resolucion ya cargada.
     * Devuelve OK + el id del caso creado o un mensaje de error en
     * Mensaje. dsp_alta_resolucion
     *
     * @param Objeto
     */
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_resolucion( :resolucion, :fecha, :monto )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':resolucion' => $Objeto->Resolucion,
            ':fecha' => $Objeto->FechaResolucion,
            ':monto' => $Objeto->MontoResolucion
        ]);
        
        return $query->queryScalar();
    }

    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_resolucion( :IdResolucion )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':IdResolucion' => $Objeto->IdResolucionSMVM
        ]);
        
        return $query->queryScalar();
    }

    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_resolucion( :idResolucion, :resolucion, :fecha, :monto )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':resolucion' => $Objeto->Resolucion,
            ':fecha' => $Objeto->FechaResolucion,
            ':monto' => $Objeto->MontoResolucion,
            ':idResolucion' => $Objeto->IdResolucionSMVM
        ]);
        
        return $query->queryScalar();
    }

    public function Buscar($Cadena = '')
    {
        $sql = 'SELECT * FROM ResolucionesSMVM WHERE Resolucion LIKE "%:cadena%" OR :cadena = ""';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena,
        ]);

        $result = $query->queryAll();
        
        return $result;
    }
}
