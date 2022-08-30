<?php
namespace Designnbuy\Designidea\Model\ResourceModel\Designidea;

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
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->string = $string;
        $this->dateTime = $dateTime;
        $this->designideaCollectionFactory = $designideaCollectionFactory;
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
        $this->_init('designnbuy_designidea_upsell_designidea', 'upsell_id');
    }
    /**
     * Save product designidea - product relations
     *
     * @access public
     * @param Designnbuy\Designidea\Model\Designidea $designidea
     * @param array $data
     * @return Designnbuy\Designidea\Model\ResourceModel\Designidea
     * @author Ajay Makwana
     */
    public function saveUpsellDesignideaRelation($designidea, $data)
    {
        $deleteCondition = $this->getConnection()->quoteInto('designidea_id=?', $designidea->getId());
        $this->getConnection()->delete($this->getMainTable(), $deleteCondition);
        if(isset($data['upsell']) && !empty($data['upsell'])){
            $data = $data['upsell'];
            foreach ($data as $upsellId => $info) {
                $this->getConnection()->insert(
                    $this->getMainTable(),
                    array(
                        'designidea_id' => $designidea->getId(),
                        'upsell_id'    => $upsellId,
                        'position'      => @$info['position']
                    )
                );
            }
        }

        return $this;
    }

    /**
     * Save  product - product designidea relations
     *
     * @access public
     * @param Magento\Catalog\Model\Product $prooduct
     * @param array $data
     * @return Designnbuy\Designidea\Model\ResourceModel\Designidea\Product
     * @@author Ajay Makwana
     */
    /*public function saveUpsellRelation($upsell, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }

        $tableName = 'designnbuy_designidea_upsell_designidea';
        $table = $this->getTable($tableName);
        $where = ['upsell_id = ?' => (int)$upsell->getId()];
        $this->getConnection()->delete($table, $where);

        $designideaData = [];

        foreach ($data['upsell'] as $key => $designidea) {
            $designideaData[] = [
                'upsell_id' => (int)$designidea->getId(),
                'designidea_id' => $key,
                'position' => (isset($designidea['position'])) ? $designidea['position'] : 0
            ];
        }

        if(isset($designideaData) && !empty($designideaData)){
            $this->getConnection()->insertMultiple($table, $designideaData);
        }
        return $this;
    }*/

    /**
     * Retrieve Designidea upsell Designidea
     * @return \Designnbuy\Designidea\Model\ResourceModel\Designidea\Collection
     */
    public function getUpsellDesignideas($designidea)
    {
        if ($designidea instanceof \Designnbuy\Designidea\Model\Designidea) {
            $productId = $designidea->getId();
        } elseif (is_numeric($designidea)) {
            $productId = $designidea;
        }

        $collection = $this->designideaCollectionFactory->create();
        $collection->addAttributeToSelect('title');
        $collection->addAttributeToSelect('image');
        $collection->addAttributeToSelect(array('svg'),'left');
        $collection->addAttributeToFilter('svg', array('neq' => 'NULL' ));
        $collection->joinAttribute('title','designnbuy_designidea/title','entity_id',null,'left',0);
        $collection->getSelect()->joinLeft(
            ['rl' => $this->getTable('designnbuy_designidea_upsell_designidea')],
            'e.entity_id = rl.upsell_id',
            ['rl.position']
        )->where(
            'rl.designidea_id = ?',
            $productId
        );
        $collection->setOrder('rl.position', 'ASC');
        return $collection;
    }
}
