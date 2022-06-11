<?php
namespace common\models;

use Yii;
use yii\base\Model;

class GestorChatApi extends Model
{
    /**
     * Permite dar de alta un chat
     * Devuelve 'OK' concatenado con el IdChat o un mensaje de error
     */
    public function AltaChat($IdExternoChat, $IdCaso, $IdPersona, $Telefono)
    {
        $sql = 'CALL dsp_alta_chat(:idExternoChat, :idCaso, :idPersona, :Telefono)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idExternoChat' => $IdExternoChat,
            ':idCaso' => $IdCaso,
            ':idPersona' => $IdPersona,
            ':Telefono' => $Telefono
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite dar de alta un chat de un mediador
     * Devuelve 'OK' concatenado con el IdChat o un mensaje de error
     */
    public function AltaChatMediador($IdExternoChat, $IdMediador, $Telefono)
    {
        $sql = 'CALL dsp_alta_chat_mediador(:idExternoChat, :idMediador, :telefono)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idExternoChat' => $IdExternoChat,
            ':idMediador' => $IdMediador,
            ':telefono' => $Telefono
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite dar de alta un chat de un mediador
     * Devuelve 'OK' concatenado con el IdChat o un mensaje de error
     */
    public function AltaChatContacto($IdExternoChat, $IdContacto, $Telefono)
    {
        $sql = 'CALL dsp_alta_chat_contacto(:idExternoChat, :idContacto, :Telefono)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idExternoChat' => $IdExternoChat,
            ':idContacto' => $IdContacto,
            ':Telefono' => $Telefono
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite dar de alta un mensaje
     * Devuelve 'OK' concatenado con el IdMensaje o un mensaje de error
     */
    public function AltaMensajeExterno($Objeto)
    {
        $sql = 'CALL dsp_alta_mensaje_externo(:idMensajeApi, :idChatApi, :contenido, :fechaEnviado, :fechaRecibido, :idUsuario)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idMensajeApi' => $Objeto['IdMensajeApi'],
            ':idChatApi' => $Objeto['IdChatApi'],
            ':contenido' => $Objeto['Contenido'],
            ':fechaEnviado' => $Objeto['FechaEnviado'],
            ':fechaRecibido' => $Objeto['FechaRecibido'],
            ':idUsuario' => $Objeto['IdUsuario']
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite dar de alta un mensaje
     * Devuelve 'OK' concatenado con el IdMensaje o un mensaje de error
     */
    public function AltaMensaje($IdExternoMensaje = null, $IdChat, $Contenido, $FechaEnviado, $FechaRecibido = null, $IdUsuario = null)
    {
        $sql = 'CALL dsp_alta_mensaje(:idExternoMensaje, :idChat, :Contenido, :FechaEnviado, :FechaRecibido, :idUsuario)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idExternoMensaje' => $IdExternoMensaje,
            ':idChat' => $IdChat,
            ':Contenido' => $Contenido,
            ':FechaEnviado' => $FechaEnviado,
            ':FechaRecibido' => $FechaRecibido,
            ':idUsuario' => $IdUsuario
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite dar de alta un mensaje de un mediador
     * Devuelve 'OK' concatenado con el IdMensaje o un mensaje de error
     */
    public function AltaMensajeMediador($IdExternoMensaje = null, $IdChatMediador, $Contenido, $FechaEnviado, $FechaRecibido = null, $IdUsuario = null)
    {
        $sql = 'CALL dsp_alta_mensaje_mediador(:idExternoMensaje, :idChatMediador, :contenido, :fechaEnviado, :fechaRecibido, :idUsuario)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idExternoMensaje' => $IdExternoMensaje,
            ':idChatMediador' => $IdChatMediador,
            ':contenido' => $Contenido,
            ':fechaEnviado' => $FechaEnviado,
            ':fechaRecibido' => $FechaRecibido,
            ':idUsuario' => $IdUsuario
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite dar de alta un mensaje de un mediador
     * Devuelve 'OK' concatenado con el IdMensaje o un mensaje de error
     */
    public function AltaMensajeContacto($IdExternoMensaje = null, $IdChatContacto, $Contenido, $FechaEnviado, $FechaRecibido = null, $IdUsuario = null)
    {
        $sql = 'CALL dsp_alta_mensaje_contacto(:idExternoMensaje, :idChatContacto, :contenido, :fechaEnviado, :fechaRecibido, :idUsuario)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idExternoMensaje' => $IdExternoMensaje,
            ':idChatContacto' => $IdChatContacto,
            ':contenido' => $Contenido,
            ':fechaEnviado' => $FechaEnviado,
            ':fechaRecibido' => $FechaRecibido,
            ':idUsuario' => $IdUsuario
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite setear el valor de IdExternoMensaje del chat
     * usando el chatId de ChatApi
     * Devuelve 'OK' o un mensaje de error
     */
    public function SetIdMensajeApiExterno($IdMensajeExterno, $IdMensajeApi)
    {
        $sql = 'CALL dsp_activar_mensaje_externo(:idMensajeExterno, :idMensajeApi)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idMensajeExterno' => $IdMensajeExterno,
            ':idMensajeApi' => $IdMensajeApi
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite setear el valor de IdExternoMensaje del chat
     * usando el chatId de ChatApi
     * Devuelve 'OK' o un mensaje de error
     */
    public function SetIdExternoMensaje($IdMensaje, $IdExternoMensaje)
    {
        $sql = 'CALL dsp_activar_mensaje(:idMensaje, :idExternoMensaje)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idExternoMensaje' => $IdExternoMensaje,
            ':idMensaje' => $IdMensaje
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite setear el valor de IdExternoMensaje del chat del mediador
     * usando el chatId de ChatApi
     * Devuelve 'OK' o un mensaje de error
     */
    public function SetIdExternoMensajeMediador($IdMensaje, $IdExternoMensaje)
    {
        $sql = 'CALL dsp_activar_mensaje_mediador(:idMensaje, :idExternoMensaje)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idExternoMensaje' => $IdExternoMensaje,
            ':idMensaje' => $IdMensaje
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite setear el valor de IdExternoMensaje del chat del mediador
     * usando el chatId de ChatApi
     * Devuelve 'OK' o un mensaje de error
     */
    public function SetIdExternoMensajeContacto($IdMensaje, $IdExternoMensaje)
    {
        $sql = 'CALL dsp_activar_mensaje_contacto(:idMensaje, :idExternoMensaje)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idExternoMensaje' => $IdExternoMensaje,
            ':idMensaje' => $IdMensaje
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite cambiar las fechas de recibido y visto de un mensaje segun corresponda
     * Devuelve 'OK' o un mensaje de error
     */
    public function SetFechaMensajeExterno($IdExternoMensaje, $FechaRecibido = null, $FechaVisto = null)
    {
        $sql = 'CALL dsp_modificar_fecha_mensaje_externo(:idExternoMensaje, :FechaRecibido, :FechaVisto)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idExternoMensaje' => $IdExternoMensaje,
            ':FechaRecibido' => $FechaRecibido,
            ':FechaVisto' => $FechaVisto
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite cambiar las fechas de recibido y visto de un mensaje segun corresponda
     * Devuelve 'OK' o un mensaje de error
     */
    public function SetFechaMensaje($IdExternoMensaje, $FechaRecibido = null, $FechaVisto = null)
    {
        $sql = 'CALL dsp_modificar_fecha_mensaje(:idExternoMensaje, :FechaRecibido, :FechaVisto)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idExternoMensaje' => $IdExternoMensaje,
            ':FechaRecibido' => $FechaRecibido,
            ':FechaVisto' => $FechaVisto
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Devuvelve los datos de un chat usando IdChat o IdExternoChat
     */
    public function DameChat($IdChat = null, $IdExternoChat = null)
    {
        $sql = 'CALL dsp_dame_chat(:idChat, :idExternoChat)';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':idExternoChat' => $IdExternoChat,
            ':idChat' => $IdChat
        ]);
        
        return $query->queryOne();
    }

    /**
     * Devuvelve los datos de un chat con mediador usando IdChat o IdExternoChat
     */
    public function DameChatMediador($IdChatMediador = null, $IdExternoChat = null)
    {
        $sql = 'CALL dsp_dame_chat_mediador(:idChatMediador, :idExternoChat)';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':idExternoChat' => $IdExternoChat,
            ':idChatMediador' => $IdChatMediador
        ]);
        
        return $query->queryOne();
    }

    /**
     * Devuvelve los datos de un chat con mediador usando IdChat o IdExternoChat
     */
    public function DameChatContacto($IdChatContacto = null, $IdExternoChat = null)
    {
        $sql = 'CALL dsp_dame_chat_contacto(:idChatContacto, :idExternoChat)';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':idExternoChat' => $IdExternoChat,
            ':idChatContacto' => $IdChatContacto
        ]);
        
        return $query->queryOne();
    }

    /**
     * Permite guardar el IdMensaje del ultimo mensaje leido en el chat
     */
    public function ModificarUltMsjLeido($IdChat, $IdMensaje)
    {
        $sql = 'CALL dsp_modificar_ulmsjleido( :idChat, :idMensaje)';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':idChat' => $IdChat,
            ':idMensaje' => $IdMensaje
        ]);

        return $query->queryScalar();
    }

    /**
     * Permite guardar el IdMensaje del ultimo mensaje leido en el chat de mediador
     */
    public function ModificarUltMsjLeidoMediador($IdChatMediador, $IdMensaje)
    {
        $sql = 'CALL dsp_modificar_ulmsjleido_mediador( :idChatMediador, :idMensaje)';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':idChatMediador' => $IdChatMediador,
            ':idMensaje' => $IdMensaje
        ]);

        return $query->queryScalar();
    }

    /**
     * Permite guardar el IdMensaje del ultimo mensaje leido en el chat de mediador
     */
    public function ModificarUltMsjLeidoContacto($IdChatContacto, $IdMensaje)
    {
        $sql = 'CALL dsp_modificar_ulmsjleido_contacto( :idChatContacto, :idMensaje)';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':idChatContacto' => $IdChatContacto,
            ':idMensaje' => $IdMensaje
        ]);

        return $query->queryScalar();
    }

    /**
     * Permite listar todos lo mensajes del chat mayores a IdUltimoMensaje.
     * Si IdUltimoMensaje es null, lista todos.
     * Indica limit y offset opcionales.
     * Retorna los chats ordenados de manera descendente.
     */
    public function ListarMensajes($IdChat, $IdUltimoMensaje, $Limit = 20, $Offset = 0)
    {
        $sql = "CALL dsp_listar_mensajes( :idChat, :idUltimoMensaje, :limit, :offset )";

        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idChat' => $IdChat,
            ':idUltimoMensaje' => $IdUltimoMensaje,
            ':limit' => $Limit,
            ':offset' => $Offset
        ]);

        return $query->queryAll();
    }

    /**
     * Permite listar todos lo mensajes del chat mayores a IdUltimoMensaje.
     * Si IdUltimoMensaje es null, lista todos.
     * Indica limit y offset opcionales.
     * Retorna los chats ordenados de manera descendente.
     */
    public function ListarMensajesExterno($IdChat)
    {
        $sql = "CALL dsp_listar_mensajes_externo( :idChat )";

        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idChat' => $IdChat
        ]);

        return $query->queryAll();
    }
    
    public function ListarChatsExterno()
    {
        $sql = "CALL dsp_listar_chats_externo()";

        $query = Yii::$app->db->createCommand($sql);

        return $query->queryAll();
    }

    /**
     * Permite listar todos lo mensajes del chat mayores a IdUltimoMensaje.
     * Si IdUltimoMensaje es null, lista todos.
     * Indica limit y offset opcionales.
     * Retorna los chats ordenados de manera descendente.
     */
    public function NuevosMensajesExterno()
    {
        $sql = "CALL dsp_listar_chats_externo_nuevos()";

        $query = Yii::$app->db->createCommand($sql);

        return $query->queryAll();
    }

    /**
     * Permite listar todos lo mensajes del chat mayores a IdUltimoMensaje.
     * Si IdUltimoMensaje es null, lista todos.
     * Indica limit y offset opcionales.
     * Retorna los chats ordenados de manera descendente.
     */
    public function ListarMensajesMediador($IdChatMediador, $IdUltimoMensaje, $Limit = 20, $Offset = 0)
    {
        $sql = "CALL dsp_listar_mensajes_mediador( :idChatMediador, :idUltimoMensaje, :limit, :offset )";

        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idChatMediador' => $IdChatMediador,
            ':idUltimoMensaje' => $IdUltimoMensaje,
            ':limit' => $Limit,
            ':offset' => $Offset
        ]);

        return $query->queryAll();
    }

    /**
     * Permite listar todos lo mensajes del chat mayores a IdUltimoMensaje.
     * Si IdUltimoMensaje es null, lista todos.
     * Indica limit y offset opcionales.
     * Retorna los chats ordenados de manera descendente.
     */
    public function ListarMensajesContacto($IdChatContacto, $IdUltimoMensaje, $Limit = 20, $Offset = 0)
    {
        $sql = "CALL dsp_listar_mensajes_contacto( :idChatContacto, :idUltimoMensaje, :limit, :offset )";

        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idChatContacto' => $IdChatContacto,
            ':idUltimoMensaje' => $IdUltimoMensaje,
            ':limit' => $Limit,
            ':offset' => $Offset
        ]);

        return $query->queryAll();
    }

    /**
     * Devuelve el ID del ultimo mensaje recibido para el usuario
     */
    public function NuevosMensajes($idUsuario)
    {
        $sql = "CALL dsp_buscar_nuevos_mensajes( :idUsuario )";

        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idUsuario' => $idUsuario
        ]);

        return $query->queryAll();
    }

    /**
     * Devuelve el ID del ultimo mensaje recibido para el usuario con el mediador
     */
    public function NuevosMensajesMediador($idUsuario)
    {
        $sql = "CALL dsp_buscar_nuevos_mensajes_mediador( :idUsuario )";

        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idUsuario' => $idUsuario
        ]);

        return $query->queryAll();
    }

    /**
     * Devuelve el ID del ultimo mensaje recibido para el usuario con el mediador
     */
    public function NuevosMensajesContacto($idUsuario)
    {
        $sql = "CALL dsp_buscar_nuevos_mensajes_contacto( :idUsuario )";

        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idUsuario' => $idUsuario
        ]);

        return $query->queryAll();
    }

    /**
     * Permite modificar el telefono y el IdExternoChat de un chat
     * Devuelve OK concatenado con el nuevo telefono o un mensaje de error
     */
    public function ModificarTelefono($IdChat, $Telefono, $IdExternoChat, $IdPersona)
    {
        $sql = 'CALL dsp_modificar_telefono_chat( :idChat, :telefono, :idExternoChat, :idPersona)';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':idChat' => $IdChat,
            ':telefono' => $Telefono,
            ':idExternoChat' => $IdExternoChat,
            ':idPersona' => $IdPersona
        ]);

        if ($query->queryScalar() === 'Error') {
            return $query->queryOne()['Message'];
        } else {
            return $query->queryScalar();
        }
    }

    /**
     * Permite modificar el telefono y el IdExternoChat de un chat
     * Devuelve OK concatenado con el nuevo telefono o un mensaje de error
     */
    public function ModificarTelefonoMediador($IdChatMediador, $Telefono, $IdExternoChat)
    {
        $sql = 'CALL dsp_modificar_telefono_chat_mediador( :idChatMediador, :telefono, :idExternoChat )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':idChatMediador' => $IdChatMediador,
            ':telefono' => $Telefono,
            ':idExternoChat' => $IdExternoChat
        ]);

        return $query->queryScalar();
    }

    public function ReemplazarCaso($IdExternoChat, $IdCaso, $IdPersona)
    {
        $sql = 'CALL dsp_reemplazar_caso_chat( :idCaso, :idExternoChat, :idPersona)';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':idCaso' => $IdCaso,
            ':idExternoChat' => $IdExternoChat,
            ':idPersona' => $IdPersona
        ]);

        return $query->queryScalar();
    }
}
