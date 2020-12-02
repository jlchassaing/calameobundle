<?php
/**
 * @author jlchassaing <jlchassaing@gmail.com>
 * @licence MIT
 */

namespace CalameoBundle\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CalameoExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('calameo_toc', [CalameoRuntime::class, 'getToc']),
            new TwigFunction('calameo_poster', [CalameoRuntime::class,'getPoster']),
            new TwigFunction('calameo_iframe', [CalameoRuntime::class,'getIframePath']),
        ];
    }
}