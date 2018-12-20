<?php if(!defined('PLX_ROOT')) exit;//todo include this head for next trad
//var_dump( isset($_GET['p']),$_GET,isset($page),$page/*,get_class($plxPlugin)*/);
$pluginName = isset($_GET['p'])?$_GET['p']:isset($page)?$page:get_class($plxPlugin);////GET['p'] is for plx <= 5.4 : explode('page=',$plxAdmin->get);$pluginName = $pluginName[1];
include(PLX_PLUGINS.$pluginName.'/lang/help.inc.php');#inclure les communs
?>
<div class="<?php echo $prfl?'hide':'' ?>">
 <p class="alert green">Astuce, comme <a title="config" href="<?php echo $cnfHref ?>"><b>les réglages</b> sont nombreux</a>. Personnalisé le avant de l'activé.</p>
 <p class="alert blue">/!\ En fonction du réglage, il est possble que le <b>dossier des courriels sauvés (eml)</b> "<i>data/<?php echo $pluginName ?></i>" soit <b>supprimé</b> lorsque le plugin est <b>désactivé</b> /!\</p>
</div>
<h2>Ce que c'est :</h2>
<p>
Page de contact compatible plxMyMultilingue a personnaliser et RSS Privés.
</p>
<h2>Ce que ça permet :</h2>
<p>
Gérer les courriels sauvés(au format EML) lors de la demande de contact d'un internaute, ainsi son courriel est archivé qu'il vous soit envoyé ou non ;)<br />
Des Flux de syndication privés (RSS/Atom) des courriels ainsi sauvés sont accessible (si la clef est activé).
</p>
<p>Pour chaques "Qestions personalisées" il est possible d'y intégrer du code "d'attributs" et de régler son type:<br />(<em>button, checkbox, color, date, datetime-local, email, file, hidden, image, month, number, password, radio, range, reset, search, submit, tel, text, textarea, time, url, week</em>).<br />
Si aucun texte de question est saisit, le label et ses deux point (:) seront absent du html (code source).</p>
<code><pre>
Exemples :
Type = button
 Texte Question 1 (fr) : vide
 Code : value="alert" style="width:25%" onClick="alert(this.value);"
Type = reset
 Texte Question 1 (fr) : vide
 Code : value="reset"

Type = date
 Texte Question 1 (fr) : date souhaitée
 Code : style="width:20%" onChange="console.log(this.value);"
Type = time
 Texte Question 2 (fr) : heure souhaitée
 Code : style="width:20%" min="8" max="20 onChange="console.log(this.value);"
</pre></code>

<h2>Histoire :</h2>
<p>
L'idée de départ est de sauver la source du courriel en local du contact qui tente de nous joindre.
</p><p>
Alors qu'il pense que nous avons reçus le courriel, une erreur quelque part et paf perte de l'info, aucun courriel de sa part dans notre boite de messagerie. :/
</p><p>
Problême réglé avec MaxiContact. :)
</p>
<h2>Ce qu'il fait :</h2>
<p>
Il enregistre toutes les tentatives réussies (capcha + requis) et permet de les visualiser soit par le biais d'un flux de syndication RSS/Atom privé ou en direct avec <a title="son admin" href="<?php echo $linkAdmin ?>">son interface coté cuisine de PluXml</a>.
</p><p>
De plus comme il y a aucun courriel de perdu, on rassure l'internaute même lors d'une <i>erreur d'envoi</i> détecté, aucun mot lui en ais fait; il est même possible de désactivé l'envoi vers soi; son message est conservé en format .eml en local.
</p><p>
Il suffit d'utiliser un bon lecteur de flux de nouvelles à la Thunderbird (RSS) pour etre au fait des nouvelles demandes, d'y inscrire une ou des adresses du <i><?php echo L_COMMENTS_PRIVATE_FEEDS ?></i>
située en haut de <a href="<?php echo $linkAdmin ?>">son espace admin</a> ou<br />
ci-après, si <a href="parametres_avances.php">la <?php echo L_CONFIG_ADVANCED_ADMIN_KEY ?> est activée</a> ;)
</p>
<?php if(!empty($plxAdmin->aConf['clef'])) : ?>
<?php $urf = $plxAdmin->racine.'feed.php?admin'.$plxAdmin->aConf['clef'].'/'.$pluginName;//url feed base ?>
<ul class="unstyled-list inline-list">
	<li><?php echo L_COMMENTS_PRIVATE_FEEDS ?> :</li>
<?php foreach($modes as $m): ?>
	<li><a title="Flux RSS privé : <?php echo $m ?> messagerie" href="<?php echo $urf.'/'.$m ?>-messagerie"><?php echo ucfirst($m);?></a></li>
<?php endforeach; ?><br />
</ul>
<?php endif;//clef (flux privés) ?>
<p>
Ps: Le nombre qui s'affiche au menu est celui des courriels non envoyés (erreur de serveur).
Il est toujours possible qu'il dise qu'il est bien envoyé, mais que vous ne receviez jamais rien.
Tout va bien il est sauvé et dispo <a title="son admin" href="<?php echo $linkAdmin ?>">ici</a> ou <a title="son RSS" href="<?php echo @$urf ?>/tous-messagerie">là</a> avec possibilité de télécharger sa source en <i>.eml</i>
</p>