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

namespace Webkul\StorePickup\Helper;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $_inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * send email
     * @param array $data
     * @return string
     */
    public function sendEmail($data)
    {
        $store = $this->_storeManager->getStore()->getId();

        $adminEmail = $this->_scopeConfig->getValue(
            'trans_email/ident_support/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $adminName  = $this->_scopeConfig->getValue(
            'trans_email/ident_support/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $configVal = $this->_scopeConfig->getValue(
            'carriers/storepickup/order_scheduling_template',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $transport = $this->_transportBuilder->setTemplateIdentifier($configVal)
            ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
            ->setTemplateVars(
                [
                    'subject' => $data['subject'],
                    'orderId' => $data['order_id'],
                    'message' => $data['message'],
                    'scheduledDateTime' => $data['scheduled_datetime'],
                    'storeName' => $data['store_name'],
                    'address' => $data['address'],
                    'contactName' => $data['contact']['name'],
                    'contactEmail' => $data['contact']['email'],
                    'contactMobile' => $data['contact']['mobile'],
                    'contactFax' => $data['contact']['fax']
                ]
            )
            ->setFrom(['email' => $adminEmail, 'name' => $adminName])
            ->addTo($data['to']['email'], $data['to']['name'])
            ->getTransport();

        $result = $this->_resultJsonFactory->create();
        try {
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
            return $result->setData(['status' => 'success']);
        } catch (\Exception $ex) {
            return $result->setData(['status' => 'failed']);
        }
    }
}
