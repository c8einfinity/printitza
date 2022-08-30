<?php
namespace Designnbuy\Template\Model\ResourceModel\Template;

class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('designnbuy_template_relatedproduct', 'related_id');
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
    public function saveProductTemplateRelation($template, $data)
    {
        if(is_array($data) && array_key_exists('product',$data) && isset($data['product'])){
            $deleteCondition = $this->getConnection()->quoteInto('template_id=?', $template->getId());
            $this->getConnection()->delete($this->getMainTable(), $deleteCondition);
            if(isset($data['product']) && !empty($data['product'])) {
                $data = $data['product'];
                foreach ($data as $productId => $info) {
                    $this->getConnection()->insert(
                        $this->getMainTable(),
                        array(
                            'template_id' => $template->getId(),
                            'related_id' => $productId,
                            'position' => @$info['position']
                        )
                    );
                }
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
    public function saveProductRelation($product, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }

        $tableName = 'designnbuy_template_relatedproduct';
        $table = $this->getTable($tableName);
        $where = ['related_id = ?' => (int)$product->getId()];
        $this->getConnection()->delete($table, $where);

        /*foreach ($data as $templateId => $info) {
            $this->getConnection()->insert(
                $this->getMainTable(),
                array(
                    'template_id' => $templateId,
                    'related_id'    => $product->getId(),
                    'position'      => @$info['position']
                )
            );
        }*/

        $templateData = [];

        foreach ($data['template'] as $key => $template) {
            $templateData[] = [
                'related_id' => (int)$product->getId(),
                'template_id' => $key,
                'position' => (isset($template['position'])) ? $template['position'] : 0
            ];
            /*$id = (int)$id;
            $printingMethodData[] = array_merge(['related_id' => (int)$product->getId(), 'printingmethod_id' => $id], 'position' => (isset($data[$id]) && is_array($data[$id])) ? $data[$id] : 0,
                (isset($data[$id]) && is_array($data[$id])) ? $data[$id] : []
            );*/
        }

        if(isset($templateData) && !empty($templateData)){
            $this->getConnection()->insertMultiple($table, $templateData);
        }


        return $this;
    }

    /**
     * Retrieve printingmethod related printingmethods
     * @return \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\Collection
     */
    public function getRelatedTemplates($product)
    {
        if ($product instanceof \Magento\Catalog\Model\Product) {
            $productId = $product->getId();
        } elseif (is_numeric($product)) {
            $productId = $product;
        }

        $collection = $this->templateCollectionFactory->create();
        $collection->addAttributeToSelect('title');
        $collection->addAttributeToSelect('image');
        $collection->addAttributeToSelect(array('svg'),'left');
        $collection->addAttributeToFilter('svg', array('neq' => 'NULL' ));
        $collection->joinAttribute('title','designnbuy_template/title','entity_id',null,'left',0);
        $collection->getSelect()->joinLeft(
            ['rl' => $this->getTable('designnbuy_template_relatedproduct')],
            'e.entity_id = rl.template_id',
            ['rl.position']
        )->where(
            'rl.related_id = ?',
            $productId
        );
        $collection->setOrder('rl.position', 'ASC');
        return $collection;
    }
}
