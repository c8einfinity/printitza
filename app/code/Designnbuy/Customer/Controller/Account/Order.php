<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Account;
use Magento\Framework\App\Filesystem\DirectoryList;

class Order extends \Magento\Framework\App\Action\Action
{

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Designnbuy\Base\Service\FTPUploader $ftp,
        \Magento\Framework\Filesystem $filesystem,
        \Designnbuy\Base\Observer\Output $outputObserver
    ) {
        parent::__construct($context);
        $this->ftp = $ftp;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->outputObserver = $outputObserver;
    }

    /**
     * Managing customer subscription page
     *
     * @return void
     */
    public function execute()
    {
        ini_set('display_errors', 1);
        
        echo "<pre>";
        print_r($this->outputObserver->generateItemOutput('000000093', 170, 'front'));
        die;
        /***********    Generate CMYK tcpdf    ***************/
        /* $svgFileName = 'design_1578303956_0_original.svg';
        $sourceImagePath = BP.'/var/log/';
        $cmykPdfName = $sourceImagePath.'design_1578303956_0_original_CMYK.pdf';
        echo "<pre>";
        print_r($this->outputObserver->generateCMYKPDF_TCPDF($svgFileName, $sourceImagePath, $cmykPdfName));
        die; */

        /*
        Host:-188.166.94.198
        Port : 2281
        User : crystal
        Password : qf.R6WhjZc=)
        */
        $localFile = $this->_mediaDirectory->getAbsolutePath('designnbuy/output/order-000000217-212.pdf');

        $remotePath = 'RainierTestFiles/order-000000217-212.pdf';
        $connection = $this->ftp->connect('188.166.94.198','crystal', 'qf.R6WhjZc=)');

        $result = $this->ftp->upload($localFile, $remotePath, 'test.com', '188.166.94.198','crystal', 'qf.R6WhjZc=)');
        echo "<pre>";
        print_r($result);
        die;
        die;


        //echo "<pre>";
        //print_r($this->outputObserver->generateItemOutput(11, 18, 'front')); //BOth
        //print_r($this->outputObserver->generateItemOutput(12, 19, 'front')); // Name
        //print_r($this->outputObserver->generateItemOutput(76, 128, 'front')); //Number
        die;
    }
}
