
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

use \EA\Engine\Types\Text;
use \EA\Engine\Types\Email;
use \EA\Engine\Types\Url;

/**
 * Appointments Controller
 *
 * @package Controllers
 */

 if(!isset($_GET['id'])){
    echo "<!DOCTYPE html>";
    echo "<html>";
    echo "<head>";
    echo "<meta charset=\"utf-8\">";
    echo "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
    echo "  <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css\">";
    echo "  <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js\"></script>";
    echo "  <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js\"></script>";
    echo "</head>";
    echo "<body>";

    echo "<div id=\"message\" class=\"alert alert-danger\" role=\"alert\">";
    echo "  <h4 class=\"alert-heading\">ID não informado!</h4>";
    echo "  <p>Houve um erro no processo de conclusão do pagamento</p>";
    echo "  <p class=\"mb-0\">Entre em contato com a Lokal Office e informe o problema.</p>";
    echo "</div>";

    echo "</body>";
    echo "</html>";

    return;
 }
$transactionCode=$_GET['id'];
$servername = "localhost";
$username = "u894261163_admin";
$password = "H@ru709192";
$id='';
$id_pagseguro='';
$id_appointmen='';
$first='';
$finished=0;
$email='';
$emailPayment='';

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


//$token ='82D12373F0BA43959C3515AC77E910F5'; //Token Sandbox Roberto
//$token ='EF2BB353DBC548D9A4BEFAEA00F1603F'; //Token Sandbox Lokal

$token ='59a3e556-0e24-45b8-a452-9dfc29ec9699f8f91a004352add633ef8e1556b1b7d3348e-ba7b-4603-b48e-03655c4cde73'; // Token produção
$email = 'simonejarouche@hotmail.com';

//$url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/'.$transactionCode.'?email='.$email.'&token='.$token; //UEL Sandbox
$url = 'https://ws.pagseguro.uol.com.br/v2/transactions/'.$transactionCode.'?email='.$email.'&token='.$token; //URL Produção



      //Inicia o cURL
      $ch = curl_init($url);
  
      //Pede o que retorne o resultado como string
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  
      //Ignora certificado SSL
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  
      //Manda executar a requisição
      $data = curl_exec($ch);
  
      //Fecha a conexão para economizar recursos do servidor
      curl_close($ch);
  

      if ($data=='Not Found') {
        echo "<!DOCTYPE html>";
          echo "<html>";
          echo "<head>";
          echo "<meta charset=\"utf-8\">";
          echo "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
          echo "  <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css\">";
          echo "  <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js\"></script>";
          echo "  <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js\"></script>";
          echo "</head>";
          echo "<body>";
          
          echo "<div id=\"message\" class=\"alert alert-danger\" role=\"alert\">";
          echo "  <h4 class=\"alert-heading\">Problema no processo de pagamento!</h4>";
          echo "  <p>Houve um erro no processo de conclusão do pagamento</p>";
          echo "  <p class=\"mb-0\">Entre em contato com a Lokal Office e informe o problema.</p>";
          echo "</div>";
          
          echo "</body>";
          echo "</html>";

          return;
      }
    
      try{

      $data = simplexml_load_string($data);

      }
      catch(Exception $e)
      {
          echo "<!DOCTYPE html>";
          echo "<html>";
          echo "<head>";
          echo "<meta charset=\"utf-8\">";
          echo "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
          echo "  <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css\">";
          echo "  <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js\"></script>";
          echo "  <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js\"></script>";
          echo "</head>";
          echo "<body>";
          
          echo "<div id=\"message\" class=\"alert alert-danger\" role=\"alert\">";
          echo "  <h4 class=\"alert-heading\">Problema no processo de pagamento!</h4>";
          echo "  <p>Houve um erro no processo de conclusão do pagamento</p>";
          echo "  <p class=\"mb-0\">Entre em contato com a Lokal Office e informe o problema.</p>";
          echo "</div>";
          
          echo "</body>";
          echo "</html>";

        return;
      }

      $data = json_encode($data);
      $data = json_decode($data);

      $id_appointment = $data->reference;
      
      //Busca hora incio e fim contratados
      $sql = "SELECT id_pagseguro, email, start_datetime, end_datetime, finished FROM ea_payments where id_appointment = '".$id_appointment."'";
      
      foreach ($conn->query($sql) as $row) {
        $id_pagseguro = $row['id_pagseguro'];
        $emailPayment = $row['email'];
        $start_datetime = $row['start_datetime'];
        $end_datetime = $row['end_datetime'];
        $finished = $row['finished'];
      }

      if($finished==1){

        echo "<!DOCTYPE html>";
        echo "<html>";
        echo "<head>";
        echo "<meta charset=\"utf-8\">";
        echo "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
        echo "  <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css\">";
        echo "  <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js\"></script>";
        echo "  <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js\"></script>";
        echo "</head>";
        echo "<body>";
        
        echo "<div id=\"message\" class=\"alert alert-info\" role=\"alert\">";
        echo "  <h4 class=\"alert-heading\">Seu agendamento já foi computado!</h4>";
        echo "  <p>O pagamento foi realizado e o agendamento foi concluido!</p>";
        echo "  <p class=\"mb-0\">Acesse seu agendamento pelo link <a href=\"http://lokaloffice.yamatohipnoseclinica.com.br/ea_appointment_manager/login\">Potal Lokal Office</a></p>";
        echo "</div>";
        
        echo "</body>";
        echo "</html>";

      return;
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

        
?>
                  


<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  
</head>
<body>

<div id="message" class="alert alert-success" role="alert">
  <h4 class="alert-heading">Pagamento Realizado!</h4>
  <p>Sua reserva para a data <?php echo $start_datetime; ?> foi concluída com sucesso!</p>
  <p class="mb-0">Acesse seu agendamento pelo link <a href="http://lokaloffice.yamatohipnoseclinica.com.br/ea_appointment_manager/login">Potal Lokal Office</a></p>;
</div>

</body>
</html>
