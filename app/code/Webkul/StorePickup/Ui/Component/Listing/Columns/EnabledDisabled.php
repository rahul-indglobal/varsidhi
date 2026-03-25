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

namespace Webkul\StorePickup\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class EnabledDisabled extends Column
{
    /**
     * Prepare Data Source.
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = 'status';
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item[$fieldName] == 1) {
                    $item[$fieldName] = __('Enabled');
                } else {
                    $item[$fieldName] = __('Disabled');
                }
            }
        }

        return $dataSource;
    }
}
