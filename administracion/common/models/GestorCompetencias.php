<?php
namespace common\models;

use Yii;
use yii\base\Model;

class GestorCompetencias extends Model
{
    /**
     * Permite dar de alta una competencia controlando que no exista una misma competencia ya cargada.
     * Devuelve OK + el id del caso creado o un mensaje de error en
     * Mensaje. dsp_alta_competencia
     *
     * @param Objeto
     */
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_competencia( :token, :Competencia, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':Competencia' => $Objeto->Competencia,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id
        ]);
        
        return $query->queryScalar();
    }

    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_competencia( :token, :IdCompetencia, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IdCompetencia' => $Objeto->IdCompetencia,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id
        ]);
        
        return $query->queryScalar();
    }

    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_competencia( :token, :Competencia, :IdCompetencia)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':Competencia' => $Objeto->Competencia,
            ':IdCompetencia' => $Objeto->IdCompetencia,
        ]);
        
        return $query->queryScalar();
    }

    public function Buscar($Cadena = '', $IncluyeBajas = 'N')
    {
        $sql = 'CALL dsp_buscar_competencias( :cadena, :incluyeBajas)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena,
            ':incluyeBajas' => $IncluyeBajas,
        ]);

        $result = $query->queryAll();

        foreach ($result as &$row) {
            $row['TiposCaso'] = json_decode($row['TiposCaso'], true);
        }
        
        return $result;
    }
}
