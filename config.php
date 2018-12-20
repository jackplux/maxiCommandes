<?php if(!defined('PLX_ROOT')) exit;//Part of maxiContact plugin for PluXml
$preprog = array('sexe','prenom','motif','tel','fax','site','profession','adresse','entreprise','message','piece');//champs préprogrammés (piece tjrs a la fin!)
$types = explode(' ','button checkbox color date datetime datetime-local email file hidden image month number password radio range reset search submit tel text textarea time url week');//Types des Questions perso
$input_types = array();
$pluginName = get_class($plxPlugin);
foreach($types as $type)
 $input_types[$type] = ucfirst($type);
unset($types,$type);
# Liste des langues disponibles et prises en charge par le plugin
$aLangs = array($plxAdmin->aConf['default_lang']);
# Si le plugin plxMyMultiLingue est installé on filtre sur les langues utilisées
# On garde par défaut le fr si aucune langue sélectionnée dans plxMyMultiLingue
if(defined('PLX_MYMULTILINGUE')) {
	$langs = plxMyMultiLingue::_Langs();
	$multiLangs = empty($langs) ? array() : explode(',', $langs);
	$aLangs = $multiLangs;
}
#un seul test ;)
$aLngs = array();
$miss_langs = !1;//FALSE
foreach($aLangs as $lang) {
 $aLngs[$lang] = file_exists(PLX_PLUGINS.$pluginName.'/lang/'.$lang.'.php');
 $miss_langs = !$aLngs[$lang]?true:$miss_langs;//add alert class or not if langs missing
}
if(!empty($_POST)) {
 plxToken::validateFormToken($_POST);# Control du token du formulaire
 $plxPlugin->setParam('del_tmp', $_POST['del_tmp'], 'numeric');#OnDeactivate
 $plxPlugin->setParam('mnuDisplay', $_POST['mnuDisplay'], 'numeric');
 $plxPlugin->setParam('mnuPos', $_POST['mnuPos'], 'numeric');
 $plxPlugin->setParam('redirectUrl', $_POST['redirectUrl'], 'string');
 $plxPlugin->setParam('successIncUrl', $_POST['successIncUrl'], 'numeric');
 $plxPlugin->setParam('email', $_POST['email'], 'string');
 $plxPlugin->setParam('email_cc', $_POST['email_cc'], 'string');
 $plxPlugin->setParam('email_bcc', $_POST['email_bcc'], 'string');
 $plxPlugin->setParam('fake_send', $_POST['fake_send'], 'numeric');#Preserve webserver
 $plxPlugin->setParam('append_subject', $_POST['append_subject'], 'numeric');
 $plxPlugin->setParam('subject_obligatoire', $_POST['subject_obligatoire'], 'string');
 $plxPlugin->setParam('template', $_POST['template'], 'string');
 $plxPlugin->setParam('captcha', $_POST['captcha'], 'numeric');
 $plxPlugin->setParam('url', plxUtils::title2url($_POST['url']), 'string');
 $plxPlugin->setParam('force_lang', ($miss_langs?$_POST['force_lang']:'0'), 'numeric');
 $plxPlugin->setParam('piece_size', $_POST['piece_size'], 'string');
 $plxPlugin->setParam('post_size', $_POST['post_size'], 'string');
 $plxPlugin->setParam('extensions_ok', $_POST['extensions_ok'], 'string');
 foreach($aLangs as $lang) {
  if($plxPlugin->getParam('force_lang') OR $aLngs[$lang]){
   $plxPlugin->setParam('mnuName_'.$lang, $_POST['mnuName_'.$lang], 'string');
   $plxPlugin->setParam('subject_'.$lang, $_POST['subject_'.$lang], 'string');
   $plxPlugin->setParam('thankyou_'.$lang, $_POST['thankyou_'.$lang], 'string');
  }
 }
 foreach($preprog as $param){//champs préprogrammés
  $plxPlugin->setParam($param, $_POST[$param], 'numeric');
  $plxPlugin->setParam($param.'_obligatoire', $_POST[$param.'_obligatoire'], 'numeric');
 }

//Nettoyage par des Parametres dynamiques
 $nombre_ades=$plxPlugin->getParam('nombre_ades');
 for($q=1;$q<=$nombre_ades;$q++){
  $plxPlugin->delParam('ades'.$q);
  $plxPlugin->delParam('ades_ou'.$q);
  $plxPlugin->delParam('ades_type'.$q);
  $plxPlugin->delParam('ades_attr'.$q);
  $plxPlugin->delParam('adesion'.$q.'_obligatoire');
  foreach($aLangs as $lang){
   $plxPlugin->delParam('adesion_'.$lang.$q);
  }
 }
 $nombre_qcm=$plxPlugin->getParam('nombre_qcm');
 if($nombre_qcm){
  for($c=1;$c<=$nombre_qcm;$c++){//multi qcm

   $plxPlugin->delParam('qcm'.$c);
   $plxPlugin->delParam('qcm_type'.$c);
   $plxPlugin->delParam('qcm_attr'.$q);
   $plxPlugin->delParam('qcm_obligatoire'.$c);
   $plxPlugin->delParam('autre'.$c);

   $nombre_qrm[$c]=$plxPlugin->getParam('nombre_qrm'.$c);
   foreach($aLangs as $lang){
    $plxPlugin->delParam('qrm_title_'.$c.$lang);
   }
   if($nombre_qrm[$c]){
    for($q=1;$q<=$nombre_qrm[$c];$q++){
     foreach($aLangs as $lang){
      $plxPlugin->delParam('qrm_'.$c.$lang.$q);
     }
    }
    $plxPlugin->delParam('nombre_qrm'.$c);
   }
  }
  $plxPlugin->delParam('nombre_qcm');
 }
//Nouvelles ou anciennes valeurs ;-)
 $plxPlugin->setParam('nombre_ades', $_POST['nombre_ades'], 'numeric');
 $nombre_ades=(int)$_POST['nombre_ades'];
 for($q=1;$q<=$nombre_ades;$q++){
  $plxPlugin->setParam('ades'.$q, $_POST['ades'.$q], 'numeric');
  $plxPlugin->setParam('ades_ou'.$q, $_POST['ades_ou'.$q], 'string');
  $plxPlugin->setParam('ades_type'.$q, $_POST['ades_type'.$q], 'string');
  $plxPlugin->setParam('ades_attr'.$q, $_POST['ades_attr'.$q], 'cdata');//str_replace('&quot;','"',$_POST['ades_attr'.$q])
  $plxPlugin->setParam('adesion'.$q.'_obligatoire', $_POST['adesion'.$q.'_obligatoire'], 'numeric');
  foreach($aLangs as $lang){
   if($plxPlugin->getParam('force_lang') OR $aLngs[$lang]){
    $plxPlugin->setParam('adesion_'.$lang.$q, $_POST['adesion_'.$lang.$q], 'string');
   }
  }
 }//rof ades
 $plxPlugin->setParam('nombre_qcm', $_POST['nombre_qcm'], 'numeric');
 $nombre_qcm = (int)$_POST['nombre_qcm'];
 if($nombre_qcm){
  for($c=1;$c<=$nombre_qcm;$c++){//multi qcm
   $plxPlugin->setParam('qcm'.$c, $_POST['qcm'.$c], 'numeric');
   $plxPlugin->setParam('qcm_type'.$c, $_POST['qcm_type'.$c], 'string');
   $plxPlugin->setParam('qcm_attr'.$c, $_POST['qcm_attr'.$c], 'cdata');
   $plxPlugin->setParam('qcm_obligatoire'.$c, $_POST['qcm_obligatoire'.$c], 'numeric');
   $plxPlugin->setParam('autre'.$c, $_POST['autre'.$c], 'numeric');
   foreach($aLangs as $lang){
    if($plxPlugin->getParam('force_lang') OR $aLngs[$lang]){
     $plxPlugin->setParam('qrm_title_'.$c.$lang, $_POST['qrm_title_'.$c.$lang], 'string');
    }
   }
   $plxPlugin->setParam('nombre_qrm'.$c, $_POST['nombre_qrm'.$c], 'numeric');
   $nombre_qrm[$c]=(int)$_POST['nombre_qrm'.$c];
   if($nombre_qrm[$c]){
    for($q=1;$q<=$nombre_qrm[$c];$q++){
     foreach($aLangs as $lang){
      if($plxPlugin->getParam('force_lang') OR $aLngs[$lang]){
       $plxPlugin->setParam('qrm_'.$c.$lang.$q, $_POST['qrm_'.$c.$lang.$q], 'string');
      }
     }
    }
   }
  }
 }//fi qcm
 $plxPlugin->saveParams();//→ð if(plxUtils::write($xml,$this->plug['parameters.xml'])) ::: $this->plug['parameters.xml'] multiple forms
#header('Location:');//MODERATOR ACCESS BY ADMIN URL ::: nook in old chromium
 header('Location:'.$plxAdmin->racine.$plxAdmin->path_url);//MODERATOR ACCESS BY ADMIN URL
 exit;
}//fi POST
#($plxMotor/$plxAdmin)->version removed in PluXml 5.5
$error = (isset($plxAdmin->version) && version_compare($plxAdmin->version, "5.2", "<="));//PluXml <= 5.2 fallback fix mode : DEV: change private TO public IN plxPlugin CLASS loadLang() to solve it
$var = array();
# initialisation des variables propres à chaque lanque
$langs = array();
foreach($aLangs as $lang) {
  if(!$error)# Tentative de chargement de chaque fichier de langue
   $langs[$lang] = $plxPlugin->loadLang(PLX_PLUGINS.$pluginName.'/lang/'.$lang.'.php');//Fatal error: Call to private method plxPlugin::loadLang() from context '' : (pluxml-5.2)   
  else $langs[$lang] = '';//$plxAdmin->plxPlugins->aPlugins[$pluginName]->loadLang(PLX_PLUGINS.$pluginName.'/lang/'.$lang.'.php');//Fix Fatal error: Call to private method plxPlugin::loadLang() from context '' : (pluxml-5.2)   
 $var['mnuName_'.$lang]  = $plxPlugin->getParam('mnuName_'.$lang)=='' ? (!$error?$langs[$lang]['L_DEFAULT_MENU_NAME']:$plxPlugin->getLang('L_DEFAULT_MENU_NAME')) : $plxPlugin->getParam('mnuName_'.$lang);
 $var['subject_'.$lang]  = $plxPlugin->getParam('subject_'.$lang)=='' ? (!$error?$langs[$lang]['L_DEFAULT_OBJECT']:$plxPlugin->getLang('L_DEFAULT_OBJECT')) : $plxPlugin->getParam('subject_'.$lang);
 $var['thankyou_'.$lang] = $plxPlugin->getParam('thankyou_'.$lang)=='' ? (!$error?$langs[$lang]['L_DEFAULT_THANKYOU']:$plxPlugin->getLang('L_DEFAULT_THANKYOU')) : $plxPlugin->getParam('thankyou_'.$lang);
}
# initialisation des variables communes à chaque langue
$var['del_tmp'] = $plxPlugin->getParam('del_tmp')=='' ? 0 : $plxPlugin->getParam('del_tmp');
$var['mnuDisplay'] = $plxPlugin->getParam('mnuDisplay')=='' ? 1 : $plxPlugin->getParam('mnuDisplay');
$var['mnuPos'] = $plxPlugin->getParam('mnuPos')=='' ? 2 : $plxPlugin->getParam('mnuPos');
$var['redirectUrl'] = $plxPlugin->getParam('redirectUrl')=='' ? '' : $plxPlugin->getParam('redirectUrl');
$var['successIncUrl'] = $plxPlugin->getParam('successIncUrl')=='' ? 0 : $plxPlugin->getParam('successIncUrl');
$var['email'] = $plxPlugin->getParam('email');//=='' ? '' : $plxPlugin->getParam('email');
$var['email_cc'] = $plxPlugin->getParam('email_cc');//=='' ? '' : $plxPlugin->getParam('email_cc');
$var['email_bcc'] = $plxPlugin->getParam('email_bcc');//=='' ? '' : $plxPlugin->getParam('email_bcc');
$var['fake_send'] = $plxPlugin->getParam('fake_send')=='' ? 1 : $plxPlugin->getParam('fake_send');
$var['append_subject'] = $plxPlugin->getParam('append_subject')=='' ? 0 : $plxPlugin->getParam('append_subject');
$var['subject_obligatoire'] = $plxPlugin->getParam('subject_obligatoire')=='' ? 0 : $plxPlugin->getParam('subject_obligatoire');
$var['template'] = $plxPlugin->getParam('template')=='' ? 'static.php' : $plxPlugin->getParam('template');
$var['captcha'] = $plxPlugin->getParam('captcha')=='' ? 1 : $plxPlugin->getParam('captcha');
$var['url'] = $plxPlugin->getParam('url')=='' ? 'contact' : $plxPlugin->getParam('url');
$var['force_lang'] = $plxPlugin->getParam('force_lang')=='' ? 0 : $plxPlugin->getParam('force_lang');
$var['piece_size'] = $plxPlugin->getParam('piece_size')=='' ? strtoupper(ini_get("upload_max_filesize")) : $plxPlugin->getParam('piece_size');
$var['post_size'] = $plxPlugin->getParam('post_size')=='' ? strtoupper(ini_get("post_max_size")) : $plxPlugin->getParam('post_size');
$var['extensions_ok'] = $plxPlugin->getParam('extensions_ok')=='' ? 'png,gif,jpg,jpeg,bmp,pdf,xcf,svg,zip' : $plxPlugin->getParam('extensions_ok');
# On récupère les templates des pages statiques
$var['files'] = plxGlob::getInstance(PLX_ROOT.$plxAdmin->aConf['racine_themes'].$plxAdmin->aConf['style']);
if ($array = $var['files']->query('/^static(-[a-z0-9-_]+)?.php$/')) {
 foreach($array as $k=>$v)
  $aTemplates[$v] = $v;
}
# On récupère les champs personalisées
$nombre_ades = $plxPlugin->getParam('nombre_ades')=='' ? 0 : $plxPlugin->getParam('nombre_ades');
if($nombre_ades)
 for ($q=1;$q<=$nombre_ades;$q++){
  foreach($aLangs as $lang){
   $var['ades'][$q] = $plxPlugin->getParam('ades'.$q)=='' ? 1: $plxPlugin->getParam('ades'.$q);
   $var['ades_ou'][$q] = $plxPlugin->getParam('ades_ou'.$q)=='' ? 'debut': $plxPlugin->getParam('ades_ou'.$q);
   $var['ades_type'][$q] = $plxPlugin->getParam('ades_type'.$q)=='' ? 'text': $plxPlugin->getParam('ades_type'.$q);
   $var['ades_attr'][$q] = $plxPlugin->getParam('ades_attr'.$q);//=='' ? '': $plxPlugin->getParam('ades_attr'.$q);
   $var['adesion_obligatoire'][$q] = $plxPlugin->getParam('adesion'.$q.'_obligatoire')=='' ? 1 : $plxPlugin->getParam('adesion'.$q.'_obligatoire');
   if($plxPlugin->getParam('force_lang') OR $aLngs[$lang]){
    $var['adesion_'.$lang][$q] = $plxPlugin->getParam('adesion_'.$lang.$q);//=='' ? $q.$lang  : $plxPlugin->getParam('adesion_'.$lang.$q);
   }
  }
 }

foreach($preprog as $param){//champs préprogrammés
 ${$param} = $plxPlugin->getParam($param)=='' ? 1 : $plxPlugin->getParam($param);
 ${$param.'_obligatoire'} = $plxPlugin->getParam($param.'_obligatoire')=='' ? '1' : $plxPlugin->getParam($param.'_obligatoire');
}

$nombre_qcm = $plxPlugin->getParam('nombre_qcm')=='' ? 0 : $plxPlugin->getParam('nombre_qcm');//not sure
if($nombre_qcm){
 for ($c=1;$c<=$nombre_qcm;$c++){//multi qcm
  $nombre_qrm[$c] = $plxPlugin->getParam('nombre_qrm'.$c)=='' ? 0 : $plxPlugin->getParam('nombre_qrm'.$c);//not sure
  $var['qcm'][$c] = $plxPlugin->getParam('qcm'.$c)=='' ? 1 : $plxPlugin->getParam('qcm'.$c);
  $var['qcm_type'][$c] = $plxPlugin->getParam('qcm_type'.$c)=='' ? 'checkbox' : $plxPlugin->getParam('qcm_type'.$c);
  $var['qcm_attr'][$c] = $plxPlugin->getParam('qcm_attr'.$c);//=='' ? '' : $plxPlugin->getParam('qcm_attr'.$c);
  $var['qcm_obligatoire'][$c] = $plxPlugin->getParam('qcm_obligatoire'.$c)=='' ? 0 : $plxPlugin->getParam('qcm_obligatoire'.$c);
  $var['autre'][$c] = $plxPlugin->getParam('autre'.$c)=='' ? 0 : $plxPlugin->getParam('autre'.$c);
  # On récupère les champs (qcm) personalisées
  foreach($aLangs as $lang){
   if($plxPlugin->getParam('force_lang') OR $aLngs[$lang]){
    $var['qrm_title_'.$c.$lang] = $plxPlugin->getParam('qrm_title_'.$c.$lang);// == '' ? $plxPlugin->getLang('L_COMMENT2') : $plxPlugin->getParam('qrm_title_'.$c.$lang);
   }
  }
  if($nombre_qrm[$c]){
   for($q=1;$q<=$nombre_qrm[$c];$q++){
    foreach($aLangs as $lang){
     if($plxPlugin->getParam('force_lang') OR $aLngs[$lang]){
      $var['qrm_'.$c.$lang][$q] = $plxPlugin->getParam('qrm_'.$c.$lang.$q);
     }
    }
   }
  }
 }
}
$fl = !!$var['force_lang'];//in perso & qcm tabs

echo '<p><img id="mc_mode" class="icon_pmc" src="'.PLX_PLUGINS.$pluginName.'/icon.png" title="'.$plxPlugin->getLang('L_DESC_CONF').'" />&nbsp;';
if(function_exists('mail')) {
 echo '<span style="color:green"><strong>'.$plxPlugin->getLang('L_MAIL_AVAILABLE').'</strong></span>';
} else {
 echo '<span style="color:#ff0000"><strong>'.$plxPlugin->getLang('L_MAIL_NOT_AVAILABLE').'</strong></span>';
}
if($error) {
 echo ' <span style="color:darkorange"><strong>'.$plxPlugin->getLang('L_ERROR_LLANG').'</strong></span>';
}
echo '</p>';
?>
<h2 class="hide"><?php echo $pluginName ?></h2>
<div id="tabContainer">
<form id="set" class="inline-form" action="" method="post" onSubmit="tabPoster();">
 <?php echo plxToken::getTokenPostMethod() ?>
 <h4><sub><sup><?php $plxPlugin->lang('L_DESC_CONF') ?></sup></sub></h4>
 <fieldset>
<?php
$prfl = ($_SESSION['profil'] > PROFIL_ADMIN);
#/i\ : $plxMotor/$plxAdmin->version removed in 5.5
$hlpvers = (isset($plxAdmin->version))?'parametres_pluginhelp.php?p=':'parametres_help.php?help=plugin&amp;page=';//<5.5 : >=5.5
$hlpHref = ($prfl?'plugin.php?p=':$hlpvers).$pluginName.($prfl?'&amp;z=lang/'.$plxPlugin->lang.'-help':'');//parametres_help.php?help=plugin&page=
?>
  <p id="action" class="<?php echo $pluginName ?> in-action-bar"><?php echo $prfl?'<span id="jeckyl">&nbsp;</span>':'' ?>
   <a href="plugin.php?p=<?php echo $pluginName ?>" title="<?php echo L_VIEW.' '.$plxPlugin->getLang('L_CACHE_LIST') ?>"><img id="admin" class="icon_pmc" alt="admin" src="<?php echo PLX_PLUGINS.$pluginName ?>/img/admin.png" /></a> 
   <input type="submit" name="submit" onClick="getElementById('set').submit();" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
   <a href="<?php echo $hlpHref ?>" title="<?php echo @L_PLUGINS_HELP_TITLE ?>"><img id="help" class="icon_pmc" alt="help" src="<?php echo PLX_PLUGINS.$pluginName ?>/img/help.png" /></a>
   <a target="_blank" href="<?php echo $plxAdmin->urlRewrite('?'.(defined('PLX_MYMULTILINGUE')?($_SESSION['data_lang']==$plxPlugin->lang ? '' : $_SESSION['data_lang'].'/'):'').$plxPlugin->getParam('url')) ?>" title="<?php echo $plxPlugin->lang('L_SEE_PAGE').' ('.$plxPlugin->getParam('mnuName_'.(defined('PLX_MYMULTILINGUE')?$_SESSION['data_lang']:$plxPlugin->lang))?>)"><?php echo $plxPlugin->lang('L_SEE')?></a>
  </p>
 </fieldset>
 <div class="tabs clear">
  <ul id="onglets" data-current="<?php echo (isset($_POST['tablive'])?$_POST['tablive']:'main') ?>">
   <li id="tabHeader_main"><a href="#main"><?php $plxPlugin->lang('L_MAIN') ?></a></li>
   <li id="tabHeader_perso"><a href="#perso"><?php $plxPlugin->lang('L_PERSO') ?></a></li>
   <li id="tabHeader_main2"><a href="#main2"><?php $plxPlugin->lang('L_MAIN2') ?></a></li>
   <li id="tabHeader_qcm"><a href="#qcm"><?php $plxPlugin->lang('L_QCM') ?></a></li>
  </ul>
 </div>
 <div class="tabscontent">
  <div class="tabpage clear" id="tabpage_main">
   <noscript><hr id="main" /><h2><?php $plxPlugin->lang('L_MAIN') ?></h2></noscript>
   <fieldset>

    <fieldset class="borders<?php echo $var['del_tmp']?' red':''; ?>">
     <div class="grid light">
      <div class="col sml-12 med-5 label-centered">
       <label for="id_del_tmp"><?php echo $plxPlugin->lang('L_DEL_TMP') ?>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_DEL_TMP_HINT') ?></span></a>&nbsp;:</label>
      </div>
      <div class="col sml-12 med-7">
     <?php plxUtils::printSelect('del_tmp',array('1'=>L_YES,'0'=>L_NO),$var['del_tmp']) ?>
      </div>
     </div>
    </fieldset>

    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_url"><?php $plxPlugin->lang('L_URL') ?>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printInput('url',$var['url'],'text','20-255') ?>
     </div>
    </div>

    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_mnuDisplay"><?php echo $plxPlugin->lang('L_MENU_DISPLAY') ?>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printSelect('mnuDisplay',array('1'=>L_YES,'0'=>L_NO),$var['mnuDisplay']) ?>
     </div>
    </div>

<?php foreach($aLangs as $lang) : ?>
    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_mnuName_<?php echo $lang ?>"><?php $plxPlugin->lang('L_MENU_TITLE') ?>&nbsp;(<?php echo $lang ?>)&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printInput('mnuName_'.$lang,$var['mnuName_'.$lang],'text','20-20') ?>
     </div>
    </div>
<?php endforeach; ?>

    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_mnuPos"><?php $plxPlugin->lang('L_MENU_POS') ?>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printInput('mnuPos',$var['mnuPos'],'text','2-5') ?>
     </div>
    </div>

    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_redirectUrl"><?php $plxPlugin->lang('L_REDIRECT_URL') ?>&nbsp;(<?php $plxPlugin->lang('L_OPTIONNEL') ?>)&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printInput('redirectUrl',$var['redirectUrl'],'text','60-255',FALSE,'" placeholder="index.php?static_3 ... plugins/maxiContact/form.inc.php') ?>
     </div>
    </div>

    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_successIncUrl"><?php echo $plxPlugin->lang('L_SUCCESS_INC_URL') ?>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_SUCCESS_INC_HINT') ?></span></a>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printSelect('successIncUrl',array('1'=>L_YES,'0'=>L_NO),$var['successIncUrl']) ?>
     </div>
    </div>

<?php foreach($aLangs as $lang) : ?>
    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_thankyou_<?php echo $lang ?>"><?php $plxPlugin->lang('L_THANKYOU_MESSAGE') ?>&nbsp;(<?php echo $lang ?>)&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printInput('thankyou_'.$lang,$var['thankyou_'.$lang],'text','60-120') ?>
     </div>
    </div>
<?php endforeach; ?>

    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_email"><?php $plxPlugin->lang('L_EMAIL') ?>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_COMMA') ?></span></a>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printInput('email',$var['email'],'text','50-120') ?>
     </div>
    </div>

    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_email_cc"><?php $plxPlugin->lang('L_EMAIL_CC') ?>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_COMMA') ?></span></a>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printInput('email_cc',$var['email_cc'],'text','50-120') ?>
     </div>
    </div>

    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_email_bcc"><?php $plxPlugin->lang('L_EMAIL_BCC') ?>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_COMMA') ?></span></a>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printInput('email_bcc',$var['email_bcc'],'text','50-120') ?>
     </div>
    </div>

    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_fake_send"><?php echo $plxPlugin->lang('L_FAKE_SEND') ?>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_FAKE_SEND_HINT') ?></span></a>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printSelect('fake_send',array('1'=>L_YES,'0'=>L_NO),$var['fake_send']) ?>
     </div>
    </div>

<?php foreach($aLangs as $lang) : ?>
    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_subject_<?php echo $lang ?>"><?php $plxPlugin->lang('L_EMAIL_SUBJECT') ?>&nbsp;(<?php echo $lang ?>)&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printInput('subject_'.$lang,$var['subject_'.$lang],'text','60-120') ?>
     </div>
    </div>
<?php endforeach; ?>
    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
     <label for="id_append_subject"><?php $plxPlugin->lang('L_APPEND_EMAIL_SUBJECT') ?>&nbsp;:</label>
     </div>
     <div class="col sml-6 med-2">
    <?php plxUtils::printSelect('append_subject',array('1'=>L_YES,'0'=>L_NO),$var['append_subject']); ?>
     </div>
     <div class="col sml-6 med-4 label-centered">
      <label for="id_subject_obligatoire">&nbsp;&nbsp;<?php echo $plxPlugin->lang('L_OBLIGATOIRE') ?>&nbsp;:</label>
    <?php plxUtils::printSelect('subject_obligatoire',array('1'=>L_YES,'0'=>L_NO),$var['subject_obligatoire']) ?>
     </div>
    </div>
<?php
  $max_file_uploads = ini_get('max_file_uploads');//Free Fix
  $max_file_uploads = empty($max_file_uploads)?20:$max_file_uploads;//Free Fix
  $maxFU = array(L_NO); for($m = 1; $m <= $max_file_uploads; $m++){$maxFU[$m] = $m;}
?>
    <fieldset class="borders">
     <div class="grid light">
      <div class="col sml-12 med-5 label-centered">
       <label for="id_piece"><?php echo $plxPlugin->lang('L_PIECE') ?>&nbsp;:</label>
      </div>
      <div class="col sml-6 med-2">
       <?php plxUtils::printSelect('piece',$maxFU,$piece,false,'" onchange="if (this.value>0) { document.getElementById(\'pieceblock\').style.display=\'block\';}else{document.getElementById(\'pieceblock\').style.display=\'none\';}') ?>
      </div>
      <div class="col sml-6 med-4 label-centered">
       <label for="id_piece_obligatoire">&nbsp;&nbsp;<?php echo $plxPlugin->lang('L_OBLIGATOIRE') ?>&nbsp;:</label>
       <?php plxUtils::printSelect('piece_obligatoire',array('1'=>L_YES,'0'=>L_NO),$piece_obligatoire) ?>
      </div>
     </div>

     <div id="pieceblock" style="display:<?php echo ($piece)?'block':'none'; ?>;">
      <div class="grid light">
       <div class="col sml-12 med-5 label-centered">
        <label for="id_piece_size"><?php echo $plxPlugin->lang('L_PIECE_SIZE') ?>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_POST_SIZE_HINT') ?></span></a>&nbsp;:</label>
       </div>
       <div class="col sml-12 med-7">
      <?php plxUtils::printInput('piece_size',$var['piece_size']) ?>
       </div>
      </div>

      <div class="grid light">
       <div class="col sml-12 med-5 label-centered">
        <label for="id_post_size"><?php echo $plxPlugin->lang('L_POST_SIZE') ?>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_POST_SIZE_HINT') ?></span></a>&nbsp;:</label>
       </div>
       <div class="col sml-12 med-7">
      <?php plxUtils::printInput('post_size',$var['post_size']) ?>
       </div>
      </div>

      <div class="grid light">
       <div class="col sml-12 med-5 label-centered">
        <label for="id_extensions_ok"><?php echo $plxPlugin->lang('L_EXTENSIONS_OK') ?>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_COMMA') ?></span></a>&nbsp;:</label>
       </div>
       <div class="col sml-12 med-7">
      <?php plxUtils::printInput('extensions_ok',$var['extensions_ok']) ?>
       </div>
      </div>
     </div>
    </fieldset>

    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_captcha"><?php echo $plxPlugin->lang('L_CAPTCHA') ?>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printSelect('captcha',array('1'=>L_YES,'0'=>L_NO),$var['captcha']) ?>
     </div>
    </div>

    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_template"><?php $plxPlugin->lang('L_TEMPLATE') ?>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printSelect('template', $aTemplates, $var['template']) ?>
     </div>
    </div>

   </fieldset>
  </div>
  <div class="tabpage clear" id="tabpage_perso">
   <noscript><hr id="perso" /><h2><?php $plxPlugin->lang('L_PERSO') ?></h2></noscript>
   <fieldset>
    <div class="grid light"<?php echo (($miss_langs OR $var['force_lang']) ?'':'style="display:none;"');?>>
     <div class="col sml-12 med-5 label-centered">
      <label for="id_captcha"><?php echo $plxPlugin->lang('L_FORCE_LANG') ?>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
    <?php plxUtils::printSelect('force_lang',array('1'=>L_YES,'0'=>L_NO),$var['force_lang']) ?>
     </div>
    </div>
    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_nombre_ades"><?php $plxPlugin->lang('L_NOMBRE_ADES') ?>&nbsp;:<?php if($nombre_ades){?>&nbsp;<a style="text-transform: capitalize;" class="needscript hide" id="toggler_ades" href="javascript:void(0)" onclick="toggleClass('toggler_ades', 'toggle_ades', '<?php echo L_ARTICLE_CHAPO_DISPLAY ?>','<?php echo L_ARTICLE_CHAPO_HIDE ?>')"><?php echo L_ARTICLE_CHAPO_HIDE ?></a><?php }?></label>
     </div>
     <div class="col sml-12 med-7">
   <?php plxUtils::printInput('nombre_ades',$nombre_ades,'number','2-10', FALSE, '" min="0') ?>
     </div>
    </div>
<?php
  $strlen = strlen($nombre_ades);
  for ($q=1;$q<=$nombre_ades;$q++){
?><br />
   <label for="ades_toggle<?php echo $q ?>"><?php echo'<img class="icon_cnf" title="'.$plxPlugin->getLang('L_'.($var['ades'][$q]?'':'IN').'ACTIVE' ).'" src="'.PLX_PLUGINS.$pluginName.'/img/'.($var['ades'][$q]?'on':'off').'.png" />'; $plxPlugin->lang('L_QUESTION_PERSO')?>&nbsp;#<code><?php echo str_pad($q, $strlen, "0", STR_PAD_LEFT).'<sup>'.($var['adesion_obligatoire'][$q]?'*':'∞').'</sup>' ?></code>&nbsp;:&nbsp;<a class="needscript hide" id="toggler_ades<?php echo $q ?>" href="javascript:void(0)" onclick="toggleDiv('toggle_ades<?php echo $q ?>', 'toggler_ades<?php echo $q ?>', '<?php echo L_ARTICLE_CHAPO_DISPLAY ?>','<?php echo L_ARTICLE_CHAPO_HIDE ?>')"><?php echo !$var['ades'][$q]?L_ARTICLE_CHAPO_DISPLAY:L_ARTICLE_CHAPO_HIDE ?></a></label>
   <div id="toggle_ades<?php echo $q ?>" class="toggle_ades<?php echo $var['ades'][$q]?'':' noscript' ?>">
<?php
   $mdnType = ($var['ades_type'][$q]!='textarea'?'input':'textarea');

   echo"
    <div class='grid light'>
     <div class='col sml-12 med-5 label-centered'>
      <label>";$plxPlugin->lang('L_QUEST');echo"&nbsp;#".$q.":</label>
     </div>
     <div class='col sml-3 med-2 label-centered'>
      <label for='id_ades".$q."'>";$plxPlugin->lang('L_ACTIVATE');echo"&nbsp;:</label>
      ";plxUtils::printSelect('ades'.$q,array('1'=>L_YES,'0'=>L_NO),$var['ades'][$q]);

   echo"
     </div>
     <div class='col sml-6 med-3 label-centered'>
      <label for='id_ades_type".$q."'>";$plxPlugin->lang('L_TYPE');echo"&nbsp;:</label>
      ";plxUtils::printSelect('ades_type'.$q,$input_types,$var['ades_type'][$q]);//ATTR wip

   echo"
     </div>
     <div class='col sml-3 med-2 label-centered'>
      <label for='id_adesion".$q."_obligatoire'>";$plxPlugin->lang('L_OBLIGATOIRE');echo"&nbsp;:</label>
      ";plxUtils::printSelect('adesion'.$q.'_obligatoire',array('1'=>L_YES,'0'=>L_NO),$var['adesion_obligatoire'][$q]);

   echo"
     </div>
    </div>";

   echo"
    <div class='grid light'>
     <div class='col sml-12 med-5 label-centered'>
      <label for='id_ades_attr".$q."'>";$plxPlugin->lang('L_ATTIBUTS');echo'&nbsp;<a class="hint"><span>'.$plxPlugin->getLang('L_ATTR_TTL').'</span></a>&nbsp;<a target="_blank" title="'.L_HELP.' Mozilla Developpers Network ('.$mdnType.')" href="https://developer.mozilla.org/'.$plxPlugin->lang.'/docs/Web/HTML/Element/'.$mdnType.'#'.$plxPlugin->getLang('L_H_MDN').'"><sup><sub>MDN</sub></sup></a>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
     ';
   plxUtils::printInput('ades_attr'.$q,plxUtils::strCheck($var['ades_attr'][$q]),'text','60-256',false,'" placeholder=\'style="width:25%;" value="16" size="18" max="20" onChange="if(this.value)" ...\' title="'.$plxPlugin->getLang('L_ATTR_TTL'));
   echo"
     </div>
    </div>";

   echo"
    <div class='grid light'>
     <div class='col sml-12 med-5 label-centered'>
      <label for='id_ades_ou".$q."'>";$plxPlugin->lang('L_AFFICHER');echo"&nbsp;:</label>
     </div>
     <div class='col sml-3 med-7'>
      
      ";plxUtils::printSelect('ades_ou'.$q,array('debut'=>$plxPlugin->getLang('L_DEBUT'),'prog'=>$plxPlugin->getLang('L_PROG'),'qcm'=>$plxPlugin->getLang('L_AQCM'),'msg'=>$plxPlugin->getLang('L_MSG')),$var['ades_ou'][$q]);

   echo"
     </div>
    </div>";

   foreach($aLangs as $lang) {
    $fe = !!$aLngs[$lang];
    $hint_lang = $fe?'':'<a class="'.($fl?'hint':'').'"><span>'.sprintf('<i>'.$plxPlugin->getLang('L_LANG_UNAVAILABLE').'</i>', PLX_PLUGINS.$pluginName.'/lang/'.$lang.'.php').'</span></a>';
    $ca = !$miss_langs?'blue':($fl?($fe?'green':'orange'):($fe?'':'red'));// 'success':'error'; 
    if(!$fe&!$fl) {echo '<p class="'.$ca.'">'.$hint_lang.'</p>';continue;}
    echo"
       <div class='grid alert ".$ca."'>
        <div class='col sml-12 med-5 label-centered'>
         <label for='id_adesion_".$lang.$q."'>".$plxPlugin->getLang('L_QUESTION_PERSO')."&nbsp;".$q."&nbsp;(".$lang.")&nbsp;".$hint_lang.":</label>
        </div>
       <div class='col sml-12 med-7'>
    ";
    plxUtils::printInput('adesion_'.$lang.$q,@$var['adesion_'.$lang][$q],'text','50-120');
    echo"   </div>
        </div>".PHP_EOL;
   }
?>
</div> <!-- toggler ades -->
<?php
  }//rof $nombre ades
?>
   </fieldset>
  </div>
  <div class="tabpage clear" id="tabpage_main2">
   <noscript><hr id="main2" /><h2><?php $plxPlugin->lang('L_MAIN2') ?></h2></noscript>
   <fieldset>
<?php foreach($preprog as $param){//champs préprogrammés
 if($param == 'piece')continue;
?>

    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_<?php echo $param ?>"><?php $plxPlugin->lang('L_'.strtoupper($param)) ?>&nbsp;:</label>
     </div>
     <div class="col sml-6 med-2">
      <?php plxUtils::printSelect($param,array('1'=>L_YES,'0'=>L_NO),${$param}) ?>
     </div>
     <div class="col sml-6 med-4 label-centered">
      <label for="id_<?php echo $param ?>_obligatoire">&nbsp;&nbsp;<?php $plxPlugin->lang('L_OBLIGATOIRE') ?>&nbsp;:</label>
      <?php plxUtils::printSelect($param.'_obligatoire',array('1'=>L_YES,'0'=>L_NO),${$param.'_obligatoire'}) ?>
     </div>
    </div>
<?php }//fi champs préprogrammés ?>
   </fieldset>
  </div>
  <div class="tabpage clear" id="tabpage_qcm">
   <noscript><hr id="qcm" /><h2><?php $plxPlugin->lang('L_QCM') ?></h2></noscript>
   <fieldset>
    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_nombre_qcm"><?php $plxPlugin->lang('L_NOMBRE_QCM') ?>&nbsp;:<?php if($nombre_qcm){?>&nbsp;<a style="text-transform: capitalize;" class="needscript hide" id="toggler_qcm" href="javascript:void(0)" onclick="toggleClass('toggler_qcm', 'toggle_qcm', '<?php echo L_ARTICLE_CHAPO_DISPLAY ?>','<?php echo L_ARTICLE_CHAPO_HIDE ?>')"><?php echo L_ARTICLE_CHAPO_HIDE ?></a><?php }?></label>
     </div>
     <div class="col sml-12 med-7">
   <?php plxUtils::printInput('nombre_qcm',$nombre_qcm,'number','2-10', FALSE, '" min="0') ?>
     </div>
    </div>
<?php
    $qcmlen = strlen($nombre_qcm);
//~ for ($c=$nombre_qcm;$c>0;$c--){//multi qcm (revert)
    for ($c=1;$c<=(int)$nombre_qcm;$c++){//multi qcm
     $qrmlen = strlen($nombre_qrm[$c]);
?><br />
   <label for="qcm_toggle<?php echo $c ?>"><?php echo '<img class="icon_cnf" src="'.PLX_PLUGINS.$pluginName.'/img/'.($var['qcm'][$c]?'on':'off').'.png" />'; $plxPlugin->lang('L_QCM')?>&nbsp;#<code><?php echo str_pad($c, $qcmlen, "0", STR_PAD_LEFT).'<sup>'.($var['qcm_obligatoire'][$c]?'*':'∞').'</sup>'.'<sup><sub>&nbsp;('.str_pad($nombre_qrm[$c], $qrmlen, "0", STR_PAD_LEFT).($var['autre'][$c]?'+¹':'·~').')</sub></sup>' ?></code>&nbsp;:&nbsp;<a class="needscript hide" id="toggler_qcm<?php echo $c ?>" href="javascript:void(0)" onclick="toggleDiv('toggle_qcm<?php echo $c ?>', 'toggler_qcm<?php echo $c ?>', '<?php echo L_ARTICLE_CHAPO_DISPLAY ?>','<?php echo L_ARTICLE_CHAPO_HIDE ?>')"><?php echo !$var['qcm'][$c]?L_ARTICLE_CHAPO_DISPLAY:L_ARTICLE_CHAPO_HIDE ?></a></label>
   <div id="toggle_qcm<?php echo $c ?>" class="toggle_qcm<?php echo $var['qcm'][$c]?'':' noscript' ?>">
    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label><?php $plxPlugin->lang('L_COMMENT') ?>&nbsp;#<code><?php echo str_pad($c, $qcmlen, "0", STR_PAD_LEFT) ?></code>&nbsp;:</label>
     </div>
     <div class="col sml-3 med-2 label-centered">
      <label for="id_qcm<?php echo $c ?>"><?php $plxPlugin->lang('L_ACTIVATE') ?>&nbsp;:</label>
      <?php plxUtils::printSelect('qcm'.$c,array('1'=>L_YES,'0'=>L_NO),$var['qcm'][$c]) ?>
     </div>
     <div class="col sml-6 med-3 label-centered">
      <label for="id_qcm_type<?php echo $c ?>"><?php $plxPlugin->lang('L_TYPE') ?>&nbsp;:</label>
      <?php plxUtils::printSelect('qcm_type'.$c,array('checkbox'=>'Checkbox','radio'=>'Radio','select'=>'Select'),$var['qcm_type'][$c]) ?>
     </div>
     <div class="col sml-3 med-2 label-centered">
      <label for="id_qcm_obligatoire<?php echo $c ?>"><?php $plxPlugin->lang('L_OBLIGATOIRE') ?>&nbsp;:</label>
      <?php plxUtils::printSelect('qcm_obligatoire'.$c,array('1'=>L_YES,'0'=>L_NO),$var['qcm_obligatoire'][$c]) ?>
     </div>
    </div>
<?php
$mdnType = ($var['qcm_type'][$c]!='select'?'input':'select');
echo "
    <div class='grid light'>
     <div class='col sml-12 med-5 label-centered'>
      <label for='id_qcm_attr".$c."'>";$plxPlugin->lang('L_ATTIBUTS');echo'&nbsp;<a class="hint"><span>'.$plxPlugin->getLang('L_ATTR_TTL').'</span></a>&nbsp;<a target="_blank" title="'.L_HELP.' Mozilla Developpers Network ('.$mdnType.')" href="https://developer.mozilla.org/'.$plxPlugin->lang.'/docs/Web/HTML/Element/'.$mdnType.'#'.$plxPlugin->getLang('L_H_MDN').'"><sup><sub>MDN</sub></sup></a>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
     ';
   plxUtils::printInput('qcm_attr'.$c,plxUtils::strCheck($var['qcm_attr'][$c]),'text','60-256',false,'" placeholder=\'style="width:25%;" value="16" size="18" max="20" onChange="if(this.value)" ...\' title="'.$plxPlugin->getLang('L_ATTR_TTL'));
   echo"   </div>
    </div>".PHP_EOL;
?>
<?php foreach($aLangs as $lang) : ?>
    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_qrm_title_<?php echo $c.$lang ?>"><?php $plxPlugin->lang('L_QCM_TITLE') ?>&nbsp;#<code><?php echo str_pad($c, $qcmlen, "0", STR_PAD_LEFT).'&nbsp;('.$lang ?>)</code>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
      <?php plxUtils::printInput('qrm_title_'.$c.$lang,$var['qrm_title_'.$c.$lang],'text','60-120') ?>
     </div>
    </div>
<?php endforeach;//$aLangs ?>
    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_nombre_qrm<?php echo $c ?>"><?php $plxPlugin->lang('L_NOMBRE_QRM') ?>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
      <?php plxUtils::printInput('nombre_qrm'.$c,$nombre_qrm[$c],'number','2-10', FALSE, '" min="0') ?>
     </div>
    </div>
    <div class="grid light">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_autre<?php echo $c ?>"><?php echo $plxPlugin->lang('L_AUTRE') ?>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
      <?php plxUtils::printSelect('autre'.$c,array('1'=>L_YES,'0'=>L_NO),$var['autre'][$c]) ?>
     </div>
    </div>
    <label for="qrm_toggle<?php echo $c ?>"<?php echo $nombre_qrm[$c]?'':' style="display:none"'?>><?php echo '<img class="icon_cnf" src="'.PLX_PLUGINS.$pluginName.'/img/'.($nombre_qrm[$c]?'on':'off').'.png" />';$plxPlugin->lang('L_ANSWERS')?>&nbsp;<?php $plxPlugin->lang('L_QCM') ?>&nbsp;#<?php echo $c ?>&nbsp;<sup>(<?php echo str_pad($nombre_qrm[$c], $qrmlen, "0", STR_PAD_LEFT)?>)</sup>&nbsp;:&nbsp;<a class="needscript hide" id="toggler_qrm<?php echo $c ?>" href="javascript:void(0)" onclick="toggleDiv('toggle_qrm<?php echo $c ?>', 'toggler_qrm<?php echo $c ?>', '<?php echo L_ARTICLE_CHAPO_DISPLAY ?>','<?php echo L_ARTICLE_CHAPO_HIDE ?>')"><?php echo !$nombre_qrm[$c]?L_ARTICLE_CHAPO_DISPLAY:L_ARTICLE_CHAPO_HIDE ?></a></label>
    <div id="toggle_qrm<?php echo $c ?>" style="display:<?php echo $nombre_qrm[$c]?'block':'none'; ?>;">
<?php
     for ($q=1;$q<=$nombre_qrm[$c];$q++){
      foreach($aLangs as $lang) {
       $fe = !!$aLngs[$lang];
       $hint_lang = $fe?'':'<a class="'.($fl?'hint':'').'"><span>'.sprintf('<i>'.$plxPlugin->getLang('L_LANG_UNAVAILABLE').'</i>', PLX_PLUGINS.$pluginName.'/lang/'.$lang.'.php').'</span></a>';
       $ca = !$miss_langs?'blue':($fl?($fe?'green':'orange'):($fe?'':'red'));// 'success':'error'; 
       if(!$fe&!$fl) {echo '<p class="'.$ca.'">'.$hint_lang.'</p>';continue;}
        echo"
     <div class='grid alert ".$ca."'>
      <div class='col sml-12 med-5 label-centered'>
       <label for='id_qrm_".$c.$lang.$q."'>".$plxPlugin->getLang('L_REPONSE_PERSO')."&nbsp;#".$c."-".$q."&nbsp;(".$lang.")&nbsp;".$hint_lang.":</label>
      </div>
     <div class='col sml-12 med-7'>".PHP_EOL;
        plxUtils::printInput('qrm_'.$c.$lang.$q,@$var['qrm_'.$c.$lang][$q],'text','50-120');
        echo"
     </div>
    </div>".PHP_EOL;
      }//hcaerof $aLangs
     }//rof $nombre_qrm?>
     </div><!-- reponses -->
    </div><!-- qrm_toggle -->
<?php
    }//rof $nombre_qcm
?>
   </fieldset>
  </div>
 </div>
</form>
</div>
<script type="text/javascript" src="<?php echo PLX_PLUGINS.$pluginName."/in/tabs/tabs.js" ?>"></script>
<script type="text/javascript" style="display:none">
//On affiche les liens (js) cachés
	var a = document.querySelectorAll('a.hide');
	for (i=0; i<a.length; i++)
		a[i].className="";/* unhide js clean file link */
//On déplace le menu dans l'action bar
	var a = document.querySelectorAll('.inline-form h4');a = a[0];
	var z = document.querySelectorAll('.action-bar');z = z[0];
	if(z){//PluXml => 5.4 fix
		var t = z.querySelectorAll('h2');t = t[0];
		t.innerHTML = t.innerHTML + ' : ' + a.innerHTML;
		a.className = 'hide';
		var a = document.getElementById('action');
		a.className = '';/* remove css nojs helper */
		a.firstChild.className = 'show';/* remove css nojs helper */
		z.appendChild(a);
	}
//hide noscript (toggles)
	var s = document.getElementsByClassName('noscript');//document.querySelectorAll('.noscript')
	for (var i = 0; i < s.length; i++)
		s[i].setAttribute('style','display:none;');
//show needscript (toggles)
	var s = document.getElementsByClassName('needscript');//document.querySelectorAll('.needscript')
	for (var i = 0; i < s.length; i++)
		s[i].className = '';//remove needscript hide

	function tabPoster(){/* Dynamic tabs onSubmit() helper */
		window.location.hash = document.getElementById('onglets').getAttribute('data-current');/* tablive with no cookie or session : Fix ScrollToTop */
	}
	function toggleClass(divId,divsClass,on,off){
		var toggler = document.getElementById(divId);
		var togglers = document.getElementsByClassName(divsClass);
		for (i=0; i<togglers.length; i++){
			if(toggler.innerHTML != off) {
				togglers[i].style.display = 'block';
				document.getElementById(togglers[i].id.replace('toggle','toggler')).innerHTML = off;
			} else {
				togglers[i].style.display = 'none';
				document.getElementById(togglers[i].id.replace('toggle','toggler')).innerHTML = on;
			}
		}
		if(toggler.innerHTML != off)
			toggler.innerHTML=off;
		else
			toggler.innerHTML=on;
	}
</script>
<?php $plxPlugin->tips();
