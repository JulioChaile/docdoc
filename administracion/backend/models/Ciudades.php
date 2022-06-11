<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 02-Apr-2018 17:48:45
 */
class Ciudades extends Model
{
    public $IdCiudad;
    public $IdProvincia;
    public $Ciudad;
    public $CodPostal;

    public function rules()
    {
        return [
            [['IdCiudad', 'IdProvincia', 'Ciudad', 'CodPostal'], 'safe']
        ];
    }

    /**
     * Permite instanciar una ciudad desde la base de datos. dsp_dame_ciudad
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_ciudad( :idCiudad )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCiudad' => $this->IdCiudad,
        ]);
        
        $this->attributes = $query->queryOne();
    }
}
