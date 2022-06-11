<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * @version 1.0
 * @created 02-Apr-2018 17:22:36
 */
class GestorCiudades extends Model
{

    /**
     * Permite buscar ciudades filtr�ndolas por una cadena de b�squeda y por provincia
     * (pIdProvincia  = 0 para todas). Ordena por Ciudad. buscar_ciudades
     *
     * @param Cadena
     * @param IdProvincia    0 para todas
     */
    public function BuscarAvanzado($Cadena = '', $IdProvincia = 0)
    {
        $sql = 'CALL dsp_buscar_ciudades( :cadena, :idProvincia)';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena,
            ':idProvincia' => $IdProvincia
        ]);
        
        return $query->queryAll();
    }

    /**
     * Permite buscar provincias filtr�ndolas por una cadena de b�squeda.
     * buscar_provincias
     *
     * @param Cadena
     */
    public function BuscarProvincias($Cadena = '')
    {
        $sql = 'CALL dsp_buscar_provincias( :cadena )';
        
        $query = Yii::$app->db->createCommand($sql);
        
        $query->bindValues([
            ':cadena' => $Cadena
        ]);
        
        return $query->queryAll();
    }
}
