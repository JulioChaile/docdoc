<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 12-Apr-2018 15:31:40
 */
class GestorJurisdicciones extends Model
{
   
    /**
     * Permtie crear una jurisdicci�n controlando que el nombre no se encuentre en uso
     * ya. Devuelve OK + el id de la jurisdicci�n creada o un mensaje de error en
     * Mensaje. alta_jurisdiccion
     *
     * @param Objeto
     */
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_jurisdiccion( :token, :jurisdiccion, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':jurisdiccion' => $Objeto->Jurisdiccion,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite modificar una jurisdicci�n controlando que el nombre no se encuentre en
     * uso ya. Devuelve OK o un mensaje de error en Mensaje. modificar_jurisdiccion
     *
     * @param Objeto
     */
    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_jurisdiccion( :token, :idJurisdiccion, :jurisdiccion,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idJurisdiccion' => $Objeto->IdJurisdiccion,
            ':jurisdiccion' => $Objeto->Jurisdiccion,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite borrar una jurisdicci�n controlando que no tenga juzgados asociados.
     * Devuelve OK o un mensaje de error en Mensaje. borrar_jurisdiccion
     *
     * @param Objeto
     */
    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_jurisdiccion( :token, :idJurisdiccion,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idJurisdiccion' => $Objeto->IdJurisdiccion,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite buscar jurisdicciones filtr�ndolas por una cadena de b�squeda e
     * indicando si se incluyen o no las dadas de baja. Ordena por Jurisdiccion.
     * buscar_jurisdicciones
     *
     * @param Cadena
     * @param IncluyeBajas    S: Si - N: No
     */
    public function Buscar($Cadena = '', $IncluyeBajas = 'N')
    {
        $sql = 'CALL dsp_buscar_jurisdicciones( :cadena, :incluyeBajas )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena,
            ':incluyeBajas' => $IncluyeBajas
        ]);
        
        return $query->queryAll();
    }
}
