<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Block\Adminhtml;

/**
 * Review edit form
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Review action pager
     *
     * @var \Designnbuy\DesignReview\Helper\Action\Pager
     */
    protected $_reviewActionPager = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Review model factory
     *
     * @var \Designnbuy\DesignReview\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Designnbuy\DesignReview\Model\ReviewFactory $reviewFactory
     * @param \Designnbuy\DesignReview\Helper\Action\Pager $reviewActionPager
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Designnbuy\DesignReview\Model\ReviewFactory $reviewFactory,
        \Designnbuy\DesignReview\Helper\Action\Pager $reviewActionPager,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_reviewActionPager = $reviewActionPager;
        $this->_reviewFactory = $reviewFactory;
        parent::__construct($context, $data);
    }

    /**
     * Initialize edit review
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'Designnbuy_DesignReview';
        $this->_controller = 'adminhtml';

        /** @var $actionPager \Designnbuy\DesignReview\Helper\Action\Pager */
        $actionPager = $this->_reviewActionPager;
        $actionPager->setStorageId('reviews');

        $reviewId = $this->getRequest()->getParam('id');
        $prevId = $actionPager->getPreviousItemId($reviewId);
        $nextId = $actionPager->getNextItemId($reviewId);
        if ($prevId !== false) {
            $this->addButton(
                'previous',
                [
                    'label' => __('Previous'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('designreview/*/*', ['id' => $prevId]) . '\')'
                ],
                3,
                10
            );

            $this->addButton(
                'save_and_previous',
                [
                    'label' => __('Save and Previous'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'save',
                                'target' => '#edit_form',
                                'eventData' => ['action' => ['args' => ['next_item' => $prevId]]],
                            ],
                        ],
                    ]
                ],
                3,
                11
            );
        }
        if ($nextId !== false) {
            $this->addButton(
                'save_and_next',
                [
                    'label' => __('Save and Next'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'save',
                                'target' => '#edit_form',
                                'eventData' => ['action' => ['args' => ['next_item' => $nextId]]],
                            ],
                        ],
                    ]
                ],
                3,
                100
            );

            $this->addButton(
                'next',
                [
                    'label' => __('Next'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('designreview/*/*', ['id' => $nextId]) . '\')'
                ],
                3,
                105
            );
        }
        $this->buttonList->update('save', 'label', __('Save Review'));
        $this->buttonList->update('save', 'id', 'save_button');
        $this->buttonList->update('delete', 'label', __('Delete Review'));

        if ($this->getRequest()->getParam('productId', false)) {
            $this->buttonList->update(
                'back',
                'onclick',
                'setLocation(\'' . $this->getUrl(
                    'catalog/product/edit',
                    ['id' => $this->getRequest()->getParam('productId', false)]
                ) . '\')'
            );
        }

        if ($this->getRequest()->getParam('customerId', false)) {
            $this->buttonList->update(
                'back',
                'onclick',
                'setLocation(\'' . $this->getUrl(
                    'customer/index/edit',
                    ['id' => $this->getRequest()->getParam('customerId', false)]
                ) . '\')'
            );
        }

        if ($this->getRequest()->getParam('ret', false) == 'pending') {
            $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $this->getUrl('catalog/*/pending') . '\')');
            $this->buttonList->update(
                'delete',
                'onclick',
                'deleteConfirm(' . '\'' . __(
                    'Are you sure you want to do this?'
                ) . '\' ' . '\'' . $this->getUrl(
                    '*/*/delete',
                    [$this->_objectId => $this->getRequest()->getParam($this->_objectId), 'ret' => 'pending']
                ) . '\'' . ')'
            );
            $this->_coreRegistry->register('ret', 'pending');
        }

        if ($this->getRequest()->getParam($this->_objectId)) {
            $reviewData = $this->_reviewFactory->create()->load($this->getRequest()->getParam($this->_objectId));
            $this->_coreRegistry->register('review_data', $reviewData);
        }

        $this->_formInitScripts[] = '
            var review = {
                updateRating: function() {
                        elements = [
                            $("select_stores"),
                            $("rating_detail").getElementsBySelector("input[type=\'radio\']")
                        ].flatten();
                        $(\'save_button\').disabled = true;
                        new Ajax.Updater(
                            "rating_detail",
                            "' .
            $this->getUrl(
                'review/*/ratingItems',
                ['_current' => true]
            ) .
            '",
                            {
                                parameters:Form.serializeElements(elements),
                                evalScripts:true,
                                onComplete:function(){ $(\'save_button\').disabled = false; }
                            }
                        );
                    }
           }
           Event.observe(window, \'load\', function(){
                 Event.observe($("select_stores"), \'change\', review.updateRating);
           });
        ';
    }

    /**
     * Get edit review header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $reviewData = $this->_coreRegistry->registry('review_data');
        if ($reviewData && $reviewData->getId()) {
            return __("Edit Review '%1'", $this->escapeHtml($reviewData->getTitle()));
        } else {
            return __('New Review');
        }
    }
}
