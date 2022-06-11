<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 11-Apr-2018 11:20:15
 */
class CiasSeguro extends Model
{
    public $IdCiaSeguro;
    public $CiaSeguro;
    public $Telefono;
    public $Direccion;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';
    
    public function rules()
    {
        return [
            [['CiaSeguro'], 'required', 'on' => self::_ALTA],
            [['IdCiaSeguro', 'CiaSeguro'], 'required', 'on' => self::_MODIFICAR],
            [['IdCiaSeguro', 'CiaSeguro', 'Telefono', 'Direccion'], 'safe']
        ];
    }
    
    /**
     * Permite instanciar una campa�a de difusi�n desde la base de datos. dame_difusion
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_cia_seguro( :idCiaSeguro )';
    
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCiaSeguro' => $this->IdCiaSeguro
        ]);
        
        $this->attributes = $query->queryOne();
    }
}
