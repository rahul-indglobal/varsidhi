<?php
namespace Varsidhi\CategorySort\Plugin;

class ToolbarPlugin
{
    public function beforeSetCollection(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, $collection)
    {
        // If no sort order is explicitly set by user
        if (!$subject->getCurrentOrder()) {
            $subject->setDefaultOrder('created_at');
            $subject->setDefaultDirection('desc');
            $collection->setOrder('created_at', 'desc');
        }

        $currentOrder = $subject->getCurrentOrder();
        if ($currentOrder == 'created_at') {
            $subject->setDefaultDirection('desc');
            $collection->setOrder('created_at', 'desc');
        }

        return [$collection];
    }
}
