<?php if(!defined('PLX_ROOT')) exit; ?>
<p>Je suis un fichier d'exemple a modifier pour être inclus avec maxiContact et je me trouve ici :<br /><i><?php echo __FILE__; ?></i></p>
<p>Voici les variables POSTÉS reçus :</p>
<?php 
foreach($_POST as $cle => $valeur)
 echo '<p>La clé "'.$cle.'" est égale à : '.$valeur.'</p>';
/*
L'exemple ci-après détourne le système des questions personnalisé ;)
Les trois premières questions sont utilisées pour le sujet et le message
La quatrième pour le chemin du fichier a joindre
Peu importe le type (de préférence en text ou hidden)
Q1 : Courriel avec votre cadeau
Q2 : Merci,
Q3 : Pour votre correspondance et voici votre cadeau.
Q4 : data/media/leFichierCadeau.zip

//le '#' est le numéro de la question (config)
//$plxPlugin->getParam('adesion_'.$plxPlugin->lang.'#')//texte de la question #
//if(!$plxPlugin->getParam('ades#'))//Rappel, teste si la question # est désactivé

Ps: ainsi, ceci permet a ce systeme d'être multilingue, même pour le ficher a joindre :)
*/
if(!empty($_POST)) {#envoi courriel + piece jointe ($fichierAJoindre) : a compléter/adapter
//NFO : \r\n est le seul saut de ligne (EOL) valide dans un courriel (multiplateforme) ou $plxPlugin->EOL ;)
 $person = ($sexe?$sexe.' ':'') . ($prenom?$prenom.' ':'') . $name . $plxPlugin->EOL;
 $subject = $plxPlugin->getParam('adesion_'.$plxPlugin->lang.'1');//idée : question 1 personnalisée inactive
//idée : questions (2 & 3) personnalisées désactivées
 $messRetour = $plxPlugin->getParam('adesion_'.$plxPlugin->lang.'2') . $person . $plxPlugin->getParam('adesion_'.$plxPlugin->lang.'3');
 $fichierAJoindre = $plxPlugin->getParam('adesion_'.$plxPlugin->lang.'4');//idée : question 4 personnalisée désactivée
 if(!is_file($fichierAJoindre)) {echo 'Fichier '.$fichierAJoindre.' invalide!';return;}
 $dest = $mail;/* A qui s'adresse ce mail (TO) ICI l'internaute */
 $dest_name = $person;/* A qui s'adresse ce mail (TO) - Nom parlant */
 $copy_dest = '';//$plxPlugin->getParam('email_cc');/* courriel pour la Copie Carbone (CC) */
 $cache_dest = $plxPlugin->getParam('email');// POUR SOI : ORIGIN $plxPlugin->getParam('email_bcc'); /* courriel pour la Copie Carbone Caché (BCC) */
 $priority = "3"; /* Permet de définir la priorité du mail, les valeurs vont de 1 (urgent) à 5 (priorité basse) */
#$extensions_ok = explode(',',$plxPlugin->getParam('extensions_ok'));//array('svg', 'png', 'gif', 'jpg', 'jpeg', 'bmp', 'pdf');#in param by default
 $taille_max = $maxUpload['value'];#param //2048000 == 2Mo
 $subject=StripSlashes($subject);
 // Formatage du corps du message
 $msg = StripSlashes($messRetour);
 $msg .= $plxPlugin->EOL . str_repeat('_',46) . $plxPlugin->EOL . str_repeat('_',46) . $plxPlugin->EOL;
 //require_once($dossier.'php/classMail.php');//On inclu la classe
 // Création de l'objet Mail: La valeur 'false' désactive la fonction autoCheck (cf: commentaire dans classMail.php)
 $m = new Mail(FALSE); //NEW LANG (fr) ,en,...
 $m->From($mail, plxUtils::removeAccents($subject,PLX_CHARSET), $dest, plxUtils::removeAccents($dest_name,PLX_CHARSET)); # envoi une notif a l'internaute lors de la verif ;)
 $m->To($dest);
 $m->Subject(plxUtils::removeAccents(plxUtils::unSlash($subject,PLX_CHARSET)));//1 est le num d'une queston désactivé (config)
 $m->Body($msg);
 $m->Organization(plxUtils::removeAccents($dest_name,PLX_CHARSET));
 $m->Priority($priority); 
 $m->lang($plxPlugin->getLang('L_CLASS_MAIL_ARRAY'));//NEW LOAD LANG
 if($copy_dest) $m->Cc($copy_dest);// une copie conforme du mail
 if($cache_dest) $m->Bcc($cache_dest);// copie cachée du mail
//attachement
 $m->Attach($fichierAJoindre, "application/octet-stream");
#var_dump($m);#DEBUG
//On envois avec #Send($save = FALSE, $send = TRUE)
 $res = $m->Send($plxPlugin->tmp.'/eml/', TRUE);// envoi a l'internaute & Sauve le courriel dans $plxPlugin->tmp.'/eml/' (se retrouve dans l'admin de maxiContact
 if(!$res) $plxPlugin->updateUnsend(1);//+1 au compteur d'echec (menu maxicontact) ::: SI ON SAUVE LE COURRIEL, sinon commenter ;)
#$res = $m->Send(FALSE, TRUE);//envoi juste le courriel
#$res = $m->Send(FALSE, FALSE);//RIEN (0 save & 0 send)
 if($res){//test si envoyé (free fr)
  echo '<img alt="'.$plxPlugin->getLang('L_SENDMAIL_OK').'." src="'.$dossier.'/img/mail_ok.gif" style="vertical-align:middle;" /><b>'.$plxPlugin->getLang('L_SENDMAIL_OK').'.</b><br />'.$logUpload.'<br /><br />'.$plxPlugin->getParam('thankyou_'.$lang);
 }
}
