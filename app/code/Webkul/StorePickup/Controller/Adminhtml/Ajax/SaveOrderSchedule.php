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

namespace Webkul\StorePickup\Controller\Adminhtml\Ajax;

class SaveOrderSchedule extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Webkul_StorePickup::pickuporders';
    
    /**
     * @var $resultPageFactory
     */
    protected $resultPageFactory;

    /**
     * @var $dataHelper
     */
    protected $dataHelper;

    /**
     * @var $orderFactory
     */
    protected $orderFactory;

    /**
     * @var $timezone
     */
    protected $timezone;

    /**
     * @var $messageManager
     */
    protected $messageManager;

    /**
     * @var $emailHelper
     */
    protected $emailHelper;

    /**
     * Constructor
     * @param Magento\Backend\App\Action\Context          $context
     * @param Magento\Framework\View\Result\PageFactory   $resultPageFactory
     * @param Webkul\StorePickup\Helper\Data              $dataHelper
     * @param Webkul\StorePickup\Helper\Email             $emailHelper
     * @param Magento\Sales\Model\OrderFactory            $orderFactory
     * @param Magento\Framework\Stdlib\DateTime\Timezone  $timezone
     * @param Psr\Log\LoggerInterface                     $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Webkul\StorePickup\Helper\Data $dataHelper,
        \Webkul\StorePickup\Helper\Email $emailHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Stdlib\DateTime\Timezone $timezone,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->dataHelper = $dataHelper;
        $this->emailHelper = $emailHelper;
        $this->orderFactory = $orderFactory;
        $this->timezone = $timezone;
        $this->logger = $logger;
        $this->messageManager = $context->getMessageManager();
        parent::__construct($context);
    }

    /**
     * Execute view action
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $status = 'failed';
        try {
            $params = $this->getRequest()->getParams();
            $params['contact'] = $this->dataHelper->unserialize($params['contact']);
            $orderId = $params['id'];
            $time = explode(":", $params['scheduledTime']);
            $date = date('m-d-Y', strtotime($params['scheduledDate']));
            $date = explode("-", $date);
            $pickupScheduledAt = mktime($time[0], $time[1], 0, $date[0], $date[1], $date[2]);
            $order = $this->orderFactory->create()->load($orderId);
            $alreadyScheduled = $order->getPickupScheduledDatetime();
            $order->setPickupScheduledDatetime($pickupScheduledAt);
            $order->setPickupScheduledAt(
                strtotime($this->timezone->date(
                    date('Y-m-d H:i:s')
                )->format('d-M-Y h:i:s'))
            );
            $order->save();
            $status = 'success';

            $msg = __('is scheduled for pickup');
            $subject = __('Pickup Order Scheduled');
            if ($alreadyScheduled) {
                $msg = __('is re-scheduled for pickup');
                $subject = __('Pickup Order Re-scheduled');
            }

            $pickupScheduledAt = date('D, d M Y h:i A', $pickupScheduledAt);
            $data = [
                'subject' => $subject,
                'order_id' => $order->getIncrementId(),
                'message' => $msg,
                'scheduled_datetime' => $pickupScheduledAt,
                'store_name' => $params['storeName'],
                'address' => $params['address'],
                'contact' => $params['contact'],
                'to' => [
                    'email' => $order->getCustomerEmail(),
                    'name' => $order->getCustomerName()
                ]
            ];

            $this->emailHelper->sendEmail($data);
            $this->messageManager->addSuccess(__('Order %1', $msg));
        } catch (\Exception $ex) {
            $this->messageManager->addSuccess(__('Unable to scheduled.'));
            $status = 'failed';
        }

        return $this->getResponse()->representJson(
            $this->dataHelper->serialize([
                'status' => $status
            ])
        );
    }
}
