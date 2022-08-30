<?php

namespace Designnbuy\Template\Model\ResourceModel;

/**
 * Page identifier generator
 */
class PageIdentifierGenerator
{
    /**
     * @var \Designnbuy\Template\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var \Designnbuy\Template\Model\CategoryFactory
     */
    protected $_categoryFactory;

    protected $_storeManager;
    /**
     * Construct
     *
     * @param \Designnbuy\Template\Model\TemplateFactory $templateFactory
     */
    public function __construct(
        \Designnbuy\Template\Model\TemplateFactory $templateFactory,
        \Designnbuy\Template\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_templateFactory = $templateFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager = $storeManager;
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function generate(\Magento\Framework\Model\AbstractModel $object)
    {
        $identifier = trim($object->getData('title'));
        if (!$identifier) {
            return;
        }

        $from = [
            'Á', 'À', 'Â', 'Ä', 'Ă', 'Ā', 'Ã', 'Å', 'Ą', 'Æ', 'Ć', 'Ċ', 'Ĉ', 'Č', 'Ç', 'Ď', 'Đ', 'Ð', 'É', 'È', 'Ė', 'Ê', 'Ë', 'Ě', 'Ē', 'Ę', 'Ə', 'Ġ', 'Ĝ', 'Ğ', 'Ģ', 'á', 'à', 'â', 'ä', 'ă', 'ā', 'ã', 'å', 'ą', 'æ', 'ć', 'ċ', 'ĉ', 'č', 'ç', 'ď', 'đ', 'ð', 'é', 'è', 'ė', 'ê', 'ë', 'ě', 'ē', 'ę', 'ə', 'ġ', 'ĝ', 'ğ', 'ģ', 'Ĥ', 'Ħ', 'I', 'Í', 'Ì', 'İ', 'Î', 'Ï', 'Ī', 'Į', 'Ĳ', 'Ĵ', 'Ķ', 'Ļ', 'Ł', 'Ń', 'Ň', 'Ñ', 'Ņ', 'Ó', 'Ò', 'Ô', 'Ö', 'Õ', 'Ő', 'Ø', 'Ơ', 'Œ', 'ĥ', 'ħ', 'ı', 'í', 'ì', 'i', 'î', 'ï', 'ī', 'į', 'ĳ', 'ĵ', 'ķ', 'ļ', 'ł', 'ń', 'ň', 'ñ', 'ņ', 'ó', 'ò', 'ô', 'ö', 'õ', 'ő', 'ø', 'ơ', 'œ', 'Ŕ', 'Ř', 'Ś', 'Ŝ', 'Š', 'Ş', 'Ť', 'Ţ', 'Þ', 'Ú', 'Ù', 'Û', 'Ü', 'Ŭ', 'Ū', 'Ů', 'Ų', 'Ű', 'Ư', 'Ŵ', 'Ý', 'Ŷ', 'Ÿ', 'Ź', 'Ż', 'Ž', 'ŕ', 'ř', 'ś', 'ŝ', 'š', 'ş', 'ß', 'ť', 'ţ', 'þ', 'ú', 'ù', 'û', 'ü', 'ŭ', 'ū', 'ů', 'ų', 'ű', 'ư', 'ŵ', 'ý', 'ŷ', 'ÿ', 'ź', 'ż', 'ž',
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
            'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
            'І', 'і', 'Ї', 'ї', 'Є', 'є',
            ' & ', '&',
        ];

        $to = [
            'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'C', 'C', 'C', 'C', 'D', 'D', 'D', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'G', 'G', 'G', 'G', 'G', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'c', 'c', 'c', 'c', 'd', 'd', 'd', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'g', 'g', 'g', 'g', 'g', 'H', 'H', 'I', 'I', 'I', 'I', 'I', 'I', 'I', 'I', 'IJ', 'J', 'K', 'L', 'L', 'N', 'N', 'N', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'CE', 'h', 'h', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'ij', 'j', 'k', 'l', 'l', 'n', 'n', 'n', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'R', 'R', 'S', 'S', 'S', 'S', 'T', 'T', 'T', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'W', 'Y', 'Y', 'Y', 'Z', 'Z', 'Z', 'r', 'r', 's', 's', 's', 's', 'B', 't', 't', 'b', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'w', 'y', 'y', 'y', 'z', 'z', 'z',
            'A', 'B', 'V', 'H', 'D', 'e', 'Io', 'Z', 'Z', 'Y', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'Ch', 'C', 'Ch', 'Sh', 'Shtch', '', 'Y', '', 'E', 'Iu', 'Ia',
            'a', 'b', 'v', 'h', 'd', 'e', 'io', 'z', 'z', 'y', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'ch', 'c', 'ch', 'sh', 'shtch', '', 'y', '', 'e', 'iu', 'ia',
            'I', 'i', 'Ji', 'ji', 'Je', 'je',
            '-and-', '-and-',
        ];

        $template = $this->_templateFactory->create();
        $category = $this->_categoryFactory->create();
        if ($object->getData('identifier')) {
            $identifier = trim($object->getData('identifier'), '-');
            $template->checkIdentifier($identifier, $this->getStoreId());
        }


        $identifier = str_replace($from, $to, $identifier);
        $identifier = mb_strtolower($identifier);
        $identifier = preg_replace('/[^A-Za-z0-9-]+/', '-', $identifier);
        $identifier = preg_replace('/[--]+/', '-', $identifier);

        $identifier = trim($identifier, '-');

        $number = 1;
        while (true) {
            $finalIdentifier = $identifier . ($number > 1 ? '-'.$number : '');
            $templateId = $template->checkIdentifier($finalIdentifier, $this->getStoreId());
            $categoryId = $category->checkIdentifier($finalIdentifier, $this->getStoreId(), $object->getWebsiteIds($object));

            if (!$templateId && !$categoryId) {
                break;
            } else {
                if ($templateId
                    && $templateId == $object->getId()
                    && $object instanceof \Designnbuy\Template\Model\Template
                ) {
                    break;
                }

                if ($categoryId
                    && $categoryId == $object->getId()
                    && $object instanceof \Designnbuy\Template\Model\Category
                ) {
                    break;
                }
            }
            $number++;
        }

        $object->setData('identifier', $finalIdentifier);
    }
}
