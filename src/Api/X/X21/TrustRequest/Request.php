<?php

namespace grandmasterx\WebMoney\Api\X\X21\TrustRequest;

use grandmasterx\WebMoney\Api\X;
use grandmasterx\WebMoney\Signer;
use grandmasterx\WebMoney\Exception\ApiException;
use grandmasterx\WebMoney\Request\RequestValidator;
use grandmasterx\WebMoney\Api\X\Request as XRequest;

/**
 * Class Request
 *
 * @link http://wiki.wmtransfer.com/projects/webmoney/wiki/Interface_X21
 */
class Request extends XRequest
{

    const CLIENT_NUMBER_TYPE_PHONE = 0;

    const CLIENT_NUMBER_TYPE_WMID = 1;

    const CLIENT_NUMBER_TYPE_EMAIL = 2;

    const CLIENT_NUMBER_TYPE_PURSE = 4;

    const SMS_TYPE_SMS = 1;

    const SMS_TYPE_USSD = 2;

    const LANGUAGE_RU = 'ru-RU';

    const LANGUAGE_EN = 'en-US';

    /** @var string lmi_payee_purse */
    protected $payeePurse;

    /** @var float lmi_day_limit */
    protected $dayLimit = 0;

    /** @var float lmi_week_limit */
    protected $weekLimit = 0;

    /** @var float lmi_month_limit */
    protected $monthLimit = 0;

    /** @var string lmi_clientnumber */
    protected $clientNumber;

    /** @var int lmi_clientnumber_type */
    protected $clientNumberType;

    /** @var int lmi_sms_type */
    protected $smsType;

    /** @var string lang */
    protected $language;

    /**
     * @param string $authType
     *
     * @throws ApiException
     */
    public function __construct($authType = self::AUTH_CLASSIC)
    {
        switch ($authType) {
            case self::AUTH_CLASSIC:
                $this->url = 'https://merchant.webmoney.ru/conf/xml/XMLTrustRequest.asp';
                break;
            case self::AUTH_LIGHT:
                $this->url = 'https://merchant.wmtransfer.com/conf/xml/XMLTrustRequest.asp';
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
            RequestValidator::TYPE_REQUIRED => [
                'signerWmid',
                'payeePurse',
                'clientNumber',
                'clientNumberType',
                'smsType'
            ],
            RequestValidator::TYPE_RANGE => [
                'clientNumberType' => [
                    self::CLIENT_NUMBER_TYPE_PHONE,
                    self::CLIENT_NUMBER_TYPE_WMID,
                    self::CLIENT_NUMBER_TYPE_EMAIL,
                    self::CLIENT_NUMBER_TYPE_PURSE
                ],
                'smsType' => [
                    self::SMS_TYPE_SMS,
                    self::SMS_TYPE_USSD
                ],
                'language' => [
                    self::LANGUAGE_RU,
                    self::LANGUAGE_EN
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function getData()
    {
        $xml = '<merchant.request>';
        $xml .= self::xmlElement('wmid', $this->signerWmid);
        $xml .= self::xmlElement('lmi_payee_purse', $this->payeePurse);
        $xml .= self::xmlElement('lmi_day_limit', $this->dayLimit);
        $xml .= self::xmlElement('lmi_week_limit', $this->weekLimit);
        $xml .= self::xmlElement('lmi_month_limit', $this->monthLimit);
        $xml .= self::xmlElement('lmi_clientnumber', $this->clientNumber);
        $xml .= self::xmlElement('lmi_clientnumber_type', $this->clientNumberType);
        $xml .= self::xmlElement('lmi_sms_type', $this->smsType);
        $xml .= self::xmlElement('sign', $this->signature);
        $xml .= self::xmlElement('lang', $this->language);
        $xml .= '</merchant.request>';
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
     */
    public function sign(Signer $requestSigner = null)
    {
        $params = [
            $this->signerWmid,
            $this->payeePurse,
            $this->clientNumber,
            $this->clientNumberType,
            $this->smsType
        ];
        if ($this->authType === self::AUTH_CLASSIC) {
            $this->signature = $requestSigner->sign(implode('', $params));
        }
    }

    /**
     * @return string lmi_payee_purse
     */
    public function getPayeePurse()
    {
        return $this->payeePurse;
    }

    /**
     * @param string $payeePurse lmi_payee_purse
     */
    public function setPayeePurse($payeePurse)
    {
        $this->payeePurse = (string)$payeePurse;
    }

    /**
     * @return float lmi_day_limit
     */
    public function getDayLimit()
    {
        return $this->dayLimit;
    }

    /**
     * @param float $dayLimit lmi_day_limit
     */
    public function setDayLimit($dayLimit)
    {
        $this->dayLimit = (float)$dayLimit;
    }

    /**
     * @return float lmi_week_limit
     */
    public function getWeekLimit()
    {
        return $this->weekLimit;
    }

    /**
     * @param float $weekLimit lmi_week_limit
     */
    public function setWeekLimit($weekLimit)
    {
        $this->weekLimit = (float)$weekLimit;
    }

    /**
     * @return float lmi_month_limit
     */
    public function getMonthLimit()
    {
        return $this->monthLimit;
    }

    /**
     * @param float $monthLimit lmi_month_limit
     */
    public function setMonthLimit($monthLimit)
    {
        $this->monthLimit = (float)$monthLimit;
    }

    /**
     * @return string lmi_clientnumber
     */
    public function getClientNumber()
    {
        return $this->clientNumber;
    }

    /**
     * @param string $clientNumber lmi_clientnumber
     */
    public function setClientNumber($clientNumber)
    {
        $this->clientNumber = (string)$clientNumber;
    }

    /**
     * @return int lmi_clientnumber_type
     */
    public function getClientNumberType()
    {
        return $this->clientNumberType;
    }

    /**
     * @param int $clientNumberType lmi_clientnumber_type
     */
    public function setClientNumberType($clientNumberType)
    {
        $this->clientNumberType = (int)$clientNumberType;
    }

    /**
     * @return int lmi_sms_type
     */
    public function getSmsType()
    {
        return $this->smsType;
    }

    /**
     * @param int $smsType lmi_sms_type
     */
    public function setSmsType($smsType)
    {
        $this->smsType = (int)$smsType;
    }

    /**
     * @return string lang
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language lang
     */
    public function setLanguage($language)
    {
        $this->language = (string)$language;
    }
}
