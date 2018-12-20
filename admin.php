<?php if(!defined('PLX_ROOT')) exit;//Part of maxiContact plugin for PluXml
$pluginName = get_class($plxPlugin);
if(!empty($_GET)){
 if(!empty($_GET['z'])){
  # Controle de l'accès à la page en fonction du profil de l'utilisateur connecté
  $plxAdmin->checkProfil(PROFIL_ADMIN, PROFIL_MANAGER);
  $inc = PLX_PLUGINS.$pluginName.'/'.$_GET['z'].'.php';
  if (file_exists($inc)) include($inc);
  else echo $_GET['z'] . ' EMPTY PLACE';
  $plxPlugin->endOfAdmin(FALSE);return;//goto ENDOFADMIN;//goto work with php.5.3+
 }
}
$url = $plxAdmin->racine.$plxAdmin->path_url.(isset($_GET['e'])?'&e=stop':'');
if(isset($_POST['write_to']) AND $_POST['write_to'] == 'write_to'){
 //OR empty($_POST['ajax'])
 //poivreetsel&ajaxmode=writeto

  $isAjax = (isset($_POST['val_sel']) && isset($_POST['ajaxmode']));//if(!empty($_GET['ajax']))
  #$plxPlugin::sendMail($name, $from, $reply, $to, $subject, $body, $contentType="text", $cc=false, $bcc=false)
  $name = $plxAdmin->aConf['title'];//$_POST[];
  $from = $_POST['email_from'];
  $reply = $_POST['email_from'];
  $to = $_POST['email'];
  $subject = $_POST['subject'];
  $body = $_POST['content'];
  $contentType = 'text';//html $_POST[];
  $cc = empty($_POST['email_cc'])?false:$_POST['email_cc'];
  $bcc = empty($_POST['email_bcc'])?false:$_POST['email_bcc'];

  $res = $plxPlugin->sendMail($name, $from, $reply, $to, $subject, $body, $contentType, $cc, $bcc);
  $error_get_last = error_get_last();
  if($error_get_last)
   $error_get_last = __CLASS__.' '.__FUNCTION__.' error_get_last<br />'.implode('<br />',$error_get_last);//deb
  else
   $error_get_last = '';
 if($isAjax){
  if($res)
   echo '<p class="alert green">'.$plxPlugin->getLang('L_SENDED_EMAIL').'</p>';
  else
   echo '<p class="alert red">'.$plxPlugin->getLang('L_UNSENDED_EMAIL') . '<br />' . $error_get_last . '</p>';//deb
  exit;
 }
 else{//NoScript
  if($res)
   plxMsg::Info($plxPlugin->getLang('L_SENDED_EMAIL'));
  else
   plxMsg::Error($plxPlugin->getLang('L_UNSENDED_EMAIL') . '<br />' . $error_get_last);//deb
  header('Location:'.$url);//redirect & notif
  exit;
 }

}//fi POST WRITE TO
$prfl = ($_SESSION['profil'] > PROFIL_ADMIN);
$cnfHref = ($prfl?'':'parametres_').'plugin.php?p='.$pluginName.($prfl?'&amp;z=config':'');//parametres_plugin.php?p=
#/i\ : $plxMotor/$plxAdmin->version removed in 5.5
$hlpvers = (isset($plxAdmin->version))?'parametres_pluginhelp.php?p=':'parametres_help.php?help=plugin&amp;page=';//<5.5 : >=5.5
$hlpHref = ($prfl?'plugin.php?p=':$hlpvers).$pluginName.($prfl?'&amp;z=lang/'.$plxPlugin->lang.'-help':'');//parametres_help.php?help=plugin&page=
plxToken::validateFormToken($_POST);# Control du token du formulaire
$c=$fresh=$freshb=$imgzip=$delzip=$e='';# init some var
$cache_dir = $plxPlugin->tmp.'/eml/';
$hf=(count(scandir($cache_dir)) == 2);//is dir empty #have file
$zipname = '.'.$pluginName.'_'.@$_SERVER['HTTP_HOST'].'_backup.eml.zip';
$modes = array('tous','hors','en');
$mode = isset($_GET['m'])?$_GET['m']:'tous';
if(!empty($_POST)){
 if (!empty($_POST['sendme']))
  $plxPlugin->sendme($_POST['sendme']);# SEND MAIL.eml (todo tester sendme($eml) func ;)
 elseif (!empty($_POST['switchme']))
  $plxPlugin->switchme($_POST['switchme']);# switch eml 0 -> 1 & vice versa
 elseif (!empty($_POST['clean']))
  if (isset($_POST['delzip']) && $_POST['delzip']=='o')# nojs fallback
   $plxPlugin->clean($zipname);
  elseif ($_POST['clean']=='zip' OR isset($_POST['zip']))#2nf 4 nojs fallback
   $plxPlugin->clean(false,true);# zip all cached files
  elseif ($_POST['clean']=='all')
   $plxPlugin->clean();
  else
   $plxPlugin->clean($_POST['clean']);#one file
 header('Location:'.$url);//MODERATOR ACCESS BY ADMIN URL # never &amp; in header loc ::: header('Location: plugin.php?p='.$pluginName.(isset($_GET['e'])?'&e=stop':''));
 exit;
}
if(file_exists($cache_dir.$zipname)){#goto zip 4 download, &confirm del zip (after 3s) & add button 4 noJs fallBack 'and clear after) !!! if zip is present on server, display download & dialog boxes all the time, sorry about that, click on refresh to stop this effect
 $imgzip = '<a href="'.$cache_dir.$zipname.'" title="'.$plxPlugin->getLang('L_ZIP_SERVER').' ('.date('Y-m-d H:i',filemtime($cache_dir.$zipname)).')">
 <img id="archive" class="icon_pmc" alt="Backup present" src="'.PLX_PLUGINS.$pluginName.'/img/archive.png" />
 </a> ';
 $freshb = '
 <button class="delete orange" type="submit" name="delzip" value="o" onClick="clean(\''.$zipname.'\');return false;" title="'.$plxPlugin->getLang('L_CACHE_ZIPDEL').'">
  <img id="zipdel" class="icon_pmc" alt="Del zip" src="'.PLX_PLUGINS.$pluginName.'/img/zipdel.png" /><span class="sml-hide med-show-inline"> '.L_DELETE.' zip</span>
 </button>';#2 delzip NoJs & js
 if(!isset($_GET['e'])){
  $e=$plxAdmin->racine.$plxAdmin->path_url.'&amp;e=stop';
  $fresh = '<meta HTTP-EQUIV="REFRESH" content="0; url='.$cache_dir.$zipname.'">';#go to download
  $delzip='/* After download */
  function cleanZip(){if (confirm("'.$plxPlugin->getLang('L_CACHE_ZIPDEL').'")) clean("'.$zipname.'");}
  window.setTimeout(cleanZip, 3000);';
 }
}
?>
<h2><span class="hide"><?php echo $pluginName ?></span></h2>
<noscript><div class="alert orange"><img alt="noscript" title="noscript" class="icon_pmc" src="<?php echo PLX_PLUGINS.$pluginName ?>/img/attention.gif" /> <?php $plxPlugin->lang('L_NOSCRIPT');?>.</div></noscript>
<form id="clean_cache" class="inline-form" method="post">
 <div id="action" class="maxiContact in-action-bar"><!-- <span id="jeckyl">&nbsp;</span> -->
<?php if(!empty($plxAdmin->aConf['clef'])) : ?>
<?php $urf = $plxAdmin->racine.'feed.php?admin'.$plxAdmin->aConf['clef'].'/'.$pluginName;//url feed base ?>
<span class="">
 <img id="rss_img_action" class="icon" src="<?php echo PLX_PLUGINS.$pluginName ?>/img/rss_action.png"> <i><?php echo L_COMMENTS_PRIVATE_FEEDS ?>&nbsp;:</i>
<?php foreach($modes as $m): ?>
 <i class=""><a title="<?php $plxPlugin->lang('L_SEE');?>" href="<?php echo $urf.'/'.$m ?>-messagerie"><?php $plxPlugin->lang('L_'.strtoupper($m));?></a></i>
<?php endforeach; ?>
</span><br />
<?php endif;//clef (flux privés)
//if($_SESSION['profil'] < PROFIL_MODERATOR)
// echo '<a class="back button blue" href="'.$cnfHref.'">'.L_PLUGINS_CONFIG.'</a>';
?>
  <a href="<?php echo $cnfHref ?>" title="<?php echo L_MENU_CONFIG ?>"><img id="config" class="icon_pmc" alt="config" src="<?php echo PLX_PLUGINS.$pluginName ?>/img/settings.png" /></a>
  <script type="text/javascript" style="display:none">function clean(file){if(file)document.getElementById('clean').value=file;document.getElementById('clean_cache').submit();}function sendme(file){if(file)document.getElementById('sendme').value=file;document.getElementById('clean_cache').submit();}function switchme(file){if(file)document.getElementById('switchme').value=file;document.getElementById('clean_cache').submit();}</script>
  <a href="<?php echo $e ?>" title="<?php $plxPlugin->lang('L_REFRESH') ?>"><img id="refresh" class="icon_pmc" alt="refresh" src="<?php echo PLX_PLUGINS.$pluginName ?>/img/reload.png" /></a> 
  <button class="update green<?php echo($hf?' hide':'')?>" type="submit" name="update" onClick="if(confirm('<?php $plxPlugin->lang('L_CACHE_CONFIRM_DEL') ?>'))clean();return false;"><img id="clear" class="icon_pmc" src="<?php echo PLX_PLUGINS.$pluginName ?>/img/trash_can_reload.png"><span class="sml-hide med-show-inline"> <?php $plxPlugin->lang('L_CLEAN_CACHE'); ?></span></button>
  <button class="download blue<?php echo($hf?' hide':'')?>" type="submit" name="zip" value="o" onClick="clean('zip');return false;"><img id="zip" class="icon_pmc" src="<?php echo PLX_PLUGINS.$pluginName ?>/img/zip.png"><span class="sml-hide med-show-inline"><?php $plxPlugin->lang('L_CACHE_ZIP'); ?></span></button>
<?php echo $freshb ?>
  <a href="<?php echo $hlpHref ?>" title="<?php echo @L_PLUGINS_HELP_TITLE ?>"><img id="help" class="icon_pmc" alt="help" src="<?php echo PLX_PLUGINS.$pluginName ?>/img/help.png" /></a>
  <a href="<?php echo $plxAdmin->urlRewrite('?'.(defined('PLX_MYMULTILINGUE')?($_SESSION['data_lang']==$plxPlugin->lang ? '' : $_SESSION['data_lang'].'/'):'').$plxPlugin->getParam('url')) ?>" title="<?php echo $plxPlugin->lang('L_SEE_PAGE').' ('.$plxPlugin->getParam('mnuName_'.(defined('PLX_MYMULTILINGUE')?$_SESSION['data_lang']:$plxPlugin->lang))?>)"><?php echo $plxPlugin->lang('L_SEE')?></a>
<?php $urf = PLX_CORE.'admin/plugin.php?p='.$pluginName;//url admin base ?>
  <ul class="menu horizontal expanded">
<?php foreach($modes as $m): ?>
   <li class="menu menu-item <?php echo $m!=$mode?'in':'' ?>active"><a href="<?php echo $urf.'&amp;m='.$m ?>"><?php $plxPlugin->lang('L_'.strtoupper($m));?></a></li>
<?php endforeach; ?>
  </ul>
 </div>
<?php /* 
#action moved by js in action bar. 
After this, button not in form tag, noscript fallback with clean input preset with all
avec $plxAdmin->version
/parametres_pluginhelp.php?p=maxiContact ::: plx5.4
*/ ?>
 <?php echo plxToken::getTokenPostMethod() ?>
 <input type="hidden" name="clean" id="clean" value="all" />
 <input type="hidden" name="sendme" id="sendme" value="" />
 <input type="hidden" name="switchme" id="switchme" value="" />
 <h4><sub><sup><?php $plxPlugin->lang('L_DESCRIPTION') ?></sup></sub></h4>
</form>
<?php
echo $imgzip;

$cache = $plxPlugin->emlList($mode);//echo admin head by default
$files = $cache['files'];
$cache_size = $cache['size'];
unset($cache);//✉ = &#9993;
$table_head = '<img id="mc_mode" class="icon_pmc" src="'.PLX_PLUGINS.$pluginName.'/icon.png" title="'.$plxPlugin->getLang('L_CACHE_LIST').'" />&nbsp;';
$table_head .= $plxPlugin->getLang('L_CACHE_LIST').' ('.count($files).') : '.date('Y-m-d H:i:s').' - '.$plxPlugin->getLang('L_TOT').' : '.$plxPlugin->size_readable($cache_size, $decimals = 2);
echo '<div id="emlList" class="scrollable-table brush_bash"><table id="eml" class="full-width"><thead><tr><td>'.$table_head.'</td></tr></thead><tfoot><tr><td>'.$table_head.'</td></tr></tfoot><tbody>';
foreach($files as $ts => $name)#findicons.com free : &#9993; is ✉
 echo '<tr id="'.$ts.'"><td><a class="hide" title="'.L_DELETE.' '.$name[0].'" href="javascript:void(0)" onClick="if(confirm(\''.$plxPlugin->getLang('L_EML_CONFIRM_DEL') .' '. $name[0] . '?\'))clean(\''.$name[0].'\');return false;"><img class="icon_pmc del_file" src="'.PLX_PLUGINS.$pluginName.'/img/del.png" title="'.L_DELETE.'" alt="del" /></a> : <i style="color:'.(!$name[2]?'red':'green').'"><sup><sub><a title="'.$plxPlugin->getLang('L_'.(!$name[2]?'UN':'').'SENDED_EMAIL').
PHP_EOL.$plxPlugin->getLang('L_FROM').' '.$name[1].
PHP_EOL.$plxPlugin->getLang('L_DOWNLOAD_EML').': '.
PHP_EOL.$plxPlugin->real(1,$name[0]).'-'.$plxPlugin->real(2,$name[0]).'" target="_blank" style="color:unset;" href="'.$cache_dir.$name[0].'">'.date('Y-m-d H:i:s',$ts).'</a></sub></sup></i> : <a id="switchme_'.$ts.'" class="hide" title="'.$plxPlugin->getLang('L_SWITCHME').' '.$name[0].'" href="javascript:switchme(\''.$name[0].'\');"><img class="icon_pmc del_file" src="'.PLX_PLUGINS.$pluginName.'/img/switch-'.($name[2]?'en':'hors').'.png" title="'.$plxPlugin->getLang('L_SWITCHME').' '.$plxPlugin->getLang('L_'.($name[2]?'UN':'').'SENDED_EMAIL').'" alt="switch" /></a> : <span>&#9993;</span> <a title="&#9993; '.$plxPlugin->getLang('L_WRITE_TO').' '.$name[1].' '.$plxPlugin->getLang('L_MAIL_TO').' (mailto:)" target="_blank" href="mailto:'.$name[1].'"> <img class="icon_pmc del_file" src="'.PLX_PLUGINS.$pluginName.'/img/mailto.png" alt="mailto" /><u style="display:none" id="email_'.$ts.'">'.$name[1].'</u></a> <a title="&#9993; '.$plxPlugin->getLang('L_WRITE_TO').' '.$name[1].' '.$plxPlugin->getLang('L_RESPOND_TO').' '.ucfirst($pluginName).'" href="#respond" onclick="writeTo(\''.$ts.'\');"><img class="icon_pmc del_file" src="'.PLX_PLUGINS.$pluginName.'/img/write.png" alt="write" />'.$name[1].'</a> : <i class="mc-sml-hide" style="color:'.(!$name[2]?'red':'green').'"><sup><sub><br class="lrg-hide" />'.
PHP_EOL.'<span id="subject_'.$ts.'">'.$name[3].'</span></sub></sup></i>'.
PHP_EOL.'<br /> <a title="'.$plxPlugin->getLang('L_SEE_EMAIL_TITLE').'" id="toggler_'.$ts.'" href="javascript:void(0)" onclick="'."toggleDiv('body_".$ts."','toggler_".$ts."','<img class=\'icon_pmc view_file\' src=\'".PLX_PLUGINS.$pluginName."/img/view.png\' title=\'".$plxPlugin->getLang('L_SEE_EMAIL')."\' alt=\'see src\' />','<img class=\'icon_pmc view_file\' src=\'".PLX_PLUGINS.$pluginName."/img/hide.png\' title=\'".$plxPlugin->getLang('L_HIDE_EMAIL')."\' alt=\'hide src\' />')".'" style="outline:none; text-decoration: none"><img class="icon_pmc view_file" src="'.PLX_PLUGINS.$pluginName.'/img/view.png" title="'.$plxPlugin->getLang('L_SEE_EMAIL').'" alt="see src" /></a> : <a id="sendme_'.$ts.'" class="hide" title="&#9993; '.$plxPlugin->getLang('L_SENDME').' '.$plxPlugin->getParam('email').'" href="javascript:void(0)" onclick="if(confirm(\''.$plxPlugin->getLang('L_EML_CONFIRM_SENDME') .' '. $plxPlugin->getParam('email') .'?\'))sendme(\''.$name[0].'\');return false;"><span style="font-size:1.618em;">✉</span> <img class="icon_pmc send_file" src="'.PLX_PLUGINS.$pluginName.'/img/sendme.png" title="'.$plxPlugin->getLang('L_SENDME').' '.$plxPlugin->getParam('email').'" alt="resend" />'.$plxPlugin->getParam('email').'</a>'.
PHP_EOL.'<div id="body_'.$ts.'" class="admin-view-email" style="display: none;">'.nl2br(trim($name[4])).'</div></td></tr>'.PHP_EOL;
echo '</tbody></table></div>'.PHP_EOL;?>
<?php if(!empty($plxAdmin->aConf['clef'])) : ?>
<?php $urf = $plxAdmin->racine.'feed.php?admin'.$plxAdmin->aConf['clef'].'/'.$pluginName;//url feed base ?>
<img id="rss_img" class="icon_pmc" src="<?php echo PLX_PLUGINS.$pluginName ?>/img/rss.png"> <i><?php echo L_COMMENTS_PRIVATE_FEEDS ?>&nbsp;:</i>
<div class="alert green">
<ul class="menu horizontal expanded">
<?php foreach($modes as $m): ?>
 <li class="menu menu-item inactive"><a title="<?php $plxPlugin->lang('L_SEE');?>" href="<?php echo $urf.'/'.$m ?>-messagerie"><?php $plxPlugin->lang('L_'.strtoupper($m));?></a></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif;//clef (flux privés) ?>

<div id="respond" class="overlay"><?php # pop css only (respond to) ?>
 <form id="form_write_to" class="popup inline-form" action="" method="post" onSubmit="post(this.id);return false;">
  <?php echo plxToken::getTokenPostMethod() ?>
  <input type="hidden" name="ajax" id="ajax" value="" />
  <input type="hidden" name="write_to" id="write_to" value="write_to" />
  <input type="hidden" name="ajaxMaxiPost" id="ajaxMaxiPost" value="" />
  <h2>
   <img class="logoPlugin" title="<?php $plxPlugin->lang('L_WRITE_TO') ?>" alt="<?php $plxPlugin->lang('L_WRITE_TO') ?>" src="<?php echo PLX_PLUGINS.$pluginName ?>/img/mail-send.png" />
   <?php $plxPlugin->lang('L_WRITE_TO') ?>
   <img class="logoPlugin" src="<?php echo PLX_PLUGINS.$pluginName ?>/img/write.png" alt="write" />
   <input type="submit" name="submit" value="<?php $plxPlugin->lang('L_FORM_BTN_SEND') ?>" />
   <input type="reset" name="reset" value="<?php $plxPlugin->lang('L_FORM_BTN_RESET') ?>" />
<?php # &amp;parm=".urlencode(plxUtils::strCheck($parm)) ?>
  </h2>
  <a class="close" title="Fermer cette popup" href="#nopopup">&times;</a>
  <div class="content">
   <div id="fieldsContainer" class="main grid">
    <fieldset class="col sml-12">
     <div class="grid">
      <div class="col sml-12 label-centered">
       <div id="maxiContactRsp"></div>
      </div>
     </div>
     <div class="grid">
      <div class="col sml-12 med-5 label-centered">
       <label for="id_subject"><?php $plxPlugin->lang('L_EMAIL_SUBJECT') ?>&nbsp;:</label>
      </div>
      <div class="col sml-12 med-7">
       <?php plxUtils::printInput('subject','','text','50-120') ?>
      </div>
     </div>
<?php foreach(array('from','','cc','bcc') as $mtype) : $mtype = $mtype?'_'.$mtype:'';#L_EMAIL CC BCC FROM ?>
     <div class="grid">
      <div class="col sml-12 med-5 label-centered">
       <label for="id_email<?php echo $mtype ?>"><?php $plxPlugin->lang('L_EMAIL'.strtoupper($mtype)) ?>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_COMMA') ?></span></a>&nbsp;:</label>
      </div>
      <div class="col sml-12 med-7">
     <?php plxUtils::printInput('email'.$mtype,'','text','50-120') ?>
      </div>
     </div>
<?php endforeach;#(array('','cc','bcc') as $mtype) ?>
     <div class="grid">
      <div class="col sml-12 med-5 label-centered">
       <label for="id_content"><?php echo L_CONTENT_FIELD ?>&nbsp;:</label>
      </div>
      <div class="col sml-12 med-7">
       <?php plxUtils::printArea('content','',50,20,false,'full-width'); ?>
      </div>
     </div>
    </fieldset>
   </div>
  </div>
 </form>
</div><?php #respond fi pop css only ?>
<script type="text/javascript" style="display:none">
<?php echo $delzip ?>
//On affiche les liens (js) cachés
 var a = document.querySelectorAll('a.hide');
 for (i=0; i<a.length; i++)
  a[i].className="";/* unhide js clean file link */
//On déplace le menu dans l'action bar
 var a = document.querySelectorAll('.inline-form h4');a = a[0];
 var z = document.querySelectorAll('.action-bar');z = z[0];
 var t = z.querySelectorAll('h2');t = t[0];
 t.innerHTML = t.innerHTML + ' : ' + a.innerHTML;
 a.className = 'hide';
 var a = document.getElementById('action')
 a.className = '';/* remove css nojs helper */
 a.firstChild.className = 'show';/* remove css nojs helper */
 z.appendChild(a);

<?php #memo# $courriels = explode( ', ',$plxPlugin->getParam('email'));if(plxUtils::checkMail($courriels[0])) ?>
 function writeTo(ts){
  window.location.hash = 'respond';//show form
  document.getElementById('id_email_from').value = '<?php echo $plxPlugin->getParam('email') ?>';
  var to = document.getElementById('email_'+ts);
  var bo = document.getElementById('body_'+ts);
  var su = document.getElementById('subject_'+ts);
  document.getElementById('id_email').value = to.textContent?to.textContent:to.innerHTML;
  document.getElementById('id_content').value = bo.textContent?bo.textContent:bo.innerHTML.replace(/<br>/gi,'');
  document.getElementById('id_subject').value = su.textContent?su.textContent:su.innerHTML;
 }
 function post(id){//Repondre avec le plugin
  document.getElementById('ajax').value = 'writeto';
  var query = document.getElementById('ajaxMaxiPost');
  var jxfrm = document.getElementById(id);//form_write_to
  var query = 'poivreetsel&ajaxmode=writeto';
  for(var e=0; e < jxfrm.length; e++)
   if(jxfrm[e].name)//Fine
    query = query + '&' + jxfrm[e].name + '=' + encodeURIComponent(jxfrm[e].value);//encodeURI()
// console.log(query);
  document.getElementById('ajaxMaxiPost').value = query;
  makeRequest('<?php echo $url ?>&ajax=writeto','ajaxMaxiPost','maxiContactRsp');
 }
</script>
<?php echo $fresh;
$plxPlugin->tips();
$plxPlugin->endOfAdmin();
eval($plxAdmin->plxPlugins->callHook('Admin'.$pluginName.'UsersFoot'));