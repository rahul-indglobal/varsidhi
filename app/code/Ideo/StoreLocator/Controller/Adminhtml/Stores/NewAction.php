<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Controller\Adminhtml\Stores;

use \Ideo\StoreLocator\Controller\Adminhtml\Stores;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Ideo\StoreLocator\Api\StoreRepositoryInterface;
use \Ideo\StoreLocator\Helper\Config as ConfigHelper;
use \Magento\Backend\Model\View\Result\ForwardFactory;
use \Ideo\StoreLocator\Api\Data\StoreInterfaceFactory;

class NewAction extends Stores
{
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $resultForwardFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Ideo\StoreLocator\Api\StoreRepositoryInterface $storeRepository
     * @param StoreInterfaceFactory $storeFactory
     * @param \Ideo\StoreLocator\Helper\Config $configHelper
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        StoreRepositoryInterface $storeRepository,
        StoreInterfaceFactory $storeFactory,
        ConfigHelper $configHelper,
        ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context, $resultPageFactory, $storeRepository, $storeFactory, $configHelper);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($error = $this->checkGoogleApiKey()) {
            return $error;
        }
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
