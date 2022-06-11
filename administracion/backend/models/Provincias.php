<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 02-Apr-2018 17:49:25
 */
class Provincias extends Model
{
    public $IdProvincia;
    public $Provincia;

    public function rules()
    {
        return [
            [['IdProvincia', 'Provincia'], 'safe']
        ];
    }

    /**
     * Permite instanciar una provinca desde la base de datos. dsp_dame_provincia
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_provincia( :idProvincia )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idProvincia' => $this->IdProvincia
        ]);
        
        $this->attributes = $query->queryOne();
    }
}
