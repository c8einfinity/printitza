<?php
namespace Designnbuy\Productattach\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface ProductattachLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Designnbuy\Productattach\Model\Productattach
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
