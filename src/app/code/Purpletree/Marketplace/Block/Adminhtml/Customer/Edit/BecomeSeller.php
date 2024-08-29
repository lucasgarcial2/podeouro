<?php

/**
 * Purpletree_Marketplace BecomeSeller
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Software
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */

namespace Purpletree\Marketplace\Block\Adminhtml\Customer\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Customer account form block
 */
class BecomeSeller extends Generic implements TabInterface
{

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller $sellercustom
     * @param \Purpletree\Marketplace\Model\SellerFactory $sellerFactory
     * @param array $data
     */
    public $customerRepository;
    public $_coreRegistry;
    public $sellercustom;
    public $_sellerFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $sellercustom,
        \Purpletree\Marketplace\Model\SellerFactory $sellerFactory,
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        $this->_coreRegistry = $registry;
        $this->sellercustom = $sellercustom;
        $this->_sellerFactory = $sellerFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    /**
     * @return string|null
     */
    public function getIsSeller()
    {
        $customer = $this->customerRepository->getById($this->getCustomerId());
        if (!empty($customer->getCustomAttribute('is_seller'))) {
            return $customer->getCustomAttribute('is_seller')->getValue();
        } else {
            return 0;
        }
    }

    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }
 
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Become A Seller');
    }
 
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Become A Seller');
    }
 
    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            if ($this->getIsSeller() == 0) {
                return true;
            }
        }
        return false;
    }
 
    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }
 
    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }
 
    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }
 
    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }
    public function getsellerEntityId()
    {
        $id = $this->getCustomerId();
        return $this->sellercustom->getsellerEntityId($id);
    }

    public function initForm()
    {
        if (!$this->canShowTab()) {
            return $this;
            return $this;
        }
        $form = $this->_formFactory->create();
         
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Become Seller')]);
        $entityId  = '';
        if (!empty($this->getsellerEntityId())) {
            $entityId = $this->getsellerEntityId();
        }
         $fieldset->addField(
             'entity_idpts',
             'hidden',
             [
                     'name' => 'seller[entity_idpts]',
                    'data-form-part' => $this->getData('target_form'),
                    'class' => 'seller',
                    'value' => $entityId
                ]
         );
         $becomeseller = $fieldset->addField(
             'is_seller',
             'checkbox',
             [
                'label' => __('Become a Seller'),
                'name' => 'seller[is_seller]',
                'data-form-part' => $this->getData('target_form'),
                'onchange' => 'this.value = this.checked;'
             ]
         );
             $becomeseller->setAfterElementHtml("   
            <script type=\"text/javascript\">
                    require([
                    'jquery',
                    'mage/template',
                    'jquery/ui',
                    'mage/translate'
                ],
                function($, mageTemplate) {
                            $('.admin__field.field.field-store_name').hide();
                            $('.admin__field.field.field-store_url').hide();
                   $(document).on('change', '#is_seller', function(event){
                        if ($(this).is(':checked')) {
                             $('.admin__field.field.field-store_name').show();
                             $('.admin__field.field.field-store_url').show();
                             $('#store_name').addClass('required-entry');
                             $('#store_name').addClass('_required');
                             $('#store_url').addClass('required-entry');
                             $('#store_url').addClass('_required');
                        } else {
                             $('.admin__field.field.field-store_name').hide();
                             $('.admin__field.field.field-store_url').hide();
                              $('#store_name').removeClass('required-entry');
                              $('#store_name').removeClass('_required');
                             $('#store_url').removeClass('required-entry');
                             $('#store_url').removeClass('_required');
                        }
                   })
                }

            );
            </script>");
        $fieldset->addField(
            'store_name',
            'text',
            [
                'name' => 'seller[store_name]',
                'label' => __('Store Name'),
                'title' => __('Store Name'),
                'required' => false,
                'data-form-part' => $this->getData('target_form'),
                'class' => 'seller'
            ]
        );
        $fieldset->addField(
            'store_url',
            'text',
            [
                'name' => 'seller[store_url]',
                'class' => 'seller',
                'label' => __('Store Url'),
                'title' => __('Store Url'),
                'data-form-part' => $this->getData('target_form'),
                'required' => false,
            ]
        );
        if ($entityId != '') {
            $model = $this->_initSeller($entityId);
            $form->setValues($model->getData());
            $form->setUseContainer(true);
        }
        $this->setForm($form);
        return $this;
    }
             /**
              * Init Seller
              *
              * @return \Purpletree\Marketplace\Model\Post
              */
    protected function _initSeller($id)
    {
        /** @var \Purpletree\Marketplace\Model\Post $post */
        $seller    = $this->_sellerFactory->create();
        if ($id != '') {
            $seller->load($id);
        }
        return $seller;
    }
    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->canShowTab()) {
            $this->initForm();
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
