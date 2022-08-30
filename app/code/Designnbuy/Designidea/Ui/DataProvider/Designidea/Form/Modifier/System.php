<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Designidea\Ui\DataProvider\Designidea\Form\Modifier;

use Designnbuy\Designidea\Model\Locator\LocatorInterface;
use Magento\Framework\UrlInterface;

/**
 * Class SystemDataProvider
 */
class System extends AbstractModifier
{
    const KEY_SUBMIT_URL = 'submit_url';
    const KEY_VALIDATE_URL = 'validate_url';
    const KEY_RELOAD_URL = 'reloadUrl';

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var array
     */
    protected $designideaUrls = [
        self::KEY_SUBMIT_URL => '*/*/save',
        //self::KEY_VALIDATE_URL => 'designidea/designidea/validate',
        self::KEY_RELOAD_URL => '*/*/reload'
    ];

    /**
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param array $designideaUrls
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        array $designideaUrls = []
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->designideaUrls = array_replace_recursive($this->designideaUrls, $designideaUrls);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $model = $this->locator->getDesignIdea();
        $attributeSetId = $model->getAttributeSetId();

        $parameters = [
            'id' => $model->getId(),
            'type' => $model->getTypeId(),
            'store' => $model->getStoreId(),
        ];
        $actionParameters = array_merge($parameters, ['set' => $attributeSetId]);
        $reloadParameters = array_merge(
            $parameters,
            [
                'popup' => 1,
                'componentJson' => 1,
                'prev_set_id' => $attributeSetId,
                'type' => $this->locator->getDesignIdea()->getTypeId()
            ]
        );

        $submitUrl = $this->urlBuilder->getUrl($this->designideaUrls[self::KEY_SUBMIT_URL], $actionParameters);
        //$validateUrl = $this->urlBuilder->getUrl($this->designideaUrls[self::KEY_VALIDATE_URL], $actionParameters);
        $reloadUrl = $this->urlBuilder->getUrl($this->designideaUrls[self::KEY_RELOAD_URL], $reloadParameters);

        return array_replace_recursive(
            $data,
            [
                'config' => [
                    self::KEY_SUBMIT_URL => $submitUrl,
                    //self::KEY_VALIDATE_URL => $validateUrl,
                    self::KEY_RELOAD_URL => $reloadUrl,
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
