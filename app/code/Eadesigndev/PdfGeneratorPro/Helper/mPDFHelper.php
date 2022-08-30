<?php

namespace Eadesigndev\PdfGeneratorPro\Helper;

use Mpdf\Mpdf;

/**
 * Class mPDFHelper
 * @package Eadesigndev\PdfGeneratorPro\Helper
 */
class mPDFHelper extends Mpdf
{

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function __construct1(
        $mode = '',
        $format = 'A4',
        $default_font_size = 0,
        $default_font = '',
        $mgl = 15,
        $mgr = 15,
        $mgt = 16,
        $mgb = 16,
        $mgh = 9,
        $mgf = 9,
        $orientation = 'P'
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //@codingStandardsIgnoreLine
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $magentoRootPath  =  $directory->getPath('var') . DIRECTORY_SEPARATOR;
        define("_MPDF_TEMP_PATH", $magentoRootPath . 'tmp/');
        define('_MPDF_TTFONTDATAPATH', $magentoRootPath . 'ttfontdata/');
        define('_MPDF_TTFONTPATH', $magentoRootPath . 'ttfonts/');
        parent::__construct(
            $mode,
            $format,
            $default_font_size,
            $default_font,
            $mgl,
            $mgr,
            $mgt,
            $mgb,
            $mgh,
            $mgf,
            $orientation
        );
    }
}
