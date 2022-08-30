<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Designnbuy\CustomerPhotoAlbum\Plugin\Product\View\Options\Type;

use Magento\Catalog\Block\Product\View\Options\Type\Select as TypeSelect;

class Select
{
    protected $request;

    public function __construct(
        \Magento\Framework\App\Request\Http $request
    ){
        $this->request = $request;
    }
    /**
     * Return html for control element
     *
     * @param TypeSelect $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetValuesHtml(TypeSelect $subject, \Closure $proceed)
    {
        $moduleName = $this->request->getModuleName();
        $controller = $this->request->getControllerName();
        $action     = $this->request->getActionName();
        $route      = $this->request->getRouteName();

        if ($moduleName.'_'.$controller.'_'.$action == 'photoalbum_album_edit') {
            $_option = $subject->getOption();
            $_option->setIsSwatch(0);
        }
        return $proceed();
    }
    
}
