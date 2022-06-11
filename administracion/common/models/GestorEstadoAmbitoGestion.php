<?php
namespace common\models;

use Yii;
use yii\base\Model;

class GestorEstadoAmbitoGestion extends Model
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
        $sql = 'CALL dsp_alta_estadoambitogestion( :token, :EstadoAmbitoGestion, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':EstadoAmbitoGestion' => $Objeto->EstadoAmbitoGestion,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id
        ]);
        
        return $query->queryScalar();
    }

    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_estadoambitogestion( :token, :IdEstadoAmbitoGestion, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IdEstadoAmbitoGestion' => $Objeto->IdEstadoAmbitoGestion,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id
        ]);
        
        return $query->queryScalar();
    }

    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_estadoambitogestion( :token, :EstadoAmbitoGestion, :IdEstadoAmbitoGestion)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':EstadoAmbitoGestion' => $Objeto->EstadoAmbitoGestion,
            ':IdEstadoAmbitoGestion' => $Objeto->IdEstadoAmbitoGestion,
        ]);
        
        return $query->queryScalar();
    }

    public function ModificarMensaje($Objeto)
    {
        $sql = 'CALL dsp_modificarmensaje_estadoambitogestion( :token, :Mensaje, :IdEstadoAmbitoGestion)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':Mensaje' => $Objeto->Mensaje ? $Objeto->Mensaje : null,
            ':IdEstadoAmbitoGestion' => $Objeto->IdEstadoAmbitoGestion,
        ]);
        
        return $query->queryScalar();
    }

    public function Buscar($Cadena = '')
    {
        $sql = 'CALL dsp_buscar_estadoambitogestion( :cadena)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena,
        ]);

        $result = $query->queryAll();
        
        return $result;
    }
}
