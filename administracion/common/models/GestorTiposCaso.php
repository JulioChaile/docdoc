<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 04-Apr-2018 18:58:30
 */
class GestorTiposCaso extends Model
{

    /**
     * Permite crear tipos de caso controlando que el nombre no se encuentre en uso ya.
     * Devuelve OK + Id del tipo de caso creado o un mensaje de error en Mensaje.
     * alta_tipocaso
     *
     * @param Objeto
     */
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_tipocaso( :token, :tipoCaso, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':tipoCaso' => $Objeto->TipoCaso,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite borrar un tipo de caso controlando que no tenga roles asociados.
     * Devuelve OK o un mensaje de error en Mensaje. borrar_tipocaso
     *
     * @param Objeto
     */
    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_tipocaso( :token, :idTipoCaso, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idTipoCaso' => $Objeto->IdTipoCaso,
        ]);
        
        return $query->queryScalar();
    }
    
    /**
    * Permite buscar tipos de caso filtr�ndolos por una cadena de b�squeda. Ordena
    * por TipoCaso.
    * buscar_tiposcaso
    *
    * @param Cadena
    * @param IncluyeBajas    S: Si - N: No
    */
    public function Buscar($Cadena = '', $IncluyeBajas = 'N')
    {
        $sql = 'CALL dsp_buscar_tiposcaso( :cadena, :incluyeBajas )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena,
            ':incluyeBajas' => $IncluyeBajas,
        ]);
        
        return $query->queryAll();
    }

    /**
     * Permite modificar un tipo de caso controlando que el nombre no se encuentre en
     * uso ya. Devuelve OK o un mensaje de error en Mensaje.
     * modificar_tipocaso
     *
     * @param Objeto
     */
    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_tipocaso( :token, :idTipoCaso, :tipoCaso ,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idTipoCaso' => $Objeto->IdTipoCaso,
            ':tipoCaso' => $Objeto->TipoCaso,
        ]);
        
        return $query->queryScalar();
    }
}
