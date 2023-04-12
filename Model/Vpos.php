<?php

namespace Uala\Bis\Model;

class Vpos extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code = 'bis';
    protected $_isOffline = true;
    protected $_isInitializeNeeded = true;
}