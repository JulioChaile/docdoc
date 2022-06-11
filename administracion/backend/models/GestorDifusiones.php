<?php
namespace backend\models;

use common\components\FechaHelper;
use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 11-Apr-2018 11:20:31
 */
class GestorDifusiones extends Model
{
 
    /**
     * Permite crear una campa�a de difusi�n controlando que el nombre no se encuentre
     * en uso ya. Devuelve OK + Id de la campa�a de difusi�n creada o un mensaje de
     * error en Mensaje. alta_difusion
     *
     * @param Objeto
     */
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_difusion( :token, :difusion, :fechaInicio, :fechaFin,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':difusion' => $Objeto->Difusion,
            ':fechaInicio' => FechaHelper::formatearDateMysql($Objeto->FechaInicio),
            ':fechaFin' => FechaHelper::formatearDateMysql($Objeto->FechaFin)
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite modificar una campa�a de difusi�n controlando que el nombre no se
     * encuentre en uso ya. Devuelve OK o un mensaje de error en Mensaje.
     * modificar_difusion
     *
     * @param Objeto
     */
    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_difusion( :token, :idDifusion, :difusion,'
                . ' :fechaInicio, :fechaFin, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idDifusion' => $Objeto->IdDifusion,
            ':difusion' => $Objeto->Difusion,
            ':fechaInicio' => FechaHelper::formatearDateMysql($Objeto->FechaInicio),
            ':fechaFin' => FechaHelper::formatearDateMysql($Objeto->FechaFin)
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite borrar una campa�a de difusi�n controlando que no existan consultas
     * asociadas. Devuelve OK o un mesnaje de error en Mensaje.
     * borrar_difusion
     *
     * @param Objeto
     */
    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_difusion( :token, :idDifusion, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idDifusion' => $Objeto->IdDifusion,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite buscar campa�as de difusi�n filtr�ndolas por nombre. buscar_difusiones
     *
     *
     * @param Cadena
     */
    public function Buscar($Cadena = '')
    {
        $sql = 'CALL dsp_buscar_difusiones( :cadena )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena
        ]);
        
        return $query->queryAll();
    }
}
