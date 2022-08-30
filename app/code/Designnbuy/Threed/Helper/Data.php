<?php
/**
 * Customer attribute data helper
 */

namespace Designnbuy\Threed\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Base media folder path
     */
    const MAP_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'threed'. DIRECTORY_SEPARATOR .'map';
    const MODEL_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'threed'. DIRECTORY_SEPARATOR .'model';

    /**
     * Media Directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_imageFactory = $imageFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Return full path to file
     *
     * @param string $path
     * @param string $file
     * @return string
     */
    public function getFilePath($path, $file)
    {
        $path = rtrim($path, '/');
        $file = ltrim($file, '/');

        return $path . '/' . $file;
    }

    /**
     * Return file name form file path
     *
     * @param string $pathFile
     * @return string
     */
    public function getFileFromPathFile($pathFile)
    {
        $file = substr($pathFile, strrpos($pathFile, '/') + 1);

        return $file;
    }

    /**
     * Get filesize in bytes.
     * @param string $file
     * @return int
     */
    public function getFileSize($file)
    {
        return $this->_mediaDirectory->stat($file)['size'];
    }

    public function fileExists($filename)
    {
        return $this->_mediaDirectory->isFile($filename);
    }

    public function getMapImagePath()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::MAP_PATH);
    }

    public function get3DModelPath()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::MODEL_PATH);
    }

}
