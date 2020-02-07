<?php

namespace Omnipay\Square\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Class CancelPaymentResponse
 *
 * @package Omnipay\Square\Message
 */
class CancelPaymentResponse extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->data['status'] === 'success';
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        return false;
    }

    /**
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->data['transactionId'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getOrderId()
    {
        return $this->data['orderId'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->data['created_at'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getReferenceId()
    {
        return $this->data['referenceId'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        $message = '';
        if (isset($this->data['code'])) {
            $message .= $this->data['code'] . ': ';
        }

        return $message . ($this->data['detail'] ?? '');
    }

    /**
     * @return string|null
     */
    public function getTransactionReference()
    {
        return $this->getTransactionId();
    }
}
