<?php

namespace backend\models;

use Yii;
use yii\base\Model;

class GestorRoles extends Model
{
    /**
     * Permite dar de alta un rol, controlando que el nombre no se encuentre en uso.
     * Devuelve OK + el id del rol creado o un mensaje de error en Mensaje.
     * alta_rol
     */
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_rol( :token, :rol, :observaciones, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':rol' => $Objeto->Rol,
            ':observaciones' => $Objeto->Observaciones,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite modificar un rol, controlando que el nombre no se encuentre en uso.
     * Devuelve OK o un mensaje de error.
     * modificar_rol
     */
    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_rol( :token, :idRol, :rol, :observaciones,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idRol' => $Objeto->IdRol,
            ':rol' => $Objeto->Rol,
            ':observaciones' => $Objeto->Observaciones,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite borrar un rol controlando que no existan permisos asociados.
     * Devuelve OK o un mensaje de error en Mensaje.
     * borrar_rol
     */
    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_rol( :token, :idRol, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idRol' => $Objeto->IdRol,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite buscar roles filtr�ndolos por una cadena de b�squeda e indicando si se
     * incluyen o no los dados de baja en pIncluyeBajas [S | N].
     * buscar_roles
     *
     * @param Cadena
     * @param IncluyeBajas
     */
    public function Buscar($Cadena = '', $IncluyeBajas = 'N')
    {
        $sql = 'CALL dsp_buscar_roles( :cadena, :incluyeBajas )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena,
            ':incluyeBajas' => $IncluyeBajas,
        ]);
        
        return $query->queryAll();
    }
}
