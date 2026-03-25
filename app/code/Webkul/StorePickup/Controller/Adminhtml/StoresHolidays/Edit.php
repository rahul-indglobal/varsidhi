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

namespace Webkul\StorePickup\Controller\Adminhtml\StoresHolidays;

class Edit extends \Webkul\StorePickup\Controller\Adminhtml\StoresHolidays
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Webkul_StorePickup::pickupholidays';

    /**
     * @var $resultPageFactory
     */
    protected $resultPageFactory;

    /**
     * @var $storesHolidaysFactory
     */
    protected $storesHolidaysFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Webkul\StorePickup\Model\StoresHolidaysFactory $storesHolidaysFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->storesHolidaysFactory = $storesHolidaysFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('entity_id');
        $model = $this->storesHolidaysFactory->create();

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Holiday no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('webkul_storepickup_holidays', $model);

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Holiday') : __('New Holiday'),
            $id ? __('Edit Holiday') : __('New Holiday')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Holidays'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Holiday') : __('New Holiday'));
        return $resultPage;
    }
}
