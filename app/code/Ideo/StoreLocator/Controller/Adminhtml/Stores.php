<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Controller\Adminhtml;

use \Magento\Backend\App\Action;
use \Magento\Framework\View\Result\PageFactory;
use \Ideo\StoreLocator\Api\StoreRepositoryInterface;
use \Ideo\StoreLocator\Api\Data\StoreInterfaceFactory;
use \Ideo\StoreLocator\Helper\Config as ConfigHelper;

abstract class Stores extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Ideo\StoreLocator\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var \Ideo\StoreLocator\Api\Data\StoreInterfaceFactory
     */
    protected $storeFactory;

    /**
     * @var \Ideo\StoreLocator\Helper\Config
     */
    private $configHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Ideo\StoreLocator\Api\StoreRepositoryInterface $storeRepository
     * @param StoreInterfaceFactory $storeFactory
     * @param \Ideo\StoreLocator\Helper\Config $configHelper
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        StoreRepositoryInterface $storeRepository,
        StoreInterfaceFactory $storeFactory,
        ConfigHelper $configHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->storeRepository = $storeRepository;
        $this->storeFactory = $storeFactory;
        $this->configHelper = $configHelper;
        parent::__construct($context);
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ideo_StoreLocator::stores');
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ideo_StoreLocator::stores');
    }

    /**
     * @return $this|bool
     */
    protected function checkGoogleApiKey()
    {
        if ($this->configHelper->getGoogleApiKeyBackend() === null) {
            $this->messageManager->addErrorMessage(__('Google Api Key is not set! Go to Stores -> Configuration -> Ideo Extensions -> Store Locator to change extension settings.'));
            /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/index');
        }
        return false;
    }
}
