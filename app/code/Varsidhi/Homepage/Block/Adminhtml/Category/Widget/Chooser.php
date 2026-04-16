<?php

namespace Varsidhi\Homepage\Block\Adminhtml\Category\Widget;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Multiselect;
use Varsidhi\Homepage\Model\Category\Source\CategoryList;

class Chooser extends Template
{
    /**
     * @var CategoryList
     */
    protected $categoryList;

    /**
     * @var Factory
     */
    protected $elementFactory;

    /**
     * @param Template\Context $context
     * @param CategoryList $categoryList
     * @param Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CategoryList $categoryList,
        Factory $elementFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->categoryList = $categoryList;
        $this->elementFactory = $elementFactory;
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element Form Element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $options = $this->categoryList->toOptionArray();
        
        /** @var Multiselect $multiselect */
        $multiselect = $this->elementFactory->create(Multiselect::class, ['data' => $element->getData()]);
        $multiselect->setForm($element->getForm());
        $multiselect->setValues($options);
        $multiselect->addClass('varsidhi-category-chooser');

        // Add search logic, reset button, and multi-select instructions
        $searchHtml = '
            <div class="admin__field-control">
                <div style="display: flex; gap: 10px; margin-bottom: 5px;">
                    <input type="text" 
                           class="admin__control-text" 
                           placeholder="' . __('Search categories...') . '" 
                           style="width: 100%;"
                           onkeyup="if(window.filterCategoryOptions) { filterCategoryOptions(this, \'' . $element->getHtmlId() . '\'); }">
                    <button type="button" 
                            class="action-secondary" 
                            onclick="if(window.resetCategorySelection) { resetCategorySelection(\'' . $element->getHtmlId() . '\'); }"
                            style="white-space: nowrap;">
                        ' . __('Reset All') . '
                    </button>
                </div>
            </div>
            <script>
                if (typeof window.filterCategoryOptions === "undefined") {
                    window.filterCategoryOptions = function(input, selectId) {
                        var filter = input.value.toLowerCase();
                        var select = document.getElementById(selectId);
                        if (!select) return;
                        var options = select.getElementsByTagName("option");
                        for (var i = 0; i < options.length; i++) {
                            var txtValue = options[i].textContent || options[i].innerText;
                            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                                options[i].style.display = "";
                            } else {
                                options[i].style.display = "none";
                            }
                        }
                    };
                }
                if (typeof window.resetCategorySelection === "undefined") {
                    window.resetCategorySelection = function(selectId) {
                        var select = document.getElementById(selectId);
                        if (!select) return;
                        var options = select.getElementsByTagName("option");
                        for (var i = 0; i < options.length; i++) {
                            options[i].selected = false;
                        }
                        // Trigger change event for Magento logic
                        var event = document.createEvent("HTMLEvents");
                        event.initEvent("change", true, true);
                        select.dispatchEvent(event);
                    };
                }
            </script>
        ';
        
        $footerHtml = '
            <div class="admin__field-note">
                ' . __('Note: To select multiple categories, hold down the <strong>Ctrl</strong> (Windows) or <strong>Command</strong> (Mac) key while clicking.') . '
            </div>
        ';

        $element->setData('after_element_html', $searchHtml . $multiselect->getElementHtml() . $footerHtml);
        return $element;
    }
}
