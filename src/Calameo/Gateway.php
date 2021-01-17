<?php
/**
 * @author jlchassaing <jlchassaing@gmail.com>
 * @licence MIT
 */

namespace CalameoBundle\Calameo;


use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Gateway 
{
   /** @var Symfony\Contracts\HttpClient\HttpClientInterface */
    private $client;
    
    /** @var \Symfony\Component\Cache\Adapter\TagAwareAdapterInterface  */
    private $cache;
    
    public function __construct(HttpClientInterface $client, TagAwareAdapterInterface $cache)
    {
        $this->client = $client;
        $this->cache = $cache;
    }
    
    
    public function fetch ($url,$key)
    {
        $cacheItem = $this->cache->getItem($key);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        } else {
            $response = $this->getData($url);
            $cacheItem->set($response);
            $this->cache->save($cacheItem);
            return $response;
        }
    }

    public function getData( $url)
    {
        $response = $this->client->request('GET', $url, ['timeout' => 2.5]);

        if ($response->getStatusCode() === 200) {
            $body = $response->getContent();
            $data = json_decode($body, true);
            return new Response($data);
        }
    }

}