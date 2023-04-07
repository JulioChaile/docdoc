<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 25-May-2018 19:59:22
 */
class EstadoCasosPendientes extends Model
{
    public $IdEstadoCasoPendiente;
    public $EstadoCasoPendiente;

    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';

    public function rules()
    {
        return [
            [['IdEstadoCasoPendiente', 'EstadoCasoPendiente'], 'required', 'on' => self::_MODIFICAR],
            ['EstadoCasoPendiente', 'required', 'on' => self::_ALTA],
            [$this->attributes(), 'safe']
        ];
    }

    public function Dame()
    {
        $sql = 'CALL dsp_dame_estadocasospendientes( :IdEstadoCasoPendiente )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':IdEstadoCasoPendiente' => $this->IdEstadoCasoPendiente
        ]);
        
        $this->attributes = $query->queryOne();
    }
}
