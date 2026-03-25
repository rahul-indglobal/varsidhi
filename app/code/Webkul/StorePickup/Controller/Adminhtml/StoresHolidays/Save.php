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

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Webkul_StorePickup::pickupholidays';

    /**
     * @var $dataPersistor
     */
    protected $dataPersistor;

    /**
     * @var $dataPersistor
     */
    protected $dataHelper;

    /**
     * @var $storesHolidaysFactory
     */
    protected $storesHolidaysFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Webkul\StorePickup\Helper\Data $dataHelper,
        \Webkul\StorePickup\Model\StoresHolidaysFactory $storesHolidaysFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->dataHelper = $dataHelper;
        $this->storesHolidaysFactory = $storesHolidaysFactory;
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
        if ($data['holidaySelector'] == 1) {
            $data['is_single_date'] = 0;
            $data['from_date'] = $data['store_holiday_start'];
            $data['to_date'] = $data['store_holiday_end'];
        } else {
            $data['is_single_date'] = 1;
            $data['from_date'] = $data['store_holiday'];
            $data['to_date'] = $data['store_holiday'];
        }

        $temp = [
            'from' => $data['store_holiday_start'],
            'to' => $data['store_holiday_end'],
            'date' => $data['store_holiday']
        ];

        $data['date'] = $this->dataHelper->serialize($temp);

        if ($data) {
            $id = $this->getRequest()->getParam('entity_id');

            $model = $this->storesHolidaysFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Holiday no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Holiday.'));
                $this->dataPersistor->clear('webkul_storepickup_holidays');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Holiday.'));
            }

            $this->dataPersistor->set('webkul_storepickup_holidays', $data);
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
