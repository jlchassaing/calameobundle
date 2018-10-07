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
     * Calameo content name
     *
     * @var string
     */
    public $name;

    /**
     * Calamo table of contents
     *
     * @var array
     */
    public $toc;

    /**
     * @return string
     */
    public function __toString()
    {
       return $this->url;
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