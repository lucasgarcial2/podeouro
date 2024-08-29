<?php
/**
 * Purpletree_Marketplace Order
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
namespace Purpletree\Marketplace\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Status
 */
class OrderStatusAdmin extends Column
{
    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Pricing\Helper\Data
     * @param array $components
     * @param array $data
     */
    public $statusCollectionFactory;
    public $order;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
          $this->statusCollectionFactory = $statusCollectionFactory;
           $this->order                             = $order;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
                    $options = $this->statusCollectionFactory->create()->toOptionArray();
        if (isset($dataSource['data']['items'])) {
             $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (!empty($item[$fieldName])) {
                    $orderstatus = $this->order->load($item[$fieldName])->getData()['status'];
                    foreach ($options as $status) {
                        if ($status['value'] == $orderstatus) {
                            $item[$fieldName] = $status['label'];
                        }
                    }
                }
            }
        }
        return $dataSource;
    }
}
