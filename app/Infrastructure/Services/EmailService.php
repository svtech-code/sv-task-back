<?php

namespace App\Infrastructure\Services;

use App\Application\Interfaces\EmailServiceInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService implements EmailServiceInterface
{
    private string $smtpHost;
    private int $smtpPort;
    private string $smtpUsername;
    private string $smtpPassword;
    private string $fromEmail;
    private string $fromName;
    private string $baseUrl;

    public function __construct()
    {
        $this->smtpHost = $_ENV['SMTP_HOST'] ?? '';
        $this->smtpPort = (int)($_ENV['SMTP_PORT'] ?? 587);
        $this->smtpUsername = $_ENV['SMTP_USERNAME'] ?? '';
        $this->smtpPassword = $_ENV['SMTP_PASSWORD'] ?? '';
        $this->fromEmail = $_ENV['MAIL_FROM_ADDRESS'] ?? '';
        $this->fromName = $_ENV['MAIL_FROM_NAME'] ?? 'SV Task';
        $this->baseUrl = $_ENV['APP_URL'] ?? 'http://localhost';
    }

    public function sendVerificationEmail(string $email, string $name, string $token): bool
    {
        try {
            $mail = $this->createMailer();

            // Configurar destinatario
            $mail->addAddress($email, $name);

            // Configurar contenido del email
            $mail->isHTML(true);
            $mail->Subject = 'Verifica tu cuenta - SV Task';

            $verificationUrl = $this->baseUrl . '/verify-email?token=' . $token;

            $mail->Body = $this->getVerificationEmailTemplate($name, $verificationUrl);
            $mail->AltBody = $this->getVerificationEmailPlainText($name, $verificationUrl);

            $result = $mail->send();

            if ($result) {
                error_log("Email de verificación enviado exitosamente a: $email");
            }

            return $result;
        } catch (Exception $e) {
            // Log detallado del error
            error_log("Error enviando email de verificación a $email: " . $e->getMessage());
            error_log("SMTP Error Info: " . ($mail->ErrorInfo ?? 'No disponible'));

            // En desarrollo, mostrar más detalles
            if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
                error_log("SMTP Debug: " . print_r($e->getTrace(), true));
            }

            return false;
        }
    }

    public function sendWelcomeEmail(string $email, string $name): bool
    {
        try {
            $mail = $this->createMailer();

            // Configurar destinatario
            $mail->addAddress($email, $name);

            // Configurar contenido del email
            $mail->isHTML(true);
            $mail->Subject = '¡Bienvenido a SV Task!';

            $mail->Body = $this->getWelcomeEmailTemplate($name);
            $mail->AltBody = $this->getWelcomeEmailPlainText($name);

            $result = $mail->send();

            if ($result) {
                error_log("Email de bienvenida enviado exitosamente a: $email");
            }

            return $result;
        } catch (Exception $e) {
            // Log detallado del error
            error_log("Error enviando email de bienvenida a $email: " . $e->getMessage());
            error_log("SMTP Error Info: " . ($mail->ErrorInfo ?? 'No disponible'));
            return false;
        }
    }

    /**
     * Create and configure PHPMailer instance
     *
     * @return PHPMailer
     * @throws Exception
     */
    private function createMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);

        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host = $this->smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $this->smtpUsername;
        $mail->Password = $this->smtpPassword;

        // Configuración de encriptación basada en el puerto
        if ($this->smtpPort == 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL para puerto 465
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // STARTTLS para puerto 587
        }

        $mail->Port = $this->smtpPort;

        // Timeouts y configuración adicional
        $mail->Timeout = 30; // 30 segundos timeout
        $mail->SMTPKeepAlive = false;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Configuración del remitente
        $mail->setFrom($this->fromEmail, $this->fromName);

        // Configuración adicional
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // Habilitar debugging en desarrollo
        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Debugoutput = 'error_log';
        }

        return $mail;
    }

    /**
     * Get HTML template for verification email
     *
     * @param string $name
     * @param string $verificationUrl
     * @return string
     */
    private function getVerificationEmailTemplate(string $name, string $verificationUrl): string
    {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Verifica tu cuenta</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4F46E5; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .button { display: inline-block; padding: 12px 24px; background-color: #4F46E5; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>SV Task</h1>
                </div>
                <div class='content'>
                    <h2>¡Hola, " . htmlspecialchars($name) . "!</h2>
                    <p>Gracias por registrarte en SV Task. Para completar tu registro, necesitamos verificar tu dirección de correo electrónico.</p>
                    <p>Haz clic en el siguiente botón para verificar tu cuenta:</p>
                    <p style='text-align: center;'>
                        <a href='" . htmlspecialchars($verificationUrl) . "' class='button'>Verificar mi cuenta</a>
                    </p>
                    <p>Si el botón no funciona, puedes copiar y pegar el siguiente enlace en tu navegador:</p>
                    <p style='word-break: break-all; font-size: 12px;'>" . htmlspecialchars($verificationUrl) . "</p>
                    <p><strong>Este enlace expirará en 24 horas.</strong></p>
                    <p>Si no has creado una cuenta en SV Task, puedes ignorar este mensaje.</p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " SV Task. Todos los derechos reservados.</p>
                    <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Get plain text version for verification email
     *
     * @param string $name
     * @param string $verificationUrl
     * @return string
     */
    private function getVerificationEmailPlainText(string $name, string $verificationUrl): string
    {
        return "
        Hola, {$name}!

        Gracias por registrarte en SV Task. Para completar tu registro, necesitamos verificar tu dirección de correo electrónico.

        Visita el siguiente enlace para verificar tu cuenta:
        {$verificationUrl}

        Este enlace expirará en 24 horas.

        Si no has creado una cuenta en SV Task, puedes ignorar este mensaje.

        © " . date('Y') . " SV Task. Todos los derechos reservados.
        Este es un correo automático, por favor no respondas a este mensaje.
        ";
    }

    /**
     * Get HTML template for welcome email
     *
     * @param string $name
     * @return string
     */
    private function getWelcomeEmailTemplate(string $name): string
    {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>¡Bienvenido a SV Task!</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #10B981; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .button { display: inline-block; padding: 12px 24px; background-color: #10B981; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>¡Bienvenido a SV Task!</h1>
                </div>
                <div class='content'>
                    <h2>¡Hola, " . htmlspecialchars($name) . "!</h2>
                    <p>¡Tu cuenta ha sido verificada exitosamente! Ahora puedes comenzar a usar SV Task para gestionar tus tareas y proyectos.</p>
                    <h3>¿Qué puedes hacer ahora?</h3>
                    <ul>
                        <li>Crear y organizar tus tareas</li>
                        <li>Colaborar con otros usuarios</li>
                        <li>Hacer seguimiento de tu progreso</li>
                        <li>Recibir notificaciones importantes</li>
                    </ul>
                    <p style='text-align: center;'>
                        <a href='" . $this->baseUrl . "' class='button'>Comenzar ahora</a>
                    </p>
                    <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos.</p>
                    <p>¡Gracias por unirte a SV Task!</p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " SV Task. Todos los derechos reservados.</p>
                    <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Get plain text version for welcome email
     *
     * @param string $name
     * @return string
     */
    private function getWelcomeEmailPlainText(string $name): string
    {
        return "
        ¡Bienvenido a SV Task!

        Hola, {$name}!

        ¡Tu cuenta ha sido verificada exitosamente! Ahora puedes comenzar a usar SV Task para gestionar tus tareas y proyectos.

        ¿Qué puedes hacer ahora?
        - Crear y organizar tus tareas
        - Colaborar con otros usuarios
        - Hacer seguimiento de tu progreso
        - Recibir notificaciones importantes

        Visita: {$this->baseUrl}

        Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos.

        ¡Gracias por unirte a SV Task!

        © " . date('Y') . " SV Task. Todos los derechos reservados.
        Este es un correo automático, por favor no respondas a este mensaje.
        ";
    }
}
