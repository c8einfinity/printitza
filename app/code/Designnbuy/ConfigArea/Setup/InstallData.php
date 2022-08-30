<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\ConfigArea\Setup;

use Designnbuy\ConfigArea\Model\ConfigArea;
use Designnbuy\ConfigArea\Model\ConfigAreaFactory;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * ConfigArea factory
     *
     * @var \Designnbuy\ConfigArea\Model\ConfigAreaFactory
     */
    private $_configareaFactory;

    /**
     * Init
     *
     * @param \Designnbuy\ConfigArea\Model\ConfigAreaFactory $configareaFactory
     */
    public function __construct(\Designnbuy\ConfigArea\Model\ConfigAreaFactory $configareaFactory)
    {
        $this->_configareaFactory = $configareaFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $data = [
            'title' => 'Hello world!',
            'meta_keywords' => 'magento 2 configarea',
            'meta_description' => 'Magento 2 configarea default configarea.',
            'identifier' => 'hello-world',
            'content_heading' => 'Hello world!',
            'content' => '<p>Welcome to <a title="Designnbuy - solutions for Magento 2" href="http://designnbuy.com/" target="_blank">Designnbuy</a> ',
            'store_ids' => [0]
        ];

        $this->_configareaFactory->create()->setData($data)->save();
    }

}
