<?php
namespace Designnbuy\Designidea\Block\Adminhtml\Designidea\Edit;

use Magento\Eav\Model\Config;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tabs as WigetTabs;
use Magento\Framework\Json\EncoderInterface;
use Magento\Backend\Model\Auth\Session;
/**
 * Admin Locator left menu.
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
/**
 * @var Config
 */

class Tabs extends WigetTabs
{
    private $eavConfig;

    /**
     * @return void
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        array $data = [],
        Config $eavConfig)
    {
        $this->eavConfig = $eavConfig;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('designidea_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Editable Artwork Information'));
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareLayout()
    {        
        $entityType = $this->getEavConfig()->getEntityType('designnbuy_designidea_eav_attribute');
        $attributes = $entityType->getAttributeCollection();
        $attributes->getSelect()->order('main_table.attribute_id ' . 'ASC');

        $this->addTab(
            'general',
            [
                'label' => __('Editable Artwork Information'),
                'content' =>
                    $this->getLayout()->createBlock(
                        'Designnbuy\Designidea\Block\Adminhtml\Designidea\Edit\Tab\Form'
                    )->setAttributes($attributes)->toHtml()
                ,
                'group_code' => 'general'
            ]
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve EAV Config Singleton
     *
     * @return \Magento\Eav\Model\Config
     */
    private function getEavConfig()
    {
        return $this->eavConfig;
    }

}
