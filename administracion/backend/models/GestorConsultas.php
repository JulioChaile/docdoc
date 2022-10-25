<?php
namespace backend\models;

use common\components\FechaHelper;
use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 11-Apr-2018 11:04:29
 */
class GestorConsultas extends Model
{
   
    /**
     * Permite dar de alta una consulta desde la web. Devuelve OK + id de la consulta
     * creada o un mensaje de error en Mensaje. alta_consulta
     *
     * @param Objeto
     */
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_consulta(:idDifusion, :apynom, :telefono,'
                . ' :texto, :dni, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idDifusion' => $Objeto->IdDifusion,
            ':apynom' => $Objeto->Apynom,
            ':telefono' => $Objeto->Telefono,
            ':texto' => $Objeto->Texto,
            ':dni' => $Objeto->DNI
        ]);
        
        return $query->queryOne();
    }

    /**
     * Permite borrar una consulta controlando que no haya sido derivada ya. Devuelve
     * OK o un mensaje de error en Mensaje. borrar_consulta
     *
     * @param Objeto
     */
    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_consulta( :token, :idConsulta, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idConsulta' => $Objeto->IdConsulta,
        ]);
        
        return $query->queryScalar();
    }


    /**
     * Permite buscar consultas filtr�ndolas por fecha de alta entre pFechaInicio y
     * pFechaFin, por apynom y/o tel�fono en pCadena, por campa�a de difusi�n, e
     * indicando si se incluyen o no las dadas de baja en pIncluyeBajas = [S|N].
     * Ordena por FechaAlta.
     * dsp_buscar_avanzado_consultas
     *
     * @param Cadena
     * @param Estado    A: Activa - B: Baja - D: Derivada.
     * @param FechaInicio
     * @param FechaFin
     * @param IdDifusion    0 para todas
     */
    public function BuscarAvanzado($Cadena = '', $Estado = 'T', $FechaInicio = null, $FechaFin = null, $IdDifusion = 0)
    {
        $sql = 'CALL dsp_buscar_avanzado_consultas( :cadena, :fechaInicio, :fechaFin,'
                . ' :estado, :idDifusion )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':cadena' => $Cadena,
            ':estado' => $Estado,
            ':fechaInicio' => FechaHelper::formatearDateMysql($FechaInicio),
            ':fechaFin' => FechaHelper::formatearDateMysql($FechaFin),
            ':idDifusion' => $IdDifusion,
            ]);
 
        return $query->queryAll();
    }

    /**
     * Permite modificar la campa�a a la que pertenece una consulta. Devuelve OK o un
     * mensaje de error en Mensaje. modificar_consulta
     *
     * @param Objeto
     */
    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_consulta( :token, :idConsulta, :idDifusion,'
                . '  :IP, :userAgent, :app)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idConsulta' => $Objeto->IdConsulta,
            ':idDifusion' => $Objeto->IdDifusion,
        ]);
        
        return $query->queryScalar();
    }
}
