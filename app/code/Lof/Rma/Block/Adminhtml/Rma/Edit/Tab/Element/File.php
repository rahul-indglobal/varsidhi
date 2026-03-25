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
namespace Lof\Rma\Block\Adminhtml\Rma\Edit\Tab\Element;
use Magento\Framework\Escaper;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
class File extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_resultFactory;
    protected $fileCollectionFactory;
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->setType('file');
        $this->setExtType('file');
        
    }
   
    public function removeClass($class)
    {
        $classes = array_unique(explode(' ', $this->getClass()));
        if (false !== ($key = array_search($class, $classes))) {
            unset($classes[$key]);
        }
        $this->setClass(implode(' ', $classes));
        return $this;
    }
    public function getElementHtml()
    {
        $this->addClass('input-file');
        if ($this->getRequired()) {
            $this->removeClass('required-entry');
            if(!$this->getData('value'))
                $this->addClass('required-file');
        }
        $element = sprintf('<input id="%s" name="%s" %s />%s%s',
            $this->getHtmlId(),
            $this->getName(),
            $this->serialize($this->getHtmlAttributes()),
            $this->getAfterElementHtml(),
            $this->_getHiddenInput()
        );

        return $this->_getPreviewHtml(). $this->_getDeleteCheckboxHtml() . $element ;
    }
    protected function _getPreviewHtml(){
        $html = '';
        if($this->getAttachment()){
            
                    $html .= '<nobr><a href="' . $this->getAttachment()->getUrl() . '">' . $this->getAttachment()->getName() . '</a> <small>[' . $this->getAttachment()->getSize() . ']</small></nobr><br>';
              
        }
        return $html;
    }
     /**
     * Return Hidden element with current value
     *
     * @return string
     */
    protected function _getHiddenInput()
    {
        return $this->_drawElementHtml(
            'input',
            [
                'type'  => 'hidden',
                'name'  => sprintf('%s[value]', $this->getName()),
                'id'    => sprintf('%s_value', $this->getHtmlId()),
                'value' => $this->getEscapedValue()
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getEscapedValue($index = null)
    {
        if (is_array($this->getValue())) {
            return false;
        }
        $value = $this->getValue();
        if (is_array($value) && $index === null) {
            $index = 'value';
        }

        return parent::getEscapedValue($index);
    }
    /**
     * Attached file name
     *
     * @return string
     */
    public function getValue()
    {
        if ($this->getAttachment()) {
            return $this->getAttachment()->getName();
        }
    }
    protected function _getDeleteCheckboxHtml()
    {
        $html = '';
        if ($this->getValue() && !$this->getRequired() && !is_array($this->getValue())) {
        
        
            $checkboxId = sprintf('%s_delete', $this->getHtmlId());
            $checkbox   = array(
                'type'  => 'checkbox',
                'name'  => sprintf('%s[delete]', $this->getName()),
                'value' => '1',
                'class' => 'checkbox',
                'id'    => $checkboxId
            );
            $label      = array(
                'for'   => $checkboxId
            );
            if ($this->getDisabled()) {
                $checkbox['disabled'] = 'disabled';
                $label['class'] = 'disabled';
            }
            $html .= '<div class="' . $this->_getDeleteCheckboxSpanClass() . '">';
            $html .= $this->_drawElementHtml('input', $checkbox) . ' ';
            $html .= $this->_drawElementHtml('label', $label, false) . $this->_getDeleteCheckboxLabel() . '</label>';
            $html .= '</div>';
        }
        return $html;
    }
    protected function _getDeleteCheckboxSpanClass()
    {
        return 'delete-file';
    }
    protected function _getDeleteCheckboxLabel()
    {
        return __('Delete File');
    }
    protected function _drawElementHtml($element, array $attributes, $closed = true)
    {
        $parts = array();
        foreach ($attributes as $k => $v) {
            $parts[] = sprintf('%s="%s"', $k, $v);
        }
        return sprintf('<%s %s%s>', $element, implode(' ', $parts), $closed ? ' /' : '');
    }

       /**
     * Return Preview/Download URL
     *
     * @return string
     */
    protected function _getPreviewUrl()
    {
        return $this->getAttachment()->getUrl();
    }
}