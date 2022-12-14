<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Designidea\Block\Adminhtml\Category\Edit\Button;

use Magento\Ui\Component\Control\Container;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
/**
 * Class Save
 */
class Save extends Generic implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if ($this->getDesignIdea()->isReadonly()) {
            return [];
        }
        
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'designidea_category_form.designidea_category_form',
                                'actionName' => 'save',
                                'params' => [
                                    false
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'class_name' => Container::SPLIT_BUTTON,
            'options' => $this->getOptions(),
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    protected function getOptions()
    {
        $options[] = [
            'id_hard' => 'save_and_new',
            'label' => __('Save & New'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'designidea_category_form.designidea_category_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => 'new',
                                        'id' => $this->getId(),
                                        'store' => $this->getStoreId()
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        /*if (!$this->context->getRequestParam('popup') && $this->getDesignIdea()->isDuplicable()) {
            $options[] = [
                'label' => __('Save & Duplicate'),
                'id_hard' => 'save_and_duplicate',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'designidea_form.designidea_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        [
                                            'back' => 'duplicate'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ];
        }*/

        $options[] = [
            'id_hard' => 'save_and_close',
            'label' => __('Save & Close'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'designidea_category_form.designidea_category_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'id' => $this->getId(),
                                        'store' => $this->getStoreId()
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        return $options;
    }
}
