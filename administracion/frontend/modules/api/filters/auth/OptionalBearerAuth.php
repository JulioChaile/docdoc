<?php

namespace frontend\modules\api\filters\auth;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\UnauthorizedHttpException;

class OptionalBearerAuth extends HttpBearerAuth
{
    /**
     * @var array list of action IDs that this filter will be applied to, but auth failure will not lead to error.
     * It may be used for actions, that are allowed for public, but return some additional data for authenticated users.
     * @see isOptional
     */
    public $optional = ['wordpress-create'];
    public $actionsClient = [];

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $response = $this->response ? : Yii::$app->getResponse();
        try {
            $identity = $this->authenticate(
                    $this->user ? : Yii::$app->getUser(),
                $this->request ? : Yii::$app->getRequest(),
                $response
            );
        } catch (UnauthorizedHttpException $e) {
            if ($this->isOptional($action)) {
                return true;
            }
            throw $e;
        }
        if ($identity !== null) {
            $IdRol = Yii::$app->user->identity->IdRol;
            if ($IdRol === 2 || $IdRol === '2') {
                return $this->clientAuth($action);
            }
        }
        if ($identity !== null || $this->isOptional($action)) {
            return true;
        } else {
            $this->challenge($response);
            $this->handleFailure($response);
            return false;
        }
    }

    protected function clientAuth($action)
    {
        $id = $this->getActionId($action);

        return in_array($id, $this->actionsClient, true);
    }

    /**
     * Returns an $action ID, convert action uniqueId into an ID relative to the module
     * @param Action $action
     * @return string
     */
    protected function getActionId($action)
    {
        if ($this->owner instanceof Module) {
            $mid = $this->owner->getUniqueId();
            $id = $action->getUniqueId();
            if ($mid !== '' && strpos($id, $mid) === 0) {
                $id = substr($id, strlen($mid) + 1);
            }
        } else {
            $id = $action->id;
        }
        return $id;
    }

    /**
     * Checks, whether the $action is optional
     *
     * @param Action $action
     * @return boolean
     * @see optional
     * @since 2.0.7
     */
    protected function isOptional($action)
    {
        $id = $this->getActionId($action);
        return in_array($id, $this->optional, true);
    }

    /**
     * {@inheritdoc}
     */
    protected function isActive($action)
    {
        return parent::isActive($action) || $this->isOptional($action);
    }

    /**
     * @inheritdoc
     */
    public function handleFailure($response)
    {
        throw new UnauthorizedHttpException('Su sesión en la aplicación finalizó. Vuelva a ingresar nuevamente.');
    }
}
