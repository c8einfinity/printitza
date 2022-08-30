<?php
namespace Designnbuy\Template\Model\ResourceModel\Template;

class Upsell extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('designnbuy_template_upsell_template', 'upsell_id');
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
    public function saveUpsellTemplateRelation($template, $data)
    {
        $deleteCondition = $this->getConnection()->quoteInto('template_id=?', $template->getId());
        $this->getConnection()->delete($this->getMainTable(), $deleteCondition);
        if(isset($data['upsell']) && !empty($data['upsell'])){
            $data = $data['upsell'];
            foreach ($data as $upsellId => $info) {
                $this->getConnection()->insert(
                    $this->getMainTable(),
                    array(
                        'template_id' => $template->getId(),
                        'upsell_id'    => $upsellId,
                        'position'      => @$info['position']
                    )
                );
            }
        }

        return $this;
    }

    /**
     * Save  product - product template relations
     *
     * @access public
     * @param Magento\Catalog\Model\Product $prooduct
     * @param array $data
     * @return Designnbuy\Template\Model\ResourceModel\Template\Product
     * @@author Ajay Makwana
     */
    /*public function saveUpsellRelation($upsell, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }

        $tableName = 'designnbuy_template_upsell_template';
        $table = $this->getTable($tableName);
        $where = ['upsell_id = ?' => (int)$upsell->getId()];
        $this->getConnection()->delete($table, $where);

        $templateData = [];

        foreach ($data['upsell'] as $key => $template) {
            $templateData[] = [
                'upsell_id' => (int)$template->getId(),
                'template_id' => $key,
                'position' => (isset($template['position'])) ? $template['position'] : 0
            ];
        }

        if(isset($templateData) && !empty($templateData)){
            $this->getConnection()->insertMultiple($table, $templateData);
        }
        return $this;
    }*/

    /**
     * Retrieve printingmethod upsell printingmethods
     * @return \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\Collection
     */
    public function getUpsellTemplates($template)
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
            ['rl' => $this->getTable('designnbuy_template_upsell_template')],
            'e.entity_id = rl.upsell_id',
            ['rl.position']
        )->where(
            'rl.template_id = ?',
            $productId
        );
        $collection->setOrder('rl.position', 'ASC');
        return $collection;
    }
}
