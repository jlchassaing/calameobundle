<?php
/**
 * @author jlchassaing <jlchassaing@gmail.com>
 * @licence MIT
 */

namespace CalameoBundle\Twig;

use CalameoBundle\Calameo\Client;
use eZ\Publish\API\Repository\Values\Content\Content;
use Twig\Extension\RuntimeExtensionInterface;

class CalameoRuntime implements RuntimeExtensionInterface
{
    public static $POSTER = 'PosterUrl';
    public static $MEDIUM = 'PictureUrl';
    public static $THUMB = 'ThumbUrl';

    const DefautPosterSize = "POSTER";

   /** @var \CalameoBundle\Calameo\Client  */
    private $calameoService;

    public function __construct(Client $calameoService)
    {
        $this->calameoService = $calameoService;

    }

    public function getToc(Content $content, $url_field)
    {
        $bookId = $this->getBookId($content, $url_field);

        $toc = $this->calameoService->getToc($bookId);
        $items = $toc->get('items');

        usort($items, function ($item1, $item2) {
            return $item1['PageNumber'] <=> $item2['PageNumber'];
        });
        return $items;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param $url_field
     * @param string $size "thumb|medium|large"
     *
     * @return mixed
     */
    public function getPoster(Content $content, $url_field, $size = self::DefautPosterSize)
    {
        $size = strtoupper($size);
        $bookId = $this->getBookId($content, $url_field);
        if ($bookId) {
            $result = $this->calameoService->getBookInfo($bookId);
            return $result->get(self::$$size);
        }
        return "://0";

    }

    public function getIframePath(Content $content, $url_field)
    {
        $bookId = $this->getBookId($content, $url_field);
        return $this->calameoService->getIframePath($bookId);
    }

    private function getBookId(Content $content, $url_field)
    {
        $url = $content->getFieldValue($url_field)->link;
        $temp = explode('/',trim($url, '/'));
        return  array_pop($temp);
    }
    
    public function getDescription($content, $url_field)
    {
        $bookId = $this->getBookId($content, $url_field);
        $result = $this->calameoService->getBookInfo($bookId);
        return $result->get('Description');
    }
}