<?php
/**
* Purpletree_Marketplace Tablerate
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
namespace Purpletree\Marketplace\Model\ResourceModel\Carrier\Tablerate;

/**
 * Query builder for table rate
 */
class RateQuery
{
    /**
     * @var \Magento\Quote\Model\Quote\Address\RateRequest
     */
    private $request;

    /**
     * RateQuery constructor.
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     */
    public function __construct(
        \Magento\Quote\Model\Quote\Address\RateRequest $request
    ) {
        $this->request = $request;
    }

    /**
     * Prepare select
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    public function prepareSelect(\Magento\Framework\DB\Select $select)
    {
        $select->where(
            'website_id = :website_id'
        )->order(
            ['dest_country_id DESC', 'dest_region_id DESC', 'dest_zip DESC', 'condition_value DESC']
        )->limit(
            1
        );

        // Render destination condition
        $orWhere = '(' . implode(
            ') OR (',
            [
                "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = :postcode",
                "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = :postcode_prefix",
                "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = ''",

                // Handle asterisk in dest_zip field
                "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = '*'",
                "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = '*'",
                "dest_country_id = '0' AND dest_region_id = :region_id AND dest_zip = '*'",
                "dest_country_id = '0' AND dest_region_id = 0 AND dest_zip = '*'",
                "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = ''",
                "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = :postcode",
                "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = :postcode_prefix"
            ]
        ) . ')';
        $select->where($orWhere);

        // Render condition by condition name
        if (is_array($this->request->getConditionName())) {
            $orWhere = [];
            foreach (range(0, count($this->request->getConditionName())) as $conditionNumber) {
                $bindNameKey = sprintf(':condition_name_%d', $conditionNumber);
                $bindValueKey = sprintf(':condition_value_%d', $conditionNumber);
                $orWhere[] = "(condition_name = {$bindNameKey} AND condition_value <= {$bindValueKey})";
            }

            if ($orWhere) {
                $select->where(implode(' OR ', $orWhere));
            }
        } else {
            $select->where('condition_name = :condition_name');
            $select->where('condition_value <= :condition_value');
        }
            $select->where('seller_id = :seller_id');
        return $select;
    }

    /**
     * Returns query bindings
     *
     * @return array
     */
    public function getBindings($sellerid = 0)
    {
        $bind = [
            ':website_id' => (int)$this->request->getWebsiteId(),
            ':country_id' => $this->request->getDestCountryId(),
            ':region_id' => (int)$this->request->getDestRegionId(),
            ':postcode' => $this->request->getDestPostcode(),
            ':seller_id' => $sellerid,
            ':postcode_prefix' => $this->getDestPostcodePrefix()
        ];

        // Render condition by condition name
        if (is_array($this->request->getConditionName())) {
            $i = 0;
            foreach ($this->request->getConditionName() as $conditionName) {
                $bindNameKey = sprintf(':condition_name_%d', $i);
                $bindValueKey = sprintf(':condition_value_%d', $i);
                $bind[$bindNameKey] = $conditionName;
                $bind[$bindValueKey] = $this->request->getData($conditionName);
                $i++;
            }
        } else {
            $bind[':condition_name'] = $this->request->getConditionName();
            $bind[':condition_value'] = round($this->request->getData($this->request->getConditionName()), 4);
        }

        return $bind;
    }

    /**
     * Returns rate request
     *
     * @return \Magento\Quote\Model\Quote\Address\RateRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the entire postcode if it contains no dash or the part of it prior to the dash in the other case
     *
     * @return string
     */
    private function getDestPostcodePrefix()
    {
        if (!preg_match("/^(.+)-(.+)$/", $this->request->getDestPostcode(), $zipParts)) {
            return $this->request->getDestPostcode();
        }

        return $zipParts[1];
    }
}
