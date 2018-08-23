<?php

namespace grandmasterx\WebMoney\Api\X\X23;

use grandmasterx\WebMoney\Api\X;
use grandmasterx\WebMoney\Signer;
use grandmasterx\WebMoney\Exception\ApiException;
use grandmasterx\WebMoney\Request\RequestValidator;

/**
 * Class Request
 *
 * @link https://wiki.wmtransfer.com/projects/webmoney/wiki/Interface_X23
 */
class Request extends X\Request
{

    /** @var int invoicerefuse\wmid */
    protected $wmid;

    /** @var int invoicerefuse\wminvid */
    protected $invoiceId;

    public function __construct($authType = self::AUTH_CLASSIC)
    {
        switch ($authType) {
            case self::AUTH_CLASSIC:
                $this->url = 'https://w3s.webmoney.ru/asp/XMLInvoiceRefusal.asp';
                break;

            case self::AUTH_LIGHT:
                $this->url = 'https://w3s.wmtransfer.com/asp/XMLInvoiceRefusalCert.asp';
                break;

            default:
                throw new ApiException('This interface doesn\'t support the authentication type given.');
        }

        parent::__construct($authType);
    }

    /**
     * @return string
     */
    public function getResponseClassName()
    {
        return Response::className();
    }

    /**
     * @param Signer $requestSigner
     *
     */
    public function sign(Signer $requestSigner = null)
    {
        if ($this->authType === self::AUTH_CLASSIC) {
            $this->signature = $requestSigner->sign(
                implode('', [$this->wmid, $this->invoiceId, $this->requestNumber])
            );
        }
    }

    /**
     * @return array
     */
    protected function getValidationRules()
    {
        return [
            RequestValidator::TYPE_REQUIRED => ['wmid', 'invoiceId'],
            RequestValidator::TYPE_DEPEND_REQUIRED => [
                'signerWmid' => ['authType' => [self::AUTH_CLASSIC]]
            ]
        ];
    }

    /**
     * @return string
     */
    public function getData()
    {
        $xml = '<w3s.request>';
        $xml .= self::xmlElement('reqn', $this->requestNumber);
        $xml .= self::xmlElement('wmid', $this->signerWmid);
        $xml .= self::xmlElement('sign', $this->signature);
        $xml .= '<invoicerefuse>';
        $xml .= self::xmlElement('wmid', $this->wmid);
        $xml .= self::xmlElement('wminvid', $this->invoiceId);
        $xml .= '</invoicerefuse>';
        $xml .= '</w3s.request>';
        return $xml;
    }

    /**
     * @return string
     */
    public function getWmid()
    {
        return $this->wmid;
    }

    /**
     * @param string $wmid
     */
    public function setWmid($wmid)
    {
        $this->wmid = (string)$wmid;
    }

    /**
     * @return string
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * @param string $invoiceId
     */
    public function setInvoiceId($invoiceId)
    {
        $this->invoiceId = (string)$invoiceId;
    }
}
