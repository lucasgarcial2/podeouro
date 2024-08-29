<?php
/**
 * Purpletree_Marketplace AbstractMassAction
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Software
 * @copyright   Copyright (c) 2020
 * @license     https://www.purpletreesoftware.com/license.html
 */
namespace Purpletree\Marketplace\Controller\Adminhtml\Sellerorder;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Purpletree\Marketplace\Controller\Adminhtml\Sellerorder;
use Purpletree\Marketplace\Model\Sellerorder as SellerorderModel;
use Purpletree\Marketplace\Model\ResourceModel\Sellerorder\CollectionFactory;

abstract class AbstractMassAction extends Sellerorder
{
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var string
     */
    protected $successMessage;
    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @param Registry $registry
     * @param SellerorderRepositoryInterface $SellerorderRepository
     * @param PageFactory $resultPageFactory
     * @param Date $dateFilter
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param $successMessage
     * @param $errorMessage
     */
    protected $dataHelper;
    protected $_sellerorder;
    
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        Date $dateFilter,
        Context $context,
        Filter $filter,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorder $sellerordermodel,
        CollectionFactory $collectionFactory
    ) {
        $this->filter                 = $filter;
        $this->collectionFactory      = $collectionFactory;
        $this->dataHelper             = $dataHelper;
        $this->_sellerorder           = $sellerordermodel;
        parent::__construct($registry, $resultPageFactory, $dateFilter, $context);
    }

    /**
     * @param SellerorderModel $Sellerorder
     * @return mixed
     */
    protected abstract function massAction(SellerorderModel $Sellerorder);

    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
   // echo "a"; die;
        try { 
            $collection = $this->filter->getCollection($this->collectionFactory->create());
           // $collectionSize = 0;
           //$entity_ids = array();
            foreach ($collection as $Sellerorder) {
            $sellerId = $Sellerorder->getSellerId();
             $orderid = $Sellerorder->getOrderId();
              $entity_ids         = $this->_sellerorder->getEntityIdfromOrderId($sellerId, $orderid);
               $this->dataHelper->caclulateCommission($entity_ids,$orderid);
            }
           // if($collectionSize) {
            $this->messageManager->addSuccessMessage(__('Commission Calculatted Successfully  for some records'));
           // } else {
           //  $this->messageManager->addErrorMessage(__('No Orders to calculate Commission found'));
           // }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __($this->errorMessage));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('*/orderlisting/index');
        return $redirectResult;
    }
}