<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Purpletree\Marketplace\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class ProductActions
 */
class Actions extends Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public $urlBuilder;
    public $sellercustom;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $sellercustom,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->sellercustom = $sellercustom;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');
        
            foreach ($dataSource['data']['items'] as &$item) {
                $sellerData = $this->sellercustom->getStoreDetails($item['entity_id']);
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'customer/*/edit',
                        ['id' => $item['entity_id'], 'store' => $storeId]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
                if (isset($sellerData['status_id'])) {
                    if ($sellerData['status_id'] == 0) {
                        $item[$this->getData('name')]['delete'] = [
                            'href' => $this->urlBuilder->getUrl(
                                'purpletree_marketplace/SellerListing/ApproveSeller',
                                ['id' => $item['entity_id'], 'store' => $storeId]
                            ),
                            'label' => __('Approve this Seller'),
                            'hidden' => false,
                        ];
                    } elseif ($sellerData['status_id'] == 1) {
                        $item[$this->getData('name')]['delete'] = [
                            'href' => $this->urlBuilder->getUrl(
                                'purpletree_marketplace/SellerListing/DisapproveSeller',
                                ['id' => $item['entity_id'], 'store' => $storeId]
                            ),
                            'label' => __('Disapprove this Seller'),
                            'hidden' => false,
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }
}
