<?php
/**
 * Created by PhpStorm.
 * User: jlchassaing
 * Date: 05/10/2018
 * Time: 10:33
 */

namespace CalameoBundle\FieldType\Calameo;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\SPI\FieldType\TypeValue;
use CalameoBundle\FieldType\Calameo\Value;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\FieldType\Value as BaseValue;

class Type extends FieldType
{

    protected $validatorConfigurationSchema = [];

    protected function createValueFromInput( $inputValue )
    {
        if (is_string($inputValue)) {
            $inputValue = new Value(['url' => $inputValue]);
        }

        return $inputValue;
    }


    /**
     * @param BaseValue $value
     *
     * @throws InvalidArgumentType
     */
    protected function checkValueStructure( BaseValue $value )
    {
        if (!is_string($value->url) or $value->url === ' ')
        {
            throw new InvalidArgumentType(
                '$value->url',
                'string',
                get_class($this)
            );
        }
    }


    public function getFieldTypeIdentifier()
    {
        return "ezcalameo";
    }

    public function getName( SPIValue $value )
    {
        return (string)$value->url;
    }

    public function getEmptyValue()
    {
        return new Value();
    }

    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        return new Value($hash);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \eZ\Publish\Core\FieldType\TextLine\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        return $value->url;
    }


}