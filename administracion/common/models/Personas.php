<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 24-Oct-2018 17:12:27
 */
class Personas extends Model
{
    public $IdPersona;
    public $IdEstudio;
    public $Tipo;
    public $Nombres;
    public $Apellidos;
    public $Email;
    public $Documento;
    public $Cuit;
    public $Domicilio;
    public $FechaAlta;
    public $Telefonos;
    
    public function rules()
    {
        return [
            [$this->attributes(), 'safe']
        ];
    }

    /**
     * Permite instanciar una persona de un estudio. dsp_dame_persona
     */
    public function Dame()
    {
        $sql = 'CALL dsp_dame_persona( :idPersona )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idPersona' => $this->IdPersona
        ]);
        
        $this->attributes = $query->queryOne();
    }

    /**
     * Permite dar de alta uno o m�s tel�fonos a una persona. Si alguno de los
     * tel�fonos existe, se lo ignora y se dan de alta solo aquellos que no existan.
     * Devuelve OK o un mensaje de error en Mensaje. dsp_alta_modifica_telefono_persona
     *
     * @param Objeto
     */
    public function AltaTelefonos($Objeto)
    {
        $sql = 'CALL dsp_alta_modifica_telefono_persona( :token, :idPersona, :telefono,'
                . ':detalle, :esPrincipal, :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idPersona' => $Objeto->IdPersona,
            ':telefono' => $Objeto->Telefono,
            ':detalle' => $Objeto->Detalle ?? '',
            ':esPrincipal' => $Objeto->EsPrincipal ?? 'N'
        ]);
        
        return $query->queryScalar();
    }

    /**
     * Permite modificar un telefono de una persona en particular. Devuelve OK o un mensaje
     * de error en Mensaje. dsp_modifica_telefono_persona
     * @param Objeto
     */
    public function ModificarTelefono($Objeto)
    {
        $sql = 'CALL dsp_modifica_telefono_persona( :token, :idPersona, :telefono, :telefonoOld, :detalle, :esPrincipal, :IP, :userAgent, :app )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idPersona' => $Objeto->IdPersona,
            ':telefono' => $Objeto->Telefono,
            ':telefonoOld' => $Objeto->TelefonoOld,
            ':detalle' => $Objeto->Detalle ?? '',
            ':esPrincipal' => $Objeto->EsPrincipal
        ]);

        return $query->queryScalar();
    }

    /**
     * Permite borrar el tel�fono de una persona del estudio. Devuelve OK o un mensaje
     * de error en Mensaje. dsp_borrar_telefono_persona
     *
     * @param Objeto
     */
    public function BorrarTelefono($Objeto)
    {
        $sql = 'CALL dsp_borrar_telefono_persona( :token, :idPersona, :telefono,'
                . ' :IP, :userAgent, :app )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':IP' => Yii::$app->request->userIP,
            ':userAgent' => Yii::$app->request->userAgent,
            ':app' => Yii::$app->id,
            ':idPersona' => $Objeto->IdPersona,
            ':telefono' => $Objeto->Telefono
        ]);
        
        return $query->queryScalar();
    }
    
    public function Buscar($IdEstudio, $Cadena, $Tipo)
    {
        $sql = 'CALL dsp_buscar_avanzado_personas( :idEstudio, :cadena, :tipo )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':idEstudio' => $IdEstudio,
            ':cadena' => $Cadena,
            ':tipo' => $Tipo
        ]);
        
        return $query->queryAll();
    }

    public function Padron($documento)
    {
        $sql = 'CALL dsp_traer_persona_padron( :Documento )';

        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':Documento' => $documento
        ]);
        
        $persona = $query->queryOne();

        if ($persona['Mensaje'] == 'OK') {
            $apNom = explode(',', $persona['Persona']);
            $persona['Apellidos'] = trim($apNom[0]);
            $persona['Nombres'] = trim($apNom[1]);
        }

        return $persona;
    }

    public function Parametros($Parametros, $IdCaso, $IdPersona)
    {
        $sql = 'CALL dsp_parametros_persona_caso( :token, :parametros, :idCaso, :idPersona )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':parametros' => json_encode($Parametros),
            ':idCaso' => $IdCaso,
            ':idPersona' => $IdPersona
        ]);

        return $query->queryScalar();
    }

    public function AltaHistoriaClinica($HistoriaClinica, $IdCaso, $IdPersona)
    {
        $sql = 'CALL dsp_alta_historiaclinica_persona_caso( :token, :idCaso, :idPersona, :estado, :numero, :centroMedico )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idCaso' => $IdCaso,
            ':idPersona' => $IdPersona,
            ':estado' => $HistoriaClinica->Estado,
            ':numero' => $HistoriaClinica->Numero,
            ':centroMedico' => $HistoriaClinica->CentroMedico,
        ]);

        return $query->queryScalar();
    }

    public function EditarHistoriaClinica($HistoriaClinica, $IdCaso, $IdPersona)
    {
        $sql = 'CALL dsp_modificar_historiaclinica_persona_caso( :token, :idHistoriaClinica, :idCaso, :idPersona, :estado, :numero, :centroMedico )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idHistoriaClinica' => $HistoriaClinica->IdHistoriaClinica,
            ':idCaso' => $IdCaso,
            ':idPersona' => $IdPersona,
            ':estado' => $HistoriaClinica->Estado,
            ':numero' => $HistoriaClinica->Numero,
            ':centroMedico' => $HistoriaClinica->CentroMedico,
        ]);

        return $query->queryScalar();
    }

    public function EditarDocumentacion($IdPersona, $IdCaso, $DocumentacionSolicitada)
    {
        $sql = 'CALL dsp_modificar_documentacion_persona_caso( :token, :idPersona, :idCaso, :documentacionSolicitada )';

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':token' => Yii::$app->user->identity->Token,
            ':idPersona' => $IdPersona,
            ':idCaso' => $IdCaso,
            ':documentacionSolicitada' => $DocumentacionSolicitada
        ]);

        return $query->queryScalar();
    }
}
