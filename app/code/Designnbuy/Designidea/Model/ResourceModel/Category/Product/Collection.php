<?php
namespace Designnbuy\Designidea\Model\ResourceModel\Category\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{

    /**
     * remember if fields have been joined
     *
     * @var bool
     */
    protected $_joinedFields = false;    

    /**
     * join the link table
     *
     * @access public
     * @return Designnbuy\Designidea\Model\ResourceModel\Category\Product\Collection
     * @author Ajay Makwana
     */
    protected function _joinedFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                ['rl' => $this->getTable('designnbuy_designidea_category_relatedproduct')],
                'e.entity_id = rl.related_id',
                ['position']
            );


            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add product design filter
     *
     * @access public
     * @param Designnbuy\Designidea\Model\Category | int $category
     * @return Designnbuy\Designidea\Model\ResourceModel\Category\Product\Collection
     * @author Ultimate Module Creator
     */
    public function addDesignIdeaCategoryFilter(\Designnbuy\Designidea\Model\Category $category)
    {

        if ($category instanceof \Designnbuy\Designidea\Model\Category) {
            //$category = $category->getId();
        }
        if (!$this->_joinedFields ) {
            $this->_joinedFields();
        }

        $this->getSelect()->where('rl.category_id = ?', $category->getId());
        return $this;
    }
}
