<?php
/**
 * Created by PhpStorm.
 * User: jlchassaing
 * Date: 09/10/2018
 * Time: 15:03
 */

namespace CalameoBundle\Tests\Calameo;

use CalameoBundle\Calameo\Client;
use function GuzzleHttp\Psr7\build_query;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CalameoTest extends KernelTestCase
{

    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        self::bootKernel();

        $this->client = static::$kernel->getContainer()->get('ezsystems.calameobundle.calameo.client');
    }

    public function providerForIsValidUrl()
    {
        return [
            ["http://fr.calameo.com/read/testClienkkey"],
            ["http://en.calameo.com/read/testClienkkey"],
            ["http://en.calameo.com/view/testClienkkey"],
            ["https://en.calameo.com/view/testClienkkey"],
        ];
    }

    /**
     * @param string $url
     *
     * @dataProvider providerForIsValidUrl
     */
    public function testIsValidUrl($url)
    {
        $test = $this->client->isValidUrl($url);

        $this->assertTrue($test);
    }

    public function providerForIsInValidUrl()
    {
        return [
            ["http://calameo.com/read/testClienkkey"],
            ["http://en.calameo.com/testClienkkey"],
            ["http://en.calameo.com/view/"],
        ];
    }

    /**
     * @param string $url
     *
     * @dataProvider providerForIsInValidUrl
     */
    public function testIsInValidUrl($url)
    {
        $test = $this->client->isValidUrl($url);

        $this->assertFalse($test);
    }


    public function provideForGetKey()
    {
        return [
            ["http://fr.calameo.com/read/testClienkkey", "testClienkkey" ],
        ];
    }


    /**
     * @param string $url
     * @param string $key
     *
     * @dataProvider provideForGetKey
     */
    public function testGetKey($url, $key)
    {
        if ($this->client->isValidUrl($url))
        {
            $this->assertEquals($key, $this->client->getKeyFromUrl($url));
        }

    }

    public function testGetIframePath()
    {
        $code = "testcode";
        $iframePath = "//v.calameo.com/?bkcode=testcode&mode=mini&showsharemenu=false&clickto=view&clicktarget=_blank";

        $urlCheckSettings = parse_url($iframePath);
        $urlIframSettings = parse_url($this->client->getIframePath($code));

        foreach ( $urlCheckSettings as $key => $value )
        {
            if ($key !== 'query')
            {
                $this->assertEquals( $urlIframSettings[ $key ], $value );
            }
            else{

                parse_str($value,  $checkQuery);
                parse_str( $urlIframSettings[ $key ],$checkIframeQuery );

                foreach ( $checkQuery as $key => $value )
                {
                    $this->assertEquals($value,$checkIframeQuery[$key]);

                }
            }
        }

    }


    function testgetRequestUrl()
    {
        $url = 'http://test.com';
        $params=['data1'=> 'value1','data2' => 'value2'];
        $signature='signature';
        $expected = $url.'?'.build_query($params).'&signature='.$signature;
        $result = $this->client->getRequestUrl($url,$params,$signature);

        $this->assertEquals($expected,$result);

    }





}