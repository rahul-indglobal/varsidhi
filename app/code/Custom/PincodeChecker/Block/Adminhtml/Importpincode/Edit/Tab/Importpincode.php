<?php

namespace Custom\PincodeChecker\Block\Adminhtml\Importpincode\Edit\Tab;

class Importpincode extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_assetRepo;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        array $data = []
    ) {
        $this->_assetRepo = $assetRepo;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()

    {

        $path = $this->_assetRepo->getUrl("Custom_PincodeChecker::importsample/sample.csv");

        $form = $this->_formFactory->create();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storemanager = $objectManager->create('Magento\Store\Model\StoreManagerInterface');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Import Pincodes'),
                'class'  => 'fieldset-wide'
            ]
        );

        $importdata_script  = $fieldset->addField(
            'file',
            'file',
            [
                'name'  => 'file',
                'label' => __('Upload File'),
                'title' => __('Upload File'),
                'required' => true,
            ]
        );

        $importdata_script->setAfterElementHtml("
        <span id='sample-file-span' ><a id='sample-file-link' href='".$path."'  >Download Sample File</a></span>
        ");

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Import Pincodes');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}