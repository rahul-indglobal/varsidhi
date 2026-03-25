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

namespace Webkul\StorePickup\Block\Adminhtml\StoreTiming;

use Magento\Framework\Registry;

class TimingDetails extends \Magento\Backend\Block\Template
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
    protected $_template = 'storetiming/timing_details.phtml';

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
     * get timings
     * @return mixed
     */
    public function getTimings()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $storeData = $this->coreRegistry->registry('webkul_storepickup_stores');
            if ($storeData) {
                if (!empty($storeData->getData()['stores_timings']['timings'])) {
                    return $storeData->getData()['stores_timings']['timings'];
                }
            }
        }

        return false;
    }

    /**
     * get timings type
     * @return mixed
     */
    public function getTimingType()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $storeData = $this->coreRegistry->registry('webkul_storepickup_stores');
            if ($storeData) {
                if (!empty($storeData->getData()['stores_timings']['timing_type'])) {
                    return $storeData->getData()['stores_timings']['timing_type'];
                }
            }
        }

        return 0;
    }

    /**
     * is day selected
     * @return boolean
     */
    public function isDaySelected()
    {
        $data = $this->getTimings();
        if ($data) {
            for ($i = 0; $i < 7; $i++) {
                if (isset($data['checkbox-day-'.$i]) && $data['checkbox-day-'.$i] == 1) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * json encode
     * @param array $data
     * @return string
     */
    public function encode($data)
    {
        return $this->dataHelper->serialize($data);
    }
}
