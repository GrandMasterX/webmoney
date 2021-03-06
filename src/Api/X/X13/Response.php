<?php

namespace grandmasterx\WebMoney\Api\X\X13;

use grandmasterx\WebMoney\Request\AbstractResponse;

/**
 * Class Response
 *
 * @link https://wiki.wmtransfer.com/projects/webmoney/wiki/Interface_X13
 */
class Response extends AbstractResponse
{

    const STATUS_COMPLETED = 0;

    const STATUS_NOT_COMPLETED = 4;
    
    const STATUS_REFUNDED = 12;

    /** @var int reqn */
    protected $requestNumber;

    /** @var int @id */
    protected $transactionId;

    /** @var int operation\opertype */
    protected $status;

    /** @var \DateTime operation\dateupd */
    protected $updateDateTime;

    public function __construct($response)
    {
        parent::__construct($response);

        $responseObject = new \SimpleXMLElement($response);
        $this->requestNumber = (int)$responseObject->reqn;
        $this->returnCode = (int)$responseObject->retval;
        $this->returnDescription = (string)$responseObject->retdesc;

        if (isset($responseObject->operation)) {
            $operation = $responseObject->operation;
            $this->transactionId = (int)$operation['id'];
            $this->status = (int)$operation->opertype;
            $this->updateDateTime = self::createDateTime((string)$operation->dateupd);
        }
    }

    /**
     * @return int
     */
    public function getRequestNumber()
    {
        return $this->requestNumber;
    }

    /**
     * @return int
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDateTime()
    {
        return $this->updateDateTime;
    }
}
