<?php
/**
 * Purpletree_Marketplace purpletree_marketplace_categorycommission_listing
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

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * @api
 * @since 101.0.0
 */
class GetCategory extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Status $status
     * @param array $components
     * @param array $data
     */
    public $category;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Category $category,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->category = $category;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @since 101.0.0
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }
        $fieldName = $this->getData('name');
        foreach ($dataSource['data']['items'] as &$item) {
            if (!empty($item[$fieldName])) {
                $name = $this->category->load($item[$fieldName])->getName();
                 $item[$fieldName] = $name.'('.$item[$fieldName].')';
            }
        }

        return $dataSource;
    }
}
