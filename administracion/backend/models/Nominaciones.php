<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 12-Apr-2018 19:30:33
 */
class Nominaciones extends Model
{
    public $IdNominacion;
    public $IdJuzgado;
    public $Nominacion;
    public $Estado;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';
    const ESTADOS = [
        'A' => 'Activa',
        'B' => 'Baja'
    ];
    
    public function attributeLabels()
    {
        return [
            'IdJuzgado' => 'Juzgado'
        ];
    }

    public function rules()
    {
        return [
            [['IdJuzgado', 'Nominacion'], 'required', 'on' => self::_ALTA],
            [['IdNominacion', 'Nominacion'], 'required', 'on' => self::_MODIFICAR],
            [['IdNominacion', 'IdJuzgado', 'Nominacion', 'Estado'], 'safe'],
        ];
    }


    /**
     * Permite instanciar una nominaci�n desde la base de datos. dsp_dame_nominacion
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_nominacion( :idNominacion )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idNominacion' => $this->IdNominacion,
        ]);
        
        $this->attributes = $query->queryOne();
    }

    /**
     * Permite cambiar el estado de una nominaci�n a Activo. Devuelve OK o un mensaje
     * de error en Mensaje. dsp_activar_nominacion
     */
    public function Activar()
    {
        $sql = 'CALL dsp_activar_nominacion( :token, :idNominacion, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idNominacion' => $this->IdNominacion,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite cambiar el estado de una nominaci�n a Baja. Devuelve OK o un mensaje de
     * error en Mensaje. dsp_darbaja_nominacion
     */
    public function DarBaja()
    {
        $sql = 'CALL dsp_darbaja_nominacion( :token, :idNominacion, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idNominacion' => $this->IdNominacion,
        ]);
        
        return $query->queryScalar();
    }
}
