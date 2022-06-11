<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 11-Apr-2018 19:37:15
 */
class ObjetivosEstudio extends Model
{
    public $IdObjetivoEstudio;
    public $IdEstudio;
    public $ObjetivoEstudio;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';

    public function rules()
    {
        return [
            [['IdEstudio','ObjetivoEstudio'], 'required', 'on' => self::_ALTA],
            [['IdObjetivoEstudio', 'IdEstudio', 'ObjetivoEstudio'], 'required', 'on' => self::_MODIFICAR],
            [['IdObjetivoEstudio', 'IdEstudio', 'ObjetivoEstudio'], 'safe']
        ];
    }

    /**
     * Permite instanciar un objetivo  desde la base de datos. dsp_dame_estadocaso
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_objetivoestudio( :idObjetivoEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idObjetivoEstudio' => $this->IdObjetivoEstudio,
        ]);
        
        $this->attributes = $query->queryOne();
    }
}
