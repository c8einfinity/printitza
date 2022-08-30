<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Block\Adminhtml\Grid\Column;

use Designnbuy\Background\Model\Url;

/**
 * Admin clipart grid statuses 
 */
class Image extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * @var \Designnbuy\Background\Model\Url
     */
    protected $_url;

    public function __construct( Url $url){
        $this->_url = $url;
    }
    /**
     * Add to column decorated status
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return [$this, 'renderImage'];
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param  \Magento\Framework\Model\AbstractModel $row
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool $isExport
     * @return string
     */
    public function renderImage($value, $row, $column, $isExport)
    {
        $cell = '';
        if ($row->getImage()) {
            $image = $this->_url->getBackgroundImageMediaUrl($row->getImage());
            $cell = '<image height="50" src ="' . $image . '" alt="' . $row->getTitle() . '" >';
        }
        return $cell;
    }
}
