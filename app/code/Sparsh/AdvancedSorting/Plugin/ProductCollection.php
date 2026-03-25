<?php
/**
 * Class ProductCollection
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_AdvancedSorting
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\AdvancedSorting\Plugin;

/**
 * Class ProductCollection
 *
 * @category Sparsh
 * @package  Sparsh_AdvancedSorting
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class ProductCollection
{
    /**
     * Reset Group select after get select column sql
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param \Magento\Framework\DB\Select                            $result
     *
     * @return mixed
     */
    public function afterGetSelectCountSql($subject, $result)
    {
        if (count($result->getPart(\Zend_Db_Select::GROUP)) > 0) {
              $result->reset(\Zend_Db_Select::GROUP);
        }
        return $result;
    }
}
