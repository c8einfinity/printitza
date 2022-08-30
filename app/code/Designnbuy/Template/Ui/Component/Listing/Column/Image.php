<?php
namespace Designnbuy\Template\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Image extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'image';

    const ALT_FIELD = 'name';

    protected $_storeManager;

    /**
     * @var \Designnbuy\Background\Model\Url
     */
    protected $_url;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Designnbuy\Template\Model\Url $url,
        array $components = [],
        array $data = [],
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if(isset($item) && !empty($item['image'])){
                    $imagePath = $this->_url->getMediaUrl($item['image']);//$mediaRelativePath.$item['image'];
                    $item[$fieldName . '_src'] = $imagePath;
                    $item[$fieldName . '_alt'] = $this->getAlt($item);
                    $item[$fieldName . '_link'] = '#';
                    $item[$fieldName . '_orig_src'] = $imagePath;
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row)
    {
        $altField = self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }
}