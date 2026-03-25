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

namespace Webkul\StorePickup\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Framework\Phrase;
use Magento\Ui\Component\Modal;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Webkul\StorePickup\Api\StoresRepositoryInterface;
use Webkul\StorePickup\Model\StoresProductsRelationFactory;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Webkul\StorePickup\Helper\Data as StorePickupHelper;
use \Magento\Framework\App\Request\Http;
use Magento\Catalog\Model\ProductFactory;

class ProductAssign extends AbstractModifier
{
    /**
     * data scope for form
     */
    const DATA_SCOPE = '';

    /**
     * data scope for store pickup data
     */
    const DATA_SCOPE_STORE_PICKUP = 'storepickup';

    /**
     * data scope for group of store pickup section
     */
    const GROUP_STORE_PICKUP = 'storepickup';

    /**
     * @var string
     * It is blank because we are store pickup section at the top
     */
    private static $previousGroup = '';

    /**
     * @var int
     */
    private static $sortOrder = 1;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var StoresRepositoryInterface
     */
    protected $storesRepository;

    /**
     * @var StoresProductsRelationFactory
     */
    protected $storesProductsRelationFactory;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * Constructor
     * @param LocatorInterface              $locator
     * @param UrlInterface                  $urlBuilder
     * @param StoresRepositoryInterface     $storesRepository
     * @param StoresProductsRelationFactory $storesProductsRelationFactory
     * @param StorePickupHelper             $dataHelper
     * @param Http                          $request
     * @param ProductFactory                $productFactory
     * @param string                        $scopeName
     * @param string                        $scopePrefix
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        StoresRepositoryInterface $storesRepository,
        StoresProductsRelationFactory $storesProductsRelationFactory,
        StorePickupHelper $dataHelper,
        Http $request,
        ProductFactory $productFactory,
        $scopeName = '',
        $scopePrefix = ''
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->storesRepository = $storesRepository;
        $this->storesProductsRelationFactory = $storesProductsRelationFactory;
        $this->dataHelper = $dataHelper;
        $this->scopeName = $scopeName;
        $this->scopePrefix = $scopePrefix;
        $this->request = $request;
        $this->productFactory = $productFactory;
    }

    /**
     * is need to hide
     * @param void
     * @return boolean
     */
    private function isNeedToHide()
    {
        $type = $this->request->getParam('type');
        $id = $this->request->getParam('id');

        if ($type && ($type != 'simple')) {
            return true;
        } elseif ($id) {
            $product = $this->productFactory->create()->load($id);
            $type = $product->getTypeId();
            if ($type && ($type != 'simple')) {
                return true;
            }
        }

        return false;
    }

    /**
     * modify meta
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->isNeedToHide()) {
            $part1 = 'Assign multiple pickup stores to product.';
            $part2 = 'Customer can go to these pickup stores to pickup his/her order.';
            $content = __('%1 %2', [$part1, $part2]);

            $meta = array_replace_recursive(
                $meta,
                [
                    static::GROUP_STORE_PICKUP => [
                        'children' => [
                            'button_set' => $this->getButtonSet(
                                $content,
                                __('Assign Pickup Stores'),
                                $this->scopePrefix . static::DATA_SCOPE_STORE_PICKUP
                            ),
                            'modal' => $this->getGenericModal(
                                __('Assign Pickup Stores'),
                                $this->scopePrefix . static::DATA_SCOPE_STORE_PICKUP
                            ),
                            static::DATA_SCOPE_STORE_PICKUP => $this->getGrid(
                                $this->scopePrefix . static::DATA_SCOPE_STORE_PICKUP
                            )
                        ],
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __('Assign Pickup Stores'),
                                    'collapsible' => true,
                                    'opened' => true,
                                    'componentType' => Fieldset::NAME,
                                    'dataScope' => static::DATA_SCOPE,
                                    'sortOrder' => $this->getNextGroupSortOrder(
                                        $meta,
                                        self::$previousGroup,
                                        self::$sortOrder
                                    )
                                ],
                            ],
                        ]
                    ]
                ]
            );
        }

        return $meta;
    }

    /**
     * Retrieve button set
     * @param Phrase $content
     * @param Phrase $buttonTitle
     * @param string $scope
     * @return array
     */
    protected function getButtonSet(Phrase $content, Phrase $buttonTitle, $scope)
    {
        $modalTarget = $this->scopeName . '.' . $scope . '.modal';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => $content,
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'button_' . $scope => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $modalTarget . '.' . $scope . '_stores_assign_listing',
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title' => $buttonTitle,
                                'provider' => null,
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Prepares config for modal slide-out panel
     * @param Phrase $title
     * @param string $scope
     * @return array
     */
    protected function getGenericModal(Phrase $title, $scope)
    {
        $listingTarget = $scope . '_stores_assign_listing';

        $modal = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'options' => [
                            'title' => $title,
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ],
                                [
                                    'text' => __('Add Selected Stores'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $listingTarget,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $listingTarget => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => $listingTarget,
                                'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                                'selectionsProvider' => $listingTarget . '.' . $listingTarget . '.stores_columns.ids',
                                'ns' => $listingTarget,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.current_product_id',
                                    'storeId' => '${ $.provider }:data.product.current_store_id',
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.current_product_id',
                                    'storeId' => '${ $.externalProvider }:params.current_store_id',
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $modal;
    }

    /**
     * Retrieve grid
     * @param string $scope
     * @return array
     */
    protected function getGrid($scope)
    {
        $dataProvider = $scope . '_stores_assign_listing';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $dataProvider,
                        'map' => [
                            'id' => 'entity_id',
                            'name' => 'name',
                            'is_enabled' => 'is_enabled',
                            'latitude' => 'latitude',
                            'longitude' => 'longitude'
                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }'
                        ],
                        'sortOrder' => 2,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => ''
                            ]
                        ]
                    ],
                    'children' => $this->fillMeta()
                ],
            ],
        ];
    }

    /**
     * Retrieve meta column
     * @return array
     */
    protected function fillMeta()
    {
        return [
            'id' => $this->getTextColumn('id', false, __('ID'), 0),
            'name' => $this->getTextColumn('name', false, __('Name'), 20),
            'is_enabled' => $this->getTextColumn('is_enabled', false, __('Enabled'), 30),
            'latitude' => $this->getTextColumn('latitude', false, __('Latitude'), 40),
            'longitude' => $this->getTextColumn('longitude', false, __('Longitude'), 50),
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 70,
                            'fit' => true,
                        ],
                    ],
                ],
            ],
            'position' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Number::NAME,
                            'formElement' => Input::NAME,
                            'componentType' => Field::NAME,
                            'dataScope' => 'position',
                            'sortOrder' => 80,
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Retrieve text column structure
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];

        return $column;
    }

    /**
     * modifyData
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();

        if (!$productId) {
            return $data;
        }

        $assignedPickupStores = $this->getAssignedPickupStores($productId);
        if (!empty($assignedPickupStores)) {
            foreach ($assignedPickupStores as $store) {
                $data[$productId]['links'][self::DATA_SCOPE_STORE_PICKUP][] =
                    $this->getPickupStoreDetail($store);
            }
        }

        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $productId;
        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_store_id'] = $this->locator->getStore()->getId();

        return $data;
    }

    /**
     * get assigned pickup stores
     * @param array $store
     * @return array
     */
    private function getAssignedPickupStores($productId)
    {
        $data = [];
        $collection = $this->storesProductsRelationFactory->create()->getCollection()
            ->addFieldToFilter('product_id', ['eq' => $productId]);

        if ($collection->getSize()) {
            $data = $collection->getData();
        }

        return $data;
    }

    /**
     * get pikup store detail
     * @param array $store
     * @return array
     */
    private function getPickupStoreDetail($store)
    {
        $pickupStore = $this->storesRepository->get($store['store_id']);

        return [
            'id' => $pickupStore->getId(),
            'name' => $pickupStore->getName(),
            'is_enabled' => ($pickupStore->getIsEnabled() ? __('Yes') : __('No')),
            'latitude' => $pickupStore->getLatitude(),
            'longitude' => $pickupStore->getLongitude()
        ];
    }
}
