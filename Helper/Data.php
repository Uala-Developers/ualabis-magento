<?php

namespace Uala\Bis\Helper;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Sales\Api\Data\OrderInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Checkout\Model\Type\Onepage;
use \Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Exception\InputException;
use \Magento\Framework\Message\ManagerInterface;
use \Magento\Checkout\Model\Cart;
use \Magento\Checkout\Model\Session;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public $scopeConfig;
    public $order;
    public $store;
    protected $checkout;
    protected $curl;
    protected $messageManager;
    protected $cart;
    protected $session;
    

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        OrderInterface $order,
        StoreManagerInterface $store,
        Onepage $checkout,
        Curl $curl,
        ManagerInterface $messageManager,
        Cart $cart,
        Session $session
    ) {
        $this->order             = $order;
        $this->store             = $store;
        $this->scopeConfig       = $scopeConfig;
        $this->checkout          = $checkout;
        $this->curl              = $curl;
        $this->messageManager    = $messageManager;
        $this->cart              = $cart;
        $this->session           = $session;
    }


    public function generateCheckoutUrl()
	{
        try {
            $userName = $this->scopeConfig->getValue('payment/bis/user_name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $clientId = $this->scopeConfig->getValue('payment/bis/client_id',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $clientSecretId = $this->scopeConfig->getValue('payment/bis/client_secret_id',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $grantType = $this->scopeConfig->getValue('payment/bis/granttype',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $id = $this->session->getLastRealOrder()->getId();
            $order = $this->order->load($id);    
            //get token
            $url = $this->getTokenUrl();
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $headers = array("Content-Type: application/json","Accept: application/json");
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $data = '{"user_name" : "'.$userName.'","client_id" : "'.$clientId.'","client_secret_id" : "'.$clientSecretId.'","grant_type" : "'.$grantType.'"}';
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $resp = curl_exec($curl);
            curl_close($curl);
            $arre=json_decode($resp, true);
            if ((isset($arre['code'])) or ($arre['access_token']=="")){
                $this->order->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true);
                $this->order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
                foreach ($this->order->getAllItems() as $item) { // Cancel order items
                    $this->cart->addOrderItem($item);
                    $item->cancel();
                }
                $this->order->save();
                $this->cart->save();    
                $this->messageManager->addError(__("Uala Bis API token error: ".$arre['description']));
                return 'checkout/';
            }
            //generate order
            $url = $this->getCheckoutUrl();
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $checkout = $this->checkout->getCheckout();
            $order = $this->order->loadByIncrementId($checkout->getLastRealOrderId());
            $headers = array("Authorization: Bearer ".$arre['access_token'],"Content-Type: application/json","Accept: application/json");
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $data = '{
            "amount": "'.round((float)$order->getGrandTotal(),2).'",
            "description": "Order # '.$order->getIncrementId().'",
            "userName": "'.$userName.'",
            "callback_fail": "'.$this->store->getStore()->getBaseUrl().'bis/payment/back",
            "callback_success": "'.$this->store->getStore()->getBaseUrl().'bis/payment/back",
            "notification_url": "'.$this->store->getStore()->getBaseUrl().'bis/payment/notification/",
	    "origin": "Magento"
            }';
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $resp = curl_exec($curl);
            $arre=json_decode($resp, true);
            curl_close($curl);    
            if (isset($arre['code'])){
		$this->order->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true);
                $this->order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
                foreach ($this->order->getAllItems() as $item) { // Cancel order items
                    $this->cart->addOrderItem($item);
                    $item->cancel();
                }
                $this->order->save();
                $this->cart->save();    
                $this->messageManager->addError(__('Uala Bis API checkout error: '.$arre['message']));
                return 'checkout/';
            }
            $order->setData("x_forwarded_for",$arre['uuid']);
            $order->save();
            return $arre['links']['checkoutLink'];
        }
        catch(InputException $e) {
            echo $e->getMessage();
        }
	}

    public function getMessage()
    {
        return $this->scopeConfig->getValue(
            'payment/bis/message',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getTokenUrl()
    {
        return $this->scopeConfig->getValue(
            'payment/bis/token_url',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCheckoutUrl()
    {
        return $this->scopeConfig->getValue(
            'payment/bis/checkout_url',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getOrderUrl()
    {
        return $this->scopeConfig->getValue(
            'payment/bis/order_url',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}
