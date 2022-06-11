<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 11-Apr-2018 19:37:15
 */
class EstadosCaso extends Model
{
    public $IdEstadoCaso;
    public $IdEstudio;
    public $EstadoCaso;
    public $Estado;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';
    const ESTADOS = [
        'A' => 'Activo',
        'B' => 'Baja'
    ];
    
    public function attributeLabels()
    {
        return [
            'IdEstudio' => 'Estudio jurídico'
        ];
    }

    public function rules()
    {
        return [
            [['IdEstudio','EstadoCaso'], 'required', 'on' => self::_ALTA],
            [['IdEstadoCaso', 'IdEstudio', 'EstadoCaso'], 'required', 'on' => self::_MODIFICAR],
            [['IdEstadoCaso', 'IdEstudio', 'EstadoCaso', 'Estado'], 'safe']
        ];
    }

    /**
     * Permite instanciar un EstadoCaso desde la base de datos. dsp_dame_estadocaso
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_estadocaso( :idEstadoCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstadoCaso' => $this->IdEstadoCaso,
        ]);
        
        $this->attributes = $query->queryOne();
    }

    /**
     * Permite cambiar el estado de un EstadoCaso a Activo. Devuelve OK o un mensaje
     * de error en Mensaje.
     * dsp_activar_estadocaso
     */
    public function Activar()
    {
        $sql = 'CALL dsp_activar_estadocaso( :token, :idEstadoCaso, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstadoCaso' => $this->IdEstadoCaso,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite cambiar el estado de un EstadoCaso a Baja. Devuelve OK o un mensaje de
     * error en Mensaje.
     * dsp_darbaja_estadocaso
     */
    public function DarBaja()
    {
        $sql = 'CALL dsp_darbaja_estadocaso( :token, :idEstadoCaso, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstadoCaso' => $this->IdEstadoCaso,
        ]);
        
        return $query->queryScalar();
    }
}
