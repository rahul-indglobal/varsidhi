<?php
/**
 * LandOfCoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   LandOfCoder
 * @package    Lof_Rma
 * @copyright  Copyright (c) 2016 Venustheme (http://www.LandOfCoder.com/)
 * @license    http://www.LandOfCoder.com/LICENSE-1.0.html
 */



namespace Lof\Rma\Api\Data;

use Lof\Rma\Api;

interface ResolutionInterface extends ReturnInterface
{
    const REFUND = 'refund';
    const EXCHANGE = 'exchange';
    const CREDIT = 'credit';

    const KEY_ID = 'reason_id';
    const KEY_NAME = 'name';
    const KEY_SORT_ORDER = 'sort_order';
    const KEY_IS_ACTIVE = 'is_active';

    const RESERVED_IDS = [1, 2, 3];
}