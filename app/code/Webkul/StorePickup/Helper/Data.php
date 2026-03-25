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

namespace Webkul\StorePickup\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Symfony\Component\Process\PhpExecutableFinder;
use Magento\Framework\Shell;

class Data extends AbstractHelper
{
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var \Webkul\StorePickup\Model\StoresFactory
     */
    protected $storesFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $cartFactory;

    /**
     * @var \Webkul\StorePickup\Model\StoresHolidaysFactory
     */
    protected $storesHolidaysFactory;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Indexer\Model\Indexer\CollectionFactory
     */
    protected $indexerCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\ProductFactory
     */
    protected $productIndexer;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $jsonHelper,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Webkul\StorePickup\Model\StoresFactory $storesFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Webkul\StorePickup\Model\StoresHolidaysFactory $storesHolidaysFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory,
        \Magento\Catalog\Model\Indexer\Category\ProductFactory $productIndexer,
        PhpExecutableFinder $phpExecutableFinder,
        Shell $shellBackground
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->jsonHelper = $jsonHelper;
        $this->curl = $curl;
        $this->storesFactory = $storesFactory;
        $this->timezone = $timezone;
        $this->orderFactory = $orderFactory;
        $this->cartFactory = $cartFactory;
        $this->storesHolidaysFactory = $storesHolidaysFactory;
        $this->countryFactory = $countryFactory;
        $this->indexerCollectionFactory = $indexerCollectionFactory;
        $this->productIndexer = $productIndexer;
        $this->phpExecutableFinder = $phpExecutableFinder;
        $this->shellBackground = $shellBackground;
        parent::__construct($context);
    }

    /**
     * Get Config Value
     * @param string
     * @return string|int|float|null
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Base URL without Storecode
     * @param void
     * @return string
     */
    public function getBaseURLWithoutStoreCode()
    {
        if ($this->storeManager->getStore()->isCurrentlySecure()) {
            return $this->scopeConfig->getValue(
                'web/secure/base_url',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } else {
            return $this->scopeConfig->getValue(
                'web/unsecure/base_url',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
    }

    /**
     * get google api key
     * @param void
     * @return string|null
     */
    public function getGoogleApiKey()
    {
        return $this->getConfigValue('cataloginventory/source_selection_distance_based_google/api_key');
    }

    /**
     * get distance provider
     * @param void
     * @return string|null
     */
    public function getDistanceProvider()
    {
        return $this->getConfigValue('cataloginventory/source_selection_distance_based/provider');
    }

    /**
     * is google map enabled
     * @param void
     * @return boolean
     */
    public function isGoogleMapEnabled()
    {
        return ($this->getDistanceProvider() == 'google') ? true : false;
    }

    /**
     * is pickup stores page enabled
     * @param void
     * @return boolean
     */
    public function isPickupStoresPageEnabled()
    {
        return $this->getConfigValue('carriers/storepickup/show_all_pickup_stores_page') &&
            $this->isGoogleMapEnabled();
    }

    /**
     * is pickup stores page's address search enabled
     * @param void
     * @return boolean
     */
    public function isAddressSearchEnabled()
    {
        return $this->getConfigValue('carriers/storepickup/enable_address_search') &&
            $this->isPickupStoresPageEnabled();
    }

    /**
     * Check storepickup module active
     * @param void
     * @return boolean
     */
    public function isModuleEnabled()
    {
        return $this->getConfigValue('carriers/storepickup/active');
    }

    /**
     * Check is scheduling emails enabled
     * @param void
     * @return boolean
     */
    public function isSchedulingEmailsEnabled()
    {
        return $this->getConfigValue('carriers/storepickup/enable_scheduling_emails');
    }

    /**
     * get within range in kilometers
     * @param void
     * @return int
     */
    public function getWithinRange()
    {
        return $this->getConfigValue('carriers/storepickup/within_range');
    }

    /**
     * check server ssl
     * @param void
     * @return boolean
     */
    public function isSecureServer()
    {
        return $this->storeManager->getStore()->isCurrentlySecure();
    }

    /**
     * json encode
     * @param array $arr
     * @return string
     */
    public function serialize($arr)
    {
        return $this->jsonHelper->serialize($arr);
    }

    /**
     * json decode
     * @param string $str
     * @return array
     */
    public function unserialize($str)
    {
        return $this->jsonHelper->unserialize($str);
    }

    /**
     * get distance between two locations
     * @param string $sourceLat
     * @param string $sourceLng
     * @param string $destLat
     * @param string $destLng
     * @return string
     */
    private function getDistanceBetweenTwoLocations($sourceLat, $sourceLng, $destLat, $destLng)
    {
        $baseUrl = $this->getBaseURLWithoutStoreCode();
        $url = $baseUrl.
        "rest/V1/storepickup/get-distance".
        "?source[lat]=".$sourceLat.
        "&source[lng]=".$sourceLng.
        "&destination[lat]=".$destLat.
        "&destination[lng]=".$destLng;

        $this->curl->get($url);
        return $this->unserialize($this->curl->getBody());
    }

    /**
     * get latitude longitude from address
     * @param array $address
     * @return array
     */
    private function getLatLngFromAddress($address)
    {
        $baseUrl = $this->getBaseURLWithoutStoreCode();
        if (!$this->isGoogleMapEnabled() && $address['country'] == 'CA') {
            $address['postcode'] = substr(trim($address['postcode']), 0, 3);
        }

        $url = $baseUrl.
        "rest/V1/storepickup/get-latlng-from-address".
        "?address[country]=".urlencode($address['country']).
        "&address[postcode]=".urlencode($address['postcode']).
        "&address[street]=".urlencode($address['street']).
        "&address[region]=".urlencode($address['region']).
        "&address[city]=".urlencode($address['city']);

        $this->curl->get($url);
        return $this->unserialize($this->curl->getBody());
    }

    /**
     * get all pickup stores
     * @return mixed
     */
    public function getAllPickupStores()
    {
        $collection = $this->storesFactory->create()->getCollection()
            ->addFieldToFilter('is_enabled', ['eq' => 1]);

        if ($collection->getSize()) {
            return $collection;
        }

        return false;
    }

    /**
     * get cart products
     * @return array
     */
    public function getCartProducts()
    {
        $productIds = [];
        $items = $this->cartFactory->create()->getQuote()
            ->getAllItems();

        foreach ($items as $item) {
            if ($item->getProduct()->getTypeId() == 'simple') {
                if (!in_array($item->getProductId(), $productIds)) {
                    $productIds[] = $item->getProductId();
                }
            }
        }

        return $productIds;
    }

    /**
     * get nearest pickup stores
     * @param $address
     * @return array
     */
    public function getNearestPickupStores($address)
    {
        $nearestStores = [];
        $response = $this->getLatLngFromAddress($address);

        if (!empty($response['lat']) && !empty($response['lng'])) {
            $pickupStores = $this->getAllPickupStores();
            $cartProducts = $this->getCartProducts();
            foreach ($pickupStores as $pickupStore) {
                if ($this->isAnyAssignedProductInCart($pickupStore, $cartProducts)) {
                    if ($pickupStore->getStoresDetails() && !empty($address['country'])) {
                        $result = $this->getFindPickupStores(
                            $pickupStore,
                            $address,
                            $response['lat'],
                            $response['lng']
                        );

                        if ($result) {
                            $nearestStores[] = $result;
                        }
                    }
                }
            }
        }

        return $nearestStores;
    }

    /**
     * is Any Assigned Product In Cart
     * @param object $pickupStore
     * @param array $cartProducts
     * @return boolean
     */
    private function isAnyAssignedProductInCart($pickupStore, $cartProducts)
    {
        $flag = false;
        $assignedProducts = $pickupStore->getAssignedProducts();
        if ($assignedProducts) {
            $assignedProducts = array_column($assignedProducts, 'entity_id');
            foreach ($cartProducts as $cartProduct) {
                $flag = true;
                if (!in_array($cartProduct, $assignedProducts)) {
                    return false;
                }
            }

            if ($flag) {
                return true;
            }
        }

        return false;
    }

    /**
     * get find pickup stores
     * @param object $pickupStore
     * @param array $address
     * @param string $originLat
     * @param string $originLng
     * @return array
     */
    private function getFindPickupStores($pickupStore, $address, $originLat, $originLng)
    {
        if ($pickupStore->getStoresDetails()['country_id'] == $address['country']) {
            $distance = $this->getDistanceBetweenTwoLocations(
                $originLat,
                $originLng,
                $pickupStore->getLatitude(),
                $pickupStore->getLongitude()
            );

            if ($distance <= ($this->getWithinRange() * 1000)) {
                return [
                    'store' => $pickupStore,
                    'distance' => $distance,
                    'origin_latitude' => $originLat,
                    'origin_longitude' => $originLng,
                    'dest_latitude' => $pickupStore->getLatitude(),
                    'dest_longitude' => $pickupStore->getLongitude()
                ];
            }
        }

        return false;
    }

    /**
     * get nearest pickup stores by range
     * @param string $originLat
     * @param string $originLng
     * @param int $range
     * @return array
     */
    public function getNearestPickupStoresByRange($originLat, $originLng, $range)
    {
        $nearestStores = [];
        if (!empty($originLat) && !empty($originLng)) {
            $pickupStores = $this->getAllPickupStores();
            foreach ($pickupStores as $pickupStore) {
                if ($pickupStore->getStoresDetails()) {
                    $result = $this->getFindPickupStoresByRange(
                        $pickupStore,
                        $range,
                        $originLat,
                        $originLng
                    );

                    if ($result) {
                        $nearestStores[] = $result;
                    }
                }
            }
        }

        return $nearestStores;
    }

    /**
     * get Find Pickup Stores By Range
     * @param object $pickupStore
     * @param float $range
     * @param string $originLat
     * @param string $originLng
     * @return array
     */
    public function getFindPickupStoresByRange($pickupStore, $range, $originLat, $originLng)
    {
        $distance = $this->getDistanceBetweenTwoLocations(
            $originLat,
            $originLng,
            $pickupStore->getLatitude(),
            $pickupStore->getLongitude()
        );

        if ($distance <= ($range * 1000)) {
            return [
                'store' => $pickupStore,
                'distance' => $distance,
                'dest_latitude' => $pickupStore->getLatitude(),
                'dest_longitude' => $pickupStore->getLongitude(),
                'origin_latitude' => $originLat,
                'origin_longitude' => $originLng,
                'is_open' => $this->isStoreOpen($pickupStore)
            ];
        }

        return false;
    }

    /**
     * is store open
     * @param $pickupStore
     * @return boolean
     */
    public function isStoreOpen($pickupStore)
    {
        if ($pickupStore->getStoresTimings()) {
            $storesTimings = $pickupStore->getStoresTimings();
            if ($storesTimings['is_urgent_close']) {
                return false;
            }

            if ($storesTimings['timing_type'] == 0) {
                return true;
            }

            if ($storesTimings['timing_type'] == 1) {
                return $this->isStoreOpenForSameTimings($storesTimings['timings']);
            }

            if ($storesTimings['timing_type'] == 2) {
                return $this->isStoreOpenForDifferentTimings($storesTimings['timings']);
            }
        }

        return false;
    }

    /**
     * is Store Open For Same Timings
     * @param $timings
     * @return boolean
     */
    private function isStoreOpenForSameTimings($timings)
    {
        $date = $this->timezone->date();
        $day = $date->format('N') - 1;

        // check is open on current day
        if ($timings['same-checkbox-day-'.$day]) {
            $currentTime = strtotime($date->format('Y-m-d h:i:s a'));
            $startTime = strtotime($timings['time-start']);
            $endTime = strtotime($timings['time-end']);

            if ($currentTime >= $startTime && $currentTime <= $endTime) {
                return true;
            }
        }

        return false;
    }

    /**
     * is Store Open For Different Timings
     * @param array $timings
     * @return boolean
     */
    private function isStoreOpenForDifferentTimings($timings)
    {
        $date = $this->timezone->date();
        $day = $date->format('N') - 1;

        // check is open on current day
        if ($timings['checkbox-day-'.$day]) {
            $currentTime = strtotime($date->format('Y-m-d h:i:s a'));
            $startTime = strtotime($timings['time-start-'.$day]);
            $endTime = strtotime($timings['time-end-'.$day]);

            if ($currentTime >= $startTime && $currentTime <= $endTime) {
                return true;
            }
        }

        return false;
    }

    /**
     * is pickup store order
     * @param int $orderId
     * @return boolean
     */
    public function isPickupStoreOrder($orderId)
    {
        $order = $this->getOrder($orderId);
        if ($order->getPickupStore()) {
            try {
                $store = $this->getPickupStore($order->getPickupStore());
                if (!empty($store->getData())) {
                    return true;
                }
            } catch (\Exception $ex) {
                return false;
            }
        }

        return false;
    }

    /**
     * get order
     * @param $orderId
     * @return object
     */
    public function getOrder($orderId)
    {
        return $this->orderFactory->create()->load($orderId);
    }

    /**
     * get pickup store
     * @param int $pickupStoreId
     * @return object
     */
    public function getPickupStore($pickupStoreId)
    {
        return $this->storesFactory->create()
            ->load($pickupStoreId);
    }

    /**
     * get store holidays with name
     * @param object $pickupStore
     * @return array
     */
    public function getStoreHolidaysWithName($pickupStore)
    {
        $holidaysData = $pickupStore->getStoresHolidays();
        if (!empty($holidaysData['holidays'])) {
            $collection = $this->storesHolidaysFactory->create()->getCollection()
                ->addFieldToFilter('entity_id', ['in' => $holidaysData['holidays']])
                ->addFieldToFilter('status', ['eq' => true]);

            if ($collection->getSize()) {
                return $this->getHolidaysFromCollection($collection->getData());
            }
        }

        return false;
    }

    /**
     * get store weekly holidays
     * @param object $pickupStore
     * @return array
     */
    public function getStoreWeeklyHolidays($pickupStore)
    {
        $timingsData = $pickupStore->getStoresTimings();

        if (!empty($timingsData)) {
            if ($timingsData['timing_type'] == 0) {
                return $this->serialize([]);
            } elseif ($timingsData['timing_type'] == 1) {
                return $this->serialize(
                    $this->getWeeklyHolidaysForSameTiming(
                        $timingsData['timings']
                    )
                );
            } else {
                return $this->serialize(
                    $this->getWeeklyHolidaysForDifferentTiming(
                        $timingsData['timings']
                    )
                );
            }
        }
    }

    /**
     * get Weekly Holidays For Same Timing
     * @param array $timings
     * @return array
     */
    private function getWeeklyHolidaysForSameTiming($timings)
    {
        $temp = [];
        for ($day = 0; $day < 7; $day++) {
            if ($timings['same-checkbox-day-'.$day] == 0) {
                $temp[] = $this->getDayFromCode($day);
            }
        }

        return $temp;
    }

    /**
     * get Weekly Holidays For Different Timing
     * @param array $timings
     * @return array
     */
    private function getWeeklyHolidaysForDifferentTiming($timings)
    {
        $temp = [];
        for ($day = 0; $day < 7; $day++) {
            if ($timings['checkbox-day-'.$day] == 0) {
                $temp[] = $this->getDayFromCode($day);
            }
        }

        return $temp;
    }

    /**
     * get day from code
     * @param string $day
     * @return int
     */
    private function getDayFromCode($day)
    {
        $temp = '';
        switch ($day) {
            case 0:
                $temp = 'Mon';
                break;
            case 1:
                $temp = 'Tue';
                break;
            case 2:
                $temp = 'Wed';
                break;
            case 3:
                $temp = 'Thu';
                break;
            case 4:
                $temp = 'Fri';
                break;
            case 5:
                $temp = 'Sat';
                break;
            case 6:
                $temp = 'Sun';
                break;
        }

        return $temp;
    }

    /**
     * get holidays from collection
     * @param array $holidays
     * @return string
     */
    private function getHolidaysFromCollection($holidays)
    {
        $data = [];
        foreach ($holidays as $holiday) {
            $date = $this->unserialize($holiday['date']);
            $temp = [];
            if ($holiday['is_single_date']) {
                $temp = [
                    'name' => $holiday['name'],
                    'date' => date('Y-m-d', strtotime($date['date']))
                ];

                $data[] = $temp;
            } else {
                $this->getDatesBetweenRange(
                    $date['from'],
                    $date['to'],
                    $holiday['name'],
                    $data
                );
            }
        }

        return $this->serialize($data);
    }

    /**
     * get dates between range
     * @param string $from
     * @param string $to
     * @param string $name
     * @param string $data
     * @return array
     */
    private function getDatesBetweenRange($from, $to, $name, &$data)
    {
        $temp = [];

        $from = strtotime($from);
        $to = strtotime($to);

        for ($curDate = $from; $curDate <= $to; $curDate += (86400)) {
            $date = date('Y-m-d', $curDate);
            $temp = [
                'name' => $name,
                'date' => $date
            ];

            $data[] = $temp;
        }
    }

    /**
     * get store timing day wise
     * @param object $pickupStore
     * @return array
     */
    public function getStoreTimingDayWise($pickupStore)
    {
        $timingsData = $pickupStore->getStoresTimings();

        if (!empty($timingsData)) {
            if ($timingsData['timing_type'] == 0) {
                return $this->serialize(['type' => 0]);
            } elseif ($timingsData['timing_type'] == 1) {
                return $this->serialize(
                    $this->getStoreTimingForSameTiming(
                        $timingsData['timings']
                    )
                );
            } else {
                return $this->serialize(
                    $this->getStoreTimingForDifferentTiming(
                        $timingsData['timings']
                    )
                );
            }
        }
    }

    /**
     * get Store Timing For Same Timing
     * @param array $timings
     * @return array
     */
    private function getStoreTimingForSameTiming($timings)
    {
        $temp = [];
        $temp = [
            'type' => 1,
            'time_start' => $timings['time-start'],
            'time_end' => $timings['time-end']
        ];

        return $temp;
    }

    /**
     * get store timing for different timing
     * @param array $timings
     * @return array
     */
    private function getStoreTimingForDifferentTiming($timings)
    {
        $data = [];
        for ($day = 0; $day < 7; $day++) {
            $data[$this->getDayFromCode($day)] = [
                'time_start' => $timings['time-start-'.$day],
                'time_end' => $timings['time-end-'.$day]
            ];
        }

        $temp = [
            'type' => 2,
            'data' => $data
        ];

        return $temp;
    }

    /**
     * get address from pickup store
     * @param object $pickupStoreId
     * @return string
     */
    public function getAddressFromPickupStore($pickupStoreId)
    {
        $pickupStore = $this->getPickupStore($pickupStoreId);
        $data = $pickupStore->getStoresDetails();
        $address = [];
        if (!empty($data['street'])) {
            $address[] = $data['street'];
        }

        if (!empty($data['city'])) {
            $address[] = $data['city'];
        }

        if (!empty($data['region'])) {
            $address[] = $data['region'];
        }

        if (!empty($data['country_id'])) {
            $country = $this->countryFactory->create()
                ->loadByCode($data['country_id']);
            if (!empty($country->getName())) {
                $address[] = $country->getName();
            }
        }

        if (!empty($data['postcode'])) {
            $address[] = $data['postcode'];
        }

        return implode(", ", $address);
    }

    /**
     * get contact of pickup store
     * @param int $pickupStoreId
     * @return array
     */
    public function getContactsOfPickupStore($pickupStoreId)
    {
        $pickupStore = $this->getPickupStore($pickupStoreId);
        $data = $pickupStore->getStoresDetails();
        return [
            'name' => empty($data['person_name']) ? 'N/A' : $data['person_name'],
            'email' => empty($data['email']) ? 'N/A' : $data['email'],
            'mobile' => empty($data['mobile']) ? 'N/A' : $data['mobile'],
            'fax' => empty($data['fax']) ? 'N/A' : $data['fax']
        ];
    }

    /**
     * do reindex product categories
     * @param void
     * @return void
     */
    public function doReIndexCategories()
    {
        $indexers = [
            'catalog_category_product',
            'catalogrule_product',
            'catalogsearch_fulltext',
            'catalog_product_category'
        ];

        $indexerCollection = $this->indexerCollectionFactory->create();

        foreach ($indexerCollection as $indexer) {
            if (in_array($indexer->getId(), $indexers)) {
                if (!$indexer->isValid()) {
                    $indexer->reindexAll();
                }
            }
        }
    }

    /**
     * do reindex products
     * @param array $ids
     * @return void
     */
    public function doReIndexProducts($ids)
    {
        $ids = array_unique($ids);
        if (!empty($ids)) {
            $indexerCollection = $this->indexerCollectionFactory->create();
            foreach ($indexerCollection as $indexer) {
                $indexer->reindexList($ids);
            }
        }
    }
}
