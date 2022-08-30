<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\PrintingMethod\Setup;

use Designnbuy\PrintingMethod\Model\PrintingMethod;
use Designnbuy\PrintingMethod\Model\PrintingMethodFactory;
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
     * PrintingMethod factory
     *
     * @var \Designnbuy\PrintingMethod\Model\PrintingMethodFactory
     */
    private $_printingmethodFactory;

    /**
     * Init
     *
     * @param \Designnbuy\PrintingMethod\Model\PrintingMethodFactory $printingmethodFactory
     */
    public function __construct(\Designnbuy\PrintingMethod\Model\PrintingMethodFactory $printingmethodFactory)
    {
        $this->_printingmethodFactory = $printingmethodFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        
    }

}
