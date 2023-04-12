<?php

namespace Uala\Bis\Model\Config\Backend;

use Magento\Framework\Option\ArrayInterface;
use Magento\User\Model\ResourceModel\User\CollectionFactory;

class Options implements ArrayInterface
{
    public function toOptionArray()
    {

        $options = [];
        $options[] = [
            'value' => 'no',
            'label' => 'Sin credenciales',
        ];
        $options[] = [
            'value' => 'test',
            'label' => 'Credenciales de prueba',
        ];
        $options[] = [
            'value' => 'prod',
            'label' => 'Credenciales de produccion',
        ];

        return $options;
    }
}
