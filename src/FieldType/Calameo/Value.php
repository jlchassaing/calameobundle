<?php
/**
 * @author jlchassaing <jlchassaing@gmail.com>
 *
 *
 */

namespace CalameoBundle\FieldType\Calameo;

use eZ\Publish\Core\FieldType\Value as CoreValue;
use eZ\Publish\SPI\FieldType\Value as InterfaceValue;

/**
 * Calameo filed type value class
 *
 * Class Value
 * @package CalameoBundle\FieldType\Calameo
 */
class Value extends CoreValue implements InterfaceValue
{

    /**
     * Calameo reed url
     *
     * @var string
     */
    public $url;

    /**
     * @var array
     */
    public $data;

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return $this->data !== null ?
         array_key_exists($name,$this->data) ? $this->data[$name] : null
            : null;
    }

    public function  __call($name,$args = null)
    {
        return $this->data[$name];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->data['Name'] === null ? $this->url: "no";
       // return ['url' => $this->url];
    }

    /**
     * Value constructor.
     *
     * @param array $properties
     */
    public function __construct( $properties = [] )
    {
        if (!is_array($properties) && $properties !== null)
        {
            $properties = ["url" => $properties];
        }

        parent::__construct( $properties );

    }




}