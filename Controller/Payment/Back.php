<?php

namespace Uala\Bis\Controller\Payment;

use \Magento\Framework\App\Action\Context;
use \Magento\Sales\Model\Service\InvoiceService;
use \Magento\Sales\Model\Order;
use \Magento\Framework\DB\Transaction; 
use Magento\Framework\Exception\InputException;
use \Uala\Bis\Helper\Data;
use \Magento\Checkout\Model\Cart;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Checkout\Model\Session;
use \Magento\Framework\Message\ManagerInterface;

class Back extends \Magento\Framework\App\Action\Action
{
    public $context;
    protected $invoiceService;
    protected $order;
    protected $transaction;
    protected $helper;
    public $scopeConfig;
    protected $cart;
    protected $session;
    protected $messageManager;

    public function __construct(
        Context $context,
        InvoiceService $invoiceService,
        Order $order,
        Transaction $transaction,
        Data $helper,
        ScopeConfigInterface $scopeConfig,
        Cart $cart,
        Session $session,
        ManagerInterface $messageManager
    ) {
        $this->invoiceService = $invoiceService;
        $this->transaction    = $transaction;
        $this->order          = $order;
        $this->context        = $context;
        $this->helper         = $helper;
        $this->scopeConfig    = $scopeConfig;
        $this->cart           = $cart;
        $this->session        = $session;
        $this->messageManager = $messageManager;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $id = $this->session->getLastRealOrder()->getId();
            $order = $this->order->load($id);    
            $cart = $this->cart;
            $userName = $this->scopeConfig->getValue('payment/bis/user_name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $clientId = $this->scopeConfig->getValue('payment/bis/client_id',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $clientSecretId = $this->scopeConfig->getValue('payment/bis/client_secret_id',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $grantType = $this->scopeConfig->getValue('payment/bis/granttype',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
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
                if (isset($arre['code'])){
                    $this->messageManager->addError('Uala Bis API token error: '.$arre['description']);
                    return "/";
                }
                if ($arre['access_token']==""){
                    $this->messageManager->addError('Uala Bis API token error: Cant get token');
                    return "/";
                }
                //check order

                $url = $this->helper->getOrderUrl()."/".$this->order->getData("x_forwarded_for");
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $headers = array("Authorization: Bearer ".$arre['access_token'],"Content-Type: application/json","Accept: application/json");
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                $resp = curl_exec($curl);
                $arre=json_decode($resp, true);
                curl_close($curl);   
                if (($arre['status']=="PROCESSED") or ($arre['status']=="APPROVED"))
                {
                    $this->order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
                    $this->order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                    $this->order->save();
                    if ($this->order->canInvoice()) {
                        $invoice = $this->invoiceService->prepareInvoice($this->order);
                        $invoice->register();
                        $invoice->save();
                        $transactionSave = $this->transaction->addObject($invoice)->addObject($invoice->getOrder());
                        $transactionSave->save();
                        $this->order->addStatusHistoryComment(__('Invoiced', $invoice->getId()))->setIsCustomerNotified(false)->save();
                    }
                    $this->_redirect('checkout/onepage/success');
                       
                } else {
                if (($arre['status']=="REJECTED"))
                {
                    $this->order->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true);
                    $this->order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
                    foreach ($this->order->getAllItems() as $item) { // Cancel order items
                        $cart->addOrderItem($item);
                        $item->cancel();
                    }
                    $this->order->save();
                    $cart->save();    
                    $this->messageManager->addError(__("Pago no aprobado"));
                    $this->_redirect('checkout/');
                } 
                if (($arre['status']=="PENDING"))
                {
                    $this->messageManager->addError(__("Estado de pago pendiente, seras notificado de cualquier actualizacion"));
                    $this->_redirect('/');
                } 
            }
            
        } catch (InputException $e) {
            echo $e->getMessage();
        }
    }

    public function getTokenUrl()
    {
        return $this->scopeConfig->getValue(
            'payment/bis/token_url',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
