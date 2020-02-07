<?php

namespace Omnipay\Square\Message;

use Omnipay\Common\Message\AbstractRequest;
use SquareConnect;

/**
 * Square Authorize Request
 */
class CompletePaymentRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://connect.squareup.com';
    protected $testEndpoint = 'https://connect.squareupsandbox.com';

    public function getAccessToken()
    {
        return $this->getParameter('accessToken');
    }

    public function setAccessToken($value)
    {
        return $this->setParameter('accessToken', $value);
    }

    public function getPaymentId()
    {
        return $this->getParameter('paymentId');
    }

    public function setPaymentId($value)
    {
        return $this->setParameter('paymentId', $value);
    }

    public function getEndpoint()
    {
        return $this->getTestMode() === true ? $this->testEndpoint : $this->liveEndpoint;
    }

    private function getApiInstance()
    {
        $api_config = new \SquareConnect\Configuration();
        $api_config->setHost($this->getEndpoint());
        $api_config->setAccessToken($this->getAccessToken());
        $api_client = new \SquareConnect\ApiClient($api_config);

        return new \SquareConnect\Api\PaymentsApi($api_client);
    }

    public function getData()
    {
        return [
            'paymentId' => $this->getPaymentId(),
        ];
    }

    public function sendData($data)
    {
        try {
            $api_instance = $this->getApiInstance();

            $result = $api_instance->completePayment($data['paymentId']);

            if ($error = $result->getErrors()) {
                $response = [
                    'status' => 'error',
                    'code' => $error['code'],
                    'detail' => $error['detail']
                ];
            } else {
                $response = [
                    'status' => 'success',
                    'transactionId' => $result->getPayment()->getId(),
                    'referenceId' => $result->getPayment()->getReferenceId(),
                    'created_at' => $result->getPayment()->getCreatedAt(),
                    'orderId' => $result->getPayment()->getOrderId()
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'detail' => 'Exception when creating transaction: ' . $e->getMessage()
            ];
        }

        return $this->createResponse($response);
    }

    public function createResponse($response)
    {
        return $this->response = new CompletePaymentResponse($this, $response);
    }
}
