<?php
namespace Designnbuy\Designidea\Model\ResourceModel\Designidea\Product;

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
     * @return Designnbuy\Designidea\Model\ResourceModel\Designidea\Product\Collection
     * @author Ajay Makwana
     */
    protected function _joinedFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                ['rl' => $this->getTable('designnbuy_designidea_relatedproduct')],
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
     * @param Designnbuy\Designidea\Model\Designidea | int $designidea
     * @return Designnbuy\Designidea\Model\ResourceModel\Designidea\Product\Collection
     * @author Ultimate Module Creator
     */
    public function addDesignIdeaFilter($designidea)
    {

        if ($designidea instanceof \Designnbuy\Designidea\Model\Designidea) {
            //$designidea = $designidea->getId();
        }
        if (!$this->_joinedFields ) {
            $this->_joinedFields();
        }

        $this->getSelect()->where('rl.designidea_id = ?', $designidea->getId());
        return $this;
    }
}
