<?php
namespace Designnbuy\Designidea\Model\ResourceModel\Designidea\Upsell;

class Collection extends \Designnbuy\Designidea\Model\ResourceModel\Designidea\Collection
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
        /*if (!$this->_joinedFields) {
            $this->getSelect()->join(
                ['rl' => $this->getTable('designnbuy_designidea_upsell_designidea')],
                'e.entity_id = rl.upsell_id',
                ['position']
            );


            $this->_joinedFields = true;
        }*/
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
    /*public function addDesignideaFilter($designidea)
    {

        if ($designidea instanceof \Designnbuy\Designidea\Model\Designidea) {
            //$designidea = $designidea->getId();
        }
        if (!$this->_joinedFields ) {
            $this->_joinedFields();
        }

        $this->getSelect()->where('rl.designidea_id = ?', $designidea->getId());
        return $this;
    }*/
}
