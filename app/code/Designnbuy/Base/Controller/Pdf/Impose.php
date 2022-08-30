<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Base\Controller\Pdf;
use \setasign\Fpdi\Fpdi as FPDF;
use setasign\Fpdi\Fpdi;
use Designnbuy\Base\Service\Impose as Imposition;

/**
 * Background home page view
 */
class Impose extends \Magento\Framework\App\Action\Action
{

    private $_files;

    /**
     * Last execute result string.
     *
     * @var string
     */
    protected $lastExecuteResult;

    protected $lastCmd;

    /**
     * exec params.
     *
     * @var array
     */
    protected $params;


    /**
     * Output Helper Class
     *
     * @var \Designnbuy\Base\Helper\Output
     */
    protected $_outputHelper;

    /**
     * Template factory.
     *
     * @var \Designnbuy\Template\Model\TemplateFactory
     */
    protected $_templateFactory;


    public $_colorArray = [];
    public $_sizeArray = [];
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Designnbuy\Base\Helper\Inkscape $inkscapeHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\File\Csv $fileCsv,
        \Designnbuy\Template\Model\TemplateFactory $templateFactory,
        \Designnbuy\Designidea\Model\DesignideaFactory $designideaFactory,
        \Designnbuy\Template\Model\CategoryFactory $categoryFactory,
        \Designnbuy\Designidea\Model\CategoryFactory $designideaCategoryFactory
    ) {
        $this->_moduleReader = $moduleReader;
        $this->_fileCsv = $fileCsv;
        $this->_templateFactory = $templateFactory;
        $this->_designideaFactory = $designideaFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_designideaCategoryFactory = $designideaCategoryFactory;
        parent::__construct($context);
    }
    /**
     * View background homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /*$colors = ['red','blue', 'black', 'green', 'yellow', 'white', 'purple'];
        $this->_colorArray = $colors;
        $sizes = ['S','M', 'L', 'X', 'XL', 'XXL', 'XXXL'];
        $sizeColors = [
            'S' => ['red', 'blue', 'green'],
            'M' => ['red', 'yellow', 'white'],
            'L' => ['blue', 'white'],
            'X' => ['black', 'purple', 'white'],
            'XL' => ['purple', 'white'],
            'XXL' => ['green'],
            'XXXL' => ['black'],
        ];
        $tempColors = [];
        foreach ($colors as $color) {
            $isCommon = true;
            foreach ($sizeColors as $sizeColor) {
                echo "<pre>";
                print_r($sizeColor);
                if (!in_array($color, $sizeColor)) {
                    $isCommon = false;
                }
            }
            if($isCommon == true){
                $tempColors[] = $color;
            }
        }

        die;*/
        // This is the directory where you put your CSV file.
        $directory = $this->_moduleReader->getModuleDir('etc', 'Designnbuy_Base');


        $file = $directory . '/sample.pdf';
        $impose = new Imposition();
        $impose->addPDF($file, '-');
        $impose->addParam('papersize', "'{4in,2.5in}'");
        $impose->addParam('offset', '"0.125in 0.125in 0.125in 0.125in"');
        $impose->export($directory .'/sample_imp.pdf');


        die;

        $file = $directory . '/album.pdf';
        $impose = new Imposition();
        $impose->addPDF($file, '-');
        $impose->addPDF($file, '1,2');
        $impose->export($directory .'/album_imp.pdf');
        // This is your CSV file.

        /*$file = $directory . '/pretemplates.csv';
        if (file_exists($file)) {
            $data = $this->_fileCsv->getData($file);

            $header = $data[0];
            for($i=1; $i<count($data); $i++) {

                echo "<pre>";
                print_r($data[$i]);

                $designideaModel = $this->_designideaFactory->create();
                $designideaModel->setStoreId(0);
                $designideaModel->setTitle('Pretemplate '.$i);
                $designideaModel->setSvg($data[$i][2]);
                $designideaModel->setProductId($data[$i][1]);
                $designideaModel->setAttributeSetId($designideaModel->getDefaultAttributeSetId());
                $designideaModel->setWebsiteIds([1]);
                $designideaModel->save();
            }

        }*/
        die;

        $file = $directory . '/template_category.csv';
        if (file_exists($file)) {
            $data = $this->_fileCsv->getData($file);

            $header = $data[0];
            for($i=1; $i<count($data); $i++) {
                $categoryData = array_combine($header, $data[$i]);

                $categoryModel = $this->_categoryFactory->create();
                $categoryModel->setStoreId(0);
                $categoryModel->load($categoryData['entity_id']);
                $categoryModel->addData($categoryData);
                $categoryModel->setAttributeSetId($categoryModel->getDefaultAttributeSetId());
                $categoryModel->setWebsiteIds([1]);
                $categoryModel->save();
            }

        }
        die;

        $file = $directory . '/producttemplates(4).csv';
        if (file_exists($file)) {
            $data = $this->_fileCsv->getData($file);

            $header = $data[0];
            for($i=1; $i<count($data); $i++) {
                $templateData = array_combine($header, $data[$i]);

                if($templateData['unit'] == 118){
                    $templateData['unit'] = 'mm';
                } else if($templateData['unit'] == 119){
                    $templateData['unit'] = 'cm';
                } else if($templateData['unit'] == 120){
                    $templateData['unit'] = 'in';
                } else if($templateData['unit'] == 121){
                    $templateData['unit'] = 'px';
                }
                $templateModel = $this->_templateFactory->create();
                $templateModel->setStoreId(0);
                $templateModel->load($templateData['entity_id']);
                $templateModel->addData($templateData);
                $templateModel->setAttributeSetId($templateModel->getDefaultAttributeSetId());
                $templateModel->setWebsiteIds([1]);
                $templateModel->save();
            }

        }
        die;

    }



}
