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

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    public function __construct(
        \Lof\Rma\Api\Repository\StatusRepositoryInterface             $statusRepository,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $orderInvoiceCollectionFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->statusRepository      = $statusRepository;
        $this->orderInvoiceCollectionFactory = $orderInvoiceCollectionFactory;
        $this->wysiwygConfig                 = $wysiwygConfig;
        $this->registry                      = $registry;
        $this->context                       = $context;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'rma_id';
        $this->_controller = 'adminhtml_rma';
        $this->_blockGroup = 'Lof_Rma';

        $this->buttonList->remove('save');

        $this->getToolbar()->addChild(
            'update-split-button',
            'Magento\Backend\Block\Widget\Button\SplitButton',
            [
                'id'           => 'update-split-button',
                'label'        => __('Save'),
                'class_name'   => 'Magento\Backend\Block\Widget\Button\SplitButton',
                'button_class' => 'widget-button-update',
                'options'      => [
                    [
                        'id'             => 'update-button',
                        'label'          => __('Save'),
                        'default'        => true,
                        'data_attribute' => [
                            'mage-init' => [
                                'button' => [
                                    'event'  => 'save',
                                    'target' => '#edit_form',
                                ],
                            ],
                        ],
                    ],
                    [
                        'id'             => 'update-continue-button',
                        'label'          => __('Save & Continue Edit'),
                        'data_attribute' => [
                            'mage-init' => [
                                'button' => [
                                    'event'  => 'saveAndContinueEdit',
                                    'target' => '#edit_form',
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $rma = $this->getRma();
        if ($rma) {
            $PrintUrl = $this->_urlBuilder->getUrl('rma/rma/print',
            ['id' => $rma->getId(), '_nosid' => true]);

            //\Magento\Store\Model\Service\StoreConfigManager
            $PrintUrl = $this->_urlBuilder->getUrl('returns/rma/print',
                ['id' => $rma->getId()]);
            $PrintUrl = str_replace('/admin/','/',$PrintUrl);

            $this->buttonList->add('print', [
                'label'   => __('Print'),
                'onclick' => 'var win = window.open(\'' .
                     $PrintUrl . '\', \'_blank\');win.focus();',
            ]);
            if ($this->allowCreateCreditmemo($rma)) {
                $this->buttonList->add('order_creditmemo_manual', [
                    'label'   => __('Credit Memo'),
                    'onclick' => 'var win = window.open(\'' .
                        $this->getCreditmemoUrl($rma) . '\', \'_blank\');win.focus();',
                ]);
            }

             $this->buttonList->add('order_exchange', [
                'label'   => __('Exchange Order'),
                'onclick' => 'var win = window.open(\'' .
                    $this->getCreateOrderUrl($rma) . '\', \'_blank\');win.focus();',
            ]);

            
        }

        return $this;
    }

    /**
     * @param \Lof\Rma\Model\Rma $rma
     *
     * @return string
     */
    public function getCreateOrderUrl($rma)
    {
        return $this->getUrl(
            'sales/order_create/start/',
            [
                'customer_id' => $rma->getCustomerId(),
                'store_id'    => $rma->getStoreId(),
                'rma_id'      => $rma->getId()
            ]
        );
    }

    /**
     * @param \Lof\Rma\Model\Rma    $rma
     *
     * @return bool
     */
    public function allowCreateCreditmemo($rma)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $order = $objectManager->get('Magento\Sales\Model\Order')->load($rma->getOrderId());
        if (!$order->canCreditmemo()) {
            return false;
        }
        return true;
    }

    /**
     * @param \Lof\Rma\Model\Rma $rma
     *
     * @return string
     */
    public function getCreditmemoUrl($rma)
    {
        $orderId = $rma->getOrderId();
        $collection = $this->orderInvoiceCollectionFactory->create()
            ->addFieldToFilter('order_id', $orderId);
        // echo $collection->getSelect();die;
        if ($collection->count() == 1) {
            $invoice = $collection->getFirstItem();

            return $this->getUrl(
                'sales/order_creditmemo/new',
                [
                    'order_id'   => $orderId,
                    'invoice_id' => $invoice->getId(),
                    'rma_id'     => $rma->getId()
                ]
            );
        } else {
            return $this->getUrl(
                'sales/order_creditmemo/new',
                [
                    'order_id' => $orderId,
                    'rma_id'   => $rma->getId()]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->wysiwygConfig->isEnabled()) {
        }
    }

    /**
     * @return \Lof\Rma\Model\Rma
     */
    public function getRma()
    {
        if ($this->registry->registry('current_rma') && $this->registry->registry('current_rma')->getId()) {
            return $this->registry->registry('current_rma');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderText()
    {
        if ($rma = $this->getRma()) {
            $status = $this->statusRepositor->get($rma->getStatusId())->getName();
            return __('RMA #%1 - %2', $rma->getIncrementId(), $status);
        } else {
            return __('Create New RMA');
        }
    }

    /************************/
}
