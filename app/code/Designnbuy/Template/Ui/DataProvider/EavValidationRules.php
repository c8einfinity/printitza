<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Template\Ui\DataProvider;

use Designnbuy\Template\Api\Data\TemplateAttributeInterface;

/**
 * Class build validation rules for catalog EAV attributes
 */
class EavValidationRules
{
    /**
     * Build validation rules
     *
     * @param ProductAttributeInterface $attribute
     * @param array $data
     * @return array
     */
    public function build(TemplateAttributeInterface $attribute, array $data)
    {
        $rules = [];
        if (!empty($data['required'])) {
            $rules['required-entry'] = true;
        }
        if ($attribute->getFrontendInput() === 'price') {
            $rules['validate-zero-or-greater'] = true;
        }

        $validationClasses = explode(' ', $attribute->getFrontendClass());

        foreach ($validationClasses as $class) {
            if (preg_match('/^maximum-length-(\d+)$/', $class, $matches)) {
                $rules = array_merge($rules, ['max_text_length' => $matches[1]]);
                continue;
            }
            if (preg_match('/^minimum-length-(\d+)$/', $class, $matches)) {
                $rules = array_merge($rules, ['min_text_length' => $matches[1]]);
                continue;
            }

            $rules = $this->mapRules($class, $rules);
        }

        return $rules;
    }

    /**
     * Map classes w. rules
     *
     * @param string $class
     * @param array $rules
     * @return array
     */
    protected function mapRules($class, array $rules)
    {
        switch ($class) {
            case 'validate-number':
            case 'validate-digits':
            case 'validate-email':
            case 'validate-url':
            case 'validate-alpha':
            case 'validate-alphanum':
                $rules = array_merge($rules, [$class => true]);
                break;
        }

        return $rules;
    }
}
