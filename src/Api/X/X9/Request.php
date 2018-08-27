<?php

namespace grandmasterx\WebMoney\Api\X\X9;

use grandmasterx\WebMoney\Api\X;
use grandmasterx\WebMoney\Signer;
use grandmasterx\WebMoney\Exception\ApiException;
use grandmasterx\WebMoney\Request\RequestValidator;

/**
 * Class Request
 *
 * @link https://wiki.wmtransfer.com/projects/webmoney/wiki/Interface_X9
 */
class Request extends X\Request
{
    /** @var string getpurses/wmid */
    protected $requestedWmid;

    public function __construct($authType = self::AUTH_CLASSIC)
    {
        switch ($authType) {
            case self::AUTH_CLASSIC:
                $this->url = 'https://w3s.webmoney.ru/asp/XMLPurses.asp';
                break;
            case self::AUTH_LIGHT:
                $this->url = 'https://w3s.wmtransfer.com/asp/XMLPursesCert.asp';
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
        $xml .= '<getpurses>';
        $xml .= self::xmlElement('wmid', $this->requestedWmid);
        $xml .= '</getpurses>';
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
     * @param Signer $requestSigner
     *
     */
    public function sign(Signer $requestSigner = null)
    {
        if ($this->authType === self::AUTH_CLASSIC) {
            $this->signature = $requestSigner->sign($this->requestedWmid . $this->requestNumber);
        }
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
