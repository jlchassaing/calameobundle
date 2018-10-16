<?php
/**
 * @author jlchassaing <jlchassaing@gmail.com>
 *
 */

namespace CalameoBundle\Calameo;



use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver;

use eZ\Publish\API\Repository\Tests\Values\User\Limitation\NewObjectStateLimitationTest;
use Guzzle\Http\Url;
use function GuzzleHttp\Psr7\build_query;
use Symfony\Component\DependencyInjection\Container;

use eZ\Publish\Core\MVC\ConfigResolverInterface;

class Client
{

    const URL_REGEX = '#^https?://\w{2,3}.calameo.\w{2,3}/\w+/(.+)$#';

    const FETCH_BOOK_TOC = 'API.fetchBookTocs';
    const FETCH_BOOK_INFO = 'getBookInfos';

    private $config;

    public function __construct(Container $container)
    {
        $this->config = $container->getParameter( 'calameo');

    }

    /**
     * @param $url
     *
     * @return false|int
     */
    public function isValidUrl($url)
    {
        return preg_match(self::URL_REGEX,$url ) === 1;

    }

    /**
     * @param $url
     *
     * @return null
     */
    public function getKeyFromUrl($url)
    {
        $res = null;
        if (preg_match(self::URL_REGEX,$url, $res ))
        {
            return $res[1];
        }
        return $res;
    }

    public function getIframePath($bookId)
    {
        $params = [
            'bkcode' => $bookId,
        ];
        $params += $this->config['iframeParams'];

        $scheme = "http";
        $path = $this->config['paths']['iframe'] . "?" . build_query($params);

        return $path;

    }

    public function getCalameoData($url)
    {
        if ($url === null){
            return [];
        }
        $code = $this->getKeyFromUrl($url);
        return ['code' => $code,
                   'iframe' => $this->getIframePath($code),
                   'toc' => $this->getToc($code),
                   'infos' => $this->getBookInfo($code),
        ];

    }

    /**
     * @param null $bookId
     *
     * @return Response|null
     */
    public function getToc($bookId = null)
    {
        return $this->fetch(self::FETCH_BOOK_TOC, $bookId);
    }

    /**
     * @param $bookId
     *
     * @return Response|null
     */
    public function getBookInfo($bookId)
    {
        return $this->fetch(self::FETCH_BOOK_INFO, $bookId);
    }

   public function fetch($action, $bookId)
   {
       if ($bookId == null) return null;
       return  $this->call([
           'action' => $action,
           "apikey" => $this->config['api']['key'],
           "book_id" => $bookId,
           "output"  => 'json',
       ]);

   }


    public function call($params)
    {
        $signature = $this->getRequestSignature($params, $this->config['api']['secret']);
        $requestUrl = $this->getRequestUrl($this->config["paths"]['toc'], $params, $signature);

        return new Response($this->getData($requestUrl));
    }

    /**
     * Generate the request Signature
     * @param array $params
     * @param string $secretKey
     * @return string
     */
    public function getRequestSignature($params, $secretKey)
    {
        $serializedKey = "";
        foreach ($params as $key=>$value)
        {
            $serializedKey .= $key.$value;
        }

        $signature = $secretKey.$serializedKey;

        return md5($signature);
    }

    /**
     * Generate the request URL
     * @param string $apiHost
     * @param string $params
     * @param string $signature
     * @return string
     */
    public function getRequestUrl($apiHost,$params,$signature)
    {
        $requestParams = build_query($params);

        $requestParams.= "&signature=".$signature;

        return $apiHost."?".$requestParams;
    }

//    public function getIframePath(GieCalameo $calameo)
//    {
//        return $this->getUrl("iframe", $calameo);
//    }

    public function getData($url)
    {
        $guzzle = new \Guzzle\Http\Client();
        $request = $guzzle->createRequest('GET',$url);
        $request->send();
        $response = $request->getResponse();

        $body = $response->getBody(true);

        return json_decode($body, true);
    }


    /**
     * build full URL to different calameo path
     * @param string $type
     * @return string
     */
    public function getUrl($type, GieCalameo $calameo)
    {

        if (isset($this->apiParams[$type]))
        {
            $FixParams = $this->apiParams[$type]['Params'];
            $Path = $this->apiParams[$type]['url'];
            $Params = $this->apiParams['keys'];

            return $this->buildUrl($Path, $this->buildParamsString($calameo, $Params, $FixParams));
        }

    }

    /**
     * build url base on url path and params to be send
     * @param string $url
     * @param array $params
     * @return string
     */
    public function buildUrl($url,$params)
    {
        return $url."?".$params;
    }

    /**
     * build query params
     * @param array $dynParams
     * @param array $fixedParams
     * @return string
     */
    public function buildParamsString(GieCalameo $calameo, $dynParams, $fixedParams = null)
    {
        $params = "";
        foreach($dynParams as $key=>$value)
        {
            if ($calameo->$value !== null)
            {
                if ($params != "")
                {
                    $params .="&";
                }
                $params .= "$key=".$calameo->$value;
            }

        }
        if (is_array( $fixedParams))
        {
            foreach($fixedParams as $key=>$value)
            {
                $params .="&";
                if ($value != "")
                {
                    $params .= "$key=$value";
                }
                else
                {
                    $params .= "$key";
                }

            }
        }


        return $params;
    }


}