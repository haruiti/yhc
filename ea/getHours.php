<?php defined('BASEPATH');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Captcha Controller
 *
 * @package Controllers
 */
$transactionCode=$_GET['id'];


$token ='82D12373F0BA43959C3515AC77E910F5'; //Token Sandbox
$email = 'haruiti@hotmail.com';
$url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/'.$transactionCode.'?email='.$email.'&token='.$token;

//$url = 'https://ws.pagseguro.uol.com.br/v2/checkout'; //URL Produção



    //Inicia o cURL
    $ch = curl_init($url);
 
    //Pede o que retorne o resultado como string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
    //Envia cabeçalhos (Caso tenha)
    // if(count($header) > 0) {
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    // }
 
    //Envia post (Caso tenha)
    if($post !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
 
    //Ignora certificado SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
    //Manda executar a requisição
    $data = curl_exec($ch);
 
    //Fecha a conexão para economizar recursos do servidor
    curl_close($ch);

    $data = simplexml_load_string($data);

    $data = json_encode($data);
    $data = json_decode($data);
    //echo $data->items->item->id;
var_dump($data) ;
 
 
    //Retorna o resultado da requisição
?>




   