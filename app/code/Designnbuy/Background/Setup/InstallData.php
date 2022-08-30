<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Setup;

use Designnbuy\Background\Model\Background;
use Designnbuy\Background\Model\BackgroundFactory;
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
     * Background factory
     *
     * @var \Designnbuy\Background\Model\BackgroundFactory
     */
    private $_backgroundFactory;

    /**
     * Init
     *
     * @param \Designnbuy\Background\Model\BackgroundFactory $backgroundFactory
     */
    public function __construct(\Designnbuy\Background\Model\BackgroundFactory $backgroundFactory)
    {
        $this->_backgroundFactory = $backgroundFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
       
    }

}
