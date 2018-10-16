<?php

/**
 * File containing the UrlTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\FieldType\Tests;

use CalameoBundle\Calameo\Client;
use CalameoBundle\FieldType\Calameo\Type as CalameoType;
use CalameoBundle\FieldType\Calameo\Value as CalameoValue;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\FieldType\ValidationError;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class CalameoTypeTest
 * @package eZ\Publish\Core\FieldType\Tests
 */
class CalameoTypeTest extends FieldTypeTest 
{
    private $client;
    
    public function setUp()
    {
        $container = $this->createMock(Container::class);
        $container->method("getParameter")
            ->willReturn(['iframeParams' => [], 'paths' => ['iframe' => '','toc'=> ''], 'api' => []]);

        $this->client = new Client($container);
    }
    
    public function getNewCalameoType()
    {
        return new CalameoType($this->client);
    }

    /**
     * @return CalameoType|FieldType
     */
    protected function createFieldTypeUnderTest()
    {
        $fieldType = $this->getNewCalameoType();
        $fieldType->setTransformationProcessor($this->getTransformationProcessorMock());

        return $fieldType;
    }

    /**
     * Returns the validator configuration schema expected from the field type.
     *
     * @return array
     */
    protected function getValidatorConfigurationSchemaExpectation()
    {
        return array();
    }

    /**
     * Returns the settings schema expected from the field type.
     *
     * @return array
     */
    protected function getSettingsSchemaExpectation()
    {
        return array();
    }

    /**
     * Returns the empty value expected from the field type.
     */
    protected function getEmptyValueExpectation()
    {
        return new CalameoValue();
    }

    /**
     * @return array
     */
    public function provideInvalidInputForAcceptValue()
    {
        return array(
            array(
                23,
                InvalidArgumentType::class,
            ),
            array(
                new CalameoValue(23),
                InvalidArgumentException::class,
            ),
        );
    }

    /**
     * @return array
     */
    public function provideValidInputForAcceptValue()
    {
        return array(
            array(
                null,
                new CalameoValue(),
            ),
            array(
                'https://www.calameo.com/read/test',
                new CalameoValue(['url' => 'https://www.calameo.com/read/test']),
            ),
            array(
                new CalameoValue('https://www.calameo.com/read/test'),
                new CalameoValue('https://www.calameo.com/read/test'),
            ),
        );
    }

    public function provideValidDataForValidate()
    {
        return [
            [
                ['url' , 'https://www.calameo.com/read/test'],
                new CalameoValue('https://www.calameo.com/read/test'),
            ]
        ];
    }

    public function provideInValidDataForValidate()
    {
        return [

        ];
    }

    /**
     * @return array
     */
    public function provideInputForToHash()
    {
        return array(
            array(
                new CalameoValue(),
                null,
            ),
            array(
                new CalameoValue('https://www.calameo.com/read/test'),
                 ['url' => 'https://www.calameo.com/read/test', 'data' => null]
            ),
            array(
                new CalameoValue('https://www.calameo.com/read/test'),
                ['url' => 'https://www.calameo.com/read/test', 'data' => null]
            ),
        );
    }

    /**
     * @return array
     */
    public function provideInputForFromHash()
    {
        return [
            [
                null,
                new CalameoValue(),
            ],
            [

                'https://www.calameo.com/read/test',
                new CalameoValue('https://www.calameo.com/read/test'),
            ],
            [
                'https://www.calameo.com/read/test',
                new CalameoValue('https://www.calameo.com/read/test')
            ],
        ];
    }


    protected function provideFieldTypeIdentifier()
    {
        return 'ezcalameo';
    }

    public function provideDataForGetName()
    {

        return null;
    }

    public function provideValidFieldSettings()
    {
        return null;
    }

    public function provideValidValidatorConfiguration()
    {
        return null;
    }
}
