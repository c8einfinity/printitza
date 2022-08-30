<?php
namespace Designnbuy\Reseller\Observer;
use Magento\Framework\Event\ObserverInterface;
class LimitCoreCollection implements ObserverInterface
{
    const XML_PATH_VALIDATE_CALLBACK = 'adminhtml/designnbuy/reseller/';
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $moduleDirReader;

    /**
     * @var \Magento\Framework\Xml\Parser
     */
    private $parser;

    protected $_callbacks      = array();

    public function __construct(
        \Magento\Framework\Module\Dir\Reader $moduleDirReader,
       // \Magento\Framework\App\ObjectManager $objectManager,
        \Designnbuy\Reseller\Model\Admin $reseller,
        \Magento\Framework\Xml\Parser $parser
    )
    {
        $this->moduleDirReader = $moduleDirReader;
       // $this->objectManager = $objectManager;
        $this->_reseller = $reseller;
        $this->parser = $parser;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->_reseller->isResellerAdmin()) {
            $collection = $observer->getEvent()->getCollection();
            if (!$callback = $this->_pickCallback('collection_load_before', $collection)) {
                return;
            }

            $this->_invokeCallback($callback, 'Designnbuy\Reseller\Model\Collections', $collection);
        }
    }

    /**
     * Get a limiter callback for an instance from mappers configuration
     *
     * @param string $callbackGroup (collection, model)
     * @param object $instance
     * @return string
     */
    public function _pickCallback($callbackGroup, $instance)
    {
        if (!$instanceClass = get_class($instance)) {
            return;
        }

        // gather callbacks from mapper configuration
        if (!isset($this->_callbacks[$callbackGroup])) {
            $this->_callbacks[$callbackGroup] = array();
            $filePath = $this->moduleDirReader->getModuleDir('etc', 'Designnbuy_Reseller'). '/mages/callback.xml';
            $xmlData = $this->parser->load($filePath);
            $xml = new \Magento\Framework\Simplexml\Config();
            $xml->loadFile($filePath);

            $callbacks = (array)$xml->getNode(self::XML_PATH_VALIDATE_CALLBACK . $callbackGroup);

            foreach ($callbacks as $className => $callback) {
                $factoryClassName = str_replace('_', '\\', $className);
                switch ($callbackGroup) {
                    case 'collection_load_before':
                        $className = $factoryClassName;
                        break;
                    case 'block_html_before':
                       // $className = Mage::getConfig()->getBlockClassName($factoryClassName);
                        break;
                    default:
                        //$className = Mage::getConfig()->getModelClassName($factoryClassName);
                }


                /*if ($className) {
                    $this->_callbacks[$callbackGroup][$className] = $this->_recognizeCallbackString($callback);
                }*/
                $this->_callbacks[$callbackGroup][$className] = $this->_recognizeCallbackString($callback);
            }
        }

        /**
         * Determine callback for current instance
         * Explicit class name has priority before inherited classes
         */
        $result = false;
        if (isset($this->_callbacks[$callbackGroup][$instanceClass])) {
            $result = $this->_callbacks[$callbackGroup][$instanceClass];
        }
        else {
            foreach ($this->_callbacks[$callbackGroup] as $className => $callback) {
                if ($instance instanceof $className) {
                    $result = $callback;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Seek for factory class name in specified callback string
     *
     * @param string $callbackString
     * @return string|array
     */
    protected function _recognizeCallbackString($callbackString)
    {
        if (preg_match('/^([^:]+?)::([^:]+?)$/', $callbackString, $matches)) {
            array_shift($matches);
            return $matches;
        }
        return $callbackString;
    }

    /**
     * Invoke specified callback depending on whether it is a string or array
     *
     * @param string|array $callback
     * @param string $defaultFactoryClassName
     * @param object $passthroughObject
     */
    protected function _invokeCallback($callback, $defaultFactoryClassName, $passthroughObject)
    {
        $class  = $defaultFactoryClassName;
        $method = $callback;
        if (is_array($callback)) {
            list($class, $method) = $callback;
        }

		\Magento\Framework\App\ObjectManager::getInstance()->create($class)->$method($passthroughObject);
        
    }
}