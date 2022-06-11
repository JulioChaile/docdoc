<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 16-Feb-2017 20:36:56
 */
class Roles extends Model
{
    public $IdRol;
    public $Rol;
    public $Observaciones;
    public $Estado;

    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';
    const ESTADOS = [
        'A' => 'Activo',
        'B' => 'Baja'
    ];
    
    public function rules()
    {
        return [
            //Alta
            [['Rol'], 'required', 'on' => self::_ALTA],
            //Modificar
            [['IdRol', 'Rol', 'Observaciones'], 'required', 'on' => self::_MODIFICAR],
            //Safe
            [['IdRol', 'Rol', 'Observaciones', 'Estado'], 'safe']
        ];
    }
    
    /**
     * Permite instanciar un Rol desde la base de datos. dsp_dame_rol
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_rol( :idRol )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idRol' => $this->IdRol,
        ]);
        
        $this->attributes = $query->queryOne();
    }

    /**
     * Permite cambiar el estado de un rol a Activo, controlando que el mismo no este
     * activo ya. Devuelve OK o un mensaje de error en Mensaje. dsp_activar_rol
     */
    public function Activar()
    {
        $sql = 'CALL dsp_activar_rol( :token, :idRol, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idRol' => $this->IdRol,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite cambiar el estado de un Rol a Baja, controlando que el mismo no este
     * dado de baja ya. Devuelve OK o un mensaje de error en Mensaje. dsp_darbaja_rol
     */
    public function DarBaja()
    {
        $sql = 'CALL dsp_darbaja_rol( :token, :idRol, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idRol' => $this->IdRol,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite listar todos los permisos asociados al rol. dsp_listar_permisos_rol
     */
    public function ListarPermisos()
    {
        $sql = 'CALL dsp_listar_permisos_rol( :idRol )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idRol' => $this->IdRol,
        ]);
        
        return $query->queryAll();
    }

    /**
     * Permite actualizar los permisos de un rol. Elimina todos los permisos actuales
     * y da de alta los contenidos en pPermisos. Renueva el token de los usuarios que
     * tengan el rol indicado. Devuelve OK o un mensaje de error en Mensaje.
     * dsp_actualizar_permisos_rol
     *
     * @param Permisos
     */
    public function ActualizarPermisos($Permisos)
    {
        $sql = 'CALL dsp_actualizar_permisos_rol( :token, :idRol, :permisos,'
                . ' :IP, :userAgent, :app)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idRol' => $this->IdRol,
            ':permisos' => $Permisos,
        ]);
        
        return $query->queryScalar();
    }
}
