<?php
namespace backend\models;

use common\components\FechaHelper;
use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 11-Apr-2018 11:20:15
 */
class Difusiones extends Model
{
    public $IdDifusion;
    public $Difusion;
    public $FechaInicio;
    public $FechaFin;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';
    
    public function rules()
    {
        return [
            [['Difusion', 'FechaInicio', 'FechaFin'], 'required', 'on' => self::_ALTA],
            [['IdDifusion', 'Difusion', 'FechaInicio', 'FechaFin'], 'required', 'on' => self::_MODIFICAR],
            [['IdDifusion', 'Difusion', 'FechaInicio', 'FechaFin'], 'safe']
        ];
    }
    
    /**
     * Permite instanciar una campa�a de difusi�n desde la base de datos. dame_difusion
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_difusion( :idDifusion )';
    
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idDifusion' => $this->IdDifusion
        ]);
        
        $this->attributes = $query->queryOne();
        $this->FechaInicio = FechaHelper::formatearDateLocal($this->FechaInicio);
        $this->FechaFin = FechaHelper::formatearDateLocal($this->FechaFin);
    }
}
