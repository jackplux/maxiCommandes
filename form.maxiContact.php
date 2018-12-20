<?php if(!defined('PLX_ROOT')) exit;//Part of maxiContact plugin for PluXml
# $this est une instance de plxShow
$this->plxMotor->plxCapcha = new plxCapcha();
$plxMotor = $this->plxMotor;//::getInstance();
$plxPlugin = $plxMotor->plxPlugins->getInstance('maxiContact');
$pluginName = get_class($plxPlugin);
$EOL = $plxPlugin->EOL;
# Si le fichier de langue n'existe pas
$lang = $plxMotor->aConf['default_lang'];
if(!file_exists(PLX_PLUGINS . $pluginName . '/lang/'.$lang.'.php')) {
 echo '<p>'.sprintf($plxPlugin->getLang('L_LANG_UNAVAILABLE'), $pluginName.'&nbsp;('.$lang.')').'</p>';
 if(!$plxPlugin->getParam('force_lang')) return;
}
$nombre_ades = $plxPlugin->getParam('nombre_ades');
$nombre_qcm = $plxPlugin->getParam('nombre_qcm');
for($c=1;$c<=$nombre_qcm;$c++){
 $nombre_qrm[$c] = $plxPlugin->getParam('nombre_qrm'.$c);
 $autre[$c] = $plxPlugin->getParam('autre'.$c);
}
$messqcm='';
$pieces = $plxPlugin->getParam('piece');
$captcha = $plxPlugin->getParam('captcha')=='' ? 1 : $plxPlugin->getParam('captcha');
$placeholder = '1';//$plxPlugin->getParam('placeholder')=='' ? '1' : $plxPlugin->getParam('placeholder');
$success=false;
$error=
$maxUpload = # valeur upload_max_filesize
$maxPost = array(); # valeur post_max_size
# Taille maxi des fichiers
$mxtmp = $plxPlugin->getParam('piece_size') ? $plxPlugin->getParam('piece_size') : ini_get("upload_max_filesize");
$maxUpload['display'] = str_replace('M', ' Mo', strtoupper($mxtmp));
$maxUpload['display'] = str_replace('K', ' Ko', $maxUpload['display']);
if(substr_count($mxtmp, 'K')) $maxUpload['value'] = str_replace('K', '', $mxtmp) * 1024;
elseif(substr_count($mxtmp, 'M')) $maxUpload['value'] = str_replace('M', '', $mxtmp) * 1024 * 1024;
elseif(substr_count($mxtmp, 'G')) $maxUpload['value'] = str_replace('G', '', $mxtmp) * 1024 * 1024 * 1024;
else $maxUpload['value'] = 0;

# Taille maxi des données
$mxtmp = $plxPlugin->getParam('post_size') ? $plxPlugin->getParam('post_size') : ini_get("post_max_size");
$maxPost['display'] = str_replace('M', ' Mo', strtoupper($mxtmp));
$maxPost['display'] = str_replace('K', ' Ko', $maxPost['display']);
if(substr_count($mxtmp, 'K')) $maxPost['value'] = str_replace('K', '', $mxtmp) * 1024;
elseif(substr_count($mxtmp, 'M')) $maxPost['value'] = str_replace('M', '', $mxtmp) * 1024 * 1024;
elseif(substr_count($mxtmp, 'G')) $maxPost['value'] = str_replace('G', '', $mxtmp) * 1024 * 1024 * 1024;
else $maxPost['value'] = 0;
unset($mxtmp);
//fi init

if(!empty($_POST)) {
 $sexe=isset($_POST['sexe'])?plxUtils::unSlash(trim($_POST['sexe'])):'';
 $name=isset($_POST['name'])?plxUtils::unSlash(trim($_POST['name'])):'';
 $prenom=isset($_POST['prenom'])?plxUtils::unSlash(trim($_POST['prenom'])):'';
 $mail=isset($_POST['mail'])?plxUtils::unSlash(trim($_POST['mail'])):'';
 $subject = '';
 if($plxPlugin->getParam('append_subject')) {
  $subject = plxUtils::unSlash(trim($_POST['subject']));
 }
 $content=isset($_POST['content'])?plxUtils::unSlash(trim($_POST['content'])):'';
 $entreprise=isset($_POST['entreprise'])?plxUtils::unSlash(trim($_POST['entreprise'])):'';
 $tel=isset($_POST['tel'])?plxUtils::unSlash(trim($_POST['tel'])):'';
 $rue=isset($_POST['rue'])?plxUtils::unSlash(trim($_POST['rue'])):'';
 $cp=isset($_POST['cp'])?plxUtils::unSlash(trim($_POST['cp'])):'';
 $ville=isset($_POST['ville'])?plxUtils::unSlash(trim($_POST['ville'])):'';
 $fax=isset($_POST['fax'])?plxUtils::unSlash(trim($_POST['fax'])):'';
 $profession=isset($_POST['profession'])?plxUtils::unSlash(trim($_POST['profession'])):'';
 $reponse=isset($_POST['reponse'])?plxUtils::unSlash(trim($_POST['reponse'])):'';
 $motif=isset($_POST['motif'])?plxUtils::unSlash(trim($_POST['motif'])):'';
 $site=isset($_POST['site'])?plxUtils::unSlash(trim($_POST['site'])):'';
 if($nombre_ades)
  for ($q=1;$q<=$nombre_ades;$q++){ 
   $retour[$q]=isset($_POST['retour'.$q])?plxUtils::unSlash(trim($_POST['retour'.$q])):'';
  }

 if($pieces)
  for ($p=1;$p<=$pieces;$p++){ #$_FILES
   $piece[$p]=isset($_FILES['piece_'.$p]['name'])?plxUtils::unSlash($_FILES['piece_'.$p]['name']):'';
  }

 if($nombre_qcm){
  for ($c=1;$c<=$nombre_qcm;$c++){//multi qcm
   $nombre_qrm[$c] = $plxPlugin->getParam('nombre_qrm'.$c);
   $typeqcm = $plxPlugin->getParam('qcm_type'.$c);//checkbox,radio ok
   if($nombre_qrm[$c]){
    for ($q=1;$q<=($nombre_qrm[$c]+1);$q++){
     $idqrm = ($typeqcm!='checkbox'?$c:$c.'_'.$q);
     $qrm[$idqrm]=isset($_POST['qrm'.$idqrm])?$_POST['qrm'.$idqrm]:false;;//isset($_POST['qrm'.$idqrm])?true:false;
    }
    $qcm[$c]=isset($_POST['qcm'.$c])?plxUtils::unSlash($_POST['qcm'.$c]):'';
   }
  }
 }

 # pour compatibilité avec le plugin plxMyCapchaImage
 if (isset($this->plxMotor->plxPlugins->aPlugins["plxCapchaImage"])/* OR strlen(@$_SESSION['capcha'])<=10*/)//si capchaImage
  $_SESSION["capcha"]=sha1(@$_SESSION["capcha"]);

 if($captcha != 0 AND (!isset($_POST['rep']) OR (empty($_POST['rep']) OR $_SESSION['capcha'] != sha1($_POST['rep']))))
  $error[] = $plxPlugin->getLang('L_ERR_ANTISPAM');
 if(empty($name))
  $error[] = $plxPlugin->getLang('L_ERR_NAME');
 if(empty($mail) OR !plxUtils::checkMail($mail))
  $error[] = $plxPlugin->getLang('L_ERR_EMAIL');

 if($plxPlugin->getParam('sexe') and $plxPlugin->getParam('sexe_obligatoire') and empty($sexe) )
  $error[] = $plxPlugin->getLang('L_ERR_SEXE');
 if($plxPlugin->getParam('append_subject') and $plxPlugin->getParam('subject_obligatoire') and empty($subject) )
  $error[] = $plxPlugin->getLang('L_ERR_SUBJECT');
 if($plxPlugin->getParam('message') and $plxPlugin->getParam('message_obligatoire') and empty($content) )
  $error[] = $plxPlugin->getLang('L_ERR_CONTENT');
 if($plxPlugin->getParam('entreprise') and $plxPlugin->getParam('entp_obligatoire') and empty($entreprise) )
  $error[] = $plxPlugin->getLang('L_ERR_ENTREPRISE');
 if($plxPlugin->getParam('prenom') and $plxPlugin->getParam('prenom_obligatoire') and empty($prenom) )
  $error[] = $plxPlugin->getLang('L_ERR_PRENOM');
 if($plxPlugin->getParam('adresse') and $plxPlugin->getParam('adrs_obligatoire') and empty($rue) )
  $error[] = $plxPlugin->getLang('L_ERR_RUE');
 if($plxPlugin->getParam('adresse') and $plxPlugin->getParam('adrs_obligatoire') and empty($cp) )
  $error[] = $plxPlugin->getLang('L_ERR_CP');
 if($plxPlugin->getParam('adresse') and $plxPlugin->getParam('adrs_obligatoire') and empty($ville) )
  $error[] = $plxPlugin->getLang('L_ERR_VILLE');
 if($plxPlugin->getParam('tel') and $plxPlugin->getParam('tel_obligatoire') and empty($tel) )
  $error[] = $plxPlugin->getLang('L_ERR_TEL');
 if($plxPlugin->getParam('fax') and $plxPlugin->getParam('fax_obligatoire') and empty($fax) )
  $error[] = $plxPlugin->getLang('L_ERR_FAX');
 if($plxPlugin->getParam('profession') and $plxPlugin->getParam('profession_obligatoire') and empty($profession) )
  $error[] = $plxPlugin->getLang('L_ERR_PROFESSION');
 if($plxPlugin->getParam('motif') and $plxPlugin->getParam('motif_obligatoire') and empty($motif) )
  $error[] = $plxPlugin->getLang('L_ERR_MOTIF');
 if($plxPlugin->getParam('site') and $plxPlugin->getParam('site_obligatoire') and empty($site) )
  $error[] = $plxPlugin->getLang('L_ERR_SITE');
  
 if($nombre_ades)
  for ($q=1;$q<=$nombre_ades;$q++){
   if($plxPlugin->getParam('ades'.$q) and $plxPlugin->getParam('adesion'.$q.'_obligatoire') and empty($retour[$q]) )
    $error[] = $plxPlugin->getLang('L_ERR_REPONSE');
  }
 if($pieces)
  for ($p=1;$p<=$pieces;$p++){
   if($plxPlugin->getParam('piece_obligatoire') and empty($piece[$p]) )
    $error[] = $plxPlugin->getLang('L_ERR_PIECE');
  }

 
 if($nombre_qcm){
  $messqcm = '';
  for($c=1;$c<=$nombre_qcm;$c++){//multi qcm
   if($plxPlugin->getParam('qcm'.$c)){//sondage (Comment nous avez vous connu)
    $nombre_qrm[$c] = $plxPlugin->getParam('nombre_qrm'.$c);
    $messqcm .= $plxPlugin->getLang('L_COMMENT3').' '.$plxPlugin->getParam('qrm_title_'.$c.$plxPlugin->lang).':'.$EOL;
    $qrm[0]=0;//init
    $typeqcm = $plxPlugin->getParam('qcm_type'.$c);//checkbox,radio ok
    for ($q=1;$q<=($nombre_qrm[$c]+1);$q++){
     $idqrm = ($typeqcm!='checkbox'?$c:$c.'_'.$q);
     if($qrm[$idqrm] == $q){
      $qrm[0]++;// si une réponse est coché
      $messqcm.=$plxPlugin->getParam('qrm_'.$c.$plxPlugin->lang.$q).', ';
     }
    }
    $q--;
    $messqcm = rtrim($messqcm,', ');
    if($autre[$c] AND ($q == $qrm[$idqrm])){// si autre est activé & coché
     $messqcm.=($qrm[0]>1?' + ':'').$plxPlugin->getLang('L_REPONSE_AUTRE').': '.$qcm[$c];//si solo == not +
     $qrm[0]++;
    }
    $messqcm.=($qrm[0]?$EOL.$EOL:$plxPlugin->getLang('L_NO_REPONSE').$EOL.$EOL);
    if($plxPlugin->getParam('qcm_obligatoire'.$c)){
     if(!$qrm[0])
      $error[] = $plxPlugin->getLang('L_ERR_COMMENT').' "<i>'.$plxPlugin->getParam('qrm_title_'.$c.$plxPlugin->lang).'</i>"<br />';
     if($autre[$c] AND ($q == $qrm[$idqrm]) AND trim($qcm[$c])=='')//si autre activé et non coché (si aucune autre reponse)
      $error[] = ucfirst($plxPlugin->getLang('L_REPONSE_PRECIS')).' ('.$plxPlugin->getLang('L_REPONSE_AUTRE').') "<i>'.$plxPlugin->getParam('qrm_title_'.$c.$plxPlugin->lang).'</i>"<br />';//Veuillez préciser (Autre) i"titre du qcm"i
    }
   }//fi qcm
  }//rof multi qcm
 }//qcm numr 

 if(!$error) {
  $messRetour = ($plxPlugin->getParam('sexe')?' '.$sexe:'').$name;
  if($plxPlugin->getParam('prenom')){$messRetour.=' ' . $prenom;}
  $messRetour.= $EOL . $plxPlugin->getLang('L_MAIL') . ': ' . $mail . $EOL;
  if($plxPlugin->getParam('motif')){$messRetour.=$plxPlugin->getLang('L_MOTIF3') . ': ' . $motif . $EOL;}
  if($plxPlugin->getParam('entreprise')){$messRetour.=$plxPlugin->getLang('L_ENTREPRISE3') . ': ' . $entreprise . $EOL;}
  if($plxPlugin->getParam('tel')){$messRetour.=$plxPlugin->getLang('L_TEL3') . ': ' . $tel . $EOL;}
  if($plxPlugin->getParam('fax')){$messRetour.=$plxPlugin->getLang('L_FAX3') . ': '.$fax . $EOL;}
  if($plxPlugin->getParam('profession')){$messRetour.=$plxPlugin->getLang('L_PROFESSION3') . ': ' . $profession . $EOL;}
  if($plxPlugin->getParam('site')){$messRetour.= $plxPlugin->getLang('L_SITE3') . ': '.$site . $EOL;}
  if($plxPlugin->getParam('adresse')){$messRetour.= $plxPlugin->getLang('L_ADRS') . ': ' . $rue . ' ' . $cp . ' ' . $ville . $EOL;}
  if($nombre_ades)
   for ($q=1;$q<=$nombre_ades;$q++){ 
    if($plxPlugin->getParam('ades'.$q)){
     $demande=$plxPlugin->getParam('adesion_'.$lang.$q);
     $type = $plxPlugin->getParam('ades_type'.$q);
     $messRetour.=$demande.': '.($type!='textarea'?'':$EOL).$retour[$q] . $EOL.($type!='textarea'?'':$EOL);
    }
   }
  if($pieces)
   for ($p=1;$p<=$pieces;$p++){
    $piece[$p] = trim($piece[$p]);
    if(!empty($piece[$p])) $messRetour .= sprintf($plxPlugin->getLang('L_PIECE3'), $piece[$p]) . $EOL;
   }
  $messRetour.=$messqcm.$EOL;unset($messqcm);//qcm (qrm)

  if($plxPlugin->getParam('message')){$messRetour.=$plxPlugin->getLang('L_MESSAGE' ) . ': ' . $EOL . $content;}

  $dossier = PLX_PLUGINS.$pluginName.'/';
//plxUtils::sendMail($name,$mail,$plxPlugin->getParam('email'),$plxPlugin->getParam('subject'),$messRetour,'text',$plxPlugin->getParam('email_cc'),$plxPlugin->getParam('email_bcc'))
  $dest = $plxPlugin->getParam('email');/* A qui s'adresse ce mail (TO) */
  $dest_name = $plxMotor->aConf['title'].' '.$plxMotor->aConf['description']; /* A qui s'adresse ce mail (TO) - Nom parlant
  Utilisé aussi pour le champs 'organisation' dans l'en-tête*/
  $copy_dest = $plxPlugin->getParam('email_cc');/* courriel pour la Copie Carbone (CC) */
  $cache_dest = $plxPlugin->getParam('email_bcc'); /* courriel pour la Copie Carbone (BCC) */
  $objet_page = (!isset($_POST['objet_page']) OR empty($_POST['objet_page'])) ? '' : ' - ' . $_POST['objet_page'];/* Libellé de la page où se trouve le formulaire (utile si vous utilisez ce script sur plusieures pages de votre site) */
//  $redirection = "merci.php"; /* Redirection vers une autre page une fois l'envoie effectué */
  $priority = "3"; /* Permet de définir la priorité du mail, les valeurs vont de 1 (urgent) à 5 (priorité basse) */
  //$reponse="Merci, votre message nous est parvenus --- ".$plxMotor->aConf['title']." - "$plxMotor->aConf['description'];//unused /* Réponse de l'envoi du mail*/     
  $extensions_ok = explode(',',$plxPlugin->getParam('extensions_ok'));//array('svg', 'png', 'gif', 'jpg', 'jpeg', 'bmp', 'pdf');#in param
  $taille_max = $maxUpload['value'];#param //2048000 == 2Mo
  $subject=StripSlashes($subject);
  // Formatage du corps du message
  //~ $msg=StripSlashes(utf8_decode($messRetour));
  $msg = StripSlashes($messRetour);
  $preamb = plxUtils::unSlash($plxPlugin->getParam('subject_'.$lang)) . $objet_page . $EOL;
  $preamb.= $plxPlugin->getLang('L_PREAMB_MAIL').": $name " . $EOL . ($subject?$plxPlugin->getLang('L_PREAMB_SUBJECT').': '.$subject.$EOL:'');
  $preamb.= str_repeat('_', 46).$EOL;
  //$preamb.=str_repeat($EOL, 2);
  $msg=$preamb . $msg . $EOL . str_repeat('_',46) . $EOL;

  require($dossier.'php/classMail.php');//On inclu la classe
  // Création de l'objet Mail: La valeur 'false' désactive la fonction autoCheck (cf: commentaire dans classMail.php)
  $m = new Mail(TRUE); //NEW LANG (fr) ,en,...
  $m->From($mail, plxUtils::removeAccents($subject,PLX_CHARSET), $dest, plxUtils::removeAccents($dest_name,PLX_CHARSET)); # envoi une notif a l'internaute lors de la verif ;)
  $m->To($dest);
  $m->Subject(plxUtils::removeAccents(plxUtils::unSlash($plxPlugin->getParam('subject_'.$lang)).($subject?' - '.$subject:''),PLX_CHARSET));
  $m->Body($msg);
  $m->Organization(plxUtils::removeAccents($dest_name,PLX_CHARSET));
  $m->Priority($priority); 
  $m->lang($plxPlugin->getLang('L_CLASS_MAIL_ARRAY'));//NEW LOAD LANG

  // S'il y a une copie conforme du mail:_
  if ($copy_dest!="") {
   $m->Cc($copy_dest);
  }

  // S'il y a une copie cachée du mail:_
  if ($cache_dest!="") {
   $m->Bcc($cache_dest);
  }

//upload photo              
  $dest_dossier = $plxPlugin->tmp.'/tmp/';//PHP Mail Temporay Upload
  $logUpload = '';//retourne l'information a lutilisateur des fichiers joint non envoyé
  $i = 0;             
  $yaDesPhoto = 'non';
  if($pieces)
  foreach($_FILES as $file_name => $file_array) {
   unset($retUpload);

   if( file_exists($file_array['tmp_name'])
   and !in_array( strtolower(substr(strrchr($file_array['name'], '.'), 1)), $extensions_ok ) )//ext nook
   {
    $retUpload = "<br /><img src='".$dossier."/img/attention.gif' style='vertical-align: middle;' /> <font style='color:red;'><b>".sprintf($plxPlugin->getLang('L_ERR_PIECE2'), $file_array['name'])."</b></font>";
//echo substr(strrchr($file_array['name'], '.'), 1);
   }
   elseif( file_exists($file_array['tmp_name'])
   and $file_array['size'] > $taille_max)
   {
    $retUpload = "<br /><img src='".$dossier."/img/attention.gif' style='vertical-align: middle;' /><font style='color:red;'><b>".sprintf($plxPlugin->getLang('L_ERR_PIECE3'), $file_array['name'], $maxUpload['display'])."</b></font>";
   }
   //test si erreur
   if(isset($retUpload)){
//echo '<p>', $erreur ,'</p>';
    $logUpload .= $retUpload;
   }
   else{// Attachement du fichier si dak
    if ($file_array['name'] !="") {
     $i++; 
     $uploadName[$i] = basename($file_array['name']);
// formatage nom fichier
     // enlever les accents
     $uploadName[$i] = strtr($uploadName[$i],
     'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
     'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
     // remplacer les caracteres autres que lettres, chiffres et point par _
     $uploadName[$i] = preg_replace('/([^.a-z0-9]+)/i', '_', $uploadName[$i]);
     if(is_uploaded_file($file_array["tmp_name"])){
      $yaDesPhoto = 'oui'; 
// copie temporaire du fichier joint.
      copy($file_array["tmp_name"], "$dest_dossier$uploadName[$i]");//move_uploaded_file($file_array["tmp_name"], "$dest_dossier$uploadName[$i]") or die ("Couldn't copy");
//attachement
      $m->Attach("$dest_dossier$uploadName[$i]", "application/octet-stream");
      $logUpload .= '<br />'.sprintf($plxPlugin->getLang('L_PIECE3'), $uploadName[$i]);//$piece[$p]
     }
    }
   }
  }//upload photo FI FILES
#sendmail
  $res = $m->Send($plxPlugin->tmp.'/eml/', !$plxPlugin->getParam('fake_send'));//dossier de backup, envoi
  if(!$res)
   $plxPlugin->updateUnsend(1);//+1 au compteur d'echec
  $res = true;// Plus jamais de courriel perdu : donc on evite de faire peur a l'internaute avec une erreur d'envoi
  if($res){//test si envoyé (free fr)
   $success = '<img alt="'.$plxPlugin->getLang('L_SENDMAIL_OK').'." src="'.$dossier.'/img/mail_ok.gif" style="vertical-align:middle;" /><b>'.$plxPlugin->getLang('L_SENDMAIL_OK').'.</b><br />'.$logUpload.'<br /><br />'.$plxPlugin->getParam('thankyou_'.$lang);      
  }else{//HISTORIC, N'y passe jamais
   $error ='<img src="'.$dossier.'/img/mail_server_error.gif" style="vertical-align:middle;" /><b>'.$plxPlugin->getLang('L_ERR_SENDMAIL').'!</b><br />'.$plxPlugin->getLang('L_ERR_SENDMAIL_PLUS')/*. ' ' .error_get_last()['message']*/;
   $plxPlugin->updateUnsend(1);
  }
  if ($yaDesPhoto != 'non' ){//s'il y a des téléversés on efface les fichier temporaires
   for ($j = $i; $j >= 1; $j--){
    if ("$dest_dossier$uploadName[$j]"!="") {
     Unlink("$dest_dossier$uploadName[$j]");
    }
   }
  }
  if (!$plxPlugin->getParam('successIncUrl') AND $plxPlugin->getParam('redirectUrl')){//redirection avant output buffer
   header('Location:'.$plxPlugin->getParam('redirectUrl'));
   exit;
  }
 }//fi !$error
 else{$error = implode('<br />'.PHP_EOL, $error);}
}//FI !empty($_POST)
else{//1st time
 $name=
 $prenom=
 $mail=
 $subject=
 $content=
 $entreprise=
 $tel=
 $rue=
 $cp=
 $ville=
 $fax=
 $profession=
 $retour[0]=
 $reponse=
 $motif=
 $sexe=
 $site=
 $ades_fields[1]=
 $qcm[0]='';
 for($q=1;$q<=$nombre_ades;$q++){//reponses perso
  $retour[$q]=null;
 }
 for($p=1;$p<=$pieces;$p++){//upload
  $piece[$p]=null;
 }
 for ($c=1;$c<=$nombre_qcm;$c++){//multi qcm
  $qcm[$c]=false;
  $typeqcm = $plxPlugin->getParam('qcm_type'.$c);//checkbox,radio,select-one ok
  for ($q=0;$q<=($nombre_qrm[$c]+($autre[$c]?1:0));$q++){//sondage (+1 pour "Autre, veuillez preciser")
   $idqrm = ($typeqcm!='checkbox'?$c:$c.'_'.$q);
   $qrm[$idqrm]=false;
  }
 }
}
#Admin Menu & Config Links helper
$adminMenuMC = $cnfHref = '';
if(isset($_SESSION['profil'])){
 if($_SESSION['profil'] < PROFIL_MODERATOR AND ($plxMotor->aConf['clef'] == $_SESSION['clef'.$pluginName])){ //PROFIL_MANAGER+ & if same $plxMotor->aConf['clef'] : by AdminAuthPrepend hook
  $rewrited = $plxMotor->urlRewrite(PLX_CORE.'admin/');
  $prfl = ($_SESSION['profil'] > PROFIL_ADMIN);
  $cnfHref = $rewrited.($prfl?'':'parametres_').'plugin.php?p='.$pluginName.($prfl?'&amp;z=config':'');//parametres_plugin.php?p=
  #/i\ : $plxMotor/$plxAdmin->version removed in 5.5
  $hlpvers = (isset($plxAdmin->version))?'parametres_pluginhelp.php?p=':'parametres_help.php?help=plugin&amp;page=';//<5.5 : >=5.5
  $hlpHref = ($prfl?'plugin.php?p=':$hlpvers).$pluginName.($prfl?'&amp;z=lang/'.$plxPlugin->lang.'-help':'');//parametres_help.php?help=plugin&page=
  loadLang(PLX_CORE.'lang/'.$plxPlugin->lang.'/admin.php');//On Charge les var de langue de l'admin
  $adminMenuMC .= '<p class="linkmcc">'.plxUtils::strCheck($plxMotor->aUsers[$_SESSION['user']]['name']).'&nbsp: '.L_ADMIN.' &amp; '.L_PLUGINS_CONFIG_TITLE.' '.$pluginName.'<br />';
  $adminMenuMC .= '<a href="'.$rewrited.'plugin.php?p='.$pluginName.'" title="'.L_VIEW.' '.$plxPlugin->getLang('L_CACHE_LIST').' ('.L_ADMIN.') '.'"><img id="admin" class="icon_pmc" alt="admin" src="'.PLX_PLUGINS.$pluginName.'/img/admin.png" /></a> ';
  $adminMenuMC .= '<a href="'.$cnfHref.'" title="'.L_VIEW.' '.$plxPlugin->getLang('L_MAIN').' ('.L_MENU_CONFIG.') '.'"><img id="config" class="icon_pmc" alt="config" src="'.PLX_PLUGINS.$pluginName.'/img/settings.png" /></a> ';
  $adminMenuMC .= '<a href="'.$hlpHref.'" title="'.@L_PLUGINS_HELP_TITLE.'"><img id="help" class="icon_pmc" alt="help" src="'. PLX_PLUGINS.$pluginName.'/img/help.png" /></a></p>';
 }
}
?>
<div id="form_contact">
 <?php if($error): ?>
 <p class="contact_error"><?php echo $error ?></p>
 <?php endif; ?>
 <?php if($success): ?>
 <p class="contact_success"><?php echo $success ?></p>
<?php //include option
if ($plxPlugin->getParam('successIncUrl') AND $plxPlugin->getParam('redirectUrl'))
 include($plxPlugin->getParam('redirectUrl'));
?>
 <?php else: echo $adminMenuMC; ?>
 <form action="#form" method="post"<?php echo ($pieces?' enctype="multipart/form-data"':'').($nombre_qcm?' onSubmit="check()"':'')?>>
  <fieldset>
<?php
//$this->plxCapcha = new plxCapcha();//tjrs aprés le POST !
//$plxMotor->plxCapcha = plxCapcha::getInstance();//tjrs aprés le POST !
#td : $after_pprog = ''; (qperso = 1 in $after_pprog au lieu d'un echo : + echo $after_pprog (après les pprogs ;) 
   $after_prog = $after_qcm = $after_msg = '';
   if($nombre_ades){#questions personalisés
    echo !$cnfHref?'':'<p><a class="linkmcc" href="'.$cnfHref.'#perso" title="'.L_VIEW.' '.$plxPlugin->getLang('L_PERSO').' ('.L_MENU_CONFIG.') '.'"><img id="config-perso" class="icon_pmc" alt="config" src="'.PLX_PLUGINS.$pluginName.'/img/settings.png" />&nbsp;'.$plxPlugin->getLang('L_PERSO').'</a></p>';//admin menu
    for ($q=1;$q<=$nombre_ades;$q++){
     if($plxPlugin->getParam('ades'.$q)){#adesion_fr1
      $a = $plxPlugin->getParam('ades_ou'.$q);#BEP #debut/prog/qcm
//    $p = $plxPlugin->getParam('ades_ordre'.$q);#BEP
      $o = $plxPlugin->getParam('adesion'.$q.'_obligatoire');
      $r = $plxPlugin->getParam('adesion_'.$plxPlugin->lang.$q);
      $type = $plxPlugin->getParam('ades_type'.$q);
      $adestype = $type!='textarea'
      ?array('input type="'.$type.'"',' value="'.plxUtils::strCheck($retour[$q]).'"',' size="-1"/>')
      :array('textarea','','>'.plxUtils::strCheck($retour[$q]).'</textarea>');
      $ades_fields[$q] = array($a,'     <p>'.($r?'<label for="retour'.$q.'">'.$r.'&nbsp;'.($o?'*':'').':</label>':'').'
      <'.$adestype[0].'  id="retour'.$q.'" name="retour'.$q.'" '.$plxPlugin->getParam('ades_attr'.$q).$adestype[1].($o?' required':'').($placeholder ? ' placeholder="'.plxUtils::strCheck($r).'" ' : '').$adestype[2].'</p>'.PHP_EOL);
     }
    }
    //ou l'afficher
    for ($q=1;$q<=$nombre_ades;$q++){
     switch($ades_fields[$q][0]){
      case 'msg':
       $after_msg .= $ades_fields[$q][1];
       break;
      case 'qcm':
       $after_qcm .= $ades_fields[$q][1];
       break;
      case 'prog':
       $after_prog .= $ades_fields[$q][1];
       break;
      case 'debut':
      default:
       echo $ades_fields[$q][1];
     }
    }
   }
   echo !$cnfHref?'':'<p><a class="linkmcc" href="'.$cnfHref.'#main2" title="'.L_VIEW.' '.$plxPlugin->getLang('L_MAIN2').' ('.L_MENU_CONFIG.') '.'"><img id="config-main2" class="icon_pmc" alt="config" src="'.PLX_PLUGINS.$pluginName.'/img/settings.png" />&nbsp;'.$plxPlugin->getLang('L_MAIN2').'</a></p>';//admin menu
   if($plxPlugin->getParam('sexe')){?>
     <p>
      <label for="sexe"><?php $plxPlugin->lang('L_SEXE2') ?>&nbsp;:</label>
      <select id="sexe" name="sexe">
       <option value="<?php $plxPlugin->lang('L_MR') ?>"><?php $plxPlugin->lang('L_MR') ?></option>
       <option value="<?php $plxPlugin->lang('L_MM') ?>"><?php $plxPlugin->lang('L_MM') ?></option>
      </select>
     </p>
<?php }?>
     <p><label for="name"><?php $plxPlugin->lang('L_FORM_NAME') ?>&nbsp;*:</label>
     <input id="name" name="name" type="text" size="-1" value="<?php echo plxUtils::strCheck($name) ?>" required <?php echo $placeholder ? 'placeholder="'.plxUtils::strCheck($plxPlugin->getLang('L_FORM_NAME')).'" ' : '' ?>/></p>
<?php if($plxPlugin->getParam('prenom')==1){?>
     <p><label for="prenom"><?php $plxPlugin->lang('L_PRENOM2') ?>&nbsp;<?php if($plxPlugin->getParam('prenom_obligatoire')){echo'*';}?>:</label>
     <input id="prenom" name="prenom" type="text" size="-1"<?php if($plxPlugin->getParam('prenom_obligatoire')){echo' required';}?> value="<?php echo plxUtils::strCheck($prenom) ?>" <?php echo $placeholder ? 'placeholder="'.plxUtils::strCheck($plxPlugin->getLang('L_PRENOM2')).'" ' : '' ?>/></p>
<?php }?>
     <p><label for="mail"><?php $plxPlugin->lang('L_FORM_MAIL') ?>&nbsp;*:</label>
     <input id="mail" name="mail" type="email" size="-1" value="<?php echo plxUtils::strCheck($mail) ?>" required <?php echo $placeholder ? 'placeholder="'.plxUtils::strCheck($plxPlugin->getLang('L_FORM_MAIL')).'" ' : '' ?>/></p>
<?php if($plxPlugin->getParam('append_subject')){?>
     <p><label for="subject"><?php $plxPlugin->lang('L_FORM_SUBJECT') ?>&nbsp;<?php if($plxPlugin->getParam('subject_obligatoire')){echo'*';}?>:</label>
     <input <?php echo $placeholder ?>id="subject" name="subject" type="text" size="-1"<?php if($plxPlugin->getParam('subject_obligatoire')){echo' required';}?> value="<?php echo plxUtils::strCheck($subject) ?>" maxlength="161" <?php echo $placeholder ? 'placeholder="'.plxUtils::strCheck($plxPlugin->getLang('L_FORM_SUBJECT')).'" ' : '' ?>/>
     </p>
<?php }
   if($plxPlugin->getParam('entreprise')==1){?>
     <p><label for="entreprise"><?php $plxPlugin->lang('L_ENTREPRISE2') ?>&nbsp;<?php if($plxPlugin->getParam('entp_obligatoire')){echo'*';}?>:</label>
     <input id="entreprise" name="entreprise" type="text" size="-1"<?php if($plxPlugin->getParam('entp_obligatoire')){echo' required';}?> value="<?php echo plxUtils::strCheck($entreprise) ?>" <?php echo $placeholder ? 'placeholder="'.plxUtils::strCheck($plxPlugin->getLang('L_ENTREPRISE2')).'" ' : '' ?>/></p>
<?php }
   if($plxPlugin->getParam('tel')){?>
     <p><label for="tel"><?php $plxPlugin->lang('L_TEL2') ?>&nbsp;<?php if($plxPlugin->getParam('tel_obligatoire')){echo'*';}?>:</label>
     <input id="tel" name="tel" type="tel" size="-1"  <?php if($plxPlugin->getParam('tel_obligatoire')){echo' required';}?> value="<?php echo plxUtils::strCheck($tel) ?>" <?php echo $placeholder ? 'placeholder="'.plxUtils::strCheck($plxPlugin->getLang('L_TEL2')).'" ' : '' ?>/></p>
<?php }
   if($plxPlugin->getParam('fax')){?>
     <p><label for="fax"><?php $plxPlugin->lang('L_FAX2') ?>&nbsp;<?php if($plxPlugin->getParam('fax_obligatoire')){echo'*';}?>:</label>
     <input id="fax" name="fax" type="tel" size="-1"<?php if($plxPlugin->getParam('fax_obligatoire')){echo' required';}?> value="<?php echo plxUtils::strCheck($fax) ?>" <?php echo $placeholder ? 'placeholder="'.plxUtils::strCheck($plxPlugin->getLang('L_FAX2')).'" ' : '' ?>/></p>
<?php }
   if($plxPlugin->getParam('profession')){?>
     <p><label for="profession"><?php $plxPlugin->lang('L_PROFESSION2') ?>&nbsp;<?php if($plxPlugin->getParam('profession_obligatoire')){echo'*';}?>:</label>
     <input id="profession" name="profession" type="text" size="-1"<?php if($plxPlugin->getParam('profession_obligatoire')){echo' required';}?> value="<?php echo plxUtils::strCheck($profession) ?>" <?php echo $placeholder ? 'placeholder="'.plxUtils::strCheck($plxPlugin->getLang('L_PROFESSION2')).'" ' : '' ?>/></p>
<?php }
   
   if($plxPlugin->getParam('site')){?>
     <p><label for="site"><?php $plxPlugin->lang('L_SITE2') ?>&nbsp;<?php if($plxPlugin->getParam('site_obligatoire')){echo'*';}?>:</label>
     <input id="site" name="site" type="url" size="-1" <?php if($plxPlugin->getParam('site_obligatoire')){echo' required';}?> value="<?php echo plxUtils::strCheck($site) ?>" <?php echo $placeholder ? 'placeholder="'.plxUtils::strCheck($plxPlugin->getLang('L_SITE2')).'" ' : '' ?>/></p>
<?php }
   if($plxPlugin->getParam('adresse')){?>
     <p><label for="rue"><?php $plxPlugin->lang('L_RUE') ?>&nbsp;<?php if($plxPlugin->getParam('adrs_obligatoire')){echo'*';}?>:</label>
     <input id="rue" name="rue" type="text" size="50"<?php if($plxPlugin->getParam('adrs_obligatoire')){echo' required';}?> value="<?php echo plxUtils::strCheck($rue) ?>" <?php echo $placeholder ? 'placeholder="'.plxUtils::strCheck($plxPlugin->getLang('L_RUE')).'" ' : '' ?>/></p>
     <p><label for="cp"><?php $plxPlugin->lang('L_CP') ?>&nbsp;<?php if($plxPlugin->getParam('adrs_obligatoire')){echo'*';}?>:</label>
     <input id="cp" name="cp" type="text" size="20"<?php if($plxPlugin->getParam('adrs_obligatoire')){echo' required';}?> value="<?php echo plxUtils::strCheck($cp) ?>" <?php echo $placeholder ? 'placeholder="'.plxUtils::strCheck($plxPlugin->getLang('L_CP')).'" ' : '' ?>/></p>
     <p><label for="ville"><?php $plxPlugin->lang('L_VILLE') ?>&nbsp;<?php if($plxPlugin->getParam('adrs_obligatoire')){echo'*';}?>:</label>
     <input id="ville" name="ville" type="text" size="-1"<?php if($plxPlugin->getParam('adrs_obligatoire')){echo' required';}?> value="<?php echo plxUtils::strCheck($ville) ?>" <?php echo $placeholder ? 'placeholder="'.plxUtils::strCheck($plxPlugin->getLang('L_VILLE')).'" ' : '' ?>/></p>
<?php }
   if($plxPlugin->getParam('motif')){?>
     <p><label for="motif"><?php $plxPlugin->lang('L_MOTIF2') ?>&nbsp;<?php if($plxPlugin->getParam('motif_obligatoire')){echo'*';}?>:</label>
     <input id="motif" name="motif" type="text" size="-1"<?php if($plxPlugin->getParam('motif_obligatoire')){echo' required';}?> value="<?php echo plxUtils::strCheck($motif) ?>" <?php echo $placeholder ? 'placeholder="'.plxUtils::strCheck($plxPlugin->getLang('L_MOTIF2')).'" ' : '' ?>/></p>
<?php }
   if($plxPlugin->getParam('piece')){?>
     <p><?php echo $plxPlugin->lang('L_MAX_UPLOAD_FILE').' : '.$maxUpload['display'].
      ($plxPlugin->getParam('piece') > 1 ? " / ".$plxPlugin->getLang('L_MAX_POST_SIZE')." : ".$maxPost['display'] : ''); ?></p>
      <input name="MAX_FILE_SIZE" value="<?php echo $maxUpload['value'] ?>" type="hidden" />
      <input name="MAX_POST_SIZE" value="<?php echo $maxPost['value'] ?>" type="hidden" />
<?php
    for($p = 1; $p <= $pieces; $p++){
?>
     <p><label for="piece_<?php echo $p ?>"><?php $plxPlugin->lang('L_PIECE2') ?>&nbsp;<?php if($plxPlugin->getParam('piece_obligatoire')){echo'*';}?>:</label>
     <input id="piece_<?php echo $p ?>" name="piece_<?php echo $p ?>" type="file"<?php if($plxPlugin->getParam('piece_obligatoire')){echo' required';}?> value="<?php echo plxUtils::strCheck($piece[$p]) ?>" <?php echo $placeholder ? 'placeholder="'.plxUtils::strCheck($plxPlugin->getLang('L_PIECE2')).'"' : '' ?> /></p>
<?php
    }
   }#fi pieces a Téléverser
   echo $after_prog;//champ perso
   if($nombre_qcm){//Sondage
    echo !$cnfHref?'':'<p><a class="linkmcc" href="'.$cnfHref.'#qcm" title="'.L_VIEW.' '.$plxPlugin->getLang('L_QCM').' ('.L_MENU_CONFIG.') '.'"><img id="config-qcm" class="icon_pmc" alt="config" src="'.PLX_PLUGINS.$pluginName.'/img/settings.png" />&nbsp;'.$plxPlugin->getLang('L_QCM').'</a></p>';//admin menu
    $selectHaveOther = false;//for toggle select other js funk
    for($c=1;$c<=$nombre_qcm;$c++){
     $typeqcm = $plxPlugin->getParam('qcm_type'.$c);//checkbox,radio ok
     if($plxPlugin->getParam('qcm'.$c) AND $plxPlugin->getParam('qrm_title_'.$c.$plxPlugin->lang)){
      $reqqcm = !!$plxPlugin->getParam('qcm_obligatoire'.$c);
?>
      <div>
       <div id="qrm<?php echo $c ?>" data-required="<?php echo ($reqqcm?'1':'0');?>" data-nb="<?php echo $nombre_qrm[$c]?>" data-autre="<?php echo $autre[$c]?>"><p><label style="vertical-align: top;" for="qcm<?php echo $c ?>"><?php echo $plxPlugin->getParam('qrm_title_'.$c.$plxPlugin->lang) ?>&nbsp;<?php if($reqqcm){echo'*';}?>:</label>
<?php
        $selectqrm = array();
        $echoqrm = '';
        for ($q=1;$q<=($nombre_qrm[$c]+1);$q++){#reponses personalisés
         $idqrm = ($typeqcm!='checkbox'?$c:$c.'_'.$q);
         $qcm_attr = $plxPlugin->getParam('qcm_attr'.$q);
         if(($nombre_qrm[$c]+1) == $q) break;
         if($qrm_val = $plxPlugin->getParam('qrm_'.$c.$plxPlugin->lang.$q)){
          if($typeqcm=='select')
           $selectqrm[$q] = $qrm_val;//params
          else
           $echoqrm .= '  <label class="qrm" for="qrm'.$c.'_'.$q.'"><input class="deldrm" id="qrm'.$c.'_'.$q.'" name="qrm'.$idqrm.'" '.$qcm_attr.' value="'.$q.'"'.($q==$qrm[$idqrm]?' checked="checked"':'').' type="'.$typeqcm.'" />&nbsp;'.$qrm_val.'</label>'.PHP_EOL;
         }
        }
        $qcm_attr_sel='';//for select
        if ($autre[$c]){//Si autre est activé
         $autredisp = $autreclass = '';
         if($typeqcm=='select'){
          $autreclass = ' full';
          $selectHaveOther = true;//for display toggle js funk
          $autredisp = ($q!=$qrm[$idqrm]?'display: none;':'');
          if(stripos($qcm_attr,'onchange="')!==FALSE)
           $qcm_attr_sel = '" '.str_ireplace('onchange="','onchange="selectOtherToggle(this.value,'.($nombre_qrm[$c]+1).','.$c.');', $qcm_attr);
          else
           $qcm_attr_sel = '" onchange="selectOtherToggle(this.value,'.($nombre_qrm[$c]+1).','.$c.');';//text autre select toggle
          $qcm_attr_sel = rtrim($qcm_attr_sel,'"');
          $selectqrm[$q] = $plxPlugin->getLang('L_REPONSE_AUTRE');//params
         }else{
          $echoqrm .= '  <input class="deldrm" id="qrm'.$c.'_'.$q.'" name="qrm'.$idqrm.'" value="'.$q.'" '.$qcm_attr.' type="'.$typeqcm.'"'.($reqqcm?' required':'').($q==$qrm[$idqrm]?' checked':'').''.' />'.PHP_EOL;
         }
         $echoqrm .= '  <input class="deldrm'.$autreclass.'" id="qcm'.$c.'" name="qcm'.$c.'" '.$qcm_attr.' type="text" size="30" style="'.$autredisp.'"'.($qrm[$idqrm]?' required':'').' placeholder="'.$plxPlugin->getLang('L_REPONSE_AUTRE').', '.$plxPlugin->getLang('L_REPONSE_PRECIS').'..." value="'.plxUtils::strCheck($qcm[$c]).'" />'.PHP_EOL;//<br />
        }
        
        if($typeqcm=='select')//todo  '.$plxPlugin->getParam('qcm_attr'.$q).'
         plxUtils::printSelect('qrm'.$idqrm, $selectqrm, $qrm[$idqrm], false, 'deldrm'.($qrm[$idqrm]?'" required="required':'').$qcm_attr_sel, 'qrm'.$c.'_1');//.$q
        echo $echoqrm;
?>
     </p></div>
    </div>
<?php
     }//fi qcm.$c AND Param qrm_title_.$c...
    }//rof nombre_qcm
?>
<script type="text/javascript"><?php /* mix of http://stackoverflow.com/a/5897277 & http://stackoverflow.com/a/32077130 */ ?>
function check() {
 var checkBoxes = new Array();
 var checkTypes = new Array('select-one','checkbox','radio');
 var checkBoxes = (function() {/* Old browser */
  var ret=[], elems = document.getElementsByClassName('deldrm'), i=0,j=elems.length;
  for (;i<j;i++) {
   if (checkTypes.indexOf(elems[i].type.toLowerCase()) != -1) {
    if( elems[i].id.search(/qrm/) !== -1 )
     ret.push(elems[i]);
   }
  }
  return ret;
 }());

 var nb = <?php echo $nombre_qcm ?>;//checkBoxes.length;//
 var r = [];
 var isChecked = [];
 for (var qcm = 0; qcm < nb; qcm++) {
  isChecked.push(false);
  numqcm = (qcm+1);
  a = document.getElementById('qcm'+numqcm);//autres
  q = document.getElementById('qrm'+numqcm);//reponses
  if(!a || !q) continue;
  r.push([a.id, q.id, q.getAttribute("data-required"), q.getAttribute("data-nb"), q.getAttribute("data-autre")]);
 }
 var t = 1;//for array key of r
 for (var i = 0; i < checkBoxes.length; i++) {
  var qrmId = false;
  if(checkBoxes[i].id){// found id
   qrmId = checkBoxes[i].id.split('_');
   qrmId = qrmId[0];
  }
  if(qrmId){ 
   if( qrmId.search(/qrm/) !== -1 )
    t = qrmId.replace(/qrm/,'');//adapt key of r
   t = t - 1;
   if(r[t] && (r[t][1] == qrmId)){
    num = r[t][3];num++;
    rehto = r[t][4];//other activated
    autre = r[t][1]+"_"+num;//chk id : qrm#_#
    other = r[t][0];//txt id ; qcm#
    requi = !r[t][2];//bool
    if ( checkBoxes[i].checked ) {//checkedyep
     isChecked[t] = true;
     if(rehto && i == (num)){//Si autre est activé
      if(checkBoxes[i].id == autre){
       document.getElementById(other).required = true;//qcm (autre) text
      }
      else{
       document.getElementById(other).required = false;//qcm (autre) text
      }
     }//fi autre
    }//fi checked
    if(rehto && !document.getElementById(other).value){
     if ( isChecked[t] ){//qcmchecked
      document.getElementById(autre).required = false;//qrm (autre) check
     }
     else{//qcm uncheck
      if(requi){//qcm recheck 
       document.getElementById(autre).required = true;// qrm (autre) check
      }
     }
    }//fi other is active & !have value
    var autreid = autre.replace('_'+num,'_1');
    if(!!document.getElementById(other).value)//have other txt
     if(document.getElementById(autre))//qrm (autre) chk + rad
      document.getElementById(autre).checked = true;//qrm (autre) chk + rad
     else
      document.getElementById(autreid).value = num;//qrm (autre) select
    if(document.getElementById(autreid) && document.getElementById(autreid).value == num){//qcm required other
     document.getElementById(other).required = true;//qrm (autre)text select
     document.getElementById(other).style.display = '';//qrm (autre)text select
    }else if(document.getElementById(autre) && document.getElementById(autre).checked){
     document.getElementById(other).required = true;//qrm (autre)text chk & rad
    }else{
     document.getElementById(other).required = false;//qrm (autre)text
    }
   }//fi qrmId
  }
 }//rof checkboxes
}//funk check
<?php if($selectHaveOther){//Si type select +autre activé ?>
function selectOtherToggle(val,nb,i){
 e = document.getElementById('qcm'+i);
 if(val == nb){
  e.style.display='';
 }else{
  e.style.display='none';
  e.style.value='';
 }
}
<?php }#fi selectHaveOther ?>
</script>
<?php
   }//fi sondage (qcm qrm)
   echo $after_qcm;//champ perso
   if($plxPlugin->getParam('message')){?>
     <p><label for="message"><?php $plxPlugin->lang('L_FORM_CONTENT') ?>&nbsp;<?php if($plxPlugin->getParam('message_obligatoire')){echo'*';}?>:</label>
     <textarea id="message" name="content" cols="60" rows="12"<?php if($plxPlugin->getParam('message_obligatoire')){echo' required';}?>><?php echo plxUtils::strCheck($content) ?></textarea></p>
<?php
   }//fi message
   echo $after_msg;//champ perso
   if($captcha):
?>
     <p><label for="id_rep"><strong><?php $plxPlugin->lang('L_FORM_ANTISPAM') ?>&nbsp;:</strong></label></p>
     <?php $this->capchaQ(); ?>&nbsp;*:
     <input id="id_rep" name="rep" type="text" size="2" maxlength="1" autocomplete="off" required style="width: auto; display: inline;" />
<?php endif; ?>
     <p>*&nbsp;<i class="text-red"><?php $plxPlugin->lang('L_CHAMPS_OBLIGATOIRES') ?></i></p>
     <p>
      <input type="submit" name="submit" value="<?php $plxPlugin->lang('L_FORM_BTN_SEND') ?>" onClick="check()" />
      <input type="reset" name="reset" value="<?php $plxPlugin->lang('L_FORM_BTN_RESET') ?>" />
     </p>
   </fieldset>
   <input type="hidden" name="objet_page" value="<?php echo $this->plxMotor->racine.$this->plxMotor->path_url ?>" />
  </form>
<?php endif; ?>
</div>
