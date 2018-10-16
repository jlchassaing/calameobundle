<?php
/**
 * Created by PhpStorm.
 * User: jlchassaing
 * Date: 16/10/2018
 * Time: 09:41
 */

namespace CalameoBundle\Calameo;


class Response
{


    public function __construct($response)
    {

        $this->data = $response['response'];
    }

    public function isError()
    {
        return $this->data['status'] === 'error';
    }

    public function getError()
    {
        return $this->data['error']['message'];
    }

    public function getErrorCode()
    {
        return $this->data['error']['code'];
    }

    public function getContent()
    {
        return $this->data['content'];
    }

    public function __get( $name )
    {
        return $this->data['content'][$name];
    }


}