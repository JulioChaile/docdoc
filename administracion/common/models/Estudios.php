<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 02-Apr-2018 16:54:14
 */
class Estudios extends Model
{
    public $IdEstudio;
    public $IdCiudad;
    public $Estudio;
    public $Domicilio;
    public $Telefono;
    public $Estado;
    public $Especialidades;
    
    const _ALTA = 'alta';
    const _MODIFICAR = 'modificar';
    const ESTADOS = [
        'A' => 'Activo',
        'B' => 'Baja'
    ];
    
    //Derivados
    public $Ciudad;
    public $IdProvincia;
    public $Provincia;
    
    public function attributeLabels()
    {
        return [
            'IdProvincia' => 'Provincia',
            'IdCiudad' => 'Ciudad'
        ];
    }
    
    public function rules()
    {
        return [
            [['IdEstudio', 'IdCiudad', 'Estudio', 'Domicilio', 'Telefono'], 'required', 'on' => self::_MODIFICAR],
            [['IdCiudad', 'Estudio', 'Domicilio', 'Telefono'], 'required', 'on' => self::_ALTA],
            [$this->attributes(), 'safe']
        ];
    }

    /**
     * Permite instanciar un estudio desde la base de datos.
     * dsp_dame_esutdio
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_estudio( :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $this->IdEstudio
        ]);
        
        $this->attributes = $query->queryOne();
    }

    /**
     * Permite cambiar el estado de un estudio a Activo. Devuelve OK o un mensaje de
     * error en Mensaje.
     * dsp_activar_estudio
     */
    public function Activar()
    {
        $sql = 'CALL dsp_activar_estudio( :token, :idEstudio, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $this->IdEstudio
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite cambiar el estado de un estudio a Activo. Devuelve OK o un mensaje de
     * error en Mensaje. dsp_darbaja_estudio
     */
    public function DarBaja()
    {
        $sql = 'CALL dsp_darbaja_estudio( :token, :idEstudio, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $this->IdEstudio
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite buscar usuarios en un estudio filtr�ndolos por una cadena de b�squeda e
     * indicando si se incluyen o no los dados de baja en pIncluyeBajas = [S|N].
     * Ordena por Apellidos, Nombres. dsp_buscar_usuarios_estudio
     *
     * @param Cadena
     * @param IncluyeBajas    S: Si - N: No
     */
    public function BuscarUsuarios($Cadena = '', $IncluyeBajas = 'N')
    {
        $sql = 'CALL dsp_buscar_usuarios_estudio( :idEstudio, :cadena, :incluyeBajas )';
        
        $query =Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $this->IdEstudio,
            ':cadena' => $Cadena,
            ':incluyeBajas' => $IncluyeBajas,
        ]);
        
        return $query->queryAll();
    }

    public function ListarEstudios($Cadena = '', $IncluyeBajas = 'N')
    {
        $sql = 'CALL dsp_buscar_estudios( :cadena, :incluyeBajas )';
        
        $query =Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena,
            ':incluyeBajas' => $IncluyeBajas,
        ]);
        
        return $query->queryAll();
    }

    /**
     * Permite crear un usuario abogado controlando que el documento, email y nombre
     * de usuario no se encuentre en uso ya. Asigna el usuario creado al estudio.
     * Devuelve OK + el id del usuario creado o un mensaje de error en Mensaje.
     * dsp_alta_usuario_estudio
     *
     * @param Objeto
     */
    public function AltaUsuario($Objeto)
    {
        $sql = 'CALL dsp_alta_usuario_estudio( :token, :idEstudio, :idUsuarioPadre,'
                . ' :idRolEstudio, :nombres, :apellidos, :usuario, :password, :email,'
                . ' :observaciones, :telefono, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $this->IdEstudio,
            ':idUsuarioPadre' => $Objeto->IdUsuarioPadre == '' ? null : $Objeto->IdUsuarioPadre,
            ':idRolEstudio' => $Objeto->IdRolEstudio,
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
     * Permite borrar un abogado de un estudio controlando que no tenga casos ni
     * juniors asociados. Devuelve OK o un mensaje de error en Mensaje.
     * dsp_borrar_usuario_estudio
     *
     * @param Objeto
     */
    public function BorrarUsuario($Objeto)
    {
        $sql = 'CALL dsp_borrar_usuario_estudio( :token, :idEstudio, :idUsuario,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $this->IdEstudio,
            ':idUsuario' => $Objeto->IdUsuario,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite listar los origenes de un estudio. Ordena por Origen.
     * dsp_listar_origenes
     */
    public function ListarOrigenes()
    {
        $sql = 'CALL dsp_listar_origenes( :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $this->IdEstudio,
        ]);
        
        return $query->queryAll();
    }
    
    /**
     * Permite listar todos los roles de un estudio. dsp_listar_roles_estudio
     */
    public function ListarRoles()
    {
        $sql = 'CALL dsp_listar_roles_estudio( :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $this->IdEstudio
        ]);
        
        return $query->queryAll();
    }
    
    /**
     * Permite dar de alta roles en un estudio siempre que no exista uno con el mismo
     * nombre. Devuelve OK + Id del rol de estudio creado o un mensaje de error en
     * Mensaje. dsp_alta_rol_estudio
     *
     * @param Objeto
     */
    public function AltaRol($Objeto)
    {
        $sql = 'CALL dsp_alta_rol_estudio( :token, :idEstudio, :rolEstudio,'
                . ' :IP, :userAgent, :app)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $this->IdEstudio,
            ':rolEstudio' => $Objeto->RolEstudio,
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite borrar un rol de un estudio siempre que no existan usuarios con ese rol
     * asignado. Devuelve OK o un mensaje de error en Mensaje. dsp_borrar_rol_estudio
     *
     * @param Objeto
     */
    public function BorrarRol($Objeto)
    {
        $sql = 'CALL dsp_borrar_rol_estudio( :token, :idRolEstudio,'
                . ':IP, :userAgent, :app)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idRolEstudio' => $Objeto->IdRolEstudio,
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite listar los Estados de Caso de un estudio. Ordena por EstadoCaso.
     * dsp_listar_estadoscaso_estudio
     */
    public function ListarEstadosCaso()
    {
        $sql = 'CALL dsp_listar_estadoscaso_estudio( :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $this->IdEstudio,
        ]);
        
        return $query->queryAll();
    }
    
    /**
     * Permite crear un EstadoCaso, controlando que el nombre no se encuentre en uso
     * ya. Devuelve OK + el id del estado-caso creado o un mensaje de error en Mensaje.
     * dsp_alta_estadocaso
     *
     * @param Objeto
     */
    public function AltaEstadoCaso($Objeto)
    {
        $sql = 'CALL dsp_alta_estadocaso( :token, :idEstudio, :estadoCaso,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $this->IdEstudio,
            ':estadoCaso' => $Objeto->EstadoCaso,
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite modificar un EstadoCaso, controlando que el nombre no se encuentre en
     * uso ya. Devuelve OK o un mensaje de error en Mensaje. dsp_modificar_estadocaso
     *
     * @param Objeto
     */
    public function ModificarEstadoCaso($Objeto)
    {
        $sql = 'CALL dsp_modificar_estadocaso( :token, :idEstadoCaso, :estadoCaso,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstadoCaso' => $Objeto->IdEstadoCaso,
            ':estadoCaso' => $Objeto->EstadoCaso,
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite borrar un EstadoCaso controlando que el mismo no tenga subestados
     * asociados. Devuelve OK o un mensaje de error en Mensaje. dsp_borrar_estadocaso
     *
     * @param Objeto
     */
    public function BorrarEstadoCaso($Objeto)
    {
        $sql = 'CALL dsp_borrar_estadocaso( :token, :idEstadoCaso,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstadoCaso' => $Objeto->IdEstadoCaso,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite listar los objetivos de un estudio. Ordena por objetivos.
     * dsp_listar_estadoscaso_estudio
     */
    public function ListarObjetivos()
    {
        $sql = 'CALL dsp_listar_objetivos_estudio( :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $this->IdEstudio,
        ]);
        
        return $query->queryAll();
    }
    
    /**
     * Permite crear un objetivo, controlando que el nombre no se encuentre en uso
     * ya. Devuelve OK + el id del estado-caso creado o un mensaje de error en Mensaje.
     * dsp_alta_objetivoestudio
     *
     * @param Objeto
     */
    public function AltaObjetivo($Objeto)
    {
        $sql = 'CALL dsp_alta_objetivoestudio( :token, :idEstudio, :objetivoEstudio,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $this->IdEstudio,
            ':objetivoEstudio' => $Objeto->ObjetivoEstudio,
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite modificar un Objetivo, controlando que el nombre no se encuentre en
     * uso ya. Devuelve OK o un mensaje de error en Mensaje. dsp_modificar_objetivoestudio
     *
     * @param Objeto
     */
    public function ModificarObjetivo($Objeto)
    {
        $sql = 'CALL dsp_modificar_objetivoestudio( :token, :idObjetivoEstudio, :objetivoEstudio,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idObjetivoEstudio' => $Objeto->IdObjetivoEstudio,
            ':objetivoEstudio' => $Objeto->ObjetivoEstudio,
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite borrar un objetivo. Devuelve OK o un mensaje de error en Mensaje. dsp_borrar_objetivoestudio
     *
     * @param Objeto
     */
    public function BorrarObjetivo($Objeto)
    {
        $sql = 'CALL dsp_borrar_objetivoestudio( :token, :idObjetivoEstudio,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idObjetivoEstudio' => $Objeto->IdObjetivoEstudio,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite listar los cuadernos de un estudio. Ordena por cuadernos.
     * dsp_listar_estadoscaso_estudio
     */
    public function ListarCuadernos()
    {
        $sql = 'CALL dsp_listar_cuadernos_estudio( :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $this->IdEstudio,
        ]);
        
        return $query->queryAll();
    }
    
    /**
     * Permite crear un objetivo, controlando que el nombre no se encuentre en uso
     * ya. Devuelve OK + el id del estado-caso creado o un mensaje de error en Mensaje.
     * dsp_alta_objetivoestudio
     *
     * @param Objeto
     */
    public function AltaCuaderno($Objeto)
    {
        $sql = 'CALL dsp_alta_cuaderno( :token, :idEstudio, :cuaderno,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $this->IdEstudio,
            ':cuaderno' => $Objeto->Cuaderno,
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite modificar un Objetivo, controlando que el nombre no se encuentre en
     * uso ya. Devuelve OK o un mensaje de error en Mensaje. dsp_modificar_objetivoestudio
     *
     * @param Objeto
     */
    public function ModificarCuaderno($Objeto)
    {
        $sql = 'CALL dsp_modificar_cuaderno( :token, :idCuaderno, :cuaderno,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idCuaderno' => $Objeto->IdCuaderno,
            ':cuaderno' => $Objeto->Cuaderno,
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite borrar un objetivo. Devuelve OK o un mensaje de error en Mensaje. dsp_borrar_objetivoestudio
     *
     * @param Objeto
     */
    public function BorrarCuaderno($Objeto)
    {
        $sql = 'CALL dsp_borrar_cuaderno( :token, :idCuaderno,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idCuaderno' => $Objeto->IdCuaderno,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite listar los eventos de un estudio.
     */
    public function BuscarEventos($id, $cadena)
    {
        $sql = 'CALL dsp_buscar_eventos( :idCalendario, :cadena )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idCalendario' => $id,
            ':cadena' => $cadena
        ]);
        
        return $query->queryAll();
    }  

    /**
     * Permite listar los calendarios de un estudio.
     */
    public function ListarCalendarios()
    {
        $sql = 'CALL dsp_listar_calendarios_estudio( :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $this->IdEstudio,
        ]);
        
        return $query->queryOne();
    }    

    /**
     * Permite listar los mensajes de un estudio.
     * dsp_listar_estadoscaso_estudio
     */
    public function ListarMensajes()
    {
        $sql = 'CALL dsp_listar_mensajes_estudio( :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $this->IdEstudio,
        ]);
        
        return $query->queryAll();
    }  

    public function AltaCalendario($Objeto)
    {
        $sql = 'CALL dsp_alta_calendario_estudio( :token, :idEstudio, :idCalendarioAPI, :titulo,'
                . ' :descripcion, :idColor )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCalendarioAPI' => $Objeto->IdCalendarioAPI,
            ':titulo' => $Objeto->Titulo,
            ':descripcion' => $Objeto->Descripcion,
            ':idColor' => $Objeto->IdColor,
            ':idEstudio' => $this->IdEstudio,
        ]);
        
        return $query->queryScalar();
    } 

    public function AltaUsuarioAcl($Objeto)
    {
        $sql = 'CALL dsp_alta_usuario_acl( :token, :idACLAPI, :idCalendario, :idUsuario, :rol)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idACLAPI' => $Objeto->IdACLAPI,
            ':idCalendario' => $Objeto->IdCalendario,
            ':idUsuario' => $Objeto->IdUsuario,
            ':rol' => $Objeto->Rol
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite crear un Mensaje, controlando que el nombre no se encuentre en uso
     * ya. Devuelve OK + el id del estado-caso creado o un mensaje de error en Mensaje.
     * dsp_alta_mensajeestudio
     *
     * @param Objeto
     */
    public function AltaMensaje($Objeto)
    {
        $sql = 'CALL dsp_alta_mensajeestudio( :token, :idEstudio, :titulo, :mensajeEstudio,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $this->IdEstudio,
            ':titulo' => $Objeto->Titulo,
            ':mensajeEstudio' => $Objeto->MensajeEstudio
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite modificar un Mensaje, controlando que el nombre no se encuentre en
     * uso ya. Devuelve OK o un mensaje de error en Mensaje. dsp_modificar_mensajeestudio
     *
     * @param Objeto
     */
    public function ModificarMensaje($Objeto)
    {
        $sql = 'CALL dsp_modificar_mensajeestudio( :token, :idMensajeEstudio, :titulo, :mensajeEstudio, :nombreTemplate, :nameSpace,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idMensajeEstudio' => $Objeto->IdMensajeEstudio,
            ':titulo' => $Objeto->Titulo,
            ':mensajeEstudio' => $Objeto->MensajeEstudio,
            ':nombreTemplate' => $Objeto->NombreTemplate,
            ':nameSpace' => $Objeto->NameSpace
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite borrar un Mensaje. Devuelve OK o un mensaje de error en Mensaje. dsp_borrar_mensajeestudio
     *
     * @param Objeto
     */
    public function BorrarMensaje($Objeto)
    {
        $sql = 'CALL dsp_borrar_mensajeestudio( :token, :idMensajeEstudio,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idMensajeEstudio' => $Objeto->IdMensajeEstudio,
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite buscar tipos de movimiento filtr�ndolos por una cadena de b�squeda y
     * por categor�a en pCategoria = P: Procesales - O: Gesti�n de oficina - T: Todos.
     * Ordena por TipoMovimiento. dsp_buscar_tiposmovimiento
     *
     * @param Cadena
     * @param Categoria    P: Procesales - O: Gesti�n de oficina - T: Todos
     */
    public function BuscarTiposMovimiento($Cadena = '', $Categoria = 'T')
    {
        $sql = 'CALL dsp_buscar_tiposmovimiento( :idEstudio, :cadena, :categoria )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $this->IdEstudio,
            ':cadena' => $Cadena,
            ':categoria' => $Categoria
        ]);
        
        return $query->queryAll();
    }
    
    /**
     * Permite crear un tipo de movimiento controlando que el nombre no est� en uso
     * para la categor�a dada. Devuelve OK + Id del tipo de movimiento creado o un
     * mensaje de error en Mensaje. dsp_alta_tipomovimiento
     *
     * @param Objeto
     */
    public function AltaTipoMovimiento($Objeto)
    {
        $sql = 'CALL dsp_alta_tipomovimiento( :token, :idEstudio, :tipoMovimiento,'
                . ' :categoria, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $this->IdEstudio,
            ':tipoMovimiento' => $Objeto->TipoMovimiento,
            ':categoria' => $Objeto->Categoria
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite modificar el nombre y categor�a de un tipo de movimiento controlando
     * que no exista el nombre para la nueva categor�a indicada. Devuelve OK o un
     * mensaje de error en Mensaje. dsp_modificar_tipomovimiento
     *
     * @param Objeto
     */
    public function ModificarTipoMovimiento($Objeto)
    {
        $sql = 'CALL dsp_modificar_tipomovimiento( :token, :idTipoMov, :tipoMovimiento,'
                . ' :categoria, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idTipoMov' => $Objeto->IdTipoMov,
            ':tipoMovimiento' => $Objeto->TipoMovimiento,
            ':categoria' => $Objeto->Categoria
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite borrar un tipo de movimiento controlando que no tenga movimientos
     * asociados. Devuelve OK o un mensaje de error en Mensaje.
     * dsp_borrar_tipomovimiento
     *
     * @param Objeto
     */
    public function BorrarTipoMovimiento($Objeto)
    {
        $sql = 'CALL dsp_borrar_tipomovimiento( :token, :idTipoMov,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idTipoMov' => $Objeto->IdTipoMov,
        ]);
        
        return $query->queryScalar();
    }
    
    /**
     * Permite listar las variables de empresa configuradas para un estudio.
     * dsp_listar_parametros_estudio
     */
    public function ListarParametros()
    {
        $sql = 'CALL dsp_listar_parametros_estudio( :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $this->IdEstudio,
        ]);
        
        return $query->queryAll();
    }
    
    /**
     * Permite cambiar el valor de un par�metro siempre y cuando sea editable.
     * Devuelve OK o el mensaje de error en Mensaje. dsp_cambiar_parametro
     *
     *
     * @param Parametro    Nombre del par�metro
     * @param Valor    Valor del par�metro
     */
    public function CambiarParametro($Parametro = null, $Valor = null)
    {
        $sql = "CALL dsp_cambiar_parametro ( :token, :idEstudio, :parametro,"
                . " :valor, :IP, :userAgent, :app ) ";

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $this->IdEstudio,
            ':parametro' => $Parametro,
            ':valor' => $Valor,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite dar de alta una persona en un estudio, controlando que si es persona
     * f�sica el documento no exista o si es persona jur�dica que el CUIT no exista.
     * Devuelve OK + Id de la persona creada o un mesaje d error en Mensaje.
     * dsp_alta_persona_estudio
     *
     * @param Persona
     */
    public function AltaPersona($Persona)
    {
        $sql = 'CALL dsp_alta_persona_estudio( :token, :idEstudio, :tipo,'
                . ' :nombres, :apellidos, :email, :documento, :cuit,'
                . ' :domicilio, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idEstudio' => $this->IdEstudio,
            ':tipo' => $Persona->Tipo,
            ':nombres' => $Persona->Nombres,
            ':apellidos' => $Persona->Apellidos,
            ':email' => $Persona->Email,
            ':documento' => $Persona->Documento,
            ':cuit' => $Persona->Cuit,
            ':domicilio' => $Persona->Domicilio,
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite borrar una persona controlando que no sea actor en un caso. Se borran
     * los tel�fonos asociados a la persona. Devuelve OK o un mensaje de error en
     * Mensaje. dsp_borrar_persona_estudio
     *
     * @param Persona
     */
    public function BorrarPersona($IdPersona)
    {
        $sql = 'CALL dsp_borrar_persona_estudio( :token, :idPersona,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idPersona' => $IdPersona
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite modificar los datos de una persona del estudio. Devuelve OK o un
     * mensaje de error en Mensaje. dsp_modificar_persona_estudio
     *
     * @param Persona
     */
    public function ModificarPersona($Persona, $EsPrincipal = '', $IdCaso = null)
    {
        $sql = 'CALL dsp_modificar_persona_estudio( :token, :idPersona,'
                . ' :nombres, :apellidos, :email, :documento, :cuit,'
                . ' :domicilio, :fechaNacimiento, :esPrincipal, :idCaso, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idPersona' => $Persona->IdPersona,
            ':nombres' => $Persona->Nombres,
            ':apellidos' => $Persona->Apellidos,
            ':email' => $Persona->Email,
            ':documento' => $Persona->Documento,
            ':cuit' => $Persona->Cuit,
            ':domicilio' => $Persona->Domicilio,
            ':fechaNacimiento' => $Persona->FechaNacimiento,
            ':esPrincipal' => $EsPrincipal,
            ':idCaso' => $IdCaso
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite buscar personas filtr�ndolas por estudio, por tipo de b�squeda en pTipo
     * = T: Todo - N: Nombre - D: Documento - C: Cuit. Ordena por Apellidos, Nombres.
     * dsp_buscar_avanzado_personas
     *
     * @param Cadena
     * @param Tipo    T: Todo - N: Nombre - D: Documento - C: Cuit.
     */
    public function BuscarAvanzadoPersonas($Cadena = '', $Tipo = 'T')
    {
        $sql = 'CALL dsp_buscar_avanzado_personas( :idEstudio, :cadena, :tipo )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $this->IdEstudio,
            ':cadena' => $Cadena,
            ':tipo' => $Tipo
        ]);
        
        return $query->queryAll();
    }
    /**
     * Permite buscar consultas derivadas a un estudio, filtr�ndolas por estado: P:
     * Pendiente - A: Aceptada - R: Rechazada - T: Todas. Ordena por FechaDerivacion.
     * dsp_buscar_consultas_estudio
     *
     * @param Estado    P: Pendiente - A: Aceptada - R: Rechazada - T: Todas
     * @param Cadena
     */
    public function BuscarConsultas($Estado = 'T', $Cadena = '')
    {
        $sql = 'CALL dsp_buscar_consultas_estudio( :idEstudio, :estado, :cadena )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $this->IdEstudio,
            ':estado' => $Estado,
            ':cadena' => $Cadena
        ]);
        
        return $query->queryAll();
    }

    
    /**
     * Permite aceptar una consulta derivada a un estudio siempre y cuando la �ltima
     * derivaci�n de la consulta sea para el estudio que intenta aceptarla, la
     * derivaci�n se encuentre en estado Pendiente y la consulta se encuentre en
     * estado Derivada. Devuelve OK o el mensaje de error en Mensaje.
     * dsp_aceptar_derivacion_consulta
     *
     * @param Consulta
     */
    public function AceptarDerivacionConsulta($Consulta)
    {
        $sql = 'CALL dsp_aceptar_derivacion_consulta( :token, :idDerivacionConsulta,'
                . ' :idEstudio, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idDerivacionConsulta' => $Consulta->IdDerivacionConsulta,
            ':idEstudio' => $this->IdEstudio
        ]);
        
        return $query->queryScalar();
    }



    /**
     * Permite rechazar una derivaci�n de consulta a un estudio siempre que sea la
     * �ltima derivaci�n de la consulta, que la derivaci�n se encuentre en estado
     * Pendiente y que la consulta se encuentre en estado Activa. Controla que el
     * estudio indicado tenga la �ltima derivaci�n de la consulta y coincida con el
     * estudio al que pertenece el usuario que ejecuta el procedimiento. Devuelve OK o
     * un mensaje de error en Mensaje. dsp_rechazar_derivacion_consulta
     *
     * @param Consulta
     */
    public function RechazarDerivacionConsulta($Consulta)
    {
        $sql = 'CALL dsp_rechazar_derivacion_consulta( :token, :idDerivacionConsulta,'
                . ' :idEstudio, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idDerivacionConsulta' => $Consulta->IdDerivacionConsulta,
            ':idEstudio' => $this->IdEstudio
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite obtener el intervalo de fechas en las que se dieron de alta todos los
     * movimientos de un estudio. dsp_dame_intervalo_fechas_movimientos
     */
    public function DameIntervaloFechasMovimientos()
    {
        $sql = 'CALL dsp_dame_intervalo_fechas_movimientos( :idEstudio )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $this->IdEstudio
        ]);
        
        return $query->queryOne();
    }

    public function ListarDocumentacionSolicitada()
    {
        $sql = 'CALL dsp_listar_documentacion_solicitada()';
        
        $query =Yii::$app->db->createCommand($sql);
        
        return $query->queryAll();
    }

    public function AltaDocumentacionSolicitada($doc)
    {
        $sql = 'CALL dsp_alta_documentacion_solicitada( :doc )';
        
        $query =Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':doc' => $doc
        ]);
        
        return $query->queryOne();
    }
}
