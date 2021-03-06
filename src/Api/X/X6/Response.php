<?php

namespace grandmasterx\WebMoney\Api\X\X6;

use grandmasterx\WebMoney\Request\AbstractResponse;

/**
 * Class Response
 *
 * @link http://wiki.wmtransfer.com/projects/webmoney/wiki/Interface_X6
 */
class Response extends AbstractResponse
{

    /** @var int reqn */
    protected $requestNumber;

    /** @var Message message */
    protected $message;

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $responseObject = new \SimpleXMLElement($response);
        $this->requestNumber = (int)$responseObject->reqn;
        $this->returnCode = (int)$responseObject->retval;
        $this->returnDescription = (string)$responseObject->retdesc;
        $this->message = new Message(
            (string)$responseObject->message->receiverwmid,
            (string)$responseObject->message->msgsubj,
            (string)$responseObject->message->msgtext,
            self::createDateTime((string)$responseObject->message->datecrt)
        );
    }

    /**
     * @return int
     */
    public function getRequestNumber()
    {
        return $this->requestNumber;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
