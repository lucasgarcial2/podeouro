<?php
/**
 * Purpletree_Marketplace Edit
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

namespace Purpletree\Marketplace\Block\Adminhtml\Payments;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * constructor
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public $_objectId;
    public $_blockGroup;
    public $_controller;
    public $_coreRegistry;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'Purpletree_Marketplace';
        $this->_controller = 'adminhtml_payments';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Payments'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete Payments'));
    }

    /**
     * Retrieve text for header element depending on loaded Payments
     *
     * @return string
     */
    public function getHeaderText()
    {
        $payments = $this->_coreRegistry->registry('purpletree_marketplace_payments');
        if ($payments->getId()) {
            return __("Edit Payments '%1'", $this->escapeHtml($payments->getTitle()));
        }
        return __('New Payments');
    }
}
