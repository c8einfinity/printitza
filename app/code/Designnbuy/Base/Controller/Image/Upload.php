<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Base\Controller\Image;
use Magento\Framework\Controller\ResultFactory;
use Designnbuy\Base\Helper\Data as DnbBaseHelper;
/**
 * Background home page view
 */
class Upload extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Designnbuy\Base\Helper\Data
     */

    private $dnbBaseHelper;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Designnbuy\Base\Helper\Inkscape $inkscapeHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        DnbBaseHelper $dnbBaseHelper
    ) {
        parent::__construct($context);
        $this->dnbBaseHelper = $dnbBaseHelper;
    }
    /**
     * View background homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest()->getParams();
        if(isset($request) && isset($request["data"]) && !empty($request["data"])) {
            $data = $request["data"];
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $ext = explode("/", $type);
            if ($ext[1] == "png" || $ext[1] == "jpg" || $ext[1] == "jpeg") {
                $filename = 'image_'.date("YmdHis").'.'.$ext[1];
                file_put_contents($this->dnbBaseHelper->getCustomerImageDir().$filename, $data);
                echo $this->dnbBaseHelper->getCustomerImageUrl().$filename ;
            }else {
                echo "please upload valid file.";
            }
        }else {
            $request['isFront'] = 1;
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            $response = $this->dnbBaseHelper->upload($request, $_FILES);
            echo $response;
            die;
        }

    }

}
