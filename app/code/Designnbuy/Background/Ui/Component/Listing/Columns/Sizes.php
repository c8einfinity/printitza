<?php
namespace Designnbuy\Background\Ui\Component\Listing\Columns;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Sizes extends Column{

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * System store
     *
     * @var SystemStore
     */
    protected $systemStore;

    protected $productFactory;

    /**
     * Background factory
     *
     * @var \Designnbuy\Background\Model\BackgroundFactory
     */
    protected $_backgroundFactory;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SystemStore $systemStore
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Designnbuy\Background\Model\BackgroundFactory $backgroundFactory,
        array $components = [],
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->escaper = $escaper;
        $this->_backgroundFactory = $backgroundFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
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
            foreach ($dataSource['data']['items'] as & $item) {
                $background = $this->_backgroundFactory->create();
                $background->load($item['background_id']);
                $images = $background->getBackgroundImages();
                if(is_array($images) && !empty($images)) {
                    $sizes = [];
                    foreach ($images as $image) {
                        $sizes[] = $image['width'] . ' X ' . $image['height'];
                    }
                    $item[$this->getData('name')] = implode(', ', $sizes);
                } else {
                    $item[$this->getData('name')] = '';
                }

            }
        }

        return $dataSource;
    }
}