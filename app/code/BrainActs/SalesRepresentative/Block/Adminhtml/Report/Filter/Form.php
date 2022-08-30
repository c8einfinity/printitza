<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Report\Filter;

use Magento\Backend\Model\Auth\Session;

/**
 * Class Form
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Form extends \Magento\Reports\Block\Adminhtml\Filter\Form
{
    /**
     * @var \BrainActs\SalesRepresentative\Model\MemberFactory
     */
    private $memberFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Model\Config
     */
    private $configFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface
     */
    private $memberRepository;

    /**
     * @var Session
     */
    private $adminSession;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \BrainActs\SalesRepresentative\Model\MemberFactory $memberFactory
     * @param \BrainActs\SalesRepresentative\Model\ConfigFactory $configFactory
     * @param \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface $memberRepository
     * @param Session $adminSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \BrainActs\SalesRepresentative\Model\MemberFactory $memberFactory,
        \BrainActs\SalesRepresentative\Model\ConfigFactory $configFactory,
        \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface $memberRepository,
        Session $adminSession,
        array $data = []
    )
    {

        $this->memberFactory = $memberFactory;
        $this->configFactory = $configFactory;
        $this->memberRepository = $memberRepository;
        $this->adminSession = $adminSession;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Add fields to base fieldset which are general to report
     * @return $this|\Magento\Reports\Block\Adminhtml\Filter\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()//@codingStandardsIgnoreLine
    {
        parent::_prepareForm();
        $form = $this->getForm();

        /** @var \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof \Magento\Framework\Data\Form\Element\Fieldset) {
            $fieldset->removeField('period_type');
            $fieldset->removeField('show_empty_rows');
            $fieldset->removeField('report_type');

            $collection = $this->memberFactory->create()->getCollection();
            $parentBlockName = $this->getParentBlock()->getNameInLayout();
            $values = [];
            if ($parentBlockName != 'salesrep.report.grid.container.profit') {
                $values[] = [
                    'label' => __('All Sales Reps'),
                    'value' => ''
                ];
            }

            foreach ($collection as $member) {
                $values[] = [
                    'label' => implode(', ', [trim($member->getFirstname()), trim($member->getLastname())]),
                    'value' => $member->getId()];
            }

            $configReportRole = $this->configFactory->create()->getReportRole();
            $currentUserRole = $this->adminSession->getUser()->getRole()->getId();

            $configReportRoleList = explode(',', $configReportRole);

            if (in_array($currentUserRole, $configReportRoleList) || $configReportRole == null
                || empty($configReportRole)) {
                $fieldset->addField(
                    'member_id',
                    'select',
                    [
                        'name' => 'member_id',
                        'label' => __('Member'),
                        'values' => $values,
                    ],
                    'member_id'
                );
            } else {
                $member = $this->memberRepository->getByUserId($this->adminSession->getUser()->getId());
                $fieldset->addField(
                    'member_id',
                    'hidden',
                    [
                        'name' => 'member_id',
                        'value' => $member->getId()
                    ],
                    'member_id'
                );
            }
        }

        return $this;
    }
}
