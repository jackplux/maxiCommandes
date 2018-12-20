<?php if(!defined('PLX_ROOT')) exit;//include common help head
//if(!isset($pluginName))
// $pluginName = isset($page)?$page:get_class($plxPlugin);//explode('page=',$plxAdmin->get);$pluginName = $pluginName[1];

$plxPlugin = $plxAdmin->plxPlugins->aPlugins[$pluginName];#on recré this (tips.inc.php)

$modes = array('tous','hors','en');
$linkAdmin = PLX_CORE.'admin/plugin.php?p='.$pluginName;
$prfl = ($_SESSION['profil'] > PROFIL_ADMIN);
$cnfHref = ($prfl?'':'parametres_').'plugin.php?p='.$pluginName.($prfl?'&amp;z=config':'');//parametres_plugin.php?p=
?>
<p class="sml-hide med-show"><!-- plx5.5 compensation --></p>
<noscript><h1><?php echo ucfirst($pluginName).' ('.L_HELP.')' ?></h1></noscript>
<div id="action" class="hide <?php echo strtolower($pluginName) ?> in-action-bar"><?php echo $prfl?'<p id="jeckyl">&nbsp;</p>':'' ?>
<h4 class="desc"><sub><sup><?php $plxPlugin->lang('L_HELP_DESC');?></sup></sub></h4>
 <a href="<?php echo $linkAdmin ?>" title="<?php echo L_ADMIN. ' : '.$pluginName ?>"><img id="admin" class="icon_pmc" alt="admin" src="<?php echo PLX_PLUGINS.$pluginName ?>/img/admin.png" /></a> 
 <a href="<?php echo $cnfHref ?>" title="<?php echo L_MENU_CONFIG . ' : '.$pluginName  ?>"><img id="config" class="icon_pmc" alt="config" src="<?php echo PLX_PLUGINS.$pluginName ?>/img/settings.png" /></a>
 <a href="<?php echo $plxAdmin->urlRewrite('?'.(defined('PLX_MYMULTILINGUE')?($_SESSION['data_lang']==$plxPlugin->lang ? '' : $_SESSION['data_lang'].'/'):'').$plxPlugin->getParam('url')) ?>" title="<?php echo $plxPlugin->lang('L_SEE_PAGE').' ('.$plxPlugin->getParam('mnuName_'.(defined('PLX_MYMULTILINGUE')?$_SESSION['data_lang']:$plxPlugin->lang))?>)"><?php echo $plxPlugin->lang('L_SEE')?></a>
</div>

<script type="text/javascript" style="display:none">
window.onload = function(e){//console.log(e);
	//console.log("window.onload", e, Date.now() ,window.tdiff, 
	//(window.tdiff[1] = Date.now()) && window.tdiff.reduce(fred) ); 
	var a = document.querySelectorAll('h4.desc');a = a[0];//console.log(a);
	var z = document.querySelectorAll('.action-bar');z = z[0];
	var t = z.querySelectorAll('h2');t = t[0];
	t.innerHTML = t.innerHTML + ' (<?php echo @L_PLUGINS_HELP ?>)&nbsp;: ' + a.innerHTML;
	a.className = 'hide';
	var a = document.getElementById('action')
	a.className = '';/* remove css nojs helper */
	//a.firstChild.className = 'show';/* remove css nojs helper */
	z.appendChild(a);
<?php if($_SESSION['profil'] > PROFIL_ADMIN): ?>
	var b = document.querySelectorAll('.back');b = b[0];
	b.className = 'hide';
<?php endif; ?>
}
</script>
<?php
include(PLX_PLUGINS.$pluginName.'/tips.inc.php');