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


namespace Lof\Rma\Block\Adminhtml\Rma\Create\Order;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    public function __construct(
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Lof\Rma\Helper\Help $help,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->context         = $context;
        $this->request         = $context->getRequest();
        $this->help            = $help;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('rma_rma_create_order_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $allowedStatuses = explode(',',$this->help->getConfig($store = null,'rma/policy/allow_in_statuses'));
        $collection = $this->help->getOrderCollection();
        $collection->addFieldToFilter('status', ['in' => $allowedStatuses]);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', [
            'header' => __('Order #'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'increment_id',
            'filter_index' => 'main_table.increment_id',
        ]);

        if (!$this->context->getStoreManager()->isSingleStoreMode()) {
            $this->addColumn('store_id', [
                'header' => __('Purchased From (Store)'),
                'index' => 'store_id',
                'type' => 'store',
                'store_view' => true,
                'display_deleted' => true,
            ]);
        }

        $this->addColumn('customer_email', [
            'header' => __('Customer email'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'customer_email',
        ]);

        $this->addColumn('created_at', [
            'header' => __('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ]);
        

        $this->addColumn('items', [
            'header' => __('Items'),
            'index' => 'shipping_address_id',
            'frame_callback' => [$this, 'callback_items'],
             'filter' => false,
        ]);

        $this->addColumn('base_grand_total', [
            'header' => __('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type' => 'currency',
            'currency' => 'base_currency_code',
        ]);

        return parent::_prepareColumns();
    }

    public function callback_items($value, $order, $column, $isExport)
    {
        if ($value) {
            $items ='';
           foreach($order->getAllItems() as $item){
               $items .= $item->getName().'('.(int)$item['qty_ordered'].'), ';
           }
           return $items;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/add',
            [
                'order_id' => $row->getId()
            ]
        );
    }
}
