<?php if(!defined('PLX_ROOT')) exit;//todo include this head for next trad
$pluginName = isset($_GET['p'])?$_GET['p']:$_GET['page'];//GET['p'] is for plx <= 5.4
include(PLX_PLUGINS.$pluginName.'/lang/help.inc.php');#inclure les communs
?>
<div class="<?php echo $prfl?'hide':'' ?>">
 <p class="alert green">Tips, because have lot <a title="config" href="<?php echo $cnfHref ?>"><b> parameters</b></a>. Personnalise before activate.</p>
 <p class="alert blue">/!\ Depending on the setting, it is possible that the <b> saved emails folder (eml)</b> "<i>data/<?php echo $pluginName ?></i>" are <b>deleted</b> when the plugin is <b>deactivated</b> /!\</p>
</div>
<h2>What is this:</h2>
<p>
Personnalisable contact page, plxMyMultilingue compatible and Private RSS.
</p>
<h2>What it allows:</h2>
<p>
Administrate emails backup (EML format) created when a user use contact form, now the message is archived and sended (maybe).<br />
A private syndication of emails is possible (RSS/Atom) (if private key is active).
</p>
<p>For all "Personalized questions" it's possible to intégrate "attributes" code and set type:<br />(<em>button, checkbox, color, date, datetime-local, email, file, hidden, image, month, number, password, radio, range, reset, search, submit, tel, text, textarea, time, url, week</em>).<br />
If field text of question is empty, the label and this two point (:) where not printed in html (source code).</p>
<code><pre>
Examples:
Type = button
 Text Question 1 (en): empty
 Code: value="alert" style="width:25%" onClick="alert(this.value);"
Type = reset
 Text Question 1 (en): empty
 Code: value="reset"

Type = date
 Text Question 1 (en): wish date
 Code: style="width:20%" onChange="console.log(this.value);"
Type = time
 Text Question 2 (en): wish hour
 Code: style="width:20%" min="8" max="20 onChange="console.log(this.value);"
</pre></code>

<h2>History:</h2>
<p>
The first idea is when a internet user use contact form, backup mail source localy for never lost a message.
</p><p>
While he thinks we received the email, a mistake somewhere and lost info, no email from him in our mailbox. :/
</p><p>
Problem solved with MaxiContact. :)
</p>
<h2>What it does:</h2>
<p>
It records all successful attempts (capcha + requireds) and allows you to view them either through a private syndication flow RSS/Atom or in direct with <a title="son admin" href="<?php echo $linkAdmin ?>">its interface on the kitchen side of PluXml</a>.
</p><p>
In addition, as there is never loss email, we reassure the Internet user even during a <i>send error</i> detected, no word has made him do it; it is even possible to disable sending to oneself; his message is kept localy in .eml format.
</p><p>
Just use one good news reader same Thunderbird (RSS) to be aware of new requests, just use following adresses <i><?php echo L_COMMENTS_PRIVATE_FEEDS ?></i>
located at the top of <a href="<?php echo $linkAdmin ?>">admin space</a> or<br />
 following, if <a href="parametres_avances.php">la <?php echo L_CONFIG_ADVANCED_ADMIN_KEY ?> is activated</a> ;)
</p>
<?php if(!empty($plxAdmin->aConf['clef'])) : ?>
<?php $urf = $plxAdmin->racine.'feed.php?admin'.$plxAdmin->aConf['clef'].'/'.$pluginName;//url feed base ?>
<ul class="unstyled-list inline-list">
	<li><?php echo L_COMMENTS_PRIVATE_FEEDS ?>:</li>
<?php foreach($modes as $m): ?>
	<li><a title="Private RSS: <?php $plxPlugin->lang('L_'.strtoupper($m));?> message" href="<?php echo $urf.'/'.$m ?>-messagerie"><?php $plxPlugin->lang('L_'.strtoupper($m));?></a></li>
<?php endforeach; ?><br />
</ul>
<?php endif;//clef (flux privés) ?>
<p>
Ps: The number which appears on the menu is that of unsent emails (server error).
It's always possible for him to say he's been sent, but you never receive anything.
It's all right, he's saved and available <a title="its admin" href="<?php echo $linkAdmin ?>">here</a> or <a title="its RSS" href="<?php echo @$urf ?>/tous-messagerie">here</a> with possibility to download <i>.eml</i> source.
</p>