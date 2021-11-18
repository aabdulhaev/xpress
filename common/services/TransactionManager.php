<?php

namespace common\services;

use common\dispatchers\DeferredEventDispatcher;

class TransactionManager
{
    private $dispatcher;

    public function __construct(DeferredEventDispatcher $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    public function wrap(callable $function): void
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if ($this->dispatcher) {
                $this->dispatcher->defer();
            }
            $function();
            $transaction->commit();
            if ($this->dispatcher) {
                $this->dispatcher->release();
            }
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
            $transaction->rollBack();
            if ($this->dispatcher) {
                $this->dispatcher->clean();
            }
            throw $e;
        }
    }
}
