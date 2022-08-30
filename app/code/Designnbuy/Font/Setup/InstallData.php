<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Setup;

use Designnbuy\Font\Model\Font;
use Designnbuy\Font\Model\FontFactory;
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
     * Font factory
     *
     * @var \Designnbuy\Font\Model\FontFactory
     */
    private $_fontFactory;

    /**
     * Init
     *
     * @param \Designnbuy\Font\Model\FontFactory $fontFactory
     */
    public function __construct(\Designnbuy\Font\Model\FontFactory $fontFactory)
    {
        $this->_fontFactory = $fontFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

    }

}
