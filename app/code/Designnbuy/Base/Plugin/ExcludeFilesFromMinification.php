<?php

namespace Designnbuy\Base\Plugin;
use Magento\Framework\View\Asset\Minification;

class ExcludeFilesFromMinification
{
    public function afterGetExcludes(Minification $subject, array $result, $contentType)
    {
        if ($contentType == 'js') {
            $result[] = 'Designnbuy_Base/js/quickEdit';
            $result[] = 'Designnbuy_Base/js/jpicker';
            $result[] = 'Designnbuy_Base/js/jscolor';
            $result[] = 'Designnbuy_Base/js/jscolor/jscolor';
            $result[] = 'Designnbuy_Base/js/jscrollpane';
            $result[] = 'Designnbuy_Base/js/raphael';
            $result[] = 'Designnbuy_Base/js/simplecolor';
            $result[] = 'Designnbuy_Base/js/cropper/cropper';
            $result[] = 'Designnbuy_Base/js/cropper/jquery-cropper';
            $result[] = 'Designnbuy_Base/js/canvas-to-blob';
        }
        return $result;
    }
}