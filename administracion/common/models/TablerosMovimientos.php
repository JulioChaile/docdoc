<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 18-May-2018 18:19:34
 */
class TablerosMovimientos extends Model
{
    public $IdTipoMovimientoTablero;
    public $IdEstudio;
    public $IdTipoMov;
    public $Orden;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';
     
    /*
    public function attributeLabels()
    {
        return [
            'IdEstudio' => 'Estudio'
        ];
    }
    */

    public function rules()
    {
        return [
            [['IdEstudio','Orden', 'IdTipoMov'], 'required', 'on' => self::_ALTA],
            [['IdTipoMovimientoTablero', 'Orden', 'IdTipoMov'], 'required', 'on' => self::_MODIFICAR],
            [['IdTipoMovimientoTablero', 'IdEstudio', 'Orden', 'IdTipoMov'], 'safe']
        ];
    }

    /**
     * Permite instanciar un origen de casos desde la base de datos. dame_origen
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_tablero_movimiento( :idTipoMovimientoTablero )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idTipoMovimientoTablero' => $this->IdTipoMovimientoTablero,
        ]);
        
        $this->attributes = $query->queryOne();
    }
}
