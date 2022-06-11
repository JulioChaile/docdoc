<?php

namespace common\models;

use yii\filters\RateLimitInterface;
use yii\web\HttpException;
use Yii;

class IPRateLimiter implements RateLimitInterface
{
    const RATE_WINDOW = 1; // Segundos
    private $rateLimit; // rq/s
    private $banIP;

    public function __construct($rateLimit = 10, $banIP = false)
    {
        $this->rateLimit = $rateLimit;
        $this->banIP = $banIP;
    }

    private function getKey($ip)
    {
        return serialize([self::class, $this->rateLimit, $this->banIP, $ip]);
    }

    private function getBanKey($ip)
    {
        return serialize([self::class, 'banear', $ip]);
    }

    private function isBanned($ip)
    {
        return Yii::$app->cache->get($this->getBanKey($ip));
    }

    // Métodos de la interfaz RateLimitInterface
    public function getRateLimit($request, $action)
    {
        return [$this->rateLimit, self::RATE_WINDOW];
    }

    public function loadAllowance($request, $action)
    {
        $ip = Yii::$app->request->userIP;

        if ($this->banIP && $this->isBanned($ip)) {
            throw new HttpException('429', 'Su IP se encuentra en nuestra lista negra. Si cree que esto es un error comuníquese con nosotros.');
        }

        $data = Yii::$app->cache->get($this->getKey($ip));
        return [$data['allowance'], $data['allowance_updated_at']];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $ip = Yii::$app->request->userIP;

        if ($this->banIP && $allowance === 0) {
            Yii::$app->cache->set($this->getBanKey($ip), true);
        }

        $data = [ 'allowance' => $allowance, 'allowance_updated_at' => $timestamp ];
        Yii::$app->cache->set($this->getKey($ip), $data);
    }
}
