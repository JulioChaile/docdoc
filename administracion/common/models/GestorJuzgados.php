<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 12-Apr-2018 17:48:25
 */
class GestorJuzgados extends Model
{
    
    /**
     * Permite crear un juzgado controlando que la jursidicci�n exista y que el nombre
     * no se encuentre en uso ya en la jurisdiccion indicada. Devuelve OK + el id del
     * juzgado creado o un mensaje de error en Mensaje.
     * alta_juzgado
     *
     * @param Objeto
     */
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_juzgado( :token, :idJurisdiccion, :juzgado, :modo_gestion, :color, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idJurisdiccion' => $Objeto->IdJurisdiccion,
            ':juzgado' => $Objeto->Juzgado,
            ':modo_gestion' => $Objeto->ModoGestion,
            ':color' => $Objeto->Color
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite modificar un juzgado controlando que la jursidicci�n exista y que el
     * nombre no se encuentre en uso en la jurisdicci�n. Devuelve OK o un mensaje de
     * error en Mensaje. modificar_juzgado
     *
     * @param Objetio
     */
    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_juzgado( :token, :idJuzgado, :juzgado, :modo_gestion, :color,  :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idJuzgado' => $Objeto->IdJuzgado,
            ':juzgado' => $Objeto->Juzgado,
            ':modo_gestion' => $Objeto->ModoGestion,
            ':color' => $Objeto->Color
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite borra un juzgado controlando que no tenga nominaciones asociadas.
     * Devuelve OK o un mensaje de error en Mensaje. borrar_juzgado
     *
     * @param Objeto
     */
    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_juzgado( :token, :idJuzgado, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idJuzgado' => $Objeto->IdJuzgado,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite buscar juzgados indicando la jurisdicci�n (pIdJurisdiccion = 0 para
     * todos), filtr�ndolos por una cadena de b�squeda e indicando si se incluyen o no
     * los dados de baja. Ordena por Juzgado.
     * buscar_juzgados
     *
     * @param IdJurisdiccion    0 para todas
     * @param Cadena
     * @param IncluyeBajas    S: Si - N: No
     */
    public function Buscar($IdJurisdiccion = 0, $Cadena = '', $IncluyeBajas = 'N')
    {
        $sql = 'CALL dsp_buscar_juzgados( :idJurisdiccion, :cadena, :incluyeBajas )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idJurisdiccion' => $IdJurisdiccion,
            ':cadena' => $Cadena,
            ':incluyeBajas' => $IncluyeBajas
        ]);
        
        return $query->queryAll();
    }
}
