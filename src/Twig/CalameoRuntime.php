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
    public static $LARGE = 'PosterUrl';
    public static $MEDIUM = 'PictureUrl';
    public static $THUMB = 'ThumbUrl';

    private $calameService;

    public function __construct(Client $calameService)
    {
        $this->calameService = $calameService;

    }

    public function getToc(Content $content, $url_field)
    {
        $bookId = $this->getBookId($content, $url_field);

        $toc = $this->calameService->getToc($bookId);
        $items = $toc->data['content']['items'];

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
    public function getPoster(Content $content, $url_field, $size = self::POSTER)
    {
        $size = strtoupper($size);
        $bookId = $this->getBookId($content, $url_field);
        $result = $this->calameService->getBookInfo($bookId);
        return $result->get(self::$$size);
    }

    public function getIframePath(Content $content, $url_field)
    {
        $bookId = $this->getBookId($content, $url_field);
        return $this->calameService->getIframePath($bookId);
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
        $result = $this->calameService->getBookInfo($bookId);
        return $result->get('Description');
    }
}