<?php

namespace common\models;

use common\models\Usuarios;
use Yii;
use yii\web\IdentityInterface;

class UsuariosEstudio extends Usuarios implements IdentityInterface
{
    public $IdEstudio;
    public $IdUsuario;
    public $IdUsuarioPadre;
    public $IdEstudioPadre;
    public $IdRolEstudio;
    public $Estado;
    public $Observaciones;
    
    //Derivados
    public $IdConsulta;
    public $RolEstudio;
    public $Estudio;
    
    public function attributeLabels()
    {
        $labels = [
            'IdRolEstudio' => 'Rol',
//            'IdEstudio' => 'Estudio',
//            'IdUsuarioPadre' => 'Usuario supervisor'
        ];
        
        return array_merge($labels, parent::attributeLabels());
    }
    
    public function rules()
    {
        $rules = [
            [['IdEstudio', 'IdUsuario', 'IdUsuarioPadre', 'IdEstudioPadre', 'IdRolEstudio',
                'Estado','RolEstudio', 'IdConsulta', 'Estudio'], 'safe']
        ];
        
        return array_merge($rules, parent::rules());
    }

    /**
     * Permite instanciar un usuario de estudio desde la base de datos.
     * dsp_dame_usuario_estudio
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_usuario_estudio( :idUsuario )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idUsuario' => $this->IdUsuario,
        ]);
        
        $this->attributes = $query->queryOne();
    }
    
    /**
     * Permite cambiar el estado de un abogado de un estudio a Activo. Devuelve OK o
     * un mensaje de error en Mensaje. dsp_activar_usuario_estudio
     */
    public function Activar()
    {
        $sql = 'CALL dsp_activar_usuario_estudio( :token, :idEstudio, :idUsuario,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $this->IdEstudio,
            ':idUsuario' => $this->IdUsuario,
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite cambiar el estado de un abogado de un estudio a Baja. Devuelve OK o un
     * mensaje de error en Mensaje. dsp_darbaja_usuario_estudio
     */
    public function DarBaja()
    {
        $sql = 'CALL dsp_darbaja_usuario_estudio( :token, :idEstudio, :idUsuario,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $this->IdEstudio,
            ':idUsuario' => $this->IdUsuario,
        ]);

        return $query->queryScalar();
    }
    
    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID. Null
     * should be returned if such an identity cannot be found or the identity is not
     * in an active state (disabled, deleted, etc.)
     *
     * @param id
     */
    public static function findIdentity($id)
    {
        $usuario = new UsuariosEstudio();
        
        $usuario->IdUsuario = $id;
        
        $usuario->Dame();
        
        return $usuario;
    }
}
