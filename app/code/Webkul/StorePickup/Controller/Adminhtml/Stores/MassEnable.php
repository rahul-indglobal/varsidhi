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
use Webkul\StorePickup\Helper\Data;
use Magento\Catalog\Model\CategoryFactory;

class MassEnable extends \Magento\Backend\App\Action
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
     * @var $dataHelper
     */
    protected $dataHelper;

    /**
     * @var $categoryFactory
     */
    protected $categoryFactory;

    /**
     * @param Context                   $context
     * @param Filter                    $filter
     * @param CollectionFactory         $collectionFactory
     * @param StoresRepositoryInterface $storesRepository
     * @param Data                      $dataHelper
     * @param CategoryFactory           $categoryFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        StoresRepositoryInterface $storesRepository,
        Data $dataHelper,
        CategoryFactory $categoryFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->storesRepository = $storesRepository;
        $this->dataHelper = $dataHelper;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     * It call when admin set enable state on some store(s)
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );

        $count = 0;
        $alreadyEnabled = 0;
        $productIds = [];

        foreach ($collection->getItems() as $item) {
            $obj = $this->storesRepository->get($item->getEntityId());
            if ($obj->getIsEnabled() == 0) {
                $obj->setIsEnabled(1);
                $this->saveStatus($obj);
                $productIds[] = $item->getEntityId();
                $count++;
            } else {
                $alreadyEnabled++;
            }
        }

        if ($alreadyEnabled == $collection->getSize()) {
            $this->messageManager->addSuccess(
                __('Selected store(s) are already enabled.')
            );
        } else {
            if ($count == 0) {
                $this->messageManager->addNotice(
                    __('No store(s) are found disabled.')
                );
            } else {
                $this->messageManager->addSuccess(
                    __('A total of %1 store(s) have been enabled.', $count)
                );

                foreach ($productIds as $productId) {
                    $urlKey = 'pickup-store-'.$productId;
                    $categoryId = $this->getCategoryId($urlKey);
                    if ($categoryId) {
                        $this->doEnableDisableCategory($categoryId, true);
                    }
                }

                $this->dataHelper->doReIndexProducts($productIds);
                $this->dataHelper->doReIndexCategories();
            }
            if ($alreadyEnabled) {
                $this->messageManager->addNotice(
                    __(
                        'You are trying to enable, %1 already enabled store(s) which can\'t be enable again.',
                        $alreadyEnabled
                    )
                );
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(
            ResultFactory::TYPE_REDIRECT
        );

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Save Enabled Status
     * @param \Webkul\StorePickup\Api\Data\StoresInterface $obj
     * @return void
     */
    private function saveStatus($obj)
    {
        $this->storesRepository->save($obj);
    }

    /**
     * do enable disbale category
     * @param int $categoryId
     * @param boolean $data
     * @return void
     */
    private function doEnableDisableCategory($categoryId, $data)
    {
        $category = $this->categoryFactory->create()->load($categoryId);
        if ($data != $category->getIsActive()) {
            $category->setIsActive($data);
            $category->save();
            $category->setStoreId(0);
            $category->save();
        }
    }

    /**
     * get Category Id
     * @param string $urlKey
     * @return int
     */
    private function getCategoryId($urlKey)
    {
        $collection = $this->categoryFactory->create()->getCollection()
            ->addFieldToFilter('url_key', ['eq' => $urlKey]);

        $categoryId = 0;
        if ($collection->getSize()) {
            foreach ($collection as $category) {
                $categoryId = $category->getId();
            }
        }

        return $categoryId;
    }
}
