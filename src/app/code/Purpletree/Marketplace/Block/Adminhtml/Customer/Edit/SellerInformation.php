<?php

/**
 * Purpletree_Marketplace SellerInformation
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
use Purpletree\Marketplace\Helper\Data as MarketplaceHelper;

/**
 * Customer account form block
 */
class SellerInformation extends Generic implements TabInterface
{
    /*
     * @var MarketplaceHelper
     */
    private $_marketplaceHelper;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Directory\Model\Config\Source\Country $countryFactory
     * @param \Purpletree\Marketplace\Model\SellerFactory $sellerFactory
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller $sellercustom
     * @param \Purpletree\Marketplace\Model\Seller\Region $getStates
     * @param MarketplaceHelper $marketplaceHelper
     * @param array $data
     */
    public $customerRepository;
    public $_coreRegistry;
    public $_countryFactory;
    public $getStates;
    public $_wysiwygConfig;
    public $_sellerFactory;
    public $sellercustom;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        \Purpletree\Marketplace\Model\SellerFactory $sellerFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $sellercustom,
        \Purpletree\Marketplace\Model\Seller\Region $getStates,
        MarketplaceHelper $marketplaceHelper,
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        $this->_coreRegistry = $registry;
        $this->_countryFactory = $countryFactory;
        $this->getStates = $getStates;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_sellerFactory = $sellerFactory;
        $this->sellercustom = $sellercustom;
        $this->_marketplaceHelper = $marketplaceHelper;
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
        return __('Seller Information');
    }
 
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Seller Information');
    }
 
    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            if ($this->getIsSeller() == 1) {
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
        }
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Seller Information')]);
        $entityId  = '';
        if ($this->getsellerEntityId() != '') {
            $entityId = $this->getsellerEntityId();
            $model = $this->_initSeller($entityId);
        
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
        }
        $fieldset->addField(
            'store_name',
            'text',
            [
                'name' => 'seller[store_name]',
                'label' => __('Store Name'),
                'title' => __('Store Name'),
                'required' => true,
                'data-form-part' => $this->getData('target_form'),
                'class' => 'seller required-entry'
            ]
        );
        $fieldset->addField(
            'status_id',
            'select',
            [
                'label' => __('Store Status'),
                'title' => __('Store Status'),
                'name' => 'seller[status_id]',
                'required' => true,
                'class' => 'seller required-entry',
                'data-form-part' => $this->getData('target_form'),
                'options' => ['1' => __('Enabled'), '0' => __('Disabled')]
            ]
        );
         $fieldset->addField(
             'store_logo',
             'image',
             [
                'name' => 'seller[store_logo]',
                'label' => __('Store Logo'),
                'title' => __('Store Logo'),
                'class' => 'seller',
                'data-form-part' => $this->getData('target_form'),
                'form' => 'customer_form',
                'note' => __('Allowed image types: jpg,png')
             ]
         );
        $fieldset->addField(
            'store_banner',
            'image',
            [
                'name' => 'seller[store_banner]',
                'label' => __('Store Banner'),
                'title' => __('Store Banner'),
                'class' => 'seller',
                'data-form-part' => $this->getData('target_form'),
                'form' => 'customer_form',
                'note' => __('Allowed image types: jpg,png')
              ]
        );
        $fieldset->addField(
            'store_url',
            'text',
            [
                'name' => 'seller[store_url]',
                'class' => 'seller required-entry validate-code',
                'label' => __('Store Url'),
                'title' => __('Store Url'),
                'data-form-part' => $this->getData('target_form'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'store_phone',
            'text',
            [
                'name' => 'seller[store_phone]',
                'class' => 'seller required-entry validate-number',
                'label' => __('Store Phone'),
                'title' => __('Store Phone'),
                'data-form-part' => $this->getData('target_form'),
                'required' => true
            ]
        );
        $afterElementHtml = '<p class="nm"><small>' . ' In percent per product sale ' . '</small></p>';
                $fieldset->addField(
                    'store_commission',
                    'text',
                    [
                    'name' => 'seller[store_commission]',
                    'class' => 'seller validate-number',
                    'label' => __('Store Commission'),
                    'title' => __('Store Commission'),
                    'data-form-part' => $this->getData('target_form'),
                    'after_element_html' => $afterElementHtml
                    ]
                );
        $fieldset->addField(
            'store_description',
            'editor',
            [
                'name' => 'seller[store_description]',
                'label' => __('Store Description'),
                'title' => __('Store Description'),
                'style' => 'height:15em',
                'config' => $this->_wysiwygConfig->getConfig(),
                'class' => 'seller',
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );
        
        $fieldset->addField(
            'store_address',
            'textarea',
            [
                'name' => 'seller[store_address]',
                'class' => 'seller required-entry',
                'label' => __('Store Address'),
                'title' => __('Store Address'),
                'data-form-part' => $this->getData('target_form'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'store_city',
            'text',
            [
                'name' => 'seller[store_city]',
                'class' => 'seller required-entry',
                'label' => __('Store City'),
                'title' => __('Store City'),
                'data-form-part' => $this->getData('target_form'),
                'required' => true
            ]
        );
        $optionsc=$this->_countryFactory->toOptionArray();

        $country = $fieldset->addField(
            'store_country',
            'select',
            [
                    'name' => 'seller[store_country]',
                    'label' => __('Store Country'),
                    'title' => __('Store Country'),
                    'class' => 'seller required-entry' ,
                    'values' => $optionsc,
                    'data-form-part' => $this->getData('target_form'),
                    'required' => true
                ]
        );
            
        if ($entityId != '') {
            if ($model->getData('store_region_id') != 0) {
                $states = [];
                if ($model->getData('store_country')) {
                    $states = $this->getStates->toOptionArray($model->getData('store_country'));
                }
                 $fieldset->addField(
                     'store_region_id',
                     'select',
                     [
                     'name' => 'seller[store_region_id]',
                     'label' => __('Store State'),
                     'title' => __('Store State'),
                     'class' => 'seller required-entry' ,
                     'values' =>  ['--Please Select Country--'],
                     'data-form-part' => $this->getData('target_form'),
                     'values' => $states,
                     'required' => true
                     ]
                 );
            } else {
                 $fieldset->addField(
                     'store_region',
                     'text',
                     [
                     'name' => 'seller[store_region]',
                     'label' => __('Store State'),
                     'title' => __('Store State'),
                     'class' => 'seller required-entry',
                     'data-form-part' => $this->getData('target_form'),
                     'required' => true
                     ]
                 );
            }
        } else {
             $fieldset->addField(
                 'store_region_id',
                 'select',
                 [
                    'name' => 'seller[store_region_id]',
                    'label' => __('Store State'),
                    'title' => __('Store State'),
                    'class' => 'seller required-entry' ,
                    'values' =>  ['--Please Select Country--'],
                    'data-form-part' => $this->getData('target_form'),
                    'required' => true
                 ]
             );
        }
         $country->setAfterElementHtml("   
            <script type=\"text/javascript\">
                    require([
                    'jquery',
                    'mage/template',
                    'jquery/ui',
                    'mage/translate'
                ],
                function($, mageTemplate) {
                   $(document).on('change', '.seller#store_country', function(event){
                            var country_name = $('.seller#store_country').val();
                        $.ajax({
								url: '". $this->getUrl('purpletree_marketplace/lists/regionlist')."',
                                data: 'country='+country_name,
                                type: 'post',
                                dataType: 'json',
                               showLoader:true,
                               success: function(data){
                                   if(data.success == 'true') {
                                        $('.seller#store_region_id').remove();
                                       $('.seller#store_region').css('display','none');
                                       if($('.seller#store_region_id').length) {
                                       } else {
                                       $('.seller#store_region').after('<select id =\"store_region_id\" data-form-part=\"customer_form\" data-ui-id=\"custom-edit-tab-become-remove-seller-fieldset-element-select-seller-region-id\" name=\"seller[store_region_id]\" title=\"Store State\" class=\"seller required-entry input-text admin__control-text required-entry _required\"></select>');
                                       }
                                       
                                    $('.seller#store_region_id').empty();
                                    $('.seller#store_region').remove();
                                    $('.seller#store_region_id').css('display','block');
                                    $('.seller#store_region_id').append(data.htmlconent);
                                   } else {
                                       $('.seller#store_region_id').css('display','none');
                                       if($('.seller#store_region').length) {
                                       }
                                       else {
                                       $('.seller#store_region_id').after('<input type=\"text\" id =\"store_region\" data-form-part=\"customer_form\" data-ui-id=\"custom-edit-tab-become-remove-seller-fieldset-element-select-seller-region-name\" name=\"seller[store_region]\" title=\"Store State\" class=\"seller required-entry input-text admin__control-text required-entry _required\" />');
                                       }
                                        $('.seller#store_region_id').remove();
                                   }
                               }
                            });
                   })
                }

            );
            </script>");
        $fieldset->addField(
            'store_zipcode',
            'text',
            [
                'name' => 'seller[store_zipcode]',
                'class' => 'seller required-entry',
                'label' => __('Store Zipcode'),
                'title' => __('Store Zipcode'),
                'data-form-part' => $this->getData('target_form'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'store_tin_number',
            'text',
            [
                'name' => 'seller[store_tin_number]',
                'class' => 'seller',
                'label' => __('Store TIN Number'),
                'title' => __('Store TIN Number'),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'store_bank_account',
            'textarea',
            [
                'name' => 'seller[store_bank_account]',
                'class' => 'seller required-entry',
                'label' => __('Store Bank Details'),
                'title' => __('Store Bank Details'),
                'data-form-part' => $this->getData('target_form'),
                'required' => true
            ]
        );
        if($this->_marketplaceHelper->isSplitPaymentMethodEnabled("stripe")) {
            $fieldset->addField(
                'stripe_account_id',
                'text',
                [
                    'name' => 'seller[stripe_account_id]',
                    'class' => 'seller required-entry',
                    'label' => __('Stripe Account ID'),
                    'title' => __('Stripe Account ID'),
                    'data-form-part' => $this->getData('target_form'),
                    'required' => true
                ]
            );
        }
        if($this->_marketplaceHelper->isSplitPaymentMethodEnabled("paypal")) {
            
        }
        
        $fieldset->addField(
            'store_shipping_policy',
            'editor',
            [
                'name' => 'seller[store_shipping_policy]',
                'label' => __('Store Shipping Policy'),
                'class' => 'seller',
                'title' => __('Store Shipping Policy'),
                'style' => 'height:15em',
                'config' => $this->_wysiwygConfig->getConfig(),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'store_return_policy',
            'editor',
            [
                'name' => 'seller[store_return_policy]',
                    'class' => 'seller',
                'label' => __('Store Return Policy'),
                'title' => __('Store Return Policy'),
                'style' => 'height:15em',
                'config' => $this->_wysiwygConfig->getConfig(),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'store_meta_keywords',
            'textarea',
            [
                'name' => 'seller[store_meta_keywords]',
                    'class' => 'seller',
                'label' => __('Store Meta Keywords'),
                'title' => __('Store Meta Keywords'),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'store_meta_descriptions',
            'textarea',
            [
                'name' => 'seller[store_meta_descriptions]',
                'label' => __('Store Meta Descriptions'),
                    'class' => 'seller',
                'title' => __('Store Meta Descriptions'),
                'data-form-part' => $this->getData('target_form'),
                'required' => false
            ]
        );
        
        if ($entityId != '') {
            $form->setValues($model->getData());
            $form->setUseContainer(true);
        }
        $this->setForm($form);
        return $this;
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
     * Prepare the layout.
     *
     * @return $this
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        $html .= $this->getLayout()->createBlock(
            'Purpletree\Marketplace\Block\Adminhtml\Customer\Edit\EdditionalBlock'
        )->toHtml();

        return $html;
    }
}
