<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_StorePickup
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\StorePickup\Block\Adminhtml\StoreHoliday;

use Magento\Framework\Registry;

class HolidayDetails extends \Magento\Backend\Block\Template
{
    /**
     * @var Webkul\StorePickup\Helper\Data
     */
    private $dataHelper;

    /**
     * @var Magento\Framework\Registry $coreRegistry
     */
    private $coreRegistry;

    /**
     * @var Template
     */
    protected $_template = 'storeholiday/holiday_details.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Webkul\StorePickup\Helper\Data $dataHelper,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->dataHelper = $dataHelper;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * get form data
     * @return mixed
     */
    public function getFormData()
    {
        $id = $this->getRequest()->getParam('entity_id');

        if ($id) {
            $storeData = $this->coreRegistry->registry('webkul_storepickup_holidays');
            if ($storeData) {
                return $storeData;
            }
        }

        return false;
    }

    /**
     * json decode
     * @param array $data
     * @return array
     */
    public function unserialize($data)
    {
        return $this->dataHelper->unserialize($data);
    }
}
