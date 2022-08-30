<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Clipart\Setup;

use Designnbuy\Clipart\Model\Clipart;
use Designnbuy\Clipart\Model\ClipartFactory;
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
     * Clipart factory
     *
     * @var \Designnbuy\Clipart\Model\ClipartFactory
     */
    private $_clipartFactory;

    /**
     * Init
     *
     * @param \Designnbuy\Clipart\Model\ClipartFactory $clipartFactory
     */
    public function __construct(\Designnbuy\Clipart\Model\ClipartFactory $clipartFactory)
    {
        $this->_clipartFactory = $clipartFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
       
    }

}
