<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 18-May-2018 18:19:28
 */
class GestorOrigenes extends Model
{
   
    /**
     * Permite crear un origen de caso controlando que no exista ya en el estudio
     * indicado. Devuelve OK + Id del origen creado o un mensaje de error en Mensaje.
     *
     * alta_origen
     *
     * @param Objeto
     */

    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_origen( :token, :origen, :idEstudio,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':origen' => $Objeto->Origen,
            ':idEstudio' => $Objeto->IdEstudio,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite modificar un origen de casos controlando que el usuario que ejecuta la
     * acci�n pertence al estudio al cual pertenece el origen indicado.
     * modificar_origen
     *
     * @param Objeto
     */
    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_origen( :token, :idOrigen, :origen,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idOrigen' => $Objeto->IdOrigen,
            ':origen' => $Objeto->Origen,
        ]);
        
        return $query->queryScalar();
    }
    

    /**
     * Permite borrar un origen controlando que no existan casos asociados. Devuelve
     * OK o un mensaje de error en Mensaje. borrar_origen
     *
     * @param Objeto
     */
    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_origen( :token, :idOrigen,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idOrigen' => $Objeto->IdOrigen,
        ]);
        
        return $query->queryScalar();
    }
}
