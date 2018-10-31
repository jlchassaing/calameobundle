<?php
/**
 * Created by PhpStorm.
 * User: jlchassaing
 * Date: 05/10/2018
 * Time: 10:33
 */

namespace CalameoBundle\FieldType\Calameo;

use CalameoBundle\Calameo\Client;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\SPI\FieldType\TypeValue;
use CalameoBundle\FieldType\Calameo\Value;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\FieldType\Value as BaseValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue as PersistenceValue;
use eZ\Publish\Core\FieldType\ValidationError;
use http\Env\Response;

class Type extends FieldType
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }


    protected $validatorConfigurationSchema = [];
    
    

    protected function createValueFromInput( $inputValue = null )
    {
        if (is_string($inputValue) && $inputValue !== '') {
            $inputValue = new Value([
                'url' => $inputValue,
                ]);
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

    public function validate( FieldDefinition $fieldDefinition, SPIValue $fieldValue )
    {
        $errors = [];

        if ($this->isEmptyValue($fieldValue)) {
            return $errors;
        }

        // Calameo URL validation
        if (!$this->client->isValidUrl($fieldValue->url)) {
            $errors[] = new ValidationError(
                'Invalid Calameo status URL %url%',
                null,
                ['%url%' => $fieldValue->url]
            );
        }
        return $errors;

    }


    public function getFieldTypeIdentifier()
    {
        return "ezcalameo";
    }

    public function getName( SPIValue $value )
    {
        /*throw new \RuntimeException(
            'Name generation provided via NameableField set via "ezpublish.fieldType.nameable" service tag'
        );*/
        return "calameo";
    }

    public function getEmptyValue()
    {
        return  new Value;
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

        return ['url' => $value->url,'data' => $value->data];
    }


    public function toPersistenceValue(SPIValue $value)
    {
        if ($value->url === null) {
            return new PersistenceValue(
                array(
                    'data' => array(),
                    'externalData' => null,
                    'sortKey' => null,
                )
            );
        }


        if ($value->name === null)
        {
            $code = $this->client->getKeyFromUrl($value->url);
            $this->addToValue($value,$this->client->getBookInfo($code))
                 ->addToValue($value,$this->client->getToc($code));

        }

        return new PersistenceValue(
            [
            'data' => $this->toHash($value),
            'sortKey' => $this->getSortInfo($value),
            ]
        );
    }

    public function addToValue($value, \CalameoBundle\Calameo\Response $data)
    {
        if (!$data->isError())
        {
            foreach ( $data->getContent() as $key => $v )
            {
                $value->$key = $v;
            }
        }
        return $this;
    }

    protected function getSortInfo( BaseValue $value )
    {
        return (string)$value->url;
    }


    public function fromPersistenceValue(PersistenceValue $fieldValue)
    {
        if ($fieldValue->data === null) {
            return $this->getEmptyValue();
        }

        return new Value($fieldValue->data);
    }
}