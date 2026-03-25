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

namespace Webkul\StorePickup\Controller\Stores;

class FindNearestPickupStores extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\StorePickup\Helper\Data
     */
    protected $dataHelper;

    /**
     * Constructor
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Webkul\StorePickup\Helper\Data $datahelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Webkul\StorePickup\Helper\Data $dataHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->dataHelper = $dataHelper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute view action
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = [];
        $result['success'] = true;

        try {
            $params = $this->getRequest()->getContent();
            $params = $this->dataHelper->unserialize($params);

            $address = [
                'country' => $params['address']['country_id'],
                'postcode' => $params['address']['postcode'],
                'street' => implode(', ', $params['address']['street']),
                'region' => $params['address']['region'],
                'city' => $params['address']['city']
            ];

            $data = [];
            $pickupStores = $this->dataHelper->getNearestPickupStores($address);

            foreach ($pickupStores as $pickupStore) {
                $data[] = [
                    'distance' => $pickupStore['distance'],
                    'distance_unit' => __('m'),
                    'origin_latitude' => $pickupStore['origin_latitude'],
                    'origin_longitude' => $pickupStore['origin_longitude'],
                    'dest_latitude' => $pickupStore['dest_latitude'],
                    'dest_longitude' => $pickupStore['dest_longitude'],
                    'store' => $pickupStore['store']->getData()
                ];
            }

            array_multisort(array_column($data, "distance"), SORT_ASC, $data);
            $pos = 0;
            foreach ($data as $store) {
                if ($store['distance'] > 1000) {
                    $data[$pos]['distance'] = round($data[$pos]['distance'] / 1000, 3);
                    $data[$pos]['distance_unit'] = __('Km');
                }

                $pos++;
            }

            $result['result'] = $data;
        } catch (\Exception $ex) {
            $result['success'] = false;
        }

        return $this->getResponse()->representJson(
            $this->dataHelper->serialize($result)
        );
    }
}
