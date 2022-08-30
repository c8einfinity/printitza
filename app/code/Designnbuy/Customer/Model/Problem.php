<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Model;

/**
 * Customer problem model
 *
 * @method \Designnbuy\Customer\Model\ResourceModel\Problem _getResource()
 * @method \Designnbuy\Customer\Model\ResourceModel\Problem getResource()
 * @method int getDesignId()
 * @method \Designnbuy\Customer\Model\Problem setDesignId(int $value)
 * @method int getQueueId()
 * @method \Designnbuy\Customer\Model\Problem setQueueId(int $value)
 * @method int getProblemErrorCode()
 * @method \Designnbuy\Customer\Model\Problem setProblemErrorCode(int $value)
 * @method string getProblemErrorText()
 * @method \Designnbuy\Customer\Model\Problem setProblemErrorText(string $value)
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Problem extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Current Design
     *
     * @var \Designnbuy\Customer\Model\Design
     */
    protected $_design = null;

    /**
     * Design factory
     *
     * @var \Designnbuy\Customer\Model\DesignFactory
     */
    protected $_designFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Designnbuy\Customer\Model\DesignFactory $designFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Designnbuy\Customer\Model\DesignFactory $designFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_designFactory = $designFactory;
    }

    /**
     * Initialize Customer Problem Model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Customer\Model\ResourceModel\Problem');
    }

    /**
     * Add Design Data
     *
     * @param \Designnbuy\Customer\Model\Design $design
     * @return $this
     */
    public function addDesignData(\Designnbuy\Customer\Model\Design $design)
    {
        $this->setDesignId($design->getId());
        return $this;
    }

    /**
     * Add Queue Data
     *
     * @param \Designnbuy\Customer\Model\Queue $queue
     * @return $this
     */
    public function addQueueData(\Designnbuy\Customer\Model\Queue $queue)
    {
        $this->setQueueId($queue->getId());
        return $this;
    }

    /**
     * Add Error Data
     *
     * @param \Exception $e
     * @return $this
     */
    public function addErrorData(\Exception $e)
    {
        $this->setProblemErrorCode($e->getCode());
        $this->setProblemErrorText($e->getMessage());
        return $this;
    }

    /**
     * Retrieve Design
     *
     * @return \Designnbuy\Customer\Model\Design
     */
    public function getDesign()
    {
        if (!$this->getDesignId()) {
            return null;
        }

        if ($this->_design === null) {
            $this->_design = $this->_designFactory->create()->load($this->getDesignId());
        }

        return $this->_design;
    }

    /**
     * Unsubscribe Design
     *
     * @return $this
     */
    public function unsubscribe()
    {
        if ($this->getDesign()) {
            $this->getDesign()->setDesignStatus(
                \Designnbuy\Customer\Model\Design::STATUS_UNSUBSCRIBED
            )->setIsStatusChanged(
                true
            )->save();
        }
        return $this;
    }
}
