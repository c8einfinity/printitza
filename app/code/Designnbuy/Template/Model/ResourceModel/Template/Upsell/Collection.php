<?php
namespace Designnbuy\Template\Model\ResourceModel\Template\Upsell;

class Collection extends \Designnbuy\Template\Model\ResourceModel\Template\Collection
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
     * @return Designnbuy\Template\Model\ResourceModel\Template\Product\Collection
     * @author Ajay Makwana
     */
    protected function _joinedFields()
    {
        /*if (!$this->_joinedFields) {
            $this->getSelect()->join(
                ['rl' => $this->getTable('designnbuy_template_upsell_template')],
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
     * @param Designnbuy\Template\Model\Template | int $template
     * @return Designnbuy\Template\Model\ResourceModel\Template\Product\Collection
     * @author Ultimate Module Creator
     */
    /*public function addTemplateFilter($template)
    {

        if ($template instanceof \Designnbuy\Template\Model\Template) {
            //$template = $template->getId();
        }
        if (!$this->_joinedFields ) {
            $this->_joinedFields();
        }

        $this->getSelect()->where('rl.template_id = ?', $template->getId());
        return $this;
    }*/
}
