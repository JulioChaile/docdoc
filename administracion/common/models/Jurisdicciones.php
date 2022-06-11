<?php
namespace common\models;

use Yii;
use yii\base\Model;

class Jurisdicciones extends Model
{
    public $IdJurisdiccion;
    public $Jurisdiccion;
    public $Estado;

    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';
    const ESTADOS = [
        'A' => 'Activa',
        'B' => 'Baja'
    ];
    
    public function rules()
    {
        return [
            ['Jurisdiccion', 'required', 'on' => self::_ALTA],
            [['IdJurisdiccion', 'Jurisdiccion'], 'required', 'on' => self::_MODIFICAR],
            [['IdJurisdiccion', 'Jurisdiccion', 'Estado'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'Jurisdiccion' => 'Jurisdicción',
        ];
    }

    /**
     * Permite instanciar una jurisdicc�n desde la base de datos. dsp_dame_jurisdiccion
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_jurisdiccion( :idJurisdiccion )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idJurisdiccion' => $this->IdJurisdiccion
        ]);
        
        $this->attributes = $query->queryOne();
    }

    /**
     * Permite cambiar el estado de una jurisdicci�n a Activa. Devuelve OK o un
     * mensaje de error en Mensaje.
     * dsp_activar_jurisdiccion
     */
    public function Activar()
    {
        $sql = 'CALL dsp_activar_jurisdiccion( :token, :idJurisdiccion,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idJurisdiccion' => $this->IdJurisdiccion
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite cambiar el estado de una jurisdicci�n a Baja. Devuelve OK o un mensaje
     * de error en Mensaje.
     * dsp_darbaja_jurisdiccion
     */
    public function DarBaja()
    {
        $sql = 'CALL dsp_darbaja_jurisdiccion( :token, :idJurisdiccion,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idJurisdiccion' => $this->IdJurisdiccion
        ]);
        
        return $query->queryScalar();
    }
}
