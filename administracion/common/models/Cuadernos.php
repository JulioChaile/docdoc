<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 11-Apr-2018 19:37:15
 */
class Cuadernos extends Model
{
    public $IdCuaderno;
    public $IdEstudio;
    public $Cuaderno;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';

    public function rules()
    {
        return [
            [['IdEstudio','Cuaderno'], 'required', 'on' => self::_ALTA],
            [['IdCuaderno', 'IdEstudio', 'Cuaderno'], 'required', 'on' => self::_MODIFICAR],
            [['IdCuaderno', 'IdEstudio', 'Cuaderno'], 'safe']
        ];
    }

    public function Dame()
    {
        $sql = 'CALL dsp_dame_cuaderno( :idCuaderno )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCuaderno' => $this->IdCuaderno,
        ]);
        
        $this->attributes = $query->queryOne();
    }
}
