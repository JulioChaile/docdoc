<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 25-May-2018 19:59:22
 */
class EstadoAmbitoGestion extends Model
{
    public $IdEstadoAmbitoGestion;
    public $EstadoAmbitoGestion;
    public $Mensaje;

    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';

    public function rules()
    {
        return [
            [['IdEstadoAmbitoGestion', 'EstadoAmbitoGestion'], 'required', 'on' => self::_MODIFICAR],
            ['EstadoAmbitoGestion', 'required', 'on' => self::_ALTA],
            [$this->attributes(), 'safe']
        ];
    }

    public function Dame()
    {
        $sql = 'CALL dsp_dame_estadoambitogestion( :IdEstadoAmbitoGestion )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':IdEstadoAmbitoGestion' => $this->IdEstadoAmbitoGestion
        ]);
        
        $this->attributes = $query->queryOne();
    }
}
