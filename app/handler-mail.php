<?php

function enviar_mail($todo = array()) {
  $retorno = array('exitoso' => 0, 'falladas' => 0);
  $malo    = array();
  if (is_array($todo)) {
    include(ROUT.'phpmailer/class.phpmailer.php');
    $mailer = new PHPMailer(true);
    $mailer->isSMTP();
    $mailer->CharSet       = CHARSET;
    $mailer->SMTPKeepAlive = TRUE;
    $mailer->SMTPAuth      = TRUE;
    $mailer->SMTPSecure    = 'ssl';
    $mailer->Host          = MAIL_SMTP_SERVER;
    $mailer->Port          = 465;
    $mailer->Username      = MAIL_SMTP_USER;
    $mailer->Password      = MAIL_SMTP_PASS;
    $mailer->From          = MAIL_FROM_MAIL;
    $mailer->FromName      = MAIL_FROM_NAME;
    $mailer->AddReplyTo(MAIL_FROM_MAIL,MAIL_FROM_NAME);
    $mailer->AltBody = 'Usted debe actualizar su cliente de email para poder ver este mensaje';
    $mailer->Wordwrap = 80;
    foreach($todo AS $a) {
      if (!empty($a['hacia']) AND !empty($a['subject']) AND !empty($a['cuerpo'])) {
        //$mailer->AddAddress($a['hacia'][0],$a['hacia'][1]);
        //if (!empty($a['hacia_cc'])) $mailer->AddCC($a['hacia_cc'][0],$a['hacia_cc'][1]);
        $mailer->AddAddress('yo@unreal4u.com','Camilo Sperberg');
        $mailer->Subject = $a['subject'];
        if (empty($a['footer'])) $a['footer'] = '<br /><br />_________________________________________<p><small>Este email es enviado autom&aacute;ticamente. <strong>No responda</strong> este email, puesto que nadie lo revisar&aacute;. Para cualquier acci&oacute;n que quiera hacer, cont&aacute;ctese directamente con el Colegio</small></p><p><small>Usted est&aacute; recibiendo este informe debido a que de esa manera lo tiene establecido en <a href="'.HOME.'opciones">sus opciones</a>. Puede visitar esa p&aacute;gina para ajustar la frecuencia con la cual desea recibir estos y otros reportes.</small></p><p><small>Los tildes en el t&iacute;tulo de este email fueron omitidos intencionalmente</small></p>';
        $mailer->Body = $a['cuerpo'].$a['footer'];
        try { $exitoso = $mailer->Send(); }
        catch (Exception $e) { $malo[] = 'El mail hacia '.$a['hacia'][0].' con subject "'.$a['subject'].'" no se pudo enviar. Error: '.$e->getMessage(); }
        if (!$exitoso) $retorno['falladas']++;
        else $retorno['exitoso']++;
      }
    }
    $mailer->SmtpClose();
  }
  return $retorno;
}
