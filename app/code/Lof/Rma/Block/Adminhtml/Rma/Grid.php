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



namespace Lof\Rma\Block\Adminhtml\Rma;

use Magento\Backend\Block\Widget\Grid\Extended as GridExtended;

class Grid extends GridExtended
{
    /**
     * @var array
     */
    protected $customFilters = [];

    /**
     * @var string
     */
    protected $activeTab;
    protected $searchCriteriaBuilder;
    protected $productRepository;
    protected $itemRepository;
    protected $itemCollectionFactory;

    public function __construct(
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        \Lof\Rma\Model\RmaFactory                       $rmaFactory,
        \Lof\Rma\Model\StatusFactory                        $statusFactory,
        \Lof\Rma\Helper\Help                            $Help,
        \Lof\Rma\Helper\Data                           $dataHelper,
        \Lof\Rma\Helper\Help                            $helper,
        \Magento\Backend\Block\Widget\Context           $context,
        \Magento\Backend\Helper\Data                    $backendHelper,
        \Magento\Catalog\Model\ProductRepository                    $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder                    $searchCriteriaBuilder,
        \Lof\Rma\Model\ItemRepository                    $itemRepository,
        \Lof\Rma\Model\ResourceModel\Item\CollectionFactory                    $itemCollectionFactory,
        array $data = []
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->rmaFactory          = $rmaFactory;
        $this->rmaHelper           = $Help;
        $this->statusFactory     = $statusFactory;
        $this->dataHelper          = $dataHelper;
        $this->itemRepository          = $itemRepository;
        $this->productRepository          = $productRepository;
        $this->searchCriteriaBuilder          = $searchCriteriaBuilder;
        $this->itemCollectionFactory          = $itemCollectionFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rma_grid');
        $this->setDefaultSort('updated_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Add custom filter
     *
     * @param string $field
     * @param string $filter
     * @return $this
     */
    public function addCustomFilter($field, $filter)
    {
        $this->customFilters[$field] = $filter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $collection = $this->rmaFactory->create()
            ->getCollection();
        foreach ($this->customFilters as $key => $value) {
            $collection->addFieldToFilter($key, $value);
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        
        $this->addColumn('increment_id', [
                'header'       => __('RMA #'),
                'index'        => 'increment_id',
                'filter_index' => 'main_table.increment_id',
            ]);
        $this->addColumn('order_increment_id', [
                'header'       => __('Order #'),
                'index'        => 'order_increment_id',
                'filter_index' => 'order.increment_id',
            ]);
        $this->addColumn('customer_name', [
                'header'       => __('Customer Name'),
                'index'        => ['customer_firstname', 'customer_lastname'],
                'type'         => 'concat',
                'separator'    => ' ',
                'filter_index' => new \Zend_Db_Expr("CONCAT(customer.firstname, ' ', customer.lastname)"),
            ]);

        $this->addColumn('email', [
                'header'       => __('Customer Email'),
                'index'        => 'customer_email',
                'type'         => 'text',
                'separator'    => ' ',
                'filter_index' => 'customer.email',
            ]);
         $this->addColumn('user_id', [
                'header'       => __('Owner'),
                'index'        => 'user_id',
                'filter_index' => 'main_table.user_id',
                'type'         => 'options',
                'options'      => $this->dataHelper->getAdminOptionArray(),
            ]);
        
            $this->addColumn('last_reply_name', [
                'header'         => __('Last Replier'),
                'index'          => 'last_reply_name',
                'filter_index'   => 'main_table.last_reply_name',
                'frame_callback' => [$this, '_lastReplyFormat'],
            ]);
        

            $this->addColumn('status_id', [
                'header'       => __('Status'),
                'index'        => 'status_id',
                'filter_index' => 'main_table.status_id',
                'type'         => 'options',
                'options'      => $this->statusFactory->create()->getCollection()->getOptionArray(),
            ]);
        
        $this->addColumn('created_at', [
                'header'       => __('Created Date'),
                'index'        => 'created_at',
                'filter_index' => 'main_table.created_at',
                'type'         => 'datetime',
            ]);
            $collection = $this->dataHelper->getFields();
        
      
            $this->addColumn('store_id', [
                'header'       => __('Store'),
                'index'        => 'store_id',
                'filter_index' => 'main_table.store_id',
                'type'         => 'options',
                'options'      => $this->rmaHelper->getCoreStoreOptionArray(),
            ]);
        
       
            $this->addColumn('items', [
                'header'           => __('Items'),
                'column_css_class' => 'nowrap',
                'type'             => 'text',
                'frame_callback'   => [$this, 'itemsFormat'],
                'filter_condition_callback'   => [$this, 'customFilterCondition'],
                //'filter_index' => 'main_table.items',
            ]);
       

            $this->addColumn(
                'action',
                [
                    'header'   => __('Action'),
                    'width'    => '50px',
                    'type'     => 'action',
                    'getter'   => 'getId',
                    'actions'  => [
                        [
                            'caption' => __('View'),
                            'url'     => [
                                'base' => 'rma/rma/edit',
                            ],
                            'field'   => 'id',
                        ],
                    ],
                    'filter'   => false,
                    'sortable' => false,
                ]
            );
        

        return parent::_prepareColumns();
    }

    public function customFilterCondition($collection, $column)
    {
        $val = trim($column->getFilter()->getValue());
        if(empty($val)) return $this;

        $searchCriteria = $this->searchCriteriaBuilder->addFilter('name', '%'.$val.'%','like')->create();
        $products = $this->productRepository->getList($searchCriteria)->getItems();
        $pArr = [];
        foreach ($products as $p){
            $pArr[] = $p['entity_id'];
        }
        $rmaItemArr_1 = [];
        if(count($pArr)>0){
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('product_id', $pArr,'in')->create();
            $rmaItems = $this->itemRepository->getList($searchCriteria)->getItems();
            foreach ($rmaItems as $item){
                $rmaItemArr_1[] = $item['rma_id'];
            }
        }

        $rmaItemArr_2 = [];
        $rmaItems = $this->itemCollectionFactory->create()
            ->addFieldToFilter(['reason.name','condition.name','resolution.name'],[['like'=>'%'.$val.'%'],['like'=>'%'.$val.'%'],['like'=>'%'.$val.'%']]);

        foreach ($rmaItems as $item){
            $rmaItemArr_2[] = $item['rma_id'];
        }

        $rmaItemArr = array_unique(array_merge($rmaItemArr_1,$rmaItemArr_2));
        if(count($rmaItemArr)>0){
            $collection->addFieldToFilter('rma_id',['in'=>$rmaItemArr]);
        }
        return $this;
    }

     /**
     * @param \Lof\Rma\Block\Adminhtml\Rma\Grid    $renderedValue
     * @param \Lof\Rma\Model\Rma                   $rma
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool                                      $isExport
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function itemsFormat($renderedValue, $rma, $column, $isExport)
    {
        $html = [];
        foreach ($this->dataHelper->getItems($rma) as $item) {
            $orderItem = $this->orderItemRepository->get($item->getOrderItemId());
            $s = '<b>' . $orderItem->getName() . '</b>';
            $s .= ' / ';
            $s .= $item->getReasonName() ? $item->getReasonName() : '-';
            $s .= ' /  ';
            $s .= $item->getConditionName() ? $item->getConditionName() : '-';
            $s .= ' / ';
            $s .= $item->getResolutionName() ? $item->getResolutionName() : '-';

            $html[] = $s;
        }

        return implode('<br>', $html);
        //return ( isset($html[0])?$html[0]:'');
    }
    

    public function getItemConditions($renderedValue, $rma, $column, $isExport)
    {
        $html = [];
        foreach ($this->dataHelper->getItems($rma) as $item) {
            $orderItem = $this->orderItemRepository->get($item->getOrderItemId());
            $s = '<b>' . $orderItem->getName() . '</b>';
            $s .= ' / ';
            $s .= $item->getReasonName() ? $item->getReasonName() : '-';
            $s .= ' /  ';
            $s .= $item->getConditionName() ? $item->getConditionName() : '-';
            $s .= ' / ';
            $s .= $item->getResolutionName() ? $item->getResolutionName() : '-';

            $html[] = $s;
        }

        //return implode('<br>', $html);
        return "<b>abc</b>";
    }


    /**
     * @param \Lof\Rma\Block\Adminhtml\Rma\Grid    $renderedValue
     * @param \Lof\Rma\Model\Rma                   $rma
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool                                      $isExport
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _lastReplyFormat($renderedValue, $rma, $column, $isExport)
    {
        $name = $rma->getLastReplyName();
        // If last message is automated, assign Last Reply Name value to owner, if such exists
        $lastMessage = $this->dataHelper->getLastMessage($rma);
        if ($lastMessage && !$lastMessage->getUserId() && !$lastMessage->getCustomerId()) {
            $name = '';
        }

        if (!$rma->getIsAdminRead()) {
            $name .= ' <img src="' . $this->_assetRepo->getUrl('Lof_Rma::images/fam_newspaper.gif') . '">';
        }

        return $name;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('rma_id');
        $this->getMassactionBlock()->setFormFieldName('rma_id');
        $this->getMassactionBlock()->addItem('delete', [
            'label'   => __('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => __('Are you sure?'),
        ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('rma/rma/edit', ['id' => $row->getId()]);
    }

    /**
     * Set active tab
     *
     * @param string $tabName
     * @return void
     */
    public function setActiveTab($tabName)
    {
        $this->activeTab = $tabName;
    }

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        if ($this->activeTab) {
            return parent::getGridUrl() . '?active_tab=' . $this->activeTab;
        }

        return parent::getGridUrl();
    }


    
}
