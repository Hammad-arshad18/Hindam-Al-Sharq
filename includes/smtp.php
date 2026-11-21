<?php
declare(strict_types=1);

/**
 * Sends one plain-text message through an SMTP server (SSL, AUTH LOGIN).
 * Returns null on success, or a short reason on failure. Never includes the password.
 */
function smtp_send(array $cfg, string $to, string $subject, string $text, string $replyTo = ''): ?string
{
    $host = (string) ($cfg['host'] ?? '');
    $port = (int) ($cfg['port'] ?? 465);
    $user = (string) ($cfg['username'] ?? '');
    $pass = (string) ($cfg['password'] ?? '');
    $from = (string) ($cfg['from'] ?? $user);
    $fromName = (string) ($cfg['from_name'] ?? '');

    if ($host === '' || $user === '' || $pass === '' || $from === '' || $to === '') {
        return 'incomplete settings';
    }

    $errno = 0;
    $errstr = '';
    $fp = @stream_socket_client(
        'ssl://' . $host . ':' . $port,
        $errno,
        $errstr,
        20,
        STREAM_CLIENT_CONNECT
    );
    if (!$fp) {
        return 'could not connect';
    }
    stream_set_timeout($fp, 20);

    $read = static function ($fp): string {
        $data = '';
        while (($line = fgets($fp, 515)) !== false) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
            $meta = stream_get_meta_data($fp);
            if (!empty($meta['timed_out'])) {
                break;
            }
        }
        return $data;
    };

    $command = static function ($fp, ?string $line, array $ok) use ($read): ?string {
        if ($line !== null && fwrite($fp, $line . "\r\n") === false) {
            return 'could not write';
        }
        $resp = $read($fp);
        $code = (int) substr($resp, 0, 3);
        if (!in_array($code, $ok, true)) {
            $brief = trim(strtok($resp, "\r\n") ?: '');
            return $brief !== '' ? $brief : 'unexpected reply';
        }
        return null;
    };

    $fail = $command($fp, null, [220]);
    if ($fail === null) {
        $ehlo = preg_replace('/[^A-Za-z0-9.-]/', '', (string) gethostname()) ?: 'localhost';
        $fail = $command($fp, 'EHLO ' . $ehlo, [250]);
    }
    if ($fail === null) {
        $fail = $command($fp, 'AUTH LOGIN', [334]);
    }
    if ($fail === null) {
        $fail = $command($fp, base64_encode($user), [334]);
    }
    if ($fail === null) {
        $fail = $command($fp, base64_encode($pass), [235]);
    }
    if ($fail === null) {
        $fail = $command($fp, 'MAIL FROM:<' . $from . '>', [250]);
    }
    if ($fail === null) {
        $fail = $command($fp, 'RCPT TO:<' . $to . '>', [250, 251]);
    }
    if ($fail === null) {
        $fail = $command($fp, 'DATA', [354]);
    }
    if ($fail === null) {
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $encodedName = $fromName !== ''
            ? '=?UTF-8?B?' . base64_encode($fromName) . '?= '
            : '';
        $headers = [
            'Date: ' . date('r'),
            'From: ' . $encodedName . '<' . $from . '>',
            'To: <' . $to . '>',
            'Subject: ' . $encodedSubject,
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
        ];
        if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $headers[] = 'Reply-To: <' . $replyTo . '>';
        }
        $body = preg_replace("/\r\n|\r|\n/", "\r\n", $text) ?? $text;
        $body = preg_replace('/^\./m', '..', $body) ?? $body;
        $payload = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
        $fail = $command($fp, $payload, [250]);
    }
    $command($fp, 'QUIT', [221]);
    fclose($fp);

    return $fail;
}
