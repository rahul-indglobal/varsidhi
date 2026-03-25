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

namespace Webkul\StorePickup\Controller\Adminhtml\Stores;

class Delete extends \Webkul\StorePickup\Controller\Adminhtml\Stores
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Webkul_StorePickup::pickupstores';

    /**
     * @var \Webkul\StorePickup\Model\StoresFactory
     */
    private $storesFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var \Webkul\StorePickup\Helper\Data
     */
    private $dataHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Webkul\StorePickup\Model\StoresFactory $storesFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Webkul\StorePickup\Model\StoresFactory $storesFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Webkul\StorePickup\Helper\Data $dataHelper
    ) {
        $this->storesFactory = $storesFactory;
        $this->categoryFactory = $categoryFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Delete action
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->storesFactory->create()->load($id);
                $model->delete();
                $this->deleteCategory($id);
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Stores.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Stores to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

    private function deleteCategory($id)
    {
        $pickupStoreCatId = $this->getPickupStoresCategoryId();

        if ($pickupStoreCatId && $id) {
            $collection = $this->categoryFactory->create()->getCollection()
                ->addFieldToFilter('url_key', ['eq' => 'pickup-store-'.$id])
                ->addFieldToFilter('parent_id', $pickupStoreCatId);

            foreach ($collection as $category) {
                $this->doDeleteCategory($category);
            }
        }
    }

    private function doDeleteCategory($category)
    {
        $category->delete();
        $this->dataHelper->doReIndexCategories();
    }

    private function getPickupStoresCategoryId()
    {
        $collection = $this->categoryFactory->create()->getCollection()
            ->addFieldToFilter('url_key', ['eq' => 'pickup-stores']);

        foreach ($collection as $category) {
            return $category->getId();
        }

        return false;
    }
}
