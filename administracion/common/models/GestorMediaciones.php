<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\components\FechaHelper;

class GestorMediaciones extends Model
{
    /**
     * Permite dar de alta una mediacion.
     * Devuelve OK + el id del caso creado o un mensaje de error en
     * Mensaje. dsp_alta_mediacion
     *
     * @param Objeto
     */
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_mediacion( :token, :idMediador, :idCaso, :idBono, :idBeneficio, :fechaBonos,'
                . ' :fechaPresentado, :fechaProximaAudiencia, :legajo, :idEstadoBeneficio )';

        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idMediador' => $Objeto->IdMediador,
            ':idCaso' => $Objeto->IdCaso,
            ':idBono' => $Objeto->IdBono,
            ':idBeneficio' => $Objeto->IdBeneficio,
            ':fechaBonos' => FechaHelper::formatearDateMysql($Objeto->FechaBonos),
            ':fechaPresentado' => FechaHelper::formatearDateMysql($Objeto->FechaPresentado),
            ':fechaProximaAudiencia' => FechaHelper::formatearDateTimeMysql($Objeto->FechaProximaAudiencia),
            ':legajo' => $Objeto->Legajo,
            'idEstadoBeneficio' => $Objeto->IdEstadoBeneficio
        ]);
        
        return $query->queryScalar();
    }

    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_mediacion( :token, :idMediacion, :idMediador, :idBono, :idBeneficio, :fechaBonos,'
                . ' :fechaPresentado, :fechaProximaAudiencia, :legajo, :idEstadoBeneficio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idMediacion' => $Objeto->IdMediacion,
            ':idMediador' => $Objeto->IdMediador,
            ':idBono' => $Objeto->IdBono,
            ':idBeneficio' => $Objeto->IdBeneficio,
            ':fechaBonos' => FechaHelper::formatearDateMysql($Objeto->FechaBonos),
            ':fechaPresentado' => FechaHelper::formatearDateMysql($Objeto->FechaPresentado),
            ':fechaProximaAudiencia' => FechaHelper::formatearDateTimeMysql($Objeto->FechaProximaAudiencia),
            ':legajo' => $Objeto->Legajo,
            'idEstadoBeneficio' => $Objeto->IdEstadoBeneficio
        ]);
        
        return $query->queryScalar();
    }

    public function DameDatos()
    {
        $sql = "CALL dsp_datos_mediaciones()";

        $query = Yii::$app->db->createCommand($sql);

        return $query->queryOne();
    }

    public function Buscar($IdEstudio, $IdUsuario, $Cadena, $Offset, $CausaPenal)
    {
        $sql = 'CALL dsp_buscar_mediaciones( :idEstudio, :idUsuario, :cadena, :offset, :causaPenal )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio,
            ':idUsuario' => $IdUsuario,
            ':cadena' => $Cadena,
            ':offset' => $Offset,
            ':causaPenal' => empty($CausaPenal) ? 0 : 1
        ]);
        
        return $query->queryAll();
    }

    public function ListarFechasAudiencia()
    {
        $sql = 'CALL dsp_listar_fechas_mediacion( :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => Yii::$app->user->identity->IdEstudio
        ]);
        
        return $query->queryAll();
    }
}
