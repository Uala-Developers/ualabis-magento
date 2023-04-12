<?php

namespace Uala\Bis\Controller\Payment;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\InputException;

class Credentials extends \Magento\Framework\App\Action\Action
{
    public $context;
    public $writer;

    public function __construct(
        Context $context,
        WriterInterface $writer
    ) {
        $this->context = $context;
        $this->writer = $writer;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $postData = $this->getRequest()->getParams();
            if (!empty($postData)) {
                //check user
                $url = "https://checkout-bff.prod.adquirencia.ar.ua.la/1/apps/authorize?state=".urldecode($postData["state"])."&code=".urldecode($postData["code"]);
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $headers = array("Content-Type: application/json","Accept: application/json");
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                $resp = curl_exec($curl);
                $arre=json_decode($resp, true);
                curl_close($curl);    
                $scope = "default";
                if (isset($arre["username"])) {
                    $this->writer->save('payment/bis/user_name', $arre["username"], $scope);
                    $this->writer->save('payment/bis/client_id', $arre["client_id"], $scope);
                    $this->writer->save('payment/bis/client_secret_id', $arre["client_secret_id"], $scope);
                    $this->writer->save('payment/bis/order_url', "https://checkout.prod.ua.la/1/order", $scope);
                    $this->writer->save('payment/bis/token_url', "https://auth.prod.ua.la/1/auth/token", $scope);
                    $this->writer->save('payment/bis/checkout_url', "https://checkout.prod.ua.la/1/checkout", $scope);
                    $this->writer->save('payment/bis/credentials', "prod", $scope);
                    echo "Credenciales de produccion configuradas correctamente<br><br>";
                    echo "<b>Recuerda presionar SAVE CONFIG en la otra ventana</b><br><br>";
                    echo '<input type="button" name="cancelvalue" value="CERRAR" onClick="self.close()">'; 
                } else {
                    echo "Credenciales NO configuradas correctamente, vuelve a intentarlo<br><br>";
                    echo '<input type="button" name="cancelvalue" value="REINTENTAR" onClick="location.href='."'https://web.prod.adquirencia.ar.ua.la/?callbackUrl=https://'+window.location.hostname+'/bis/payment/credentials'".';">'; 
                }          
            }    
        } catch (InputException $e) {
            echo $e->getMessage();
        }
    }
}
