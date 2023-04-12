<?php
namespace Uala\Bis\Controller\Payment;

use \Magento\Framework\App\Action\Context;
use \Magento\Sales\Model\Order;
use \Magento\Framework\Exception\NotFoundException;
use \Magento\Sales\Model\Service\InvoiceService;
use \Magento\Framework\DB\Transaction; 
use \Magento\Framework\App\RequestInterface;
use \Magento\Framework\App\Request\InvalidRequestException;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\InputException;

class Notification extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\CsrfAwareActionInterface
{
    public $context;
    protected $order;
    protected $invoiceService;
    protected $transaction;
    protected $scopeConfig;
    
        /**
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @param RequestInterface $request
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    public function __construct(
        Context $context,
        Order $order,
        InvoiceService $invoiceService,
        Transaction $transaction,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->context = $context;
        $this->order = $order;
        $this->invoiceService = $invoiceService;
        $this->transaction    = $transaction;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function execute()
    {
   
    
        try {
            
            $postData = json_decode($this->getRequest()->getContent(), true);
            $uuid = $postData['uuid'];
            $status = $postData['status'];
            $order = $this->order->load($uuid,"x_forwarded_for");

            if (($status=="PROCESSED") or ($status=="APPROVED"))
                {
                    $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
                    if ($order->canInvoice()) {
                        $invoice = $this->invoiceService->prepareInvoice($this->order);
                        $invoice->register();
                        $invoice->save();
                        $transactionSave = $this->transaction->addObject($invoice)->addObject($invoice->getOrder());
                        $transactionSave->save();
                        $order->addStatusHistoryComment(__('Invoiced', $invoice->getId()))->setIsCustomerNotified(false)->save();
                    }
                    $message =  __('Notificacion Automatica de Uala: el pago fue aprobado');
                    $message .= __('<br/> Orden Uala: '.$postData['ref_number']);
                    $message .= __('<br/> UUID: '.$postData['uuid']);
                    $message .= __('<br/> Status: '.$postData['status']);
                    $order->addStatusToHistory(\Magento\Sales\Model\Order::STATE_PROCESSING, $message, true);
                    $order->save();
                } else
                {
                    if (($status=="REJECTED"))
                        {
                            $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true);
                            $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
                            foreach ($order->getAllItems() as $item) { // Cancel order items
                                $item->cancel();
                            }
                            $order->save();
                        }  
                }        
        } catch (InputException $e) {
            echo $e->getMessage();
        }
    }
}
