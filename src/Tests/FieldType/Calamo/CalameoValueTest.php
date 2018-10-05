<?php
/**
 * Created by PhpStorm.
 * User: jlchassaing
 * Date: 05/10/2018
 * Time: 10:12
 */

namespace CalameoBundle\Tests\FieldType\Calameo;

use CalameoBundle\FieldType\Calameo\Value;
use PHPUnit\Framework\TestCase;

class CalameoValueTest extends TestCase
{
    public function testCreateValueFromString()
    {
        $url = "http://test.url";

        $newValue = new Value($url);

        $this->assertEquals($url,$newValue->url);
    }

}