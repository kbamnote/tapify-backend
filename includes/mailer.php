<?php
/**
 * TAPIFY - Custom SMTP Mailer
 * Lightweight SMTP class - no external library needed
 * Works with Hostinger, Gmail, and most SMTP servers
 */

class TapifyMailer {
    private $host;
    private $port;
    private $username;
    private $password;
    private $secure; // 'ssl' or 'tls'
    private $fromEmail;
    private $fromName;
    private $socket;
    private $debug = false;
    private $lastError = '';

    public function __construct($config) {
        $this->host = $config['host'] ?? 'smtp.hostinger.com';
        $this->port = $config['port'] ?? 465;
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->secure = $config['secure'] ?? 'ssl';
        $this->fromEmail = $config['from_email'] ?? $this->username;
        $this->fromName = $config['from_name'] ?? 'Tapify';
        $this->debug = $config['debug'] ?? false;
    }

    public function send($to, $subject, $htmlBody, $textBody = '', $toName = '') {
        try {
            // Connect
            $protocol = $this->secure === 'ssl' ? 'ssl://' : '';
            $this->socket = @stream_socket_client(
                $protocol . $this->host . ':' . $this->port,
                $errno, $errstr, 30
            );

            if (!$this->socket) {
                throw new Exception("Connection failed: $errstr ($errno)");
            }

            $this->expect('220');

            // EHLO
            $this->send_cmd("EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
            $this->expect('250');

            // STARTTLS for TLS port 587
            if ($this->secure === 'tls') {
                $this->send_cmd('STARTTLS');
                $this->expect('220');
                stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $this->send_cmd("EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
                $this->expect('250');
            }

            // AUTH LOGIN
            $this->send_cmd('AUTH LOGIN');
            $this->expect('334');
            $this->send_cmd(base64_encode($this->username));
            $this->expect('334');
            $this->send_cmd(base64_encode($this->password));
            $this->expect('235');

            // MAIL FROM
            $this->send_cmd("MAIL FROM:<{$this->fromEmail}>");
            $this->expect('250');

            // RCPT TO
            $this->send_cmd("RCPT TO:<$to>");
            $this->expect('250');

            // DATA
            $this->send_cmd('DATA');
            $this->expect('354');

            // Build message
            $boundary = md5(uniqid() . time());
            $headers = [];
            $headers[] = "From: " . $this->encodeHeader($this->fromName) . " <{$this->fromEmail}>";
            $headers[] = "To: " . ($toName ? $this->encodeHeader($toName) . " <$to>" : "<$to>");
            $headers[] = "Subject: " . $this->encodeHeader($subject);
            $headers[] = "Date: " . date('r');
            $headers[] = "Message-ID: <" . md5(uniqid()) . "@{$this->host}>";
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "X-Mailer: TapifyMailer/1.0";

            if (!empty($textBody)) {
                $headers[] = "Content-Type: multipart/alternative; boundary=\"$boundary\"";
                $body = "\r\n--$boundary\r\n";
                $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
                $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
                $body .= $textBody . "\r\n";
                $body .= "\r\n--$boundary\r\n";
                $body .= "Content-Type: text/html; charset=UTF-8\r\n";
                $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
                $body .= $htmlBody . "\r\n";
                $body .= "\r\n--$boundary--\r\n";
            } else {
                $headers[] = "Content-Type: text/html; charset=UTF-8";
                $headers[] = "Content-Transfer-Encoding: 8bit";
                $body = "\r\n" . $htmlBody . "\r\n";
            }

            $message = implode("\r\n", $headers) . $body;

            // Escape leading dots
            $message = preg_replace('/^\./m', '..', $message);

            $this->send_raw($message);
            $this->send_cmd('.');
            $this->expect('250');

            // QUIT
            $this->send_cmd('QUIT');
            fclose($this->socket);
            return true;

        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            if ($this->socket) {
                @fclose($this->socket);
            }
            error_log('TapifyMailer error: ' . $e->getMessage());
            return false;
        }
    }

    public function getLastError() {
        return $this->lastError;
    }

    private function send_cmd($cmd) {
        fwrite($this->socket, $cmd . "\r\n");
        if ($this->debug) error_log("SMTP > $cmd");
    }

    private function send_raw($data) {
        fwrite($this->socket, $data . "\r\n");
    }

    private function expect($expected_code) {
        $response = '';
        $line = fgets($this->socket, 515);
        while ($line !== false) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') break;
            $line = fgets($this->socket, 515);
        }

        if ($this->debug) error_log("SMTP < $response");

        $code = substr(trim($response), 0, 3);
        if ($code !== $expected_code) {
            throw new Exception("Expected $expected_code, got: $response");
        }
        return $response;
    }

    private function encodeHeader($str) {
        if (preg_match('/[\x80-\xFF]/', $str)) {
            return '=?UTF-8?B?' . base64_encode($str) . '?=';
        }
        return $str;
    }
}
