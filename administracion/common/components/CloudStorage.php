<?php
namespace common\components;

use Exception;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\StorageObject;
use Yii;
use yii\base\Component;

class CloudStorage extends Component
{
    private $_bucket = null;
    public $key;
    public $bucketName;

    /**
     * Inicializa el cliente de Google Cloud Storage y el bucket
     *
     * @param type $config Array de configuraciones
     * @throws Exception
     */
    public function __construct()
    {
        $config = [
            'key' => '{
                "type": "service_account",
                "project_id": "docdoc-265916",
                "private_key_id": "5309af82e0bc4d771662ae0fa932b0572ee9649c",
                "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDqwssVAAp/ZAxf\nhPaGRBE9iwSVKMh0R/bZRbZoeId1VFjpP3E7/IiiMISlfaru+nS7lZ96dwdql+gK\nrwYiM60NlVmCB/g5QA/BiD3XIpRDkIB2kx+4vnWuSa122mfZ7R1tjo5UDXXK7mxh\nrRZu7KHWS/Ow6aua14ZCanHvmtEmoA+L0VlaZZwYYECKT8dEmwzEJPCd4/8+RjaS\nHlA1antdzOygFhsyYGMZy/MSE2fE3yDb8syUv8X9ll/w6pflgaTuywC/d3OJSsC8\n6Umcz0+PjjWErpacq9lQxDLg2161+qyvv7R3G3vbpXJQSG6ULMSEjvZ88DuzyjMZ\n39qItB6RAgMBAAECggEAU96qugJtPazLJNb2UeqAdEm1pepPjwdkv6PBspoY3sh3\nCUGSnTkvwS3vPcZjKoSM7rVaJ+DdY+4IRsTXvqFSmm84bpWVTzK9TklzumfOq0K1\nOmd+ZjyZA16sG7GUd41YPZs36vxyEEFUtCKnyJI+kTZKRfJ9TdDg1Np9gPoA7biC\n/tGVNjUNM+2sjBvPI8JArElQoT2JZv3l3py1Hq7hIgXSUjuDKQbxcFFBwIFR6l8G\nol3MLcny9j38uBt+dJMXO3JH9gVH2PzVTR5kUZKgm5SFtGCfRfRES27rMxoXRFPT\nkJ7NE3mKKE2WTXWZ41SP5NR0DOywg94lE8neROKTpwKBgQD9+ohMLm7syQde1uhy\nm2ITieTqCh9i2V791zDy+ug0kjYB+tdOD2SVfDLgODLD4eXKCuu858x0JrXkdOFo\nHrDwaEb0hYAbN1WOeCHytJggErwy0UGdXBCxyuOZRe/1SRvJ9BafCqjlMT3Xwe1F\nZMBjUIXW7I62jnZeHOSuWEg2dwKBgQDsoRsVzQQ7m6ftozIc5cfrC7+ei983h2mk\naYd62uRrwLdeuAJ4LLVJHcBptPKVYXlUs7WMfXrNzMkVldkxFoL9SNDdWB0Z37lq\n80lWnL1dJ/NGEitkA+acmb9Pd8ZkJSYhxHEccK2BFBVR3gQMB+XioOqt5DPo/6n9\nzheEnS6tNwKBgQD3JnjCIaFiHNJmUR3MgTa0qsivk4AtcjhFLsZ8fPvARNP3o0En\nvkT0TvM3TJjiE47IyU3T+4HzOcRhd/ftmYg3ulHqG4upcHR6ep8WjvVGqNSpYwbF\n+dRpH3XSLsOu3yECqtvkkrv+pKd4sUeS8tNhEffcSUErl4DKXrWOj2xeSwKBgAJD\njFHKE1dKpvGkFQ+ntyDtjNjEd889MWqMQ+qN+494WYjDc+qYaueXLEcWnxeExjdk\nPMFqVelwIyBcvaY1k+0+bBkiBa1AsbJvP21ftIQWpMIv3FBppSQsaGMnPzOoE1RR\nX8+o2FAa1BVjbWB8FtvzNCuTuldpUsQF272+DztDAoGBAMcK5zwyf6xPPsu+AqSq\ndTNLctgV8Dr2csTjOrnNpc0il8/VSX4KnlzbiDiaT4IVenLi1sQuGJdlTOPk35Q8\nRVUy/CTPhis2IOrx89T8N/RkpU9vJzhfY9KcRc/JeKdNQ8BYprqMS6lYktFV9m90\nJnSyj4Q20JoTIesK3zPLnFYV\n-----END PRIVATE KEY-----\n",
                "client_email": "calendar-docdoc@docdoc-265916.iam.gserviceaccount.com",
                "client_id": "111288232335637158004",
                "auth_uri": "https://accounts.google.com/o/oauth2/auth",
                "token_uri": "https://oauth2.googleapis.com/token",
                "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
                "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/calendar-docdoc%40docdoc-265916.iam.gserviceaccount.com"
            }',
            'bucketName' => 'docdoc-segmento'
        ];

        foreach ($config as $property => $value) {
            if (!$this->hasProperty($property)) {
                throw new Exception("La propiedad {$property} no está definida");
            }
            if (is_null($value)) {
                throw new Exception("La propiedad {$property} no puede ser nula.");
            }
            $this->{$property} = $value;
        }

        parent::__construct($config);
    }
    
    public function init()
    {
        parent::init();
        
        $config = [
            'keyFile' => (array) json_decode($this->key),
        ];

        $client = new StorageClient($config);
        $this->_bucket = $client->bucket($this->bucketName);

        if (!$this->_bucket->exists()) {
            throw new Exception('El bucket no existe en Google Cloud Storage.');
        }
    }

    public function escribirArchivo($path, $contenido)
    {
        $this->_bucket->upload($contenido, [
            'name' => $path,
        ]);
    }

    public function getLink($fileName)
    {
        $corsConfig = [
            [
                'origin' => ['https://app.docdoc.com.ar', 'https://notificaciones.docdoc.com.ar', 'http://localhost:8080', 'http://localhost:8000', 'https://www.docdoc.com.ar', 'https://192.168.100.11:8080', 'http://192.168.100.11:8080'], // Reemplaza con los dominios permitidos
                'method' => ['PUT', 'POST', 'GET'], // Reemplaza con los métodos HTTP permitidos
                'maxAgeSeconds' => 3600, // Reemplaza con la duración máxima en segundos
                'responseHeader' => ['Content-Type', 'Access-Control-Allow-Origin']
            ]
        ];
        $this->_bucket->update([
            'cors' => $corsConfig
        ]);

        $object = $this->_bucket->object($fileName);

        // return $this->_bucket->info()['cors'];

        return $object->signedUrl(
            # This URL is valid for 15 minutes
            new \DateTime('15 min'),
            [
                'method' => 'PUT',
                'contentType' => 'application/octet-stream',
                'version' => 'v4',
            ]
        );
    }

    public function leerArchivo($path)
    {
        $object = $this->getObject($path);

        if (!$object->exists()) {
            throw new Exception('El objeto no existe en Google Cloud Storage.');
        }
             
        return $object->downloadAsString();
    }

    public function borrarArchivo($path)
    {
        $object = $this->getObject($path);

        if (!$object->exists()) {
            throw new Exception('El objeto no existe en Google Cloud Storage.');
        }

        $object->delete();
    }

    private function getObject($path): StorageObject
    {
        $object = $this->_bucket->object($path);

        if (!$object->exists()) {
            throw new Exception('El objeto no existe en Google Cloud Storage.');
        }

        return $object;
    }
    
    public function getBucketName()
    {
        return $this->bucketName;
    }
    public function setBucketName($bucketName)
    {
        $this->bucketName = $bucketName;
    }
    
    public function getKeyFilePath()
    {
        return $this->keyFilePath;
    }
    
    public function setKeyFilePath($keyFilePath)
    {
        $this->keyFilePath = $keyFilePath;
    }
}
