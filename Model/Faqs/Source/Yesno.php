<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Model\Faqs\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 */
class Yesno implements OptionSourceInterface
{
    
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        
        $availableOptions = ['1' => 'Yes', '0' => 'No'];
        
        $options = [];
        foreach ($availableOptions as $key => $label) {
            $options[] = [
                'label' => $label,
                'value' => $key,
            ];
        }
        return $options;
    }
}
