<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 11-Apr-2018 11:20:19
 */
class Consultas extends Model
{
    public $IdConsulta;
    public $IdDifusion;
    public $Apynom;
    public $Telefono;
    public $Texto;
    public $FechaAlta;
    public $Estado;

    //Derivados
    public $IdDerivacionConsulta;
    public $Derivaciones;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';
    const ESTADOS = [
        'A' => 'Activa',
        'B' => 'Baja',
        'D' => 'Derivada'
    ];
    
    public function attributeLabels()
    {
        return [
            'IdDifusion' => 'Campaña',
            'Apynom' => 'Apellido/s y Nombre/s',
            'Telefono' => 'Teléfono'
        ];
    }
    
    public function rules()
    {
        return [
            [['Apynom', 'Telefono', 'Texto'], 'required', 'on' => self::_ALTA],
            [['IdConsulta', 'IdDifusion'], 'required', 'on' => self::_MODIFICAR],
            [$this->attributes(), 'safe']
        ];
    }

    /**
     * Permite instanciar una consulta desde al base de datos. dsp_dame_consulta
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_consulta( :idConsulta )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idConsulta' => $this->IdConsulta
        ]);
        
        $this->attributes = $query->queryOne();
    }

    /**
     * Permite cambiar el estado de una consulta a Activa, siempre que no est� activa
     * ya. Devuelve OK o un mensaje de error en Mensaje. dsp_activar_consulta
     */
    public function Activar()
    {
        $sql = 'CALL dsp_activar_consulta( :token, :idConsulta, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idConsulta' => $this->IdConsulta
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite cambiar el estado de una consulta a Baja, siempre que no se encuentre
     * dada de baja ya ni Derivada. Devuelve OK o un mensaje de error en Mensaje.
     * dsp_darbaja_consulta
     */
    public function DarBaja()
    {
        $sql = 'CALL dsp_darbaja_consulta( :token, :idConsulta, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idConsulta' => $this->IdConsulta
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite derivar una consulta cambiando su estado a Derivada e indicando el
     * estudio al cual se deriva, controlando que la consulta se encuentre Activa y no
     * haya sido derivada ya. Devuelve OK o un mensaje de error en Mensaje.
     * dsp_derivar_consulta
     *
     * @param Objeto
     */
    public function Derivar($Objeto)
    {
        $sql = 'CALL dsp_derivar_consulta( :token, :idConsulta, :idEstudio,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idConsulta' => $Objeto->IdConsulta,
            ':idEstudio' => $Objeto->IdEstudio
        ]);
        
        return $query->queryScalar();
    }
}
