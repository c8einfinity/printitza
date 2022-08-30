<?php
namespace Designnbuy\Template\Model\ResourceModel\Template;

class Crosssell extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var \Designnbuy\Designidea\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollectionFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->string = $string;
        $this->dateTime = $dateTime;
        $this->templateCollectionFactory = $templateCollectionFactory;
    }

    /**
     * initialize resource model
     *
     * @access protected
     * @see Magento\Framework\Model\ResourceModel\Db\AbstractDb::_construct()
     * @author
     */
    protected function  _construct()
    {
        $this->_init('designnbuy_template_crosssell_template', 'crosssell_id');
    }
    /**
     * Save product template - product relations
     *
     * @access public
     * @param Designnbuy\Template\Model\Template $template
     * @param array $data
     * @return Designnbuy\Template\Model\ResourceModel\Template
     * @author Ajay Makwana
     */
    public function saveCrosssellTemplateRelation($template, $data)
    {
        $deleteCondition = $this->getConnection()->quoteInto('template_id=?', $template->getId());
        $this->getConnection()->delete($this->getMainTable(), $deleteCondition);
        if(isset($data['crosssell']) && !empty($data['crosssell'])){
            $data = $data['crosssell'];
            foreach ($data as $crosssellId => $info) {
                $this->getConnection()->insert(
                    $this->getMainTable(),
                    array(
                        'template_id' => $template->getId(),
                        'crosssell_id'    => $crosssellId,
                        'position'      => @$info['position']
                    )
                );
            }
        }

        return $this;
    }

    /**
     * Retrieve printingmethod upsell printingmethods
     * @return \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\Collection
     */
    public function getCrosssellTemplates($template)
    {
        if ($template instanceof \Designnbuy\Template\Model\Template) {
            $productId = $template->getId();
        } elseif (is_numeric($template)) {
            $productId = $template;
        }

        $collection = $this->templateCollectionFactory->create();
        $collection->addAttributeToSelect('title');
        $collection->addAttributeToSelect('image');
        $collection->addAttributeToSelect(array('svg'),'left');
        $collection->addAttributeToFilter('svg', array('neq' => 'NULL' ));
        $collection->joinAttribute('title','designnbuy_template/title','entity_id',null,'left',0);
        $collection->getSelect()->joinLeft(
            ['rl' => $this->getTable('designnbuy_template_crosssell_template')],
            'e.entity_id = rl.crosssell_id',
            ['rl.position']
        )->where(
            'rl.template_id = ?',
            $productId
        );
        $collection->setOrder('rl.position', 'ASC');
        return $collection;
    }
}
