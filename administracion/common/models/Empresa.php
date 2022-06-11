<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 21-Mar-2016 09:15:43
 */
class Empresa extends Model
{
    public $Parametro;
    public $IdEstudio;
    public $Descripcion;
    public $Rango;
    public $Valor;
    public $EsEditable;
    public $EsInicial;

    const SCENARIO_EDITAR = 'editar';
    
    /**
     * Etiquetas de los campos.
     *
     * @return Array Etiquetas
     */
    public function attributeLabels()
    {
        return [
            'Parametro' => 'Parámetro'
        ];
    }

    /**
     * Reglas para validar los formularios.
     *
     * @return Array Reglas de validación
     */
    public function rules()
    {
        return [
            ['Valor', 'trim'],
            // Editar
            [['Parametro', 'Valor'], 'required', 'on' => self::SCENARIO_EDITAR],
            // Safe
            [['IdEstudio', 'Valor', 'Parametro', 'Descripcion'], 'safe'],
        ];
    }

    /**
     * Permite traer en formato resultset los parámetros de la empresa que necesitan
     *    cargarse al inicio de sesión (EsInicial = S). dame_datos_empresa
     */
    public function DameDatos()
    {
        $sql = "CALL dsp_dame_datos_empresa () ";

        $query = Yii::$app->db->createCommand($sql);

        return $query->queryAll();
    }

    /**
     * Permite buscar los parámetros editables del sistema dada una cadena de búsqueda.
     * dsp_buscar_parametros
     *
     * @param cadena    Cadena vacía para todos
     * @param idEstudio 0 para administración
     */
    public function BuscarParametros($cadena, $idEstudio=0)
    {
        $sql = 'CALL dsp_buscar_parametros( :cadena, :idestudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $cadena,
            ':idestudio' => $idEstudio
        ]);
        
        return $query->queryAll();
    }

    /**
     * Permite modificar un parámetro de empresa indicando el nuevo valor.
     */
    public function Modificar()
    {
        $sql = 'CALL dsp_cambiar_parametro( :token, :idestudio, :parametro, :valor,
        :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idestudio' => 0,
            ':parametro' => $this->Parametro,
            ':valor' => $this->Valor
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Procedimiento que sirve para instanciar un parámetro del sistema desde la base
     * de datos. ssp_dame_parametro
     *
     * @param Parametro
     */
    public function DameParametro($Parametro)
    {
        $sql = "CALL dsp_dame_parametro ( :parametro ) ";

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':parametro' => $Parametro,
        ]);

        $this->attributes = $query->queryOne();
    }
    
    public function BuscarPadron($Tipo = 'T', $Cadena = '')
    {
        $sql = 'CALL dsp_buscar_padron( :tipo, :cadena )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':tipo' => $Tipo,
            ':cadena' => $Cadena
        ]);
        
        return $query->queryAll();
    }
}
