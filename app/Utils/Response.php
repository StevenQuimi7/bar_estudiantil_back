<?php

namespace App\Utils;

class Response
{
    protected $ok;
    protected $msj_error;
    protected $data;
    protected $code;

    public function __construct($ok = true, $msj_error = "", $data = null, $code=500)
    {
        $this->ok = $ok;
        $this->msj_error = $msj_error;
        $this->data = $data;
        $this->code = $code;
    }

    public function getOk()
    {
        return $this->ok;
    }

    public function getMsjError()
    {
        return $this->msj_error;
    }

    public function getData()
    {
        return $this->data;
    }
    public function getCode()
    {
        return $this->code;
    }

    public function setOk($ok)
    {
        $this->ok = $ok;
    }
    public function setCode($code)
    {
        $this->code = $code;
    }

    public function setMsjError($msj_error)
    {
        $this->msj_error = $msj_error;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}