<?php

namespace grandmasterx\WebMoney;

use grandmasterx\WebMoney\Exception\CoreException;
use grandmasterx\WebMoney\Request\AbstractRequest;
use grandmasterx\WebMoney\Request\AbstractResponse;
use grandmasterx\WebMoney\Request\Requester\AbstractRequester;

class WebMoney
{

    /** @var AbstractRequester */
    private $xmlRequester;

    /**
     * @param AbstractRequester $xmlRequester
     */
    public function __construct(AbstractRequester $xmlRequester)
    {
        $this->xmlRequester = $xmlRequester;
    }

    /**
     * @param AbstractRequest $requestObject
     *
     * @return AbstractResponse
     * @throws CoreException
     */
    public function request(AbstractRequest $requestObject)
    {
        if (!$requestObject->validate()) {
            throw new CoreException('Incorrect request data. See getErrors().');
        }
        return $this->xmlRequester->perform($requestObject);
    }
}
