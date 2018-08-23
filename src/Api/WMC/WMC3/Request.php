<?php

namespace grandmasterx\WebMoney\Api\WMC\WMC3;

use DateTime;
use grandmasterx\WebMoney\Signer;
use grandmasterx\WebMoney\Api\WMC;
use grandmasterx\WebMoney\Exception\ApiException;
use grandmasterx\WebMoney\Request\RequestValidator;
use grandmasterx\WebMoney\Api\WMC\Request as WMCRequest;

/**
 * Class Request
 *
 * @link http://wiki.wmtransfer.com/projects/webmoney/wiki/Interface_WMC3
 */
class Request extends WMCRequest
{

    /** @var DateTime datestart */
    protected $dateStart;

    /** @var DateTime dateend */
    protected $dateEnd;

    /** @var int wmtranid */
    protected $transactionId;

    /**
     * @param string $authType
     *
     * @throws ApiException
     */
    public function __construct($authType = self::AUTH_CLASSIC)
    {
        switch ($authType) {
            case self::AUTH_CLASSIC:
                $this->url = 'https://transfer.gdcert.com/ATM/Xml/History1.ashx';
                break;
            case self::AUTH_LIGHT:
                $this->url = 'https://transfer.gdcert.com/ATM/Xml/History1.ashx';
                break;
            default:
                throw new ApiException('This interface doesn\'t support the authentication type given.');
        }

        parent::__construct($authType);
    }

    /**
     * @return array
     */
    protected function getValidationRules()
    {
        return [RequestValidator::TYPE_REQUIRED => ['datestart', 'dateend', 'transactionId']];
    }

    /**
     * @return string
     */
    public function getData()
    {
        $xml = '<w3s.request lang="' . $this->getLang() . '">';
        $xml .= self::xmlElement('wmid', $this->getSignerWmid());
        $xml .= '<sign type="' . $this->getAuthTypeNum() . '">' . $this->getSignature() . '</sign>';
        $xml .= self::xmlElement('datestart', $this->getStartDateTime()->format('Ymd H:i:s'));
        $xml .= self::xmlElement('dateend', $this->getEndDateTime()->format('Ymd H:i:s'));
        $xml .= self::xmlElement('wmtranid', $this->getTransactionId());
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
            $this->getStartDateTime()->format('Ymd H:i:s'),
            $this->getEndDateTime()->format('Ymd H:i:s'),
            $this->getTransactionId()
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
            $this->getStartDateTime()->format('Ymd H:i:s'),
            $this->getEndDateTime()->format('Ymd H:i:s'),
            $this->getTransactionId()
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
     * @param int $id
     */
    public function setTransactionId($id)
    {
        $this->transactionId = (int)$id;
    }

    /**
     * @return DateTime
     */
    public function getStartDateTime()
    {
        return $this->dateStart;
    }

    /**
     * @param DateTime $date
     */
    public function setStartDateTime(DateTime $date)
    {
        $this->datestart = $date;
    }

    /**
     * @return DateTime
     */
    public function getEndDateTime()
    {
        return $this->dateEnd;
    }

    /**
     * @param DateTime $date
     */
    public function setEndDateTime(DateTime $date)
    {
        $this->dateend = $date;
    }
}
