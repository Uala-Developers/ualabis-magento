<?php

namespace Uala\Bis\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use \Uala\Bis\Helper\Data;

class CustomConfigProvider implements ConfigProviderInterface
{

    protected $helper;

    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    public function getConfig()
    {
        $config = [
            'payment' => [
                'bis' => [
                    'message' => $this->helper->getMessage()
                ]
            ]
        ];
        return $config;
    }
}