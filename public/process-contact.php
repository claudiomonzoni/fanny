<?php
// Configuración de correo electrónico
$recipient_email = "your-email@example.com"; // Cambia esto por tu correo electrónico
$subject = "Nuevo mensaje de contacto desde el sitio web";

// Inicializar variables de respuesta
$response = array(
    'success' => false,
    'message' => ''
);

// Verificar si es una solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y sanitizar datos del formulario
    $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $service = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    
    // Validar datos
    if (empty($fullname) || empty($email) || empty($service)) {
        $response['message'] = "Por favor, complete todos los campos requeridos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Por favor, ingrese un correo electrónico válido.";
    } else {
        // Construir el cuerpo del correo
        $email_body = "Nuevo mensaje de contacto:\n\n";
        $email_body .= "Nombre: $fullname\n";
        $email_body .= "Email: $email\n";
        $email_body .= "Teléfono: $phone\n";
        $email_body .= "Servicio: $service\n";
        $email_body .= "Mensaje:\n$message\n";
        
        // Cabeceras del correo
        $headers = "From: $email" . "\r\n" .
                   "Reply-To: $email" . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();
        
        // Enviar correo
        if (mail($recipient_email, $subject, $email_body, $headers)) {
            $response['success'] = true;
            $response['message'] = "¡Gracias por tu mensaje! Te contactaremos pronto.";
        } else {
            $response['message'] = "Lo sentimos, hubo un problema al enviar tu mensaje. Por favor, inténtalo de nuevo más tarde.";
        }
    }
} else {
    $response['message'] = "Método de solicitud no válido.";
}

// Devolver respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>