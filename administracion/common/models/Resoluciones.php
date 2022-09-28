<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\components\FechaHelper;

/**
 * @version 1.0
 * @created 25-May-2018 19:59:22
 */
class Resoluciones extends Model
{
    public $IdResolucionSMVM;
    public $Resolucion;
    public $FechaResolucion;
    public $MontoResolucion;

    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';

    public function rules()
    {
        return [
            [['IdResolucion', 'Resolucion', 'FechaResolucion', 'MontoResolucion'], 'required', 'on' => self::_MODIFICAR],
            [['Resolucion', 'FechaResolucion', 'MontoResolucion'], 'required', 'on' => self::_ALTA],
            [$this->attributes(), 'safe']
        ];
    }

    public function Dame()
    {
        $sql = 'SELECT * FROM ResolucionesSMVM WHERE IdResolucionSMVM = :idResolucionSMVM';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':IdResolucion' => $this->IdResolucionSMVM
        ]);
        
        $this->attributes = $query->queryOne();
    }
}
