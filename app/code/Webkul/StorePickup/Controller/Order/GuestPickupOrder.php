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

namespace Webkul\StorePickup\Controller\Order;

use Magento\Framework\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Sales\Helper\Guest as GuestHelper;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

/**
 * Guest order view action.
 */
class GuestPickupOrder extends Action\Action implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * @var \Magento\Sales\Helper\Guest
     */
    protected $guestHelper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Sales\Helper\Guest $guestHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        GuestHelper $guestHelper,
        PageFactory $resultPageFactory
    ) {
        $this->guestHelper = $guestHelper;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $result = $this->guestHelper->loadValidOrder($this->getRequest());
        if ($result instanceof ResultInterface) {
            return $result;
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->guestHelper->getBreadcrumbs($resultPage);
        return $resultPage;
    }
}
