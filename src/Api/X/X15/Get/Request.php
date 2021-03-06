<?php

namespace grandmasterx\WebMoney\Api\X\X15\Get;

use grandmasterx\WebMoney\Api\X;
use grandmasterx\WebMoney\Signer;
use grandmasterx\WebMoney\Exception\ApiException;
use grandmasterx\WebMoney\Request\RequestValidator;

/**
 * Class Request
 *
 * @link https://wiki.wmtransfer.com/projects/webmoney/wiki/Interface_X15
 */
class Request extends X\Request
{

    /** @var string gettrustlist/wmid */
    protected $requestedWmid;

    public function __construct($authType = self::AUTH_CLASSIC)
    {
        if (!in_array($authType, [self::AUTH_CLASSIC, self::AUTH_LIGHT])) {
            throw new ApiException('This interface doesn\'t support the authentication type given.');
        }

        parent::__construct($authType);
    }

    public function getUrl()
    {
        if ($this->authType === self::AUTH_CLASSIC) {
            if ($this->signerWmid === $this->requestedWmid) {
                $this->url = 'https://w3s.webmoney.ru/asp/XMLTrustList.asp';
            } else {
                $this->url = 'https://w3s.webmoney.ru/asp/XMLTrustList2.asp';
            }
        } elseif ($this->authType === self::AUTH_LIGHT) {
            if ($this->signerWmid === $this->requestedWmid) {
                $this->url = 'https://w3s.webmoney.ru/asp/XMLTrustListCert.asp';
            } else {
                $this->url = 'https://w3s.webmoney.ru/asp/XMLTrustList2Cert.asp';
            }
        }

        return parent::getUrl();
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
        $params = [
            $this->requestedWmid,
            $this->requestNumber
        ];
        if ($this->authType === self::AUTH_CLASSIC) {
            $this->signature = $requestSigner->sign(implode('', $params));
        }
    }

    /**
     * @return array
     */
    protected function getValidationRules()
    {
        return [
            RequestValidator::TYPE_REQUIRED => ['requestedWmid'],
            RequestValidator::TYPE_DEPEND_REQUIRED => ['signerWmid' => ['authType' => [self::AUTH_CLASSIC]]]
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
        $xml .= '<gettrustlist>';
        $xml .= self::xmlElement('wmid', $this->requestedWmid);
        $xml .= '</gettrustlist>';
        $xml .= '</w3s.request>';

        return $xml;
    }

    /**
     * @return string
     */
    public function getRequestedWmid()
    {
        return $this->requestedWmid;
    }

    /**
     * @param string $requestedWmid
     */
    public function setRequestedWmid($requestedWmid)
    {
        $this->requestedWmid = (string)$requestedWmid;
    }
}
