<?php

/**
 * File containing the UrlTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\FieldType\Tests;

use CalameoBundle\FieldType\Calameo\Type as CalameoType;
use CalameoBundle\FieldType\Calameo\Value as CalameoValue;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;

/**
 * Class CalameoTypeTest
 * @package eZ\Publish\Core\FieldType\Tests
 */
class CalameoTypeTest extends FieldTypeTest
{
    /**
     * @return CalameoType|FieldType
     */
    protected function createFieldTypeUnderTest()
    {
        $fieldType = new CalameoType();
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
                InvalidArgumentException::class,
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
                'http://example.com/sindelfingen',
                new CalameoValue('http://example.com/sindelfingen'),
            ),
            array(
                new CalameoValue('http://example.com/sindelfingen'),
                new CalameoValue('http://example.com/sindelfingen'),
            ),
        );
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
                new CalameoValue('http://example.com/sindelfingen'),
                 'http://example.com/sindelfingen',
            ),
            array(
                new CalameoValue('http://example.com/sindelfingen'),
                'http://example.com/sindelfingen',
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

                'http://example.com/sindelfingen',
                new CalameoValue('http://example.com/sindelfingen'),
            ],
            [
                'http://example.com/sindelfingen',
                new CalameoValue('http://example.com/sindelfingen')
            ],
        ];
    }


    protected function provideFieldTypeIdentifier()
    {
        return 'ezcalameo';
    }

    public function provideDataForGetName()
    {
        return array(
            array($this->getEmptyValueExpectation(), ''),
            array(new CalameoValue(''), ''),
        );
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