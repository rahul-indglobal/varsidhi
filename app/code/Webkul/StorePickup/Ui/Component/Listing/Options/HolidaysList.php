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

namespace Webkul\StorePickup\Ui\Component\Listing\Options;

class HolidaysList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var $storesHolidaysFactory
     */
    private $storesHolidaysFactory;

    /**
     * @var $dataHelper
     */
    private $dataHelper;

    /**
     * Constructor
     * @param Webkul\StorePickup\Model\StoresHolidaysFactory $storesHolidaysFactory
     * @param Webkul\StorePickup\Helper\Data                 $dataHelper
     */
    public function __construct(
        \Webkul\StorePickup\Model\StoresHolidaysFactory $storesHolidaysFactory,
        \Webkul\StorePickup\Helper\Data $dataHelper
    ) {
        $this->storesHolidaysFactory = $storesHolidaysFactory;
        $this->dataHelper = $dataHelper;
    }

    /**
     * to oprion array
     * @return array
     */
    public function toOptionArray()
    {
        $data = [];
        $collection = $this->storesHolidaysFactory->create()->getCollection()
            ->addFieldToFilter('status', ['eq' => true]);

        foreach ($collection as $holiday) {
            $label = "";
            $date = $this->dataHelper->unserialize($holiday->getDate());
            if ($holiday->getIsSingleDate()) {
                if (!empty($date['date'])) {
                    $label = " (".$date['date'].")";
                }
            } else {
                if (!empty($date['from']) && !empty($date['to'])) {
                    $label = " (".$date['from']." - ".$date['to'].")";
                }
            }

            $data[] = [
                'value' => $holiday->getEntityId(),
                'label' => __($holiday->getName().$label)
            ];
        }

        return $data;
    }
}
