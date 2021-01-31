<?php

    $servername = "localhost";
    $username = "u894261163_admin";
    $password = "H@ru709192";
    $notificationCode = preg_replace('/[^[:alnum:]-]/','', $_POST["notificationCode"]);

    $data['token'] = '59a3e556-0e24-45b8-a452-9dfc29ec9699f8f91a004352add633ef8e1556b1b7d3348e-ba7b-4603-b48e-03655c4cde73'; //Token Produção
    //$data['token'] ='82D12373F0BA43959C3515AC77E910F5'; //Token Sandbox Roberto
    //$data['token'] ='EF2BB353DBC548D9A4BEFAEA00F1603F'; //Token Sandbox Lokal
    $data['email'] = 'simonejarouche@hotmail.com';
    $data = http_build_query($data);
    $url = 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/'.$notificationCode.'?'.$data;
  
    $curl = curl_init($url);
    
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    $xml= curl_exec($curl);
    curl_close($curl);
    $xml= simplexml_load_string($xml);

    $reference = $xml->reference;
    $status =$xml->status;

    if($reference && $status){
        if ($status == '3'){
            try {
                $conn = new PDO("mysql:host=$servername;dbname=u894261163_app", $username, $password);
                // set the PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
            }
            catch(PDOException $e)
            {
                echo "Connection failed: " . $e->getMessage();
                die();
            }

            $id_appointment = $reference;
      
            //Busca hora incio e fim contratados
            $sql = "SELECT id_pagseguro, email, start_datetime, end_datetime, finished FROM ea_payments where id_appointment = '".$id_appointment."'";
            
            foreach ($conn->query($sql) as $row) {
              $id_pagseguro = $row['id_pagseguro'];
              $emailPayment = $row['email'];
              $start_datetime = $row['start_datetime'];
              $end_datetime = $row['end_datetime'];
              $finished = $row['finished'];
            }
      
            //insere true para que não repita as operações de BD ao dar um reload na página 
            $sql = "UPDATE ea_payments SET finished = '1' WHERE id_appointment= '".$id_appointment."'";
        
            // Prepare statement
            $stmt = $conn->prepare($sql);
        
            // execute the query
            $stmt->execute();
      
      
            //insere transaction code no id_pagseguro para qu enão possa ser duplicado ao dar um reload na página 
            $sql2 = "UPDATE ea_appointments SET start_datetime='".$start_datetime. "' , end_datetime ='".$end_datetime. "' WHERE id = '".$id_appointment."'";
      
            // Prepare statement
            $stmt2 = $conn->prepare($sql2);
        
            // execute the query
            $stmt2->execute();
      
            $conn = null;

        }
        
    }