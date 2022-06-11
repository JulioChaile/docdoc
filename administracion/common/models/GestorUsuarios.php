<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 16-Feb-2017 20:36:56
 */
class GestorUsuarios extends Model
{
    /**
     * Permite dar de alta un Usuario, controlando que el nombre de usuario no exista
     * ya. El usuario se crea con la variable DebeCambiarPass = 'S' para que se cambie
     * la contrase�a en el primer inicio de sesi�n.
     * Devuelve OK + el id del usuario creado o un mensaje de error en Mensaje.
     * alta_usuario
     */
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_usuario( :token, :idRol, :nombres, :apellidos,'
                . ' :usuario, :password, :email, :observaciones, :telefono, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idRol' => $Objeto->IdRol,
            ':nombres' => $Objeto->Nombres,
            ':apellidos' => $Objeto->Apellidos,
            ':usuario' => $Objeto->Usuario,
            ':password' => password_hash($Objeto->Password, PASSWORD_DEFAULT),
            ':email' => $Objeto->Email,
            ':observaciones' => $Objeto->Observaciones,
            ':telefono' => $Objeto->TelefonoUsuario
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite dar de alta un Usuario cliente, controlando que el nombre de usuario no exista
     * ya.
     * Devuelve OK + el id del usuario creado o un mensaje de error en Mensaje.
     * alta_usuario
     */
    public function AltaCliente($Objeto)
    {
        $sql = 'CALL dsp_alta_cliente(:idRol, :nombres, :apellidos,'
                . ' :usuario, :password, :email, :observaciones, :telefono, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => 'D',
            ':idRol' => 2,
            ':nombres' => $Objeto->Nombres,
            ':apellidos' => $Objeto->Apellidos,
            ':usuario' => $Objeto->Usuario,
            ':password' => password_hash($Objeto->Password, PASSWORD_DEFAULT),
            ':email' => $Objeto->Email,
            ':observaciones' => $Objeto->Observaciones,
            ':telefono' => $Objeto->TelefonoUsuario
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite modificar los datos de un usuario controlando que el nombre de usuario
     * no se encuentre en uso ya.
     * Devuelve OK o un mensaje de error en Mensaje.
     * modificar_usuario
     */
    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_usuario( :token, :idUsuario, :idRol, :usuario, :nombres,'
                . ' :apellidos, :email, :observaciones, :telefono, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idUsuario' => $Objeto->IdUsuario,
            ':idRol' => $Objeto->IdRol,
            ':usuario' => $Objeto->Usuario,
            ':nombres' => $Objeto->Nombres,
            ':apellidos' => $Objeto->Apellidos,
            ':email' => $Objeto->Email,
            //':password' => password_hash($Objeto->Password, PASSWORD_DEFAULT),
            ':observaciones' => $Objeto->Observaciones,
            ':telefono' => $Objeto->TelefonoUsuario
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite borrar un usuario controlando que el mismo no tenga movimientos de caja
     * asociados.
     * Devuelve OK o un mensaje de error en Mensaje.
     * borrar_usuario
     */
    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_usuario( :token, :idUsuario, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idUsuario' => $Objeto->IdUsuario,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite buscar usuarios filtr�ndolos por una cadena de b�squeda e indicando si
     * se incluyen o no las bajas en pIncluyeBajas = [S: Si | N: No].
     * Ordena por Apellido/s, Nombre/s.
     * buscar_usuarios
     *
     * @param Cadena
     * @param IncluyeBajas
     */
    public function Buscar($Tipo = 'T', $Cadena = '', $IncluyeBajas = 'N')
    {
        $sql = 'CALL dsp_buscar_avanzado_usuarios( :tipo, :cadena, :incluyeBajas )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':tipo' => $Tipo,
            ':cadena' => $Cadena,
            ':incluyeBajas' => $IncluyeBajas,
        ]);
        
        return $query->queryAll();
    }
}
