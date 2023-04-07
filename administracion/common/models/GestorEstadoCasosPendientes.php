<?php
namespace common\models;

use Yii;
use yii\base\Model;

class GestorEstadoCasosPendientes extends Model
{
    /**
     * Permite dar de alta estado de ambito de gestion controlando que no exista un mismo estado ambito de gestion ya cargado.
     * Devuelve OK + el id del caso creado o un mensaje de error en
     * Mensaje. dsp_alta_EstadoAmbitoGestion
     *
     * @param Objeto
     */
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_estadocasopendiente( :token, :EstadoCasoPendiente, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':EstadoCasoPendiente' => $Objeto->EstadoCasoPendiente,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id
        ]);
        
        return $query->queryScalar();
    }

    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_estadocasopendiente( :token, :IdEstadoCasoPendiente, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IdEstadoCasoPendiente' => $Objeto->IdEstadoCasoPendiente,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id
        ]);
        
        return $query->queryScalar();
    }

    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_estadocasopendiente( :token, :EstadoCasoPendiente, :IdEstadoCasoPendiente)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':EstadoCasoPendiente' => $Objeto->EstadoCasoPendiente,
            ':IdEstadoCasoPendiente' => $Objeto->IdEstadoCasoPendiente,
        ]);
        
        return $query->queryScalar();
    }
}
