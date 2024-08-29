<?php
/**
 * Sample_News extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Sample
 * @package   Sample_News
 * @copyright 2016 Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru
 */
namespace Purpletree\Marketplace\Controller\Adminhtml\Sellerorder;

use Purpletree\Marketplace\Model\Sellerorder;

class Massactioncalculatecommission extends AbstractMassAction
{
    /**
     * @var bool
     */
    protected $isActive = false;

    /**
     * @param Sellerorder $Sellerorder
     * @return $this
     */
    protected function massAction(Sellerorder $Sellerorder)
    {
        return $Sellerorder;
    }
}