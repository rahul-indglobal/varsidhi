<?php
/**
 * LandOfCoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   LandOfCoder
 * @package    Lof_Rma
 * @copyright  Copyright (c) 2016 Venustheme (http://www.LandOfCoder.com/)
 * @license    http://www.LandOfCoder.com/LICENSE-1.0.html
 */



namespace Lof\Rma\Helper;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mail extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Lof\Rma\Model\Mail\TransportBuilder                    $transportBuilder,
        \Lof\Rma\Helper\Data                                    $rmaHelper,
        \Lof\Rma\Helper\Help                                    $Helper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface      $localeDate,
        \Magento\Sales\Api\OrderRepositoryInterface               $orderRepository,
        \Magento\Email\Model\TemplateFactory                      $emailTemplateFactory,
        \Magento\Customer\Model\CustomerFactory                   $customerFactory,
        \Magento\User\Model\UserFactory                           $userFactory,
        \Magento\Store\Model\StoreManagerInterface                $storeManager,
        \Magento\Framework\App\Helper\Context                     $context,
        \Magento\Framework\Translate\Inline\StateInterface        $inlineTranslation
    ) {
        $this->emailTemplateFactory = $emailTemplateFactory;
        $this->transportBuilder     = $transportBuilder;
        $this->rmaHelper            = $rmaHelper;
        $this->helper               = $Helper;
        $this->localeDate              = $localeDate;
        $this->customerFactory      = $customerFactory;
        $this->userFactory          = $userFactory;
        $this->orderRepository      = $orderRepository;
        $this->storeManager         = $storeManager;
        $this->context              = $context;
        $this->inlineTranslation    = $inlineTranslation;

        parent::__construct($context);
    }

    /**
     * @var array
     */
    public $emails = [];

    /**
     * @return string
     */
    protected function getSender()
    {
       return $this->helper->getConfig($store = null,'rma/notification/sender_email');
    }

    /**
     * @param string $templateName
     * @param string $senderName
     * @param string $senderEmail
     * @param string $recipientEmail
     * @param string $recipientName
     * @param array  $variables
     * @param int    $storeId
     * @param string $code
     * @param array  $attachments
     *
     * @return bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function send(
        $templateName,
        $senderName,
        $senderEmail,
        $recipientEmail,
        $recipientName,
        $variables,
        $storeId,
        $code,
        $attachments
    ) {
        if (!$senderEmail || !$recipientEmail || $templateName == 'none') {
            return false;
        }

        /** @var \Lof\Rma\Api\Data\AttachmentInterface $attachment */
        foreach ($attachments as $attachment) {
            $this->transportBuilder->addAttachment(
                $attachment->getBody(),
                $attachment->getType(),
                \Zend_Mime::DISPOSITION_ATTACHMENT,
                \Zend_Mime::ENCODING_BASE64,
                $attachment->getName()
            );
        }

        // Add blind carbon copy of all emails if such exists
        $bcc = $this->helper->getConfig($store = null,'rma/notification/send_email_bcc');
        if ($bcc) {
            if (strpos($bcc, ',')) {
                $bcc = explode(', ', $bcc);
            }
        }
        if ($bcc) {
            $this->transportBuilder->addBcc($bcc);
        }

        $hiddenCode = $hiddenSeparator = '';
       

        $variables = array_merge($variables, [
            'hidden_separator' => $hiddenSeparator,
            'hidden_code'      => $hiddenCode,
        ]);

        $this->inlineTranslation->suspend();
        $this->transportBuilder
            ->setTemplateIdentifier($templateName)
            ->setTemplateOptions(
                [
                    'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId ? $storeId : $this->storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($variables);

        $this->transportBuilder
            ->setFrom(
                [
                    'name'  => $senderName,
                    'email' => $senderEmail,
                ]
            )
            ->addTo($recipientEmail, $recipientName)
            ->setReplyTo($senderEmail);

        $transport = $this->transportBuilder->getTransport();

        /* @var \Magento\Framework\Mail\Transport $transport */
        $transport->sendMessage();

        $this->inlineTranslation->resume();

        return true;
    }

    /**
     * @param \Lof\Rma\Api\Data\RmaInterface            $rma
     * @param \Lof\Rma\Api\Data\MessageInterface|string $message
     * @param boolean                                        $isAllowParseVariables
     * @return void
     */
    public function sendNotificationCustomer($rma, $message, $isAllowParseVariables = false)
    {
        $attachments = [];
        if (is_object($message)) {
            $attachments = $this->rmaHelper->getAttachments('message',$message->getId());
             if ($message->getIsHtml()) {
                    $message = $message->getText();
                } else {
                   $message = nl2br($message->getText());
                }
        }
        if ($isAllowParseVariables) {
            $message = $this->parseVariables($message, $rma);
        }
        $storeId = $rma->getStoreId();
        $templateName = $this->helper->getConfig($store = null,'rma/notification/customer_email_template');

        $customer = $this->customerFactory->create()->load($rma->getCustomerId());
        $recipientEmail = $rma->getEmail() ? $rma->getEmail() : $customer->getEmail();
        $recipientName  = $rma->getFirstname() .' '.$rma->getLastname();
        $rmaUrl = $this->_urlBuilder->getUrl('rma/rma/view', ['id' => $rma->getId(), '_nosid' => true]);
        $variables = [
            'rmaUrl'   => $rmaUrl,
            'store'    => $this->storeManager->getStore($storeId),
        ];
        
        $message = $this->processVariable($message, $variables, $storeId);
        $variables['message'] = $message;

        $senderName = $this->context->getScopeConfig()->getValue("trans_email/ident_{$this->getSender()}/name");
        $senderEmail = $this->context->getScopeConfig()->getValue("trans_email/ident_{$this->getSender()}/email");
       
      
        $this->send(
            $templateName,
            $senderName,
            $senderEmail,
            $recipientEmail,
            $recipientName,
            $variables,
            $storeId,
            $rma->getCode(),
            $attachments
        );
    }

    /**
     * @param \Lof\Rma\Api\Data\RmaInterface            $rma
     * @param \Lof\Rma\Api\Data\MessageInterface|string $message
     * @param boolean                                        $isAllowParseVariables
     * @return void
     */
    public function sendNotificationAdmin($rma, $message, $isAllowParseVariables = false)
    {
        
        if ($isAllowParseVariables) {
            $message = $this->parseVariables($message, $rma);
        }

        $attachments = [];
        if (is_object($message)) {
            $attachments = $this->rmaHelper->getAttachments('message',$message->getId());
             if ($message->getIsHtml()) {
                    $message = $message->getText();
                } else {
                   $message = nl2br($message->getText());
                }
        }
        $storeId = $rma->getStoreId();
        $templateName =  $this->helper->getConfig($store = null,'rma/notification/admin_email_template');
        if ($user = $this->userFactory->create()->load($rma->getUserId())) {
            $recipientEmail = $user->getEmail();
        } else {
            return;
        }

        $recipientName = '';
        $customer = $this->customerFactory->create()->load($rma->getCustomerId());
        $rmaUrl = $this->_urlBuilder->getUrl('rma/rma/view', ['id' => $rma->getId(), '_nosid' => true]);
        $variables = [
            'customer'              => $customer,
            'rma'                   => $rma,
            'rma_user_name'         => $user->getName(),
            'rmaUrl'                => $rmaUrl,
            'rma_status'            => $this->rmaHelper->getStatus($rma)->getName(),
            'rma_createdAtFormated' => $this->localeDate->formatDate($rma->getCreatedAt(), \IntlDateFormatter::MEDIUM),
            'rma_updatedAtFormated' => $this->localeDate->formatDate($rma->getUpdatedAt(), \IntlDateFormatter::MEDIUM),
            'store'                 => $this->storeManager->getStore($storeId),
        ];
        $message = $this->processVariable($message, $variables, $storeId);
        $variables['message'] = $message;

        $senderName = $this->context->getScopeConfig()->getValue("trans_email/ident_{$this->getSender()}/name");
        $senderEmail = $this->context->getScopeConfig()->getValue("trans_email/ident_{$this->getSender()}/email");
        $this->send(
            $templateName,
            $senderName,
            $senderEmail,
            $recipientEmail,
            $recipientName,
            $variables,
            $storeId,
            $rma->getCode(),
            $attachments
        );
    }

    /**
     * @param string                              $recipientEmail
     * @param string                              $recipientName
     * @param \Lof\Rma\Model\Rule            $rule
     * @param \Lof\Rma\Api\Data\RmaInterface $rma
     * @return void
     */
    public function sendNotificationRule($recipientEmail, $recipientName, $rule, $rma)
    {
        $this->rmaAdapter->setData($rma->getData());
        $attachments = [];

        $text = '';
        if ($message = $this->rmaHelper->getLastMessage($rma)) {
            if ($rule->getIsSendAttachment()) {
                $attachments = $this->rmaHelper->getAttachments('message',$message->getId());
            }
             if ($message->getIsHtml()) {
                     $text = $message->getText();
                } else {
                    $text = nl2br($message->getText());
                }
        }

        $storeId = $rma->getStoreId();
        $templateName =  $this->helper->getConfig($store = null,'rma/notification/rule_template');;
        $customer = $this->customerFactory->create()->load($rma->getCustomerId());
        $variables = [
            'customer'      => $customer,
            'store'         => $this->storeManager->getStore($storeId),
            'email_subject' => $rule->getEmailSubject(),
            'email_body'    => $rule->getEmailBody(),
        ];
        $text = $this->processVariable($text, $variables, $storeId);
        $variables['message'] = $text;
        $senderName = $this->context->getScopeConfig()->getValue("trans_email/ident_{$this->getSender()}/name");
        $senderEmail = $this->context->getScopeConfig()->getValue("trans_email/ident_{$this->getSender()}/email");
        $this->send(
            $templateName,
            $senderName,
            $senderEmail,
            $recipientEmail,
            $recipientName,
            $variables,
            $storeId,
            $rma->getCode(),
            $attachments
        );
    }

    /**
     * Can parse template and return ready text.
     *
     * @param string $text  Text with variables like {{var customer.name}}.
     * @param array  $variables Array of variables.
     * @param int    $storeId
     *
     * @return string - ready text
     */
    protected function processVariable($text, $variables, $storeId)
    {
        $template = $this->emailTemplateFactory->create();
        $template->setDesignConfig([
            'area'  => 'frontend',
            'store' => $storeId,
        ]);
        $template->setTemplateText($text);
        $html = $template->getProcessedTemplate($variables);

        return $html;
    }

    /**
     * @param string                              $text
     * @param \Lof\Rma\Api\Data\RmaInterface $rma
     *
     * @return string
     */
    public function parseVariables($text, $rma)
    {
        $variables = [
            'rma'      => $rma,
            'store'    => $this->storeManager->getStore($rma->getStoreId()),
            'order'    => $this->orderRepository->get($rma->getOrderId()),
            'status'   => $this->rmaHelper->getStatus($rma),
            'customer' => $this->customerFactory->create()->load($rma->getCustomerId()),
        ];
        $text = $this->processVariable($text, $variables, $rma->getStoreId());

        return $text;
    }
}
