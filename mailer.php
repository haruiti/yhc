<?php
    // My modifications to mailer script from:
    // http://blog.teamtreehouse.com/create-ajax-contact-form
    // Added input sanitizing to prevent injection

    // Only process POST reqeusts.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form fields and remove whitespace.
        $name = strip_tags(trim($_POST["name"]));
				$name = str_replace(array("\r","\n"),array(" "," "),$name);
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $phone = strip_tags(trim($_POST["phone"]));
        $message = trim($_POST["message"]);

        // Check that data was sent to the mailer.
        if ( strlen($name) < 4 OR empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Set a 400 (bad request) response code and exit.
            http_response_code(400);
            echo "Encontramos problemas nos seus dados, não se preocupe, apenas revise e envie novamente.";
            exit;
        }

        // Set the recipient email address.
        // FIXME: Update this to your desired email address.
        $recipient = "contato@yamatohipnoseclinica.com.br";

        // Set the email subject.
        $subject = "Novo contato de $name";

        // Build the email content.
        $email_content = "Nome: $name\n";
        $email_content .= "Email: $email\n";
        $email_content .= "Telefone: $phone\n\n";
        $email_content .= "Mensagem: $message\n";

        // Build the email headers.
        $email_headers = "From: $name <$email>";

        // Send the email.
        if (mail($recipient, $subject, $email_content, $email_headers)) {
            // Set a 200 (okay) response code.
            http_response_code(200);
            echo "Sua mensagem foi enviada! Em breve entraremos em contato.";
        } else {
            // Set a 500 (internal server error) response code.
            http_response_code(500);
            echo "Não foi possível enviar sua mensagem, não se preocupe, tente novamente daqui a alguns minutos.";
        }

    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "Houve um problema no envio, por favor tente novamente daqui a alguns minutos.";
    }

?>
