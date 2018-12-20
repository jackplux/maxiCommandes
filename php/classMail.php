<?php if(!defined('PLX_ROOT')) exit; // maybe from mlemos :https://www.phpclasses.org/package/3169-PHP-Decode-MIME-e-mail-messages.html  https://www.phpclasses.org/browse/file/14672.html
class Mail {
 var $fp = NULL;
 var $check_msg = '';
 var $charset = '';
 var $errno;
 var $EOL = "\r\n";//chr(13) . chr(10)  www.mimevalidator.net ok
 var $sendto = array();
 var $from, $msubject;
 var $acc = array();
 var $abcc = array();
 var $aattach = array();
 var $alang = array(
'checkMail' => "Class Mail, method Mail : Adresse Invalide",
'checkExp_ok_msg' => "Votre courriel, destiné à %s, a été envoyé!",
'checkExp_ok_sbj' => "Votre courriel au sujet de: %s...",
'checkExp_unknown' => " - [Adresse (%s) non reconnue!]",
'From_error' => "Class Mail: Erreur, From n'est pas de la bonne forme"
  );//default lang
 var $priorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );
 /*
 * Mail contructor
 */
 function Mail($check=false, $charset='utf-8') {
  $this->autoCheck($check);
  $this->charset = $charset;
 }
 /*
 * Load Lang @array
 */
 function lang($alang) {
  if(is_array($alang))
   $this->alang = $alang;
 }
 //-----------------------------------------------------------------------------------------------------
 // Fonctions nécessaires au test de l'adresse e-mail de l'expéditeur:_
 //-----------------------------------------------------------------------------------------------------
 /*
 * Envoie les données ($data) sur l'objet de connection (this->fp).
 */
 function mySend($data){
  echo nl2br($data)."<br>$this->EOL";
  fputs($this->fp, $data.$this->EOL);
  $this->recv();
 }
 /*
 * Effectue la réception de données de l'objet de connection (this->fp).
 * En cas d'echec de commande ou d'adresse non valide (donc pour une réponse telenet de type 512),
 * on effetcue une un print de la réponse en rouge. Sinon, la réponse est imprimée en bleu.
 */
 function recv(){
  $response=fgets($this->fp, 512);
  list($this->errno, $errmsg) = explode(" ", $response);
  if($this->errno<500){
   //echo "<font color=\"black\">$response</font>$this->EOL<br />";//ok
  }else{
   echo "<font color=\"red\">$response</font>$this->EOL<br />";
   exit;
  }
 }
 /*
 * Ouvre une connection sur un server.
 */
 function open($adressServer, $port, $ti=2){
  $this->fp = @fsockopen($adressServer, $port, $this->errno, $errstr, $ti);
  if(!$this->fp){
   //utilisé pour le debug...
   //echo "<b>echec d'ouverture $adressServer</b><br><font color=\"red\">$errstr ($errno).</font><hr>$this->EOL";
   return;
  }
  $this->recv();
 }
 /*
 * Fermeture de la connection.
 */
 function close(){
  fclose($this->fp);
 }
 /* * Active ou désactive le contrôle (basic) des adresses e-mail (to, cc, bcc, etc.).
 */
 function autoCheck( $bool ) {
  if( $bool )
   $this->checkAddress = true;
  else
   $this->checkAddress = false;
 }
 /* Obsolete ou a MAJ
 * Retourne vrai si l'adresse ($address) e-mail respecte une certaine syntaxe.
 * Ce test n'est, en fait, pas utilisé, car trop restrictif; en effet, il restreint l'ensemble des adresses
 * à des extensions de domaines spécifiques (net, com, gov, mil, etc.). Il paraît plus sensé de placer ce
 * genre de test dans un JavaScript. En effet, un JavaScript permet d'éviter de 'poster' le formulaire si
 * le test n'est pas réussi.
 *
 * regex from Manuel Lemos (mlemos@acm.org)
 */
 function ValidEmail($address) {
  return checkMail($address);#compat
  if( ereg( ".*<(.+)>", $address, $regs ) ) {
   $address = $regs[1];
  }
  if(ereg( "^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)\$",$address) ){
   return FALSE;
  }
  else
   return FALSE;
 }
 /**
  * Méthode qui vérifie le bon formatage d'une adresse email
  * src : Pluxml.org plxutil class
  * @param	mail		adresse email à vérifier
  * @return	boolean		vrai si adresse email bien formatée
  **/
 function checkMail($mail) {

  if (strlen($mail) > 80)
   return false;
  return preg_match('/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|("[^"]+"))@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\])|(([a-zA-Z\d\-]+\.)+[a-zA-Z]{2,}))$/', $mail);
 }

 /*
 * Contrôle la validité d'une ou de plusieurs adresses ($aad - un tableau d'adresses ou une seule adresse).
 * Si une erreur est détectée, un message d'erreur est imprimé.
 *
 * Comme cette fonction fait appel à la fonction 'ValidEmail', elle n'est pas non plus utilisée lors de
 * l'envoi d'un e-mail.
 */
 function CheckAdresses( $aad ) {
  for($i=0;$i< sizeof( $aad); $i++ ) {
   if( ! $this->checkMail( $aad[$i]) ) {
    echo $this->alang['checkMail'].' '.$aad[$i];
    exit;
   }
  }
 }
 /*
 * Contrôle l'adresse de l'expéditeur en lançant des commandes telnet au serveur mail.
 * Si le serveur n'est pas reconnu (donc connection telnet pas possible), alors un message
 * de mise en garde est inséré dans le sujet du mail.
 * une fois le test effectué, un e-mail de confirmation est envoyé à l'expéditeur (même si
 * le serveur mail n'est pas reconnu).
 *
 * Ce test n'as pas pour but d'empêcher l'envoi d'e-mail, mais de permettre au destinataire
 * (ici le webmaster) de trier rapidement les mails qu'il reçoit depuis le formulaire.
 */
 function checkExp($adresse, $sbj, $dest, $dest_name){
  $terminator=".";
  $ok_msg = sprintf($this->alang['checkExp_ok_msg'].$this->EOL.$this->EOL, $dest_name);
  $ok_sbj = sprintf($this->alang['checkExp_ok_sbj'],$sbj);
  $check = false;
  $domain = substr(strstr("$adresse", "@"),1);
  $this->open("pop.".$domain, 110);
  if(!$this->fp)
   $this->open("mail.".$domain, 110);
  if(!$this->fp)
   $this->open("imap.".$domain, 143);
  if(!$this->fp)
   $this->open("imap.".$domain, 220);
  if(!$this->fp)//ajout https en test
   $this->open("https://.".$domain, 80);
  if(!$this->fp)//ajout http en test
   $this->open("http://.".$domain, 80);
  if($this->fp){
   /*
   Les commandes suivante ne sont pas nécessaires; ce qui nous intéresse c'est si le domaine existe.
   Elles peuvent être utilisées pour d'autrea typea de vérification... ...
   */
   //$this->mySend("HELO $domain"); //$this->mySend("VRFY $adresse");
   //$this->mySend("QUIT");
   //$this->close();
   $check = true;
  }
  else{//le domaine n'existe pas!
   $this->check_msg = sprintf($this->alang['checkExp_unknown'],$adresse);
   $check = false;
  }
  /*
  On envoie quand même un mail à l'expéditeur...
  Au cas oû l'utilisateur a une adresse chez Caramail, par exemple!
  */
  // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini a text/html
  // En-têtes additionnels
  // Pas besoin de faire apparaître le destinataire plusieurs fois!
  // $headers .= 'To: '. $adresse . $this->EOL;//~ $msg=StripSlashes(utf8_decode($messRetour));#4 DEST_NAME
  $headers  = 'From: ' . $dest_name .' <'. $dest . '>' . $this->EOL;
  $headers .= 'Reply-To: ' . $dest_name .' <'. $dest . '>' . $this->EOL;
  $headers .= 'Subject: ' . $ok_sbj . $this->EOL;
  $headers .= 'X-Mailer: PHP/' . phpversion() . $this->EOL;
  $headers .= 'MIME-Version: 1.0' . $this->EOL;
  $headers .= 'Content-Type: text/plain; charset=' . $this->charset . $this->EOL . 'Content-Transfer-Encoding: 7bit' . str_repeat($this->EOL, 2);
# $headers .= 'Content-type: text/plain; charset=' .$this->charset.$this->EOL;
  //~ if ($this->charset == 'utf-8')
   //~ $headers = utf8_decode($headers);//WIT BAD à in dest_name fix RC1.0
  $headers = mb_convert_encoding($headers,'7bit',$this->charset);//WIT BAD à in dest_name fix RC1
  mail($adresse, $ok_sbj, $ok_msg, $headers);//on envois la notif au demandant

  return $check;// On retourne l'état du contrôle:_
 }
 //-----------------------------------------------------------------------------------------------------
 // Fonction nécessaires à la construction des diférentes parties de l'e-mail:_
 //-----------------------------------------------------------------------------------------------------
 /*
 * Défini le champs 'objet' de l'e-mail.
 */
 function Subject( $subject ) {
  $this->msubject = strtr( $subject.$this->check_msg, $this->EOL , ' ' );
  $this->msubject = strtr( $subject.$this->check_msg, chr(13) , ' ' );
  $this->msubject = strtr( $subject.$this->check_msg, chr(10) , ' ' );
 }
 /*
 * Défini l'expéditeur.
 */
 function From( $from, $sbj, $dest, $dest_n ) {
  if( ! is_string($from) ) {
   echo $this->alang['From_error'];
   exit;
  }
  $this->from = $from;
  if( $this->checkAddress == true )//INTEST 
   $this->checkExp($this->from, $sbj, $dest, $dest_n);
 }
 /*
 * Défini le(s) destinataire(s).
 */
 function To( $to ) {
  // TODO : test validité sur to si le destinataire n'est pas fixé.
  if( is_array( $to ) )
   $this->sendto = $to;
  else
   $this->sendto[] = $to;
  if( $this->checkAddress == true )
   $this->CheckAdresses( $this->sendto );
 }
 /*
 * Défini le/s destinatair/s de la copie conforme.
 */
 function Cc( $cc ) {
  if( is_array($cc) )
   $this->acc= $cc;
  else
   $this->acc[]= $cc;
  if( $this->checkAddress == true )
   $this->CheckAdresses( $this->acc );
 }
 /*
 * Défini le/s destinataire/s de la copie cachée.
 */
 function Bcc( $bcc ) {
  if( is_array($bcc) ) {
   $this->abcc = $bcc;
  } else {
   $this->abcc[]= $bcc;
  }
  if( $this->checkAddress == true )
   $this->CheckAdresses( $this->abcc );
 }
 /*
 * Défini le corps du message. */
 function Body( $body ) {
  $this->body = $body;
 }
 /*
 * Effectue le formatage et l'envoi de l'e-mail
 */
 function Send($save = FALSE, $send = TRUE) {
# $e = new \Exception;var_dump($e->getTraceAsString());#debug
  // construction de l'en-tête:_
  if(empty($this->headers))
   $this->_build_headers();//+ attach
  $SR = array(array('@','.'),array('_AT_','_dot_'));
  // envoie du mail aux destinataires principaux:_
  for($i = 0; $i < sizeof($this->sendto); $i++) {
   if($send){
//les.pages.perso.chez.free.fr/l-art-d-envoyer-des-mails-depuis-les-pp-de-free.io
    $start_time = time();
    $res = @mail($this->sendto[$i], $this->msubject, $this->body, $this->headers);//Warning:  mail() [function.mail]: 80& in maxiContact/php/classMail.php (OLD free php 5.1)
    $time = time() - $start_time;//free.fr
#   $resultat = mail($this->sendto[$i], $this->msubject, $this->headers . $this->body);//no? .
#   $resultat = mail($to             , $subject       , $message  , $additional_headers, $additional_parameters);//exemple
    if(strpos($_SERVER["HTTP_HOST"],'free.fr')) $res = $res & ($time>1);//free.fr
    //~ $res = mail($this->sendto[$i], $this->msubject, $this->body, $this->headers);//standard
   }else{$res=FALSE;}
#  var_dump("send mail($i)",$this->sendto[$i], $this->msubject, $this->body, $this->headers,$this);#local debug
   $ok = $res?'1':'0';
   if($save)
    file_put_contents($save.$ok.'~'.time().'-'.str_replace($SR[0],$SR[1],$this->from).'-'.str_replace($SR[0],$SR[1],$this->sendto[$i]).'.eml',$this->headers . $this->body);#$res = true;//(OLD) Plus jamais de courriel perdu : donc on evite de faire peur a l'internaute avec une erreur d'envoi
#traitement supplémentaire possible avec $res?
  }
  return $res;//ajout by SWD
 }
 /*
 * Construit l'en-tête 'organisation'.
 */
 function Organization( $org ) {
  if( trim( $org != "" ) )
   $this->organization= $org;
 }
 /*
 * Défini la priorité du message.
 * $priority : un entier pris entre 1(la plus haute) et 5(la plus basse).
 * ex: $m->Priority(1) ; => priorité la plus élevée
 */
 function Priority( $priority ) {
  if( ! intval( $priority ) )
   return false;
  if( ! isset( $this->priorities[$priority-1]) )
   return false;
  $this->priority= $this->priorities[$priority-1];
   return true;
 }
 /*
 * Défini un fichier ($filename) à attacher au message.
 * $filename : nom du fichier (chemin y compris).
 * $filetype : MIME-type du fichier; par défaut: 'application/x-unknown-content-type'
 * $disposition : Renseigne sur la façon d'afficher le fichier ("inline" ou "attachment");
 * par défaut: "attachment".
 */
 function Attach( $filename, $filetype='application/x-unknown-content-type', $disposition = "attachment" ) {
  // TODO : si filetype="", alors chercher dans un tableau de {MIME-type|extension du fichier}
  $this->aattach[] = $filename;
  $this->actype[] = $filetype;
  $this->adispo[] = $disposition;
 }
 /*
 * Retourne le message complet, en-têtes et corps.
 * Cette fonction peut être utilisée pour afficher le message en 'plain text' ou
 * dans un log par exemple.
 */
 function Get() {
  if(empty($this->headers))
   $this->_build_headers();//+ attach
  $mail = $this->headers . $this->body;
  //$mail .= $this->EOL . $this->body;
  return $mail;
 }
 //-----------------------------------------------------------------------------------------------------
 // Méthode privée (usage interne seulement):_
 //-----------------------------------------------------------------------------------------------------
 /*
 * Construction des en-têtes du message. + attach
 * * (Utilisation interne uniquement)
 */
 function _build_headers() {
  // creation de l'en-tête
# $this->headers= 'From: ' . $this->from . $this->EOL;# idee $this->headers  = "'From: '.$this->name.' <'.$this->to.'>'.$this->EOL;
  $tofrom = $this->sendto[0];
  $this->headers = 'From: ' . $tofrom . $this->EOL;
  $this->headers .= 'Reply-To: ' . $this->from . $this->EOL;
  $this->headers .= 'Subject: ' . $this->msubject . $this->EOL;
  $this->to = implode( ', ', $this->sendto );
  if( count($this->acc) > 0 ) {
   $this->cc = implode( ', ', $this->acc );
   $this->headers .= 'CC: ' . $this->cc . $this->EOL;
  }
  if( count($this->abcc) > 0 ) {
   $this->bcc= implode( ', ', $this->abcc );
   $this->headers .= 'BCC: ' . $this->bcc . $this->EOL;
  }
  if( $this->organization != '' )
   $this->headers .= 'Organization: ' . $this->organization . $this->EOL;
  if( $this->priority != '' )
   $this->headers .= 'X-Priority: ' . $this->priority . $this->EOL;
  $this->headers .= 'Date: '.date('D, j M Y G:i:s O') . $this->EOL; // Sat, 7 Jun 2001 12:35:58 -0700
  $this->headers .= 'MIME-Version: 1.0' . $this->EOL; // Sat, 7 Jun 2001 12:35:58 -0700
# if ($this->charset == 'utf-8')
  $this->headers = mb_convert_encoding($this->headers,'7bit',$this->charset);//WIT BAD à accentué in dest_name fix RC1
  $this->_build_attachement();
 }
 /*
 * Contrôle et attachement de la/des pièce/s jointe/s.
 * * (Utilisation interne uniquement)
 */
 function _build_attachement() {
  $headerTxt = 'Content-Type: text/plain; charset=' . $this->charset . $this->EOL . 'Content-Transfer-Encoding: 8bit' . str_repeat($this->EOL, 2);
  if(empty($this->aattach)){
   $this->headers .= $headerTxt;
#  $this->body .= $this->EOL;
   return;
  }
#  $this->body .= $this->EOL;
  $this->boundary= '------------' . md5( uniqid('myboundary') ); // TODO : bound variable
# $sep=chr(13) . chr(10);# (\r \n) :: origin
# $sep=chr(10);//sanityze  no ok stackoverflow.com/questions/30887610/error-with-php-mail-multiple-or-malformed-newlines-found-in-additional-header#30897935
  $sep=$this->EOL;
  $ata=array();
  $k=0;
// for each attached file, do...
  for( $i=0; $i < sizeof($this->aattach); $i++ ) {
   $filename = $this->aattach[$i];
   $basename = basename($filename);
   // content-type:_
   $ctype = $this->actype[$i];
   $disposition = $this->adispo[$i];
   if( ! file_exists( $filename) ) {
    //echo "Class Mail, method attach : file $filename can't be found"; exit;
    continue;
   }
   $subhdr= '--'.$this->boundary . $this->EOL . 'Content-type: ' . $ctype . ';' . $this->EOL . ' name="' . $basename . '"' . $this->EOL .'Content-Transfer-Encoding: base64' . $this->EOL . 'Content-Disposition: ' . $disposition . ';' . $this->EOL . ' filename="' . $basename . '"' . $this->EOL;
   $ata[$k++] = $subhdr;
   // non encoded line length:_
   $linesz = filesize( $filename)+1;
   $fp = fopen( $filename, 'r' );
   $data = base64_encode(fread($fp, $linesz));
   fclose($fp);
   $ata[$k++] = chunk_split($data, 76, $this->EOL);
  }
  if(empty($ata)){
   $this->headers .= $headerTxt;
   return;
  }
  $this->headers .= 'Content-Type: multipart/mixed;'.$this->EOL.' boundary="' . $this->boundary . '"' . str_repeat($this->EOL, 2);
  $this->attachment = implode($sep, $ata);
  $this->body = 'This is a multi-part message in MIME format.' . $this->EOL . '--'.$this->boundary . $this->EOL . $headerTxt . $this->body . str_repeat($this->EOL, 2) . $this->attachment;
 }
} // class Mail
