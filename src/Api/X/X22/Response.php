<?php

namespace grandmasterx\WebMoney\Api\X\X22;

use grandmasterx\WebMoney\Exception\ApiException;
use grandmasterx\WebMoney\Request\AbstractResponse;

class Response extends AbstractResponse
{

    const URL_LANG_RU = 'ru';
    
    const URL_LANG_EN = 'en';

    /** @var string transtoken */
    protected $transactionToken;

    /** @var int validityperiodinhours */
    protected $validityPeriodInHours;

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);
        $responseObject = new \SimpleXMLElement($response);
        $this->returnCode = (int)$responseObject->retval;
        $this->returnDescription = (string)$responseObject->retdesc;
        if (isset($responseObject->transtoken)) {
            $this->transactionToken = (string)$responseObject->transtoken;
        }
        if (isset($responseObject->validityperiodinhours)) {
            $this->validityPeriodInHours = (string)$responseObject->validityperiodinhours;
        }
    }

    /**
     * @return string token
     */
    public function getTransactionToken()
    {
        return $this->transactionToken;
    }

    /**
     * @return string
     */
    public function getValidityPeriodInHours()
    {
        return $this->validityPeriodInHours;
    }

    /**
     * @param string $lang
     * @throws ApiException
     * @return string URL
     */
    public function getUrl($lang = self::URL_LANG_EN)
    {
        switch ($lang) {
            case self::URL_LANG_RU:
                return 'https://merchant.webmoney.ru/lmi/payment.asp?gid=' . $this->transactionToken;
            case self::URL_LANG_EN:
                return 'https://merchant.wmtransfer.com/lmi/payment.asp?gid=' . $this->transactionToken;
            default:
                throw new ApiException('Unknown lang value: ' . $lang);
        }
    }
}
