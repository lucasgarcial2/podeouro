<?php
/**
 * Purpletree_Marketplace Disapprove
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

namespace Purpletree\Marketplace\Controller\Adminhtml\Reviews;

class Disapprove extends \Magento\Backend\App\Action
{
    /**
     * constructor
     *
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public $reviewsFactory;
    public $reviewscustom;

    public function __construct(
        \Purpletree\Marketplace\Model\ReviewsFactory $reviewsFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Reviews $reviewscustom,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->reviewsFactory = $reviewsFactory;
            $this->reviewscustom = $reviewscustom;
        parent::__construct($context);
    }
    
    /**
     * execute the action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $reviewdata = $this->reviewsFactory->create()->load($id);
                $reviewdata->setStatus(0);
                $reviewdata->save();
                $this->messageManager->addSuccess(__('Reviews has been Disapproved.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong.'));
            }
        }
        $resultRedirect->setPath('purpletree_marketplace/reviews/index/');
        return $resultRedirect;
    }
}
