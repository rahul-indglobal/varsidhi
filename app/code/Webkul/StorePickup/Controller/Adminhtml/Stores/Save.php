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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Registry;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Webkul_StorePickup::pickupstores';

    /**
     * @var Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Webkul\StorePickup\Model\StoresFactory
     */
    private $storesFactory;

    /**
     * @var \Webkul\StorePickup\Helper\Data
     */
    private $dataHelper;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var \Webkul\StorePickup\Model\StoresProductsRelationFactory
     */
    protected $storesProductRelationFactory;

    /**
     * @var $assignedProductIds
     */
    private $assignedProductIds;

    /**
     * @var $needToAssignProductIds
     */
    private $needToAssignProductIds;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Webkul\StorePickup\Model\StoresFactory $storesFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Webkul\StorePickup\Model\StoresFactory $storesFactory,
        \Webkul\StorePickup\Helper\Data $dataHelper,
        \Webkul\StorePickup\Model\StoresProductsRelationFactory $storesProductRelationFactory,
        EventManager $eventManager
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->storesFactory = $storesFactory;
        $this->dataHelper = $dataHelper;
        $this->storesProductRelationFactory = $storesProductRelationFactory;
        $this->eventManager = $eventManager;
        parent::__construct($context);
    }

    /**
     * Save action
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $this->assignedProductIds = [];
        $this->needToAssignProductIds = [];

        $intialData = $this->dataPersistor->get('webkul_storepickup_stores_for_save_file');

        if (!empty($intialData)) {
            if (!empty(array_values($intialData)[0]['product_assignment']['assigned_products'])) {
                $assignedProducts = array_values($intialData)[0]['product_assignment']['assigned_products'];
                $this->assignedProductIds = array_column(
                    $assignedProducts,
                    'entity_id'
                );
            }
        }

        if ($data) {
            $id = $this->getRequest()->getParam('entity_id');

            $model = $this->storesFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Stores no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $savedData = $model->save();
                if ($savedData) {
                    $this->assignProducts($savedData->getData());
                }
                $this->messageManager->addSuccessMessage(__('You saved the Stores.'));
                $this->eventManager->dispatch('pickup_store_save_after', ['pickup_store' => $model]);
                $this->dataHelper->doReIndexProducts(
                    array_merge(
                        $this->assignedProductIds,
                        $this->needToAssignProductIds
                    )
                );
                $this->dataPersistor->clear('webkul_storepickup_stores');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Stores.'));
            }

            $this->dataPersistor->set('webkul_storepickup_stores', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    private function assignProducts($data)
    {
        $assignedData = [];
        if (!empty($data['product_assignment']['assigned_products'])) {
            foreach ($data['product_assignment']['assigned_products'] as $assignedProduct) {
                $this->needToAssignProductIds[] = $assignedProduct['entity_id'];
                $assignedData[$assignedProduct['entity_id']] = [
                    'store_id' => $data['entity_id'],
                    'product_id' => $assignedProduct['entity_id'],
                    'qty' => 1
                ];
            }
        }

        $storeProductRelation = $this->storesProductRelationFactory->create();
        $storeProductRelation->saveAssignedProducts($assignedData, $data['entity_id']);
    }
}
