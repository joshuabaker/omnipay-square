<?php

namespace Omnipay\Square\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Class CancelPaymentRequest
 *
 * @package Omnipay\Square\Message
 */
class CancelPaymentRequest extends AbstractRequest
{
    /**
     * @var string
     */
    protected $liveEndpoint = 'https://connect.squareup.com';

    /**
     * @var string
     */
    protected $testEndpoint = 'https://connect.squareupsandbox.com';

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->getParameter('accessToken');
    }

    /**
     * @param $value
     *
     * @return \Omnipay\Square\Message\CancelPaymentRequest
     */
    public function setAccessToken($value)
    {
        return $this->setParameter('accessToken', $value);
    }

    /**
     * @return mixed|string
     */
    public function getTransactionReference()
    {
        return $this->getParameter('transactionReference');
    }

    /**
     * @param string $value
     *
     * @return \Omnipay\Common\Message\AbstractRequest|\Omnipay\Square\Message\CompletePaymentRequest
     */
    public function setTransactionReference($value)
    {
        return $this->setParameter('transactionReference', $value);
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->getTestMode() === true ? $this->testEndpoint : $this->liveEndpoint;
    }

    /**
     * @return \SquareConnect\Api\PaymentsApi
     */
    private function getApiInstance()
    {
        $api_config = new \SquareConnect\Configuration();
        $api_config->setHost($this->getEndpoint());
        $api_config->setAccessToken($this->getAccessToken());
        $api_client = new \SquareConnect\ApiClient($api_config);

        return new \SquareConnect\Api\PaymentsApi($api_client);
    }

    /**
     * @return array|mixed
     */
    public function getData()
    {
        return [
            'paymentId' => $this->getTransactionReference(),
        ];
    }

    /**
     * @param mixed $data
     *
     * @return \Omnipay\Square\Message\CancelPaymentResponse
     */
    public function sendData($data)
    {
        try {
            $api_instance = $this->getApiInstance();

            $result = $api_instance->cancelPayment($data['paymentId']);

            if ($error = $result->getErrors()) {
                $response = [
                    'status' => 'error',
                    'code' => $error['code'],
                    'detail' => $error['detail'],
                ];
            } else {
                $response = [
                    'status' => 'success',
                    'transactionId' => $result->getPayment()->getId(),
                    'referenceId' => $result->getPayment()->getReferenceId(),
                    'created_at' => $result->getPayment()->getCreatedAt(),
                    'orderId' => $result->getPayment()->getOrderId(),
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'detail' => 'Exception when creating transaction: ' . $e->getMessage(),
            ];
        }

        return $this->createResponse($response);
    }

    /**
     * @param $response
     *
     * @return \Omnipay\Square\Message\CancelPaymentResponse
     */
    public function createResponse($response)
    {
        return $this->response = new CancelPaymentResponse($this, $response);
    }
}
