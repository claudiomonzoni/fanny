<?php
// process-contact.php - Versión corregida y segura

// Configuración de correo electrónico (ajusta el email)
$recipient_email = "claudiomonzoni@hotmail.com"; // 
//$recipient_email = "sacred-space@fanny-vandewiele.com"; // 
$subject = "New contact message from the website";

// Archivo de log en la misma carpeta (asegúrate de que el servidor pueda escribirlo)
$log_file = __DIR__ . '/contact_log.txt';

// Inicializar variables de respuesta
$response = [
    'success' => false,
    'message' => ''
];

// Forzar JSON como respuesta al final (pero lo enviaremos después de la lógica)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Recoger y sanitizar datos del formulario con htmlspecialchars
$fullname = isset($_POST['fullname']) ? htmlspecialchars(trim($_POST['fullname']), ENT_QUOTES, 'UTF-8') : '';
$emailRaw = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$email = $emailRaw ? filter_var($emailRaw, FILTER_VALIDATE_EMAIL) : false;
$phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone']), ENT_QUOTES, 'UTF-8') : '';
$service = isset($_POST['service']) ? htmlspecialchars(trim($_POST['service']), ENT_QUOTES, 'UTF-8') : '';
$message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message']), ENT_QUOTES, 'UTF-8') : '';

// Validaciones básicas
if (empty($fullname) || empty($email) || empty($service)) {
    $response['message'] = 'Please complete all required fields.';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if ($email === false) {
    $response['message'] = 'Please enter a valid email address.';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Construir el cuerpo del correo
$email_body = "New message:\n\n";
$email_body .= "Name: $fullname\n";
$email_body .= "Email: $email\n";
$email_body .= "Phone: $phone\n";
$email_body .= "Service: $service\n";
$email_body .= "Message:\n$message\n";

// Cabeceras del correo
$headers = "From: $email" . "\r\n" .
           "Reply-To: $email" . "\r\n" .
           "X-Mailer: PHP/" . phpversion();

// Intentar enviar con mail() solo si la función existe
$mail_sent = false;
if (function_exists('mail')) {
    // Algunos hosting requieren que el "From" sea un email del dominio; si falla, revisa eso.
    $mail_sent = @mail($recipient_email, $subject, $email_body, $headers);
    if ($mail_sent) {
        $response['success'] = true;
        $response['message'] = 'Thank you for your message! We will contact you soon.';
    } else {
        $response['message'] = "There was a problem sending your message with mail(). We'll log it and notify admin.";
    }
} else {
    $response['message'] = "Mail function is not available on this server. Message will be saved to log.";
}

// Si no se envió por mail(), guardar en log y devolver false (pero útil para debugging)
if (!$mail_sent) {
    $log_entry = "[" . date('Y-m-d H:i:s') . "]\n";
    $log_entry .= "To: $recipient_email\n";
    $log_entry .= "Subject: $subject\n";
    $log_entry .= "Body:\n$email_body\n";
    $log_entry .= "----------------------------------------\n";
    // Intentamos escribir el log (no detenemos la ejecución si falla)
    @file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    // No marcamos success si no fue enviado
}

// Devolver respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;
