<?php
/**
 * Purpletree_Marketplace Payments
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Infotech Private Limited
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */
 
namespace Purpletree\Marketplace\Block\Adminhtml\Payments\Edit\Tab;

class Payments extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
   
    /**
     * constructor
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Config\Model\Config\Source\Yesno $booleanOptions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller $sellerinfo
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public $sellerinfo;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $sellerinfo,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->sellerinfo            = $sellerinfo;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * Prepare form
     * @return $this
     */
    protected function _prepareForm()
    {
        $this->toOptionArray();
        $payments = $this->_coreRegistry->registry('purpletree_marketplace_payments');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('payments');
        $form->setFieldNameSuffix('payments');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Payments Information'),
                'class'  => 'fieldset-wide'
            ]
        );
        if ($payments->getId()) {
            $fieldset->addField(
                'entity_id',
                'hidden',
                ['name' => 'entity_id']
            );
        }
        $fieldset->addField(
            'transaction_id',
            'text',
            [
                'name'  => 'transaction_id',
                'label' => __('Transaction Id'),
                'title' => __('Transaction Id'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'payment_mode',
            'text',
            [
                'name'  => 'payment_mode',
                'label' => __('Payment Mode'),
                'title' => __('Payment Mode'),
                 'required' => true,
            ]
        );
        $fieldset->addField(
            'amount',
            'text',
            [
                'name'  => 'amount',
                'label' => __('Amount'),
                'title' => __('Amount'),
                'class' => 'validate-digits required-entry validate-greater-than-zero', //add multiple classess
                 'required' => true,
            ]
        );
        $fieldset->addField(
            'seller_id',
            'select',
            [
                'name'  => 'seller_id',
                'label' => __('Seller'),
                'title' => __('Seller'),
                'values' => $this->toOptionArray(),
                'value' => $this->getRequest()->getParam('id')
            ]
        );
        $fieldset->addField(
            'status',
            'text',
            [
                'name'  => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
            ]
        );
        $paymentsData = $this->_session->getData('purpletree_marketplace_payments_data', true);
        if ($paymentsData) {
            $payments->addData($paymentsData);
        } else {
            if (!$payments->getId()) {
                $payments->addData($payments->getDefaultValues());
            }
        }
        $form->addValues($payments->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
    /**
     * Prepare label for tab
     * @return string
     */
    public function toOptionArray()
    {
        $sellers = $this->sellerinfo->getAllSellers();
        $options = [];
        foreach ($sellers as $seller) {
            $options[] = [
                    'value' => $seller['seller_id'],
                'label' => $seller['store_name']
            ];
        }
          return $options;
    }
    public function getTabLabel()
    {
        return __('Payments');
    }
    /**
     * Prepare title for tab
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }
    /**
     * Can show tab in tabs
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * Tab is hidden
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
