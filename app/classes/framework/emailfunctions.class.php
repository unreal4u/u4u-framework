<?php

/**
 * Class que reune varias funciones comunes de aplicar en mails.
 * @package Class
 * @author Camilo Sperberg
 * @version 1.5
 */
class emailfunctions {

    /**
   * Extrae un email de un campo tipo {"Camilo Sperberg" <yo@unreal4u.com>}
   * @param string $mails una cadena tipo {"Camilo Sperberg" <yo@unreal4u.com>}
   * @return array Arreglo que contiene array(0 => {mail} , 1 => {nombre});
   */
    public function extract_mails($mails) {
        $every_one = explode(';', $mails);
        foreach ($every_one as $a) {
            $mail = substr($a, strpos($a, '<') + 1, -1);
            $nombre = trim(substr($a, 1, strpos($a, '<') - 3), ' "');
            $mailing[] = array(
                $mail, $nombre
            );
        }
        return $mailing;
    }

    /**
   * Almacena en la DB un email que debe ser enviada
   * @param array({0} => {mail}, {1} => {nombre}) $hacia
   * @param string $subject
   * @param string $cuerpo
   * @param string $footer
   * @return array
   */
    public function queue_mail($hacia = array(), $subject = '', $cuerpo = '', $footer = '') {
        $res = TRUE;
        if (is_array($hacia) and !empty($subject) and !empty($cuerpo)) {
            if (empty($footer)) {
                $footer = $this->r['he']->c_tag('br') . $this->r['he']->c_tag('br') . '_______________' . $this->r['he']->c_tag('br');
                $footer .= $this->r['he']->c_tag('small', 'Este mail es enviado de forma autom&aacute;tica. Por favor no responda.');
            }
            foreach ($hacia as $a) {
                if (!empty($a[0])) {
                    $resultado = $this->r['db']->query('INSERT INTO sist_mails (hacia_mail,hacia_nombre,subject,cuerpo,footer,fecha_ingreso) VALUES (?,?,?,?,?,NOW())', $a[0], $a[1], $subject, $cuerpo, $footer);
                    if ($resultado == FALSE and $res == TRUE)
                        $res = FALSE;
                }
            }
        }
        return $res;
    }

    /**
   * Convierte una cadena de email en una imagen
   * @param string $email
   * @return string
   *
   * @FIXME no funciona :P verificar por qué.
   */
    public function drawEmail($email) {
        $im = imagecreate(220, 20);
        $black = imagecolorallocate($im, 0, 0, 0);
        $white = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $white);
        $px = (((imagesx($im) - 7.5) * strlen($email)) / 2);
        imagestring($im, 3, $px, 9, $email, $black);
        header("Content-type: image/png");
        imagepng($im);
        return imagedestroy($im);
    }

    /**
   * Revisa si un email es válido o no
   * @param string $email
   * @return bool
   */
    public function validEmail($email) {
        $isValid = true;
        if (version_compare(PHP_VERSION, '5.2.0', '<=')) {
            $atIndex = strrpos($email, "@");
            if (is_bool($atIndex) && !$atIndex)
                $isValid = FALSE;
            else {
                $domain = substr($email, $atIndex + 1);
                $local = substr($email, 0, $atIndex);
                $localLen = strlen($local);
                $domainLen = strlen($domain);
                if ($localLen < 1 || $localLen > 64)
                    $isValid = FALSE;
                else if ($domainLen < 1 || $domainLen > 255)
                    $isValid = FALSE;
                else if ($local[0] == '.' || $local[$localLen - 1] == '.')
                    $isValid = FALSE;
                else if (preg_match('/\\.\\./', $local))
                    $isValid = FALSE;
                else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
                    $isValid = FALSE;
                else if (preg_match('/\\.\\./', $domain))
                    $isValid = FALSE;
                else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
                    if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local)))
                        $isValid = FALSE;
                }
                if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A")))
                    $isValid = FALSE;
            }
        } else
            $isValid = filter_var($email, FILTER_VALIDATE_EMAIL);
        return $isValid;
    }

    public function mandar_mail($hacia = 'unreal4u@chw.net', $subject = 'Mail de la página', $mensaje = 'Prueba', $fromaddress = '', $sIP = '') {
        $eol = PHP_EOL;
        if ($sIP == '')
            $sIP = md5($subject . date('dmY'));
        if ($fromaddress == '')
            $fromaddress = '"Página Web" <noreply@' . $_SERVER['SERVER_NAME'] . '>';
        $headers = 'From: ' . $fromaddress . $eol; // de ...
        $headers .= 'Reply-To: ' . $fromaddress . $eol; // responder a...
        $headers .= 'Return-Receipt-To: ' . $fromaddress . $eol; // responder a...
        $headers .= 'Return-Path: ' . $fromaddress . $eol; // responder a...
        $headers .= 'Message-ID: <' . time() . ' no-reply@' . $_SERVER['SERVER_NAME'] . '>' . $eol; // anti-spam
        $headers .= 'X-Mailer: MyMailer v0.001' . $eol; // info
        $headers .= 'Content-Type: multipart/alternative; boundary="' . $sIP . '"' . $eol . $eol; // anti-spam
        // En caso de que no podamos leer html \\
        $msg = '--' . $sIP . $eol;
        $msg .= 'Content-Type: text/plain; charset=iso-8859-1' . $eol;
        $msg .= 'Content-Transfer-Encoding: 7bit' . $eol . $eol;
        $msg .= 'Este e-mail requiere que active HTML.' . $eol;
        $msg .= 'Si usted esta leyendo esto, por favor actualice su cliente de correo.' . $eol;
        $msg .= 'Acentos y tildes omitidos con intencion.' . $eol;
        $msg .= '------- Mensaje cortado -------' . $eol . $eol;
        // Lo "normal", que podamos leer html \\
        $msg .= '--' . $sIP . $eol;
        $msg .= 'Content-Type: text/html; charset=iso-8859-1' . $eol;
        $msg .= 'Content-Transfer-Encoding: 7bit' . $eol . $eol;
        $msg .= $mensaje . $eol . $eol;
        ini_set('sendmail_from', $fromaddress); // anti-spam
        if (mail($hacia, $subject, wordwrap($msg, 70, $eol), $headers)) {
            ini_restore('sendmail_from');
            return TRUE;
        } else {
            ini_restore('sendmail_from');
            return FALSE;
        }
    }
}

