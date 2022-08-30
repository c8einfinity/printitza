<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Base\Controller\Import;

/**
 * Background home page view
 */
class Csv extends \Magento\Framework\App\Action\Action
{
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
        ini_set('display_errors', 'On');
        error_reporting(E_ALL);
        /*$json = file_get_contents('http://127.0.0.1/aiod/32_magento23/custom_option1.json');
        $options = json_decode($json, true);*/
        $start = microtime(true);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of Object manager
        $productId = 1007;
        $product = $objectManager->create('\Magento\Catalog\Model\Product')->load($productId);
        $i = 0;
        $options = [];
        foreach ($product->getOptions() as $_option) {

            $options[$i] = $_option->getData();
            $values = $_option->getValues();
            foreach ($values as $_value) {
                var_dump($_value);
                $options[$i]['values'][] = $_value;
            }
            $i++;
        }


        die;
        die;
        $productData = $objectManager->create('\Designnbuy\Canvas\Model\Canvas')->getProductData($productId);
        $total = microtime(true) - $start;
        echo $total;
        echo "<pre>";

        die;
        die;
        $collections = $objectManager->get('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($product);
        $result = $collections->toArray();
        echo "<pre>";
        print_r($result);
        die;
        die;
        $product->setPrice(10); // price of product
        $product->setStockData(
            array(
                'use_config_manage_stock' => 0,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 999999999
            )
        );
        $product->save();
        die;
        $product->setHasOptions(1);
        $product->setCanSaveCustomOptions(true);
        foreach ($options as $arrayOption) {
            $option = $objectManager->create('\Magento\Catalog\Model\Product\Option')
                ->setProductId($productId)
                ->setStoreId($product->getStoreId())
                ->addData($arrayOption);
            $option->save();
            $product->addOption($option);
        }
        echo "<pre>";
        print_r($options);
        die;
        // This is the directory where you put your CSV file.
        $directory = $this->_moduleReader->getModuleDir('etc', 'Designnbuy_Base');
        $file = $directory . '/catalog_product_20190405_091625.csv';
        if (file_exists($file)) {
            $data = $this->_fileCsv->getData($file);
            $customOptionsDatas = $data[1][77];
            $customOptionsDatas = explode('|',$customOptionsDatas);

            foreach ($customOptionsDatas as $customOptionsData) {
                $customOptions = explode(',',$customOptionsData);
                echo "<pre>";
                print_r($customOptions);
            }

            die;
        }

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

        $file = $directory . '/catalog_product_20190405_091625.csv';
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
