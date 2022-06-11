<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 11-Apr-2018 19:09:43
 */
class GestorEstadosCaso extends Model
{

    /**
     * Permite crear un EstadoCaso, controlando que el nombre no se encuentre en uso
     * ya. Devuelve OK + el id del estado-caso creado o un mensaje de error en Mensaje.
     * alta_estadocaso
     *
     * @param Objeto
     */
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_estadocaso( :token, :idEstudio, :estadoCaso,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $Objeto->IdEstudio,
            ':estadoCaso' => $Objeto->EstadoCaso,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite borrar un EstadoCaso controlando que el mismo no tenga subestados
     * asociados. Devuelve OK o un mensaje de error en Mensaje.
     * borrar_estadocaso
     *
     * @param Objeto
     */
    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_estadocaso( :token, :idEstadoCaso,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstadoCaso' => $Objeto->IdEstadoCaso,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite buscar estados de caso filtr�ndolos por una cadena de b�squeda e
     * indicando si se incluyen o no los dados de baja en pIncluyeBajas = [S|N].
     * Ordena por EstadoCaso. buscar_estadoscaso
     *
     * @param Cadena
     * @param IncluyeBajas    S: Si - N: No
     */
    public function Buscar($Cadena = '', $IncluyeBajas = 'N')
    {
        $sql = 'CALL dsp_buscar_estadoscaso( :cadena, :incluyeBajas )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena,
            ':incluyeBajas' => $IncluyeBajas
        ]);
        
        return $query->queryAll();
    }

    /**
     * Permite modificar un EstadoCaso, controlando que el nombre no se encuentre en
     * uso ya. Devuelve OK o un mensaje de error en Mensaje. modificar_estadocaso
     *
     * @param Objeto
     */
    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_estadocaso( :token, :idEstadoCaso, :estadoCaso,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstadoCaso' => $Objeto->IdEstadoCaso,
            ':estadoCaso' => $Objeto->EstadoCaso,
        ]);
        
        return $query->queryScalar();
    }
}
