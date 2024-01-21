<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 11-Apr-2018 19:37:15
 */
class ComboObjetivos extends Model
{
    public $IdComboObjetivos;
    public $IdEstudio;
    public $ComboObjetivos;
    public $Objetivos;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';

    //public function rules()
    //{
    //    return [
    //        [['IdEstudio','ObjetivoEstudio', 'IdTipoMov', 'ColorMov'], 'required', 'on' => self::_ALTA],
    //        [['IdObjetivoEstudio', 'IdEstudio', 'ObjetivoEstudio', 'IdTipoMov', 'ColorMov'], 'required', 'on' => self::_MODIFICAR],
    //        [['IdObjetivoEstudio', 'IdEstudio', 'ObjetivoEstudio', 'IdTipoMov', 'ColorMov'], 'safe']
    //    ];
    //}

    /**
     * Permite instanciar un objetivo  desde la base de datos. dsp_dame_estadocaso
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_comboobjetivosestudio( :idComboObjetivos )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idComboObjetivos' => $this->IdComboObjetivos,
        ]);
        
        $this->attributes = $query->queryOne();
    }
}
