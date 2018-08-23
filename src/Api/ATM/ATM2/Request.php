<?php

namespace grandmasterx\WebMoney\Api\ATM\ATM2;

use DateTime;
use grandmasterx\WebMoney\Signer;
use grandmasterx\WebMoney\Api\ATM;
use grandmasterx\WebMoney\Exception\ApiException;
use grandmasterx\WebMoney\Request\RequestValidator;

/**
 * Class Request
 *
 * @link http://wiki.wmtransfer.com/projects/webmoney/wiki/Interface_ATM2
 */
class Request extends ATM\Request
{

    const TRANSACTION_TYPE_REAL = 0;

    const TRANSACTION_TYPE_TEST = 1;

    /** @var int payment/@id */
    protected $transactionId;

    /** @var string payment/@currency */
    protected $currency;

    /** @var int payment/@test */
    protected $test;

    /** @var string payment/@exchange */
    protected $exchange;

    /** @var string payment/purse */
    protected $payeePurse;

    /** @var float payment/price */
    protected $price;

    /** @var DateTime payment/date */
    protected $date;

    /** @var int payment/point */
    protected $point;

    /**
     * @param string $authType
     *
     * @throws ApiException
     */
    public function __construct($authType = self::AUTH_CLASSIC)
    {
        $this->url = 'https://transfer.gdcert.com/ATM/Xml/Payment2.ashx';
        $this->setTest(self::TRANSACTION_TYPE_REAL);
        parent::__construct($authType);
    }

    /**
     * @return array
     */
    protected function getValidationRules()
    {
        return [
            RequestValidator::TYPE_REQUIRED => ['lang', 'transactionId', 'currency', 'test', 'price', 'date', 'point']
        ];
    }

    /**
     * @return string
     */
    public function getData()
    {
        $xml = '<w3s.request lang="' . $this->getLang() . '">';
        $xml .= self::xmlElement('wmid', $this->getSignerWmid());
        $xml .= '<sign type="' . $this->getAuthTypeNum() . '">' . $this->getSignature() . '</sign>';
        $xml .= '<payment id="' . $this->getTransactionId() . '" currency="' . $this->getCurrency() . '" test="' . $this->getTest() . '" exchange="' . $this->getExchange() . '">';
        $xml .= self::xmlElement('purse', $this->getPayeePurse());
        $xml .= self::xmlElement('price', $this->getPrice());
        $xml .= self::xmlElement('date', $this->getDate()->format('Ymd H:i:s'));
        $xml .= self::xmlElement('point', $this->getPoint());
        $xml .= '</payment>';
        $xml .= '</w3s.request>';
        return $xml;
    }

    /**
     * @return string
     */
    public function getResponseClassName()
    {
        return Response::className();
    }

    /**
     * @inheritdoc
     */
    public function lightAuth($certificate, $key, $keyPassword = '')
    {
        $params = [
            $this->getSignerWmid(),
            $this->getTransactionId(),
            $this->getCurrency(),
            $this->getTest(),
            $this->getPayeePurse(),
            $this->getPrice(),
            $this->getDate()->format('Ymd H:i:s'),
            $this->getPoint()
        ];
        if ($this->authType === self::AUTH_LIGHT) {
            $this->setSignature($this->signLight(implode('', $params), $key, $keyPassword));
        }
        parent::lightAuth($certificate, $key, $keyPassword);
    }

    /**
     * @param Signer $requestSigner
     */
    public function sign(Signer $requestSigner = null)
    {
        $params = [
            $this->getSignerWmid(),
            $this->getTransactionId(),
            $this->getCurrency(),
            $this->getTest(),
            $this->getPayeePurse(),
            $this->getPrice(),
            $this->getDate()->format('Ymd H:i:s'),
            $this->getPoint()
        ];
        if ($this->authType === self::AUTH_CLASSIC) {
            $this->setSignature($requestSigner->sign(implode('', $params)));
        }
    }

    /**
     * @return int
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param int
     */
    public function setTransactionId($id)
    {
        $this->transactionId = (int)$id;
    }

    /**
     * @return string
     */
    public function getPayeePurse()
    {
        return $this->payeePurse;
    }

    /**
     * @param string $payeePurse
     */
    public function setPayeePurse($payeePurse)
    {
        $this->payeePurse = (string)$payeePurse;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = (float)$price;
    }

    /**
     * @return int
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * @param int $test
     */
    public function setTest($test)
    {
        $this->test = (int)$test;
    }

    /**
     * @return string CURRENCY_EUR|CURRENCY_RUB|CURRENCY_USD
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency CURRENCY_EUR|CURRENCY_RUB|CURRENCY_USD
     */
    public function setCurrency($currency)
    {
        $this->currency = (string)$currency;
    }

    /**
     * @return int
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @param string $exchange
     */
    public function setExchange($exchange)
    {
        $this->exchange = (string)$exchange;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * @param int $point
     */
    public function setPoint($point)
    {
        $this->point = (int)$point;
    }
}
