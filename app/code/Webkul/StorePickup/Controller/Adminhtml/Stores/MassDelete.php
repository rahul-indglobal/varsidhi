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

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\StorePickup\Api\StoresRepositoryInterface;
use Webkul\StorePickup\Model\ResourceModel\Stores\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Webkul_StorePickup::pickupstores';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StoresRepositoryInterface
     */
    protected $storesRepository;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var \Webkul\StorePickup\Helper\Data
     */
    private $dataHelper;

    /**
     * @param Context                   $context
     * @param Filter                    $filter
     * @param CollectionFactory         $collectionFactory
     * @param StoresRepositoryInterface $storesRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        StoresRepositoryInterface $storesRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Webkul\StorePickup\Helper\Data $dataHelper
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->storesRepository = $storesRepository;
        $this->categoryFactory = $categoryFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * Execute action
     * It call when admin want to delete some store(s)
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );

        $count = $collection->getSize();
        $ids = [];
        foreach ($collection->getItems() as $item) {
            $ids[] = 'pickup-store-'.$item->getEntityId();
            $this->deleteStoreById($item->getEntityId());
        }

        if (!empty($ids)) {
            $this->deleteCategories($ids);
            $this->dataHelper->doReIndexCategories();
        }

        $this->messageManager->addSuccess(
            __('A total of %1 store(s) have been deleted.', $count)
        );

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(
            ResultFactory::TYPE_REDIRECT
        );

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Do delete store by id
     * @param int $id
     * @return void
     */
    private function deleteStoreById($id)
    {
        $this->storesRepository->deleteById($id);
    }

    private function deleteCategories($ids)
    {
        $pickupStoreCatId = $this->getPickupStoresCategoryId();

        $collection = $this->categoryFactory->create()->getCollection()
            ->addFieldToFilter('url_key', ['in' => $ids])
            ->addFieldToFilter('parent_id', $pickupStoreCatId);

        foreach ($collection as $category) {
            $this->doDeleteCategory($category);
        }
    }

    private function doDeleteCategory($category)
    {
        $category->delete();
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
