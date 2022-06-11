<?php
namespace backend\models;

use yii\base\Model;

/**
 * @version 1.0
 * @created 27-Jun-2018 23:40:35
 */
class RolesEstudio extends Model
{
    public $IdRolEstudio;
    public $IdEstudio;
    public $RolEstudio;
    
    
    public function rules()
    {
        return [
            [['IdRolEstudio', 'IdEstudio', 'RolEstudio'], 'safe']
        ];
    }
}
