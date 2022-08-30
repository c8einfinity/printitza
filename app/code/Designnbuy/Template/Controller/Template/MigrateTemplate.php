<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Template\Controller\Template;

use Magento\Framework\Controller\ResultFactory;
use DOMDocument;

/**
 * Template Template view
 */
class MigrateTemplate extends \Designnbuy\Template\App\Action\Action
{
    protected $helper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Designnbuy\Base\Helper\Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }
    /**
     * View Template template action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        
        //$ch = curl_init('http://127.0.0.1/aiod/32_magento23/template/template/templatedata?id=699');
        $ch = curl_init('http://allinonedesigner-v3.designnbuy.in/template/template/templatedata?id=811,812,813,814,815,816,817,818,819,820,821,822,823,824,825,826,827,828,829,830,831,832,833');
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        $templateData = json_decode($result,true);
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $rootPath  =  $directory->getRoot();
        $targetBaseUrl = 'http://allinonedesigner-v3.designnbuy.in';
        foreach($templateData as $key => $singlePeoduct){
            
            foreach($singlePeoduct['svgdata'] as $keysvg => $singleSvg){
                $doc = new DOMDocument();
                $doc->preserveWhiteSpace = False;
                $doc->loadXML($singleSvg);
                foreach ($doc->getElementsByTagNameNS('http://www.w3.org/2000/svg', 'svg') as $element){
                    foreach ($element->getElementsByTagName("*") as $tags){
                        if($tags->localName=='image'){
                            if($tags->getAttribute('xlink:href')!=''){
                                $dt = explode($targetBaseUrl,$tags->getAttribute('xlink:href'));
                                if(isset($dt[1]) && $dt[1] != ""){
                                    $src = $tags->getAttribute('xlink:href');
                                    $dest = $rootPath . $dt[1];
                                    //echo "src = ".$src.'<br/>';
                                    //echo "dest = ".$dest.'<br/>';
                                    $headers = get_headers($tags->getAttribute('xlink:href'));
                                    $linkStatus = substr($headers[0], 9, 3);
                                    if($linkStatus == 200){
                                        file_put_contents($dest, file_get_contents($tags->getAttribute('xlink:href')));
                                    }
                                }
                            }
                            if($tags->getAttribute('output-src')!=''){
                                $dt = explode($targetBaseUrl,$tags->getAttribute('output-src'));
                                if(isset($dt[1]) && $dt[1] != ""){
                                    $src = $tags->getAttribute('output-src');
                                    $dest = $rootPath . $dt[1];
                                    //echo "src = ".$src.'<br/>';
                                    //echo "dest = ".$dest.'<br/>';
                                    $headers = get_headers($tags->getAttribute('output-src'));
                                    $linkStatus = substr($headers[0], 9, 3);
                                    if($linkStatus == 200){
                                        file_put_contents($dest, file_get_contents($tags->getAttribute('output-src')));
                                    }
                                }
                            }
                            if($tags->getAttribute('templateSrc')!=''){
                                $dt = explode($targetBaseUrl,$tags->getAttribute('templateSrc'));
                                if(isset($dt[1]) && $dt[1] != ""){
                                    $src = $tags->getAttribute('templateSrc');
                                    $dest = $rootPath . $dt[1];
                                    //echo "src = ".$src.'<br/>';
                                    //echo "dest = ".$dest.'<br/>';
                                    $headers = get_headers($tags->getAttribute('templateSrc'));
                                    $linkStatus = substr($headers[0], 9, 3);
                                    if($linkStatus == 200){
                                        file_put_contents($dest, file_get_contents($tags->getAttribute('templateSrc')));
                                    }
                                }
                            }
                            if($tags->getAttribute('orighref')!=''){
                                $dt = explode($targetBaseUrl,$tags->getAttribute('orighref'));
                                if(isset($dt[1]) && $dt[1] != ""){
                                    $src = $tags->getAttribute('orighref');
                                    $dest = $rootPath . $dt[1];
                                    //echo "src = ".$src.'<br/>';
                                    //echo "dest = ".$dest.'<br/>';
                                    $headers = get_headers($tags->getAttribute('orighref'));
                                    $linkStatus = substr($headers[0], 9, 3);
                                    if($linkStatus == 200){
                                        file_put_contents($dest, file_get_contents($tags->getAttribute('orighref')));
                                    }
                                }
                            }
                        }
                    }
                }
                file_put_contents($this->helper->getTemplateSVGDir().$keysvg, $singleSvg);
            }
            $headers = get_headers($targetBaseUrl.'/pub/media/designnbuy/template/'.$singlePeoduct['image']);
            $linkStatus = substr($headers[0], 9, 3);
            if($linkStatus == 200){
				$imagePathinfo = pathinfo($this->helper->getTemplateSVGDir().$singlePeoduct['image']);
				
				if (!file_exists($imagePathinfo['dirname'])) {
					mkdir($imagePathinfo['dirname'], 0777, true);
				}
				
				file_put_contents($this->helper->getTemplateSVGDir().$singlePeoduct['image'], file_get_contents($targetBaseUrl.'/pub/media/designnbuy/template/'.$singlePeoduct['image']));
            }
            $duplicate = $objectManager->create('Designnbuy\Template\Model\Template')->migrateTemplate($singlePeoduct);
            echo "success = ".$key."<br/>";
        }
        exit;

    }
}
