<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Model;

use Magento\Framework\App\TemplateTypesInterface;

/**
 * Customer queue model.
 *
 * @method \Designnbuy\Customer\Model\ResourceModel\Queue _getResource()
 * @method \Designnbuy\Customer\Model\ResourceModel\Queue getResource()
 * @method int getTemplateId()
 * @method \Designnbuy\Customer\Model\Queue setTemplateId(int $value)
 * @method int getCustomerType()
 * @method \Designnbuy\Customer\Model\Queue setCustomerType(int $value)
 * @method string getCustomerText()
 * @method \Designnbuy\Customer\Model\Queue setCustomerText(string $value)
 * @method string getCustomerStyles()
 * @method \Designnbuy\Customer\Model\Queue setCustomerStyles(string $value)
 * @method string getCustomerSubject()
 * @method \Designnbuy\Customer\Model\Queue setCustomerSubject(string $value)
 * @method string getCustomerSenderName()
 * @method \Designnbuy\Customer\Model\Queue setCustomerSenderName(string $value)
 * @method string getCustomerSenderEmail()
 * @method \Designnbuy\Customer\Model\Queue setCustomerSenderEmail(string $value)
 * @method int getQueueStatus()
 * @method \Designnbuy\Customer\Model\Queue setQueueStatus(int $value)
 * @method string getQueueStartAt()
 * @method \Designnbuy\Customer\Model\Queue setQueueStartAt(string $value)
 * @method string getQueueFinishAt()
 * @method \Designnbuy\Customer\Model\Queue setQueueFinishAt(string $value)
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Queue extends \Magento\Framework\Model\AbstractModel implements TemplateTypesInterface
{
    /**
     * Customer Template object
     *
     * @var \Designnbuy\Customer\Model\Template
     */
    protected $_template;

    /**
     * Designs collection
     *
     * @var \Designnbuy\Customer\Model\ResourceModel\Design\Collection
     */
    protected $_designsCollection;

    /**
     * Save stores flag.
     *
     * @var boolean
     */
    protected $_saveStoresFlag = false;

    /**
     * Stores assigned to queue.
     *
     * @var array
     */
    protected $_stores = [];

    const STATUS_NEVER = 0;

    const STATUS_SENDING = 1;

    const STATUS_CANCEL = 2;

    const STATUS_SENT = 3;

    const STATUS_PAUSE = 4;

    /**
     * Filter for customer text
     *
     * @var \Designnbuy\Customer\Model\Template\Filter
     */
    protected $_templateFilter;

    /**
     * Date
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Problem factory
     *
     * @var \Designnbuy\Customer\Model\ProblemFactory
     */
    protected $_problemFactory;

    /**
     * Template factory
     *
     * @var \Designnbuy\Customer\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var \Designnbuy\Customer\Model\Queue\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Designnbuy\Customer\Model\Template\Filter $templateFilter
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Designnbuy\Customer\Model\TemplateFactory $templateFactory
     * @param \Designnbuy\Customer\Model\ProblemFactory $problemFactory
     * @param \Designnbuy\Customer\Model\ResourceModel\Design\CollectionFactory $designCollectionFactory
     * @param \Designnbuy\Customer\Model\Queue\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Designnbuy\Customer\Model\Template\Filter $templateFilter,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Designnbuy\Customer\Model\TemplateFactory $templateFactory,
        \Designnbuy\Customer\Model\ProblemFactory $problemFactory,
        \Designnbuy\Customer\Model\ResourceModel\Design\CollectionFactory $designCollectionFactory,
        \Designnbuy\Customer\Model\Queue\TransportBuilder $transportBuilder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_templateFilter = $templateFilter;
        $this->_date = $date;
        $this->_templateFactory = $templateFactory;
        $this->_problemFactory = $problemFactory;
        $this->_designsCollection = $designCollectionFactory->create();
        $this->_transportBuilder = $transportBuilder;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Designnbuy\Customer\Model\ResourceModel\Queue');
    }

    /**
     * Return: is this queue newly created or not.
     *
     * @return boolean
     */
    public function isNew()
    {
        return $this->getQueueStatus() === null;
    }

    /**
     * Set $_data['queue_start'] based on string from backend, which based on locale.
     *
     * @param string|null $startAt start date of the mailing queue
     * @return $this
     */
    public function setQueueStartAtByString($startAt)
    {
        if ($startAt === null || $startAt == '') {
            $this->setQueueStartAt(null);
        } else {
            $time = (new \DateTime($startAt))->getTimestamp();
            $this->setQueueStartAt($this->_date->gmtDate(null, $time));
        }
        return $this;
    }

    /**
     * Send messages to designs for this queue
     *
     * @param int $count
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function sendPerDesign($count = 20)
    {
        if ($this->getQueueStatus() != self::STATUS_SENDING &&
            ($this->getQueueStatus() != self::STATUS_NEVER &&
            $this->getQueueStartAt())
        ) {
            return $this;
        }

        if (!$this->_designsCollection->getQueueJoinedFlag()) {
            $this->_designsCollection->useQueue($this);
        }

        if ($this->_designsCollection->getSize() == 0) {
            $this->_finishQueue();
            return $this;
        }

        $collection = $this->_designsCollection->useOnlyUnsent()->showCustomerInfo()->setPageSize(
            $count
        )->setCurPage(
            1
        )->load();

        $this->_transportBuilder->setTemplateData(
            [
                'template_subject' => $this->getCustomerSubject(),
                'template_text' => $this->getCustomerText(),
                'template_styles' => $this->getCustomerStyles(),
                'template_filter' => $this->_templateFilter,
                'template_type' => self::TYPE_HTML,
            ]
        );

        /** @var \Designnbuy\Customer\Model\Design $item */
        foreach ($collection->getItems() as $item) {
            $transport = $this->_transportBuilder->setTemplateOptions(
                ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $item->getStoreId()]
            )->setTemplateVars(
                ['design' => $item]
            )->setFrom(
                ['name' => $this->getCustomerSenderEmail(), 'email' => $this->getCustomerSenderName()]
            )->addTo(
                $item->getDesignEmail(),
                $item->getDesignFullName()
            )->getTransport();

            try {
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $e) {
                /** @var \Designnbuy\Customer\Model\Problem $problem */
                $problem = $this->_problemFactory->create();
                $problem->addDesignData($item);
                $problem->addQueueData($this);
                $problem->addErrorData($e);
                $problem->save();
            }
            $item->received($this);
        }

        if (count($collection->getItems()) < $count - 1 || count($collection->getItems()) == 0) {
            $this->_finishQueue();
        }
        return $this;
    }

    /**
     * Finish queue: set status SENT and update finish date
     *
     * @return $this
     */
    protected function _finishQueue()
    {
        $this->setQueueFinishAt($this->_date->gmtDate());
        $this->setQueueStatus(self::STATUS_SENT);
        $this->save();

        return $this;
    }

    /**
     * Getter data for saving
     *
     * @return array
     */
    public function getDataForSave()
    {
        $data = [];
        $data['template_id'] = $this->getTemplateId();
        $data['queue_status'] = $this->getQueueStatus();
        $data['queue_start_at'] = $this->getQueueStartAt();
        $data['queue_finish_at'] = $this->getQueueFinishAt();
        return $data;
    }

    /**
     * Add designs to queue.
     *
     * @param array $designIds
     * @return $this
     */
    public function addDesignsToQueue(array $designIds)
    {
        $this->_getResource()->addDesignsToQueue($this, $designIds);
        return $this;
    }

    /**
     * Setter for save stores flag.
     *
     * @param boolean|integer|string $value
     * @return $this
     */
    public function setSaveStoresFlag($value)
    {
        $this->_saveStoresFlag = (bool)$value;
        return $this;
    }

    /**
     * Getter for save stores flag.
     *
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getSaveStoresFlag()
    {
        return $this->_saveStoresFlag;
    }

    /**
     * Setter for stores of queue.
     *
     * @param array $storesIds
     * @return $this
     */
    public function setStores(array $storesIds)
    {
        $this->setSaveStoresFlag(true);
        $this->_stores = $storesIds;
        return $this;
    }

    /**
     * Getter for stores of queue.
     *
     * @return array
     */
    public function getStores()
    {
        if (!$this->_stores) {
            $this->_stores = $this->_getResource()->getStores($this);
        }

        return $this->_stores;
    }

    /**
     * Retrieve Customer Template object
     *
     * @return \Designnbuy\Customer\Model\Template
     */
    public function getTemplate()
    {
        if ($this->_template === null) {
            $this->_template = $this->_templateFactory->create()->load($this->getTemplateId());
        }
        return $this->_template;
    }

    /**
     * Return true if template type eq text
     *
     * @return boolean
     */
    public function isPlain()
    {
        return $this->getType() == self::TYPE_TEXT;
    }

    /**
     * Getter for template type
     *
     * @return int|string
     */
    public function getType()
    {
        return $this->getCustomerType();
    }
}
