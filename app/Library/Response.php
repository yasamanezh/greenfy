<?php

namespace App\Library;

class Response
{

    const HTTP_OK = 200;
    const HTTP_INVALID_REQUEST = 400;
    const HTTP_NOT_FOUND = 404;
    const NOT_ACCEPTABLE = 406;

    /** @var  string $data */
    private $data;
    /** @var  integer $statusCode */
    private $statusCode;
    /** @var  boolean $success */
    private $success;

    public function __construct($data = null, $success = true, $statusCode = Response::HTTP_OK)
    {
        $this->data = $data;
        $this->success = $success;
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return  Response
     */
    public function setMessage($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return  Response
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @param bool $success
     * @return  Response
     */
    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

    public function getArray()
    {
        $ret = get_object_vars($this);
        if ($ret["data"] == null)
            unset($ret["data"]);

        return $ret;
    }

    public function getJson(){
        return json_encode($this->getArray());
    }
}
