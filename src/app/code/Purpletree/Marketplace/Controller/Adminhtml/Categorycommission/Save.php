<?php
/**
 * Purpletree_Marketplace Save
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
 
namespace Purpletree\Marketplace\Controller\Adminhtml\Categorycommission;

class Save extends \Purpletree\Marketplace\Controller\Adminhtml\Categorycommission
{
    /**
     * constructor
     *
     * @param \Purpletree\Marketplace\Model\CategorycommissionFactory $categorycommissionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public $_context;

    public function __construct(
        \Purpletree\Marketplace\Model\CategorycommissionFactory $categorycommissionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_context = $context;
        parent::__construct($categorycommissionFactory, $registry, $context);
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Marketplace::commission');
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('categorycommission');
        $resultRedirect = $this->resultRedirectFactory->create();
        
        if ($data) {
            $categorycommission = $this->_initCategorycommission();
            $categorycommission->setData($data);
            $this->_eventManager->dispatch(
                'purpletree_marketplace_categorycommission_prepare_save',
                [
                    'categorycommission' => $categorycommission,
                    'request' => $this->getRequest()
                ]
            );
            try {
                try {
                    $categorycommission->save();
                    $this->messageManager->addSuccess(__('The Commission has been saved.'));
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the Commission.'));
                }
               
                $this->_context->getSession()->setPurpletreeMarketplaceCategorycommissionData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'purpletree_marketplace/categorycommission/edit',
                        [
                            'entity_id' => $categorycommission->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('purpletree_marketplace/categorycommission/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Commission.'));
            }
            $this->_getSession()->setPurpletreeMarketplaceCategorycommissionData($data);
            $resultRedirect->setPath(
                'purpletree_marketplace/categorycommission/edit',
                [
                    'entity_id' => $categorycommission->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
                
        $resultRedirect->setPath('purpletree_marketplace/categorycommission/');
        return $resultRedirect;
    }
}
