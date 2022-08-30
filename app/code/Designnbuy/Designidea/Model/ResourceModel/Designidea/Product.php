<?php
namespace Designnbuy\Designidea\Model\ResourceModel\Designidea;

class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * initialize resource model
     *
     * @access protected
     * @see Magento\Framework\Model\ResourceModel\Db\AbstractDb::_construct()
     * @author
     */
    protected function  _construct()
    {
        $this->_init('designnbuy_designidea_relatedproduct', 'related_id');
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
    public function saveProductDesignIdeaRelation($designidea, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->getConnection()->quoteInto('designidea_id=?', $designidea->getId());
        $this->getConnection()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $productId => $info) {
            $this->getConnection()->insert(
                $this->getMainTable(),
                array(
                    'designidea_id' => $designidea->getId(),
                    'related_id'    => $productId,
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }

    /**
     * Save  product - product designIdea relations
     *
     * @access public
     * @param Magento\Catalog\Model\Product $prooduct
     * @param array $data
     * @return Designnbuy\Designidea\Model\ResourceModel\Designidea\Product
     * @@author Ajay Makwana
     */
    public function saveProductRelation($product, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->getConnection()->quoteInto('product_id=?', $product->getId());
        $this->getConnection()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $designIdeaId => $info) {
            $this->getConnection()->insert(
                $this->getMainTable(),
                array(
                    'designidea_id' => $designIdeaId,
                    'related_id'    => $product->getId(),
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }
}
