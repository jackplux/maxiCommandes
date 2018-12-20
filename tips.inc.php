<?php if(!defined('PLX_ROOT')) exit;//Part of maxiContact plugin for PluXml
$greffe = isset($this)?$this:$plxPlugin;
$liberapayUrl = PLX_PLUGINS . get_class($greffe) . '/img/liberapay-'.$greffe->lang.'.svg';
$liberapayImg = file_exists($liberapayUrl);
$liberapayUrl = $liberapayImg?$liberapayUrl:'https://liberapay.com/assets/widgets/donate.svg';
$liberatag = $liberapayImg?'div':'script';
?><div id="scrollToTop" title="<?php $greffe->lang('L_SCROLLTOTOP');?>"><a href="#">⇧</a></div>
<span id="liberapay" style="position:fixed;bottom:0.16em;right:0.64em;" title="<?php $greffe->lang('L_LIBERAPAY');?>."><div id="nsl"><a href="https://liberapay.com/sudwebdesign/donate"><img alt="<?php $greffe->lang('L_LIBERAPAY');?>." src="<?php echo $liberapayUrl ?>"></a></div><<?php echo $liberatag ?> onload="getElementById('nsl').style.display='none';" style="display:none;" src="https://liberapay.com/sudwebdesign/widgets/button.js"></<?php echo $liberatag ?>>
</span>