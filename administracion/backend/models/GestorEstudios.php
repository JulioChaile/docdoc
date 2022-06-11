<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 26-Mar-2018 11:34:30
 */
class GestorEstudios extends Model
{

    /**
     * Permite crear un estudio controlando que el nombre no se encuentre en uso ya.
     * Devuelve OK + el id del estudio creado o un mensaje de error en Mensaje.
     * alta_estudio
     *
     * @param Objeto
     */
    public function Alta($Objeto)
    {
        $sql = 'CALL dsp_alta_estudio( :token, :idCiudad, :estudio, :domicilio,'
                . ' :telefono, :especialidades, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idCiudad' => $Objeto->IdCiudad,
            ':estudio' => $Objeto->Estudio,
            ':domicilio' => $Objeto->Domicilio,
            ':telefono' => $Objeto->Telefono,
            ':especialidades' => $Objeto->Especialidades
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite modificar un estudio controlando que el nombre no se encuentre en uso
     * ya. Devuelve OK o un mensaje de error en Mensaje. modificar_estudio
     *
     * @param Objeto
     */
    public function Modificar($Objeto)
    {
        $sql = 'CALL dsp_modificar_estudio( :token, :idEstudio, :idCiudad, :estudio,'
                . ' :domicilio, :telefono, :especialidades, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $Objeto->IdEstudio,
            ':idCiudad' => $Objeto->IdCiudad,
            ':estudio' => $Objeto->Estudio,
            ':domicilio' => $Objeto->Domicilio,
            ':telefono' => $Objeto->Telefono,
            ':especialidades' => $Objeto->Especialidades
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite buscar estudios filtr�ndolos por una cadena de b�squeda e indicando si
     * se incluyen o no los dados de baja. Ordena por Estudio. buscar_estudios
     *
     * @param Cadena
     * @param IncluyeBajas    S: Si - N: No
     */
    public function Buscar($Cadena = '', $IncluyeBajas = 'N')
    {
        $sql = 'CALL dsp_buscar_estudios( :cadena, :incluyeBajas )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena,
            ':incluyeBajas' => $IncluyeBajas
        ]);
        
        return $query->queryAll();
    }

    /**
     * Permite borrar un estudio controlando que no tenga abogados ni casos asociados.
     * Devuelve OK o un mensaje de error en Mensaje. borrar_estudio
     *
     * @param Objeto
     */
    public function Borrar($Objeto)
    {
        $sql = 'CALL dsp_borrar_estudio( :token, :idEstudio, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $Objeto->IdEstudio,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite crear un Estudio, un Usuario, agregar el usuario al estudio y agregar el usuario a un caso.
     * Devuelve OK + el id del Usuario creado o un Mensaje de error en Mensaje.
     * dsp_alta_cliente_invitacion
     * 
     * @param Estudio
     * @param Usuario
     * @param Caso
     */
    public function AltaClienteInvitacion($Estudio, $Usuario, $Caso)
    {
        $sql = 'CALL dsp_alta_cliente_invitacion( :idCiudad, :estudio, :domicilio,'
        . ' :telefono, :especialidades, :idCaso, :nombres, :apellidos, :usuario, :password, :email, :telefonoUsuario, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);

        Yii::info($Usuario->Password);
        
        $query->bindValues([
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idCiudad' => $Estudio->IdCiudad,
            ':estudio' => $Estudio->Estudio,
            ':domicilio' => $Estudio->Domicilio,
            ':telefono' => $Estudio->Telefono,
            ':especialidades' => $Estudio->Especialidades,
            ':idCaso' => $Caso->IdCaso,
            ':nombres' => $Usuario->Nombres,
            ':apellidos' => $Usuario->Apellidos,
            ':usuario' => $Usuario->Usuario,
            ':telefonoUsuario' => $Usuario->TelefonoUsuario,
            ':password' => password_hash($Usuario->Password, PASSWORD_DEFAULT),
            ':email' => $Usuario->Email
        ]);
        
        return $query->queryScalar();
    }
}
