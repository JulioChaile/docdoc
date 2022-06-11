<?php
namespace common\models;

use Yii;
use yii\base\Model;

class UsuariosCaso extends Model
{
    public $IdUsuarioCaso;
    public $IdCaso;
    public $IdEstudio;
    public $IdUsuario;
    public $Permiso;
    public $EsCreador;

    public function rules()
    {
        return [
            [$this->attributes(), 'safe']
        ];
    }

    /**
     * Permite instanciar un usuario caso desde la base de datos.
     * dsp_dame_usuario_caso
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_usuario_caso( :idUsuario, :idCaso )';
        
        $query = Yii::$app->db->createCommand($sql);
    
        $query->bindValues([
            ':idUsuario' => $this->IdUsuario,
            ':idCaso' => $this->IdCaso
        ]);
        
        $this->attributes = $query->queryOne();
    }
    
}
