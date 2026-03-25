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

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Webkul_StorePickup::pickupholidays';

    /**
     * @var $jsonFactory
     */
    protected $jsonFactory;

    /**
     * @var $storesHolidaysFactory
     */
    protected $storesHolidaysFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Webkul\StorePickup\Model\StoresHolidaysFactory $storesHolidaysFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Webkul\StorePickup\Model\StoresHolidaysFactory $storesHolidaysFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->storesHolidaysFactory = $storesHolidaysFactory;
    }

    /**
     * Inline edit action
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $modelId) {
                    /** @var \Webkul\StorePickup\Model\StoresHolidays $model */
                    $model = $this->storesHolidaysFactory->create()->load($modelId);
                    try {
                        $model->setData($this->mergeArray($model->getData(), $postItems[$modelId]));
                        $model->save();
                    } catch (\Exception $e) {
                        $messages[] = "[Holiday ID: {$modelId}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * merge array
     * @param array $data
     * @param array $id
     * @return array
     */
    private function mergeArray($data, $id)
    {
        return array_merge($data, $id);
    }
}
