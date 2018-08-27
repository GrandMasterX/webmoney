<?php

namespace grandmasterx\WebMoney\Request\Requester;

use grandmasterx\WebMoney\Request\AbstractRequest;
use grandmasterx\WebMoney\Request\AbstractResponse;

abstract class AbstractRequester
{
    protected $verifyCertificate;

    /**
     * @param bool $verifyCertificate Use the WM root certificate to protect against DNS spoofing
     */
    public function __construct($verifyCertificate = true)
    {
        $this->verifyCertificate = $verifyCertificate;
    }

    /**
     * @param AbstractRequest $request
     *
     * @return AbstractResponse
     */
    public function perform(AbstractRequest $request)
    {
        $response = $this->request($request);
        $responseClassName = $request->getResponseClassName();
        return new $responseClassName($response);
    }

    /**
     * @param AbstractRequest $request
     *
     * @return mixed
     */
    abstract protected function request(AbstractRequest $request);
}
