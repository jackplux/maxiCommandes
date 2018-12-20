<?php
/**
 * Plugin maxiContact pour PluXml 5.6
 *
 * @version 1.2.0
 * @date 20/10/2018
 * @author Thomas Ingles
 **/
class maxiContact extends plxPlugin {
	public $lang = '';#pour certaine variables
	public $tmp;#tmp (uploads) & save .eml
	public $nfo;#tmp (array) .eml for get_info speedy memory
	public $EOL = "\r\n";//chr(13) . chr(10)  www.mimevalidator.net ok
	public function tips() {
		include (PLX_PLUGINS.__CLASS__.'/tips.inc.php');
	}
	/**
	 * Constructeur de la classe
	 *
	 * @param default_lang langue par défaut
	 * @return stdio
	 * @author Thomas Ingles
	 **/
	public function __construct($default_lang) {
		$this->tmp = PLX_ROOT.'data/'.__CLASS__;#tmp (uploads) & save .eml
		# gestion du multilingue plxMyMultiLingue//if(defined('PLX_MYMULTILINGUE'))# Si plugin plxMyMultilingue présent (ou non :)
		$this->lang = $default_lang;
		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		$this->release = '1.2.0';//$this->getInfo('version'); jamais appelé si en mode public (front-end)
		if(defined('PLX_ADMIN')){# déclaration des hooks admin
			if(!defined('L_HELP')) define('L_HELP',$this->getLang('L_HELP'));
			# Personnalisation du menu admin
			$this->setAdminMenu($this->getInfo('title'), 1, $this->getInfo('description') . 'v' . $this->release . ' ' . $this->getInfo('date'));
			# droits pour accèder à la page config.php du plugin
			$this->setConfigProfil(PROFIL_ADMIN, PROFIL_MANAGER);
			# droits pour accèder à la page admin.php du plugin
			$this->setAdminProfil(PROFIL_ADMIN, PROFIL_MANAGER);
			$this->addHook('AdminAuthPrepend', 'AdminAuthPrepend');// 1.2.0 AdminAuthPrepend Only if logon attempt
			$this->addHook('AdminTopBottom', 'AdminTopBottom');
			$this->addHook('AdminTopEndHead', 'AdminTopEndHead');
			$this->addHook('AdminFootEndBody', 'AdminFootEndBody');
		}else{
			$courriels = explode( ', ',$this->getParam('email'));
			if(plxUtils::checkMail($courriels[0])) {//+ d'1 courriel
				$this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
				$this->addHook('plxShowConstruct', 'plxShowConstruct');
				$this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
				$this->addHook('plxShowPageTitle', 'plxShowPageTitle');
				$this->addHook('ThemeEndHead', 'ThemeEndHead');
				$this->addHook('SitemapStatics', 'SitemapStatics');
				$this->addHook('plxFeedPreChauffageBegin', 'plxFeedPreChauffageBegin');
			}
		}
	}
	public function updateUnsend($up=1){# Si $up = 0 on retourne le compte des courriels non envoyés
		$fileCount = $this->tmp.'/.eml-unsended.zip';
		touch($fileCount);//uptime OR create it
		$compte = (int)@file_get_contents($fileCount);
		if($up===0) return $compte;
		file_put_contents($fileCount,$compte+$up);//+|- 1
	}
	public function onUpdate(){# Si le fichier update est présent a la racine du plugin
		$old_release = $this->getParam('release');//1.2.0
		if($this->release == $old_release)
			return FALSE;//if update file are undeleted
		if(version_compare($old_release,'1.2.0','<')){//Old
			$aLangs = array($this->lang);
			#	Si le plugin plxMyMultiLingue est installé on filtre sur les langues utilisées
			#	On garde par défaut le fr si aucune langue sélectionnée dans plxMyMultiLingue
			if(defined('PLX_MYMULTILINGUE')) {
				$langs = plxMyMultiLingue::_Langs();
				$multiLangs = empty($langs) ? array() : explode(',', $langs);
				$aLangs = $multiLangs;
			}
//perso
			$nombre_ades=(int)$this->getParam('nombre_ades');
			for ($q=1;$q<=$nombre_ades;$q++){
				if(!$this->getParam('ades_ou'.$q))
					$this->setParam('ades_ou'.$q, 'debut', 'string');#v > 1.2.0
				if(!$this->getParam('ades_type'.$q))
					$this->setParam('ades_type'.$q, 'text', 'string');#v > 1.2.0
				if(!$this->getParam('ades_attr'.$q))
					$this->setParam('ades_attr'.$q, '', 'string');#v > 1.2.0
			}
//qcm
			$qcm_120 = $this->getParam('nombre_qcm');#v >=1.2.0
			$old_qcm = $this->getParam('comment');#v <=1.1.0
			if($old_qcm){//import params#v <=1.1.0
				$this->setParam('nombre_qcm', 1, 'numeric');//
				$this->setParam('qcm1', 1, 'numeric');//active
				$this->setParam('qcm_type1', 'checkbox', 'string');
				$this->setParam('qcm_attr1', '', 'cdata');
				$this->setParam('qcm_obligatoire1', $this->getParam('comment_obligatoire'), 'numeric');
				$this->setParam('autre1', $this->getParam('autre'), 'numeric');
				$nombre_qrm=(int)$this->getParam('nombre_qrm');#v <=1.1.0
				if($nombre_qrm){
					$this->setParam('nombre_qrm1', $nombre_qrm, 'numeric');
					foreach($aLangs as $lang){
						$this->setParam('qrm_title_1'.$lang, $this->getParam('qrm_title_'.$lang), 'string');
						$this->delParam('qrm_title_'.$lang);
					}
					for($q=1;$q<=$nombre_qrm;$q++){
						foreach($aLangs as $lang){
							$this->setParam('qrm_1'.$lang.$q, $this->getParam('qrm_'.$lang.$q), 'string');
							$this->delParam('qrm_'.$lang.$q);//del old
						}
					}
				}//Fi $nombre_qrm Old
			}//Fi Old qcm
			if(!$old_qcm AND !$qcm_120)#v > 1.2.0 protect
				$this->setParam('nombre_qcm', 0, 'numeric');
//Delete old param1.1.0 for ALL CASE
			foreach($aLangs as $lang){
				$this->delParam('qrm_title_'.$lang);
			}
			$this->delParam('nombre_qrm');
			$this->delParam('autre');
			$this->delParam('comment_obligatoire');
			$this->delParam('comment');
  //Fi	Delete unused params
			$this->delParam('label');
			$this->delParam('placeholder');
		}//fi old <1.2.0
		if(!defined('PLX_ADMIN'))//Fix fatal error plug saveparam msg class unloaded (public mode)
			include_once(PLX_CORE.'lib/class.plx.msg.php');
			//loadLang(PLX_CORE.'lang/'.$this->lang.'/admin.php');
		//Fox  L_SAVE_SUCESSFUL not a constant (auth + public)
		if(!defined('L_SAVE_SUCCESSFUL'))
			define('L_SAVE_SUCCESSFUL',__CLASS__.' : '.__FUNCTION__.' &gt; v'.$this->release.' SUCCESS');
		$this->setParam('release', $this->release, 'string');
		$this->setParam('del_tmp', 0, 'numeric');#OnDeactivate
		$this->saveParams();

		return array('cssCache' => true);#mise a jour du cache des css
	}
	public function OnActivate(){# Méthode appelée quand on active le plugin : pour créer les répertoires
		if(!is_dir($this->tmp))#cache dir check
			mkdir($this->tmp);
		if(!is_dir($this->tmp.'/tmp'))#cache upload dir check
			mkdir($this->tmp.'/tmp');
		if(!is_dir($this->tmp.'/eml'))#cache eml check
			mkdir($this->tmp.'/eml');
//$this->cssCache('site');Fatal error: Call to undefined method maxiContact::cssCache() old Pluxml

		$cnf_release = $this->getParam('release');
		if($this->release == $cnf_release)
			return;//si même release on stoppe
		$this->setParam('release', $this->release, 'string');
		$this->saveParams();
	}
	public function OnDeactivate(){#Supprime le dossier temporaire, clean and remove tmp dir
		if(!$this->getParam('del_tmp'))//1.2.0
			return;
		$plxMotor = plxMotor::getInstance();
		if(!$plxMotor->plxPlugins->deleteDir($this->tmp)){
			if(extension_loaded('glob')){
				$cached = glob($this->tmp."*");#*.php
				foreach ($cached as $file)
					unlink($file);
				unset($cached);
			}
			else{
				if($cached = opendir($this->tmp)){
					while(($file = readdir($cached))!== false){
						if( $file == '.' || $file == '..' )
							continue;
						unlink($this->tmp.$file);
					}
					closedir($cached);
				}
			}
			rmdir($this->tmp);
			if(is_dir($this->tmp))// l'effacement a échoué
				rename($this->tmp,PLX_ROOT.'.trash_me');// rename "spécial Free" rename empty folders to PluXml-Root-folder/.trash_me (effet de bord non garanti de rename)
		}
	}
 /**
 * Méthode d'envoi de mail
 *
 * @param name string    Nom de l'expéditeur
 * @param from string    Email de l'expéditeur
 * @param to  array/string Adresse(s) du(des) destinataires(s)
 * @param subject string   Objet du mail
 * @param body string   contenu du mail
 * @return   boolean   renvoie FAUX en cas d'erreur d'envoi
 * @author Amaury Graillat
 **/
 public function sendMail($name, $from, $reply, $to, $subject, $body, $contentType="text", $cc=false, $bcc=false) {
  if(is_array($to))
   $to = implode(', ', $to);
  if(is_array($cc))
   $cc = implode(', ', $cc);
  if(is_array($bcc))
   $bcc = implode(', ', $bcc);
  $EOL = $this->EOL;
  $headers  = "From: ".plxUtils::removeAccents($name,PLX_CHARSET)." <".$from.">".$EOL;
  $headers .= "Reply-To: ".$reply."\r\n";
  $headers .= 'MIME-Version: 1.0'."\r\n";
  // Content-Type
  if($contentType == 'html')
   $headers .= 'Content-type: text/html; charset="'.PLX_CHARSET.'"'.$EOL;
  else
   $headers .= 'Content-type: text/plain; charset="'.PLX_CHARSET.'"'.$EOL;
  $headers .= 'Content-transfer-encoding: 8bit'.$EOL;
  $headers .= 'Date: '.date("D, j M Y G:i:s O").$EOL; // Sat, 7 Jun 2001 12:35:58 -0700
  if($cc != "")
   $headers .= 'Cc: '.$cc.$EOL;
  if($bcc != "")
   $headers .= 'Bcc: '.$bcc.$EOL;
//var_dump(__CLASS__,__FUNCTION__,$name, $from, $reply, $to, $contentType="text", $cc=false, $bcc, $subject, $body, $headers);exit;//dbg
//les.pages.perso.chez.free.fr/l-art-d-envoyer-des-mails-depuis-les-pp-de-free.io
  $start_time = time();
  $res = mail($to, plxUtils::removeAccents($subject,PLX_CHARSET), $body, $headers);
  $time = time() - $start_time;//free.fr
  if(strpos($_SERVER["HTTP_HOST"],'free.fr')) $res = $res & ($time>1);//free.fr
  return $res;
 }
	/**
	 * Méthode qui inclus dataTable (balise css & js) (AdminTopEndHead)
	 *
	 * @return	stdio
	 * @author	Thomas Ingles
	 **/
	public function dataTableIncHead($w=false) {//cdn idée
		$v = PLX_PLUGINS.$this->plug['name'].'/in/Vanilla-DataTables/vanilla-dataTables.min.';//https://github.com/Mobius1/Vanilla-DataTables/pull/65 & jscompress.com
		if($w)
			$v = $w;
?>
	<link rel="stylesheet" type="text/css" href="<?php echo $v ?>css?v=1.6.14" media="screen" />
	<script type="text/javascript" src="<?php echo $v ?>js?v=1.6.14"></script>
<?php
	}
	/**v 1.2.0
	 * Méthode qui permet au menu public d'etre certain qu'il s'agit du bon site (MULTIPLE PLUXML IN SAME HOST)
	 *
	 * @author Thomas Ingles
	 **/
	public function AdminAuthPrepend(){
		echo '<?php '; ?>
		if(!empty($_POST))//On login attempt only ::: $_POST['login'] $_POST['password']
			$_SESSION['clef<?php echo __CLASS__?>'] = $plxAdmin->aConf['clef'];
?><?php
	}
	/**
	 * Méthode qui ajoute le fichier css dans le fichier header.php du thème admin
	 * @return stdio
	 * @author Thomas Ingles
	 **/
	public function AdminTopEndHead() {
		if(strstr($_SERVER['REQUEST_URI'],'parametres_plugin.php?p='.__CLASS__) OR
		(  strstr($_SERVER['REQUEST_URI'],'plugin.php?p='.__CLASS__) AND isset($_GET['z']) )
		) {//les onglets (tabs)
			echo '<link href="'.PLX_PLUGINS.__CLASS__.'/in/tabs/style.css?v='.$this->release.'" rel="stylesheet" type="text/css" async />
			<style type="text/css">#form_'.__CLASS__.' .form-control{width:auto !important;}</style>'.PHP_EOL;
		}
# inclure admin.css, 
echo '<?php '; ?>
		#if (isset($plxAdmin->version) && version_compare($plxAdmin->version, "5.2", "<="))#limité a pluxml 5.2 & antérieur (non, pour tous) :i:$plxMotor/$plxAdmin->version removed in 5.5
			#if (((basename($_SERVER['SCRIPT_NAME'])=='plugin.php' || basename($_SERVER['SCRIPT_NAME'])=='parametres_plugin.php')) && (isset($_GET['p']) &&
   if((@$_GET['p']=='<?php echo __CLASS__ ?>') OR (@$_GET['page']=='<?php echo __CLASS__ ?>'))# plug & param & help
				echo '<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'<?php echo __CLASS__ ?>/css/admin-nocache.css?v=<?php echo $this->release ?>" async />'.
				PHP_EOL.'<?php $this->dataTableIncHead() ?>';    
<?php echo ' ?>';
	}
	/**
	 * badge des email non envoyés du menu
	 * Méthode qui ajoute le fichier js dans le fichier foot.php du thème admin et le badge au besoin
	 * @return stdio
	 * @author Thomas Ingles
	 **/
	public function AdminFootEndBody() {
		if($_SESSION['profil']<PROFIL_MODERATOR) {
//			$this->updateUnsend( $this->emlList(true) );//+ tous le non envoyés (dev)
			$nb_unsend = $this->updateUnsend(0);// donne le compte d'echecs
//			$this->emlList(true);//real (alpha !SLOW)
			if($nb_unsend>0){//si email(s) non envoyé(s)
				echo '<span id="eml_'.__CLASS__.'" class="badge" style="display:none" onclick="window.location=\''.PLX_CORE.'admin/plugin.php?p='.__CLASS__.'\';return false;">'.$nb_unsend.'</span>';
?>
<script type="text/javascript" style="display:none">
	var e = document.getElementById('eml_<?php echo __CLASS__ ?>');
	var m = document.getElementById('mnu_<?php echo __CLASS__ ?>');
	if(!m) m = document.getElementById('mnu_<?php echo strtolower(__CLASS__) ?>');
	if(m){
		e.style.display = '';//remove css nojs hide helper
		m.firstChild.appendChild(e);
		//~ m.firstChild.innerHTML = m.firstChild.innerHTML + e.innerHTML;
	}
</script>
<?php
				if(isset($_GET['p']) && $_GET['p']==__CLASS__)#(strstr($_SERVER['REQUEST_URI'],'plugin.php?p='.__CLASS__))
					echo '<script type="text/javascript" src="'.PLX_PLUGINS.__CLASS__.'/in/ajax.js?v='.$this->release.'" async></script>';
			}
		}
	}
	public function endOfAdmin($js=true){//vanilla datatable
		if($js){//fix for config (admin hack manager)
?>
<script style="display:none" type="text/javascript">
//~ var myTable = document.querySelector("#tebleId");//4 modern browser
//~ var dataTable = new DataTable(myTable);
function gotable(id){
	dataTable = new DataTable('#'+id, {
//	searchable: false,//true is default
//	fixedHeight: false,//true is default
//	sortable: false,//true is default (load ok but duplicate all td's content on sort seeing vdt-1.2.2 && chromium-31)
		perPage: 5,
		perPageSelect: [5, 10, 15, 20, 25, 50, 100],
//		Customise the display text
		labels: {
			placeholder: "<?php $this->lang('L_LABEL_JSDTABLE_PLACEHOLDE') ?>", // The search input placeholder
			perPage: "<?php $this->lang('L_LABEL_JSDTABLE_PERPGS') ?>", // per-page dropdown label
			noRows: "<?php $this->lang('L_LABEL_JSDTABLE_NODATA') ?>", // Message shown when there are no search results
			info: "<?php $this->lang('L_LABEL_JSDTABLE_INFO') ?>", // Info of Item Number (start end / rows)
		},
	});
}
window.onload = function(){
	//table_load();
	window.setTimeout(function() {
		gotable('eml');
	}, 333);
};
</script>
<?php
		}//fi $js
	}//funk endOfAdmin
	public function switchme($eml){# (wit) (ADMIN POST 1 to 0 && 0 to 1 eml)
		$file = $this->tmp.'/eml/'.$eml;
		if(!file_exists($file)) return false;
		if(strpos($file,'eml/0')){$fileren = str_replace('eml/0','eml/1',$file);$this->updateUnsend(-1);}
		else {$fileren = str_replace('eml/1','eml/0',$file);$this->updateUnsend();}
		return rename($file,$fileren);
	}
	public function sendme($eml){#send eml to 1st email (wit) (ADMIN POST sendme eml)
		$file = $this->tmp.'/eml/'.$eml;
		if(!file_exists($file)) return plxMsg::Error(sprintf($this->getLang('L_NOT_FOUND'),$eml));

		$headers = $this->get_info($eml,'Headers');//this is dak ;)
		$body = $this->get_info($eml,'FullBody');//this is dak ;)
		$sbj = $this->get_info($eml,'Subject');//this is dak ;)
//var_dump('mail by senndme()',$this->getParam('email'), $sbj, $body, $headers);exit;//dbg
		$res = false;
//les.pages.perso.chez.free.fr/l-art-d-envoyer-des-mails-depuis-les-pp-de-free.io
		$start_time = time();
		$res = mail($this->getParam('email'), $this->getLang('L_COPYOF') . ' ' . $sbj, $body, $headers);//this is dak ;)
		$time = time() - $start_time;
		$error_get_last = error_get_last();
		if($error_get_last)
			$error_get_last = __CLASS__.' '.__FUNCTION__.' error_get_last<br />'.implode('<br />',$error_get_last);
		else
			$error_get_last = '';
#		 bool mail ( string $to , string $subject , string $message [, mixed $additional_headers [, string $additional_parameters ]] )
		//$resultat = mail($this->sendto[$i], $this->msubject,$this->body, $this->headers);
		//$resultat = mail($to             , $subject       , $message  , $additional_headers, $additional_parameters);//exemple
		if(strpos($_SERVER["HTTP_HOST"],'free.fr')) $res = $res & ($time>1);//is free.fr
		//$res = $res & $error_get_last;

		$ren = false;//$fileren = str_replace('eml/0','eml/1',$file);//V-alpha
		if(strpos($file,'eml/0') AND $res){
			$ren = $this->switchme($eml);//rename($file,$fileren);//V-alpha
			//$this->updateUnsend(-1);//in switchme()
		}

		if($res)
		 $return = plxMsg::Info($this->getLang('L_SENDED_EMAIL'));
		else
		 $return = plxMsg::Error($this->getLang('L_UNSENDED_EMAIL') .'<br />' . $error_get_last);//deb
		return $return;//notif
	}

	public function clearDir($d='/tmp'){#cleaner dir (/tmp by default)
		$o = $this->tmp;
		$this->tmp = $o.$d;
		$this->OnDeactivate();#clear & del dir
		if(!is_dir($this->tmp))#dir check
			mkdir($this->tmp);#new dir
		$this->tmp = $o;#restore ?
	}
	public function size_readable($bytes, $decimals = 2){# Méthode appelée dans l'administration pour calculer la taille du cache * @return	human readable size * @author	Rommel Santor : http://rommelsantor.com/
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}
	public function get_info($file,$type='fromTo'){
		if($type=='sended'){
			$tmp = explode('~',$file);
			return $tmp[0];
		}
		if(!isset($this->nfo[$file])){
			$this->nfo = array();//clear
			$this->nfo[$file] = file_get_contents($this->tmp.'/eml/'.$file);
		}
		if($type=='fromTo')
			return preg_match('~Reply-To: (.*?)'.$this->EOL.'~', $this->nfo[$file], $matches) ? $matches[1] : '';
/*
 boundary="------------6bfc998ae0c684e617fbc3ee45a330c9"
*/
		$boundary_pos = strpos($this->nfo[$file],'boundary');# int 345
		if(($type=='Headers' OR $type=='FullBody' OR $type=='Boundary') AND $boundary_pos){
			$boundary = strstr($this->nfo[$file],'boundary');
			$boundary = explode($this->EOL,$boundary);
			$boundary = explode('"',$boundary[0]);
			$boundary = $boundary[1];
			if($type=='Boundary')
				return $boundary;
			$boundary_mail = explode('boundary=',$this->nfo[$file]);
			if($type=='Headers')
				return $boundary_mail[0] . 'boundary="'.$boundary.'"'.str_repeat($this->EOL, 2);
			if($type=='FullBody')
				return str_replace('"'.$boundary.'"'.str_repeat($this->EOL, 2),'',$boundary_mail[1]);
		}
// var_dump('maxiContact get_info boundary ', $boundary_pos, $boundary, $boundary_headers, $boundary_body, strstr($this->nfo[$file],'boundary'),$this->nfo[$file]);exit;
		if($type=='Headers'){//its work, sorry
			$ret = explode('8bit',$this->nfo[$file]);
			return $ret[0].'8bit';
		}
		if($type=='Body' OR $type=='FullBody'){//its work, sorry
			$ret = explode('8bit',$this->nfo[$file]);
			$ret = explode(str_repeat('_',46),$ret[1]);
			return $ret[0].$ret[1];
		}
		if(!empty($type))
			return preg_match('~'.$type.': (.*?)'.$this->EOL.'~i', $this->nfo[$file], $matches) ? $matches[1] : '';
	}
	public function get_time($file){
		return filemtime($this->tmp.'/eml/'.$file);
	}
	public function cChrono(){# retourne le temps calcul
		return round(getMicrotime()-PLX_MICROTIME,3).'s';
	}
	public function real($i,$a){# found url & more in last comment in cached source
		$a=explode('-',$a);
		return $a[$i];
	}
	public function emlList($mode = 'admin'){# list cache dir (admin)
		$cache_dir = $this->tmp.'/eml/';
		$cache_size = 0;
		$files = array();
		if (extension_loaded('glob')){
			$cached = glob($cache_dir."*.eml");
			foreach ($cached as $file){
				$sended = $this->get_info($file,'sended');
				if($sended&&$mode=='hors') continue;
				elseif(!$sended&&$mode=='en') continue;
				$size = filesize($cache_dir.$file);
				$files[$this->get_time($file)] = array((basename($file)), $this->get_info($file), $sended, $this->get_info($file,'Subject'), $this->get_info($file,'Body'), $this->get_info($file,'From'), $size);//Content-Type
				$cache_size += $size;
			}
		}
		else{
			if($cached = opendir($cache_dir)){
				while(($file = readdir($cached))!== false){
					if( $file == '.' || $file == '..' )
						continue;
					if(strtolower(strrchr($file,'.')==".eml")){
						$sended = $this->get_info($file,'sended');
						if($sended&&$mode=='hors') continue;
						elseif(!$sended&&$mode=='en') continue;
						$size = filesize($cache_dir.$file);
						$files[$this->get_time($file)] = array((basename($file)), $this->get_info($file), $sended, $this->get_info($file,'Subject'), $this->get_info($file,'Body'), $this->get_info($file,'From'), $size);//Content-Type
						$cache_size += $size;
					}
				}
				closedir($cached);
			}
		}
		unset($cached,$file,$sended);//,$cache_size
		switch($mode){
#			case 'menu'://alpha (de + en + lent si beaucoup de courriel) : mais réel
			case 'count':// en direct (inutilisé)
//var_dump('emlList count ou menu',$mode,$files,count($files));
				return count($files);//of unSended email
#			case 'admin':
#			case 'feed':
			default:
				krsort($files);
				return array('files' => $files, 'size' => $cache_size);
		}
	}
		# Hook plugins
		#if(eval($this->plxPlugins->callHook('plxFeedPreChauffageBegin'))) return;
	public function plxFeedPreChauffageBegin() {// $plxAdmin->aConf['clef']
		echo '<?php '; ?># Traitement initial
/*
		if($this->get AND preg_match('#^(?:atom/|rss/)?<?php echo __CLASS__ ?>$#',$this->get)) {
			$this->mode = '<?php echo __CLASS__ ?>'; # Mode du flux
			return true; # stop
		}
*/
		if($this->get AND preg_match('#^admin([a-zA-Z0-9]+)/<?php echo __CLASS__ ?>/(tous|en|hors)-messagerie/?$#',$this->get,$capture)) {
			//$this->mode = 'admin<?php echo __CLASS__ ?>'; # Mode du flux
			$this->cible = '-';	# /!\: il ne faut pas initialiser à blanc sinon ça prend par défaut les commentaires en ligne (faille sécurité)
			if ($capture[1] == $this->clef) {
				if(empty($this->clef)) { # Clef non initialisée
					header('Content-Type: text/plain; charset='.PLX_CHARSET);
					echo L_FEED_NO_PRIVATE_URL;
					exit;
				}
				if( $capture[2] == 'tous' OR $capture[2] == 'en' OR $capture[2] == 'hors' )# mode
					$this->plxPlugins->aPlugins['<?php echo __CLASS__ ?>']->getAdminFeed($this->racine,$this->clef,$this->aConf['title'],$capture[2]);# Traitement initial
				exit;
				//return true;#why do not stop exec ?
			}
		}
?><?php
	}

	/**
	 * Méthode qui affiche le flux RSS des courriels du site pour l'administration
	 * Inspiré de plxFeed getAdminComments()
	 * @return	flux sur stdout
	 * @author	Florent MONTHEL, Amaury GRAILLAT, Thomas Ingles
	 **/
	public function getAdminFeed($racine,$clef,$title,$mode='tous') {// $plxAdmin->aConf['clef'], $this->aConf['title']
		# Traitement initial
		$last_updated = '197001010100';
		$cache_dir = $racine.str_replace('./','',$this->tmp).'/eml/';
		$cache = $this->emlList($mode);
		$files = $cache['files'];
		$cache_size = $cache['size'];
		unset($cache);
//		$img = '<img id="mc_mode" class="icon_pmc" src="'.PLX_PLUGINS.__CLASS__.'/icon.png" title="'.$this->getLang('L_CACHE_LIST').'" />';
		$info = '('.count($files).') - '.$this->getLang('L_TOT').' : '.$this->size_readable($cache_size, $decimals = 2);

		$entry = '';
		foreach($files as $ts => $name){#findicons.com free
			$date_eml = date('YmdHi',$ts);//201806010436
			$title_eml = plxDate::formatDate($date_eml,'#day #num_day #month #num_year(4), #hour:#minute');
			$title_eml .= ' '.$this->getLang('L_FROM').' '.strip_tags(html_entity_decode($name[1].' '.$this->getLang('L_'.(!$name[2]?'UN':'').'SENDED_EMAIL'), ENT_QUOTES, PLX_CHARSET));
			$link_eml = plxUtils::strCheck('<a href="'.$cache_dir.$name[0].'" title="'.$this->real(1,$name[0]).'-'.$this->real(2,$name[0]).'">'.$this->getLang('L_DOWNLOAD_EML').'</a>');
			$link_plg = $racine.'core/admin/plugin.php?p='.__CLASS__.'&amp;mode='.$mode;
			$link_iad = $link_plg.'#'.$ts;
			if($date_eml > $last_updated)
				$last_updated = $date_eml;
			$entry .= "\t<item>\n";
			$entry .= "\t\t".'<title>'.$title_eml.'</title> '."\n";
			$entry .= "\t\t".'<link>'.$link_iad.'</link>'."\n";
			$entry .= "\t\t".'<guid>'.$link_iad.'</guid>'."\n";
			$entry .= "\t\t".'<description>'.$link_eml.plxUtils::strCheck(nl2br(strip_tags($name[4]))).'</description>'."\n";
			$entry .= "\t\t".'<pubDate>'.plxDate::dateIso2rfc822($date_eml).'</pubDate>'."\n";//$ts;'.plxDate::dateIso2rfc822($ts).'
			$entry .= "\t\t".'<dc:creator>'.plxUtils::strCheck($name[1]).'</dc:creator>'."\n";
			$entry .= "\t</item>\n";
		}

		$link = plxUtils::strCheck($racine.'core/admin/plugin.php?p='. __CLASS__);
		//~ $title = $this->aConf['title'].' - '.$this->getLang('L_FEED_EMAIL');
		$title .= ' - '.$this->getLang('L_FEED_EMAIL').' - '.$this->getLang('L_'.strtoupper($mode));
		$link_feed = $racine.'feed.php?admin'.$clef.'/'.__CLASS__;
		# On affiche le flux
		//header('Content-Type: application/rss+xml; charset='.PLX_CHARSET);//plx 5.6
		header('Content-Type: text/xml; charset='.PLX_CHARSET);//DE plux 5.4 avec source colorisé (firefox.60)
		$last_updated = plxDate::dateIso2rfc822($last_updated);
		echo '<?xml version="1.0" encoding="'.PLX_CHARSET.'" ?>'."\n";
?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
	<title><?php echo $title ?></title>
	<description><?php echo ucfirst(__CLASS__) ?> - <?php $this->lang('L_DESCRIPTION') ?> <?php echo plxUtils::strCheck($info) ?></description>
	<link><?php echo $link ?></link>
	<language><?php echo $this->lang ?></language>
	<atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="self" type="application/rss+xml" href="' . $link_feed . '" />
	<lastBuildDate><?php echo $last_updated ?></lastBuildDate>
	<generator>PluXml + <?php echo ucfirst(__CLASS__) ?></generator>
	<image>
		<url><?php echo $racine.'plugins/'.__CLASS__.'/icon.png' ?></url>
		<title>PluXml + <?php echo ucfirst(__CLASS__) ?> Plugin</title>
		<link><?php echo $link ?></link>
		<description>PluXml + <?php echo ucfirst(__CLASS__) ?> Plugin</description>
	</image>
<?php echo $entry; ?>
	</channel>
</rss>
<?php
	}
	public function clean($file=false,$zip=false){# Méthode appelée dans l'administration pour nettoyer le cache et créer le zip de sauvegarde * @return	null * @author	i M@N, Stephane F. Thomas Ingles. #clean cache dir or one file, or zip all cached pages
		$cache_dir = $this->tmp.'/eml/';
		if($file){
			$sended = $this->get_info($file,$type='sended');//0 | 1
			if(!$sended)
				$this->updateUnsend(-1);//MAJ du fichier du compteur
			$file = $cache_dir.$file;
			unlink($file);//On l'éfface de la mémoire
			return plxMsg::Info($this->getLang('L_FILE_CLEANED').'&nbsp;: '.$file);
		}
		if($zip){
			include(PLX_PLUGINS.__CLASS__.'/php/ZipHelper.php');# return text if error
			$rootPath = realpath($cache_dir);
			$zipname = $cache_dir.'.'.__CLASS__.'_'.@$_SERVER['HTTP_HOST'].'_backup.eml.zip';#lost $zip->filename after close() :/
			$zip = new ZipArchive();# Initialize archive object
			if(!$zip->open($zipname, ZipArchive::CREATE | ZipArchive::OVERWRITE))
				return plxMsg::Info($this->getLang('L_CACHE_ZIP_PB').$this->getLang('L_CACHE_ZIP_PC').' : '.zipStatusString($zip->status));
			$zip->setArchiveComment(__CLASS__.' backup : '.date('Y-m-d H:i'));
		}
		if(extension_loaded('glob')){
			if($zip){
				$zip->addGlob($cache_dir."*.eml");
			}else{
				$cached = glob($cache_dir."*.eml");
				foreach ($cached as $file) {
					unlink($file);
				}
				unset($cached);
			}
		}
		else{
			if($cached = opendir($cache_dir)){
				while(($file = readdir($cached))!== false){
					if( $file == '.' || $file == '..' )
					continue;
					if(strtolower(strrchr($file,'.')==".eml")){
						if($zip){
							$filePath = realpath($cache_dir.$file);#Get real ...
							$relativePath = substr($filePath, strlen($rootPath) + 1);# ... and relative path for current file
							$zip->addFile($filePath, $relativePath);
						}else
							unlink($cache_dir.$file);
					}
				}
				closedir($cached);
			}
		}
		if($zip){
			$zip->close();#Zip archive will be created only after closing object && display real zip status
			if($zip->status != ZIPARCHIVE::ER_OK)
				return plxMsg::Info($this->getLang('L_CACHE_ZIP_PB').$this->getLang('L_CACHE_ZIP_PW').' : '.zipStatusString($zip->status));
			return plxMsg::Info($this->getLang('L_CACHE_ZIPPED'));
		}
		else{
			$compte = $this->updateUnsend(0); $this->updateUnsend(0 - $compte);//RAZ du fichier compteur
			return plxMsg::Info($this->getLang('L_CACHE_CLEANED'));
		}
	}
	/**
	 * Méthode de traitement du hook plxShowConstruct, si le mode = maxiContact, incorpore le formulaire dans le tableau de statiques (aStats)
	 *
	 * @return stdio
	 * @author Thomas Ingles
	 **/
	public function plxShowConstruct() {
		echo '<?php '; ?>
		# infos sur la page statique (maxiContact)
		if($this->plxMotor->mode=='<?php echo __CLASS__?>') {
			$array = array();
			$array[$this->plxMotor->cible] = array(
			'name'  => '<?php echo addslashes($this->getParam('mnuName_'.$this->lang))?>',
			'menu'  => '',
			'url'  => '<?php echo __CLASS__?>',//$this->getParam('url')
			'readable' => 1,
			'active' => 1,
			'group'  => ''
		);
			$this->plxMotor->aStats = array_merge($this->plxMotor->aStats, $array);
		}
?><?php
	}
	/**
	 * Méthode de traitement du hook plxMotorPreChauffageBegin
	 *
	 * @return stdio
	 * @author Thomas Ingles
	 **/
	public function plxMotorPreChauffageBegin() {
		$template = $this->getParam('template')==''?'static.php':$this->getParam('template');
		echo '<?php '; ?>
		if($this->get && $this->get === '<?php echo $this->getParam('url')?>') {
			$this->mode = '<?php echo __CLASS__?>';
			$prefix = str_repeat('../', substr_count(trim(PLX_ROOT.$this->aConf['racine_statiques'], '/'), '/'));
			$this->cible = $prefix.'plugins/<?php echo __CLASS__?>/form';
			$this->template = '<?php echo $template?>';
			return true;
		}
?><?php
	}
	/**
	 * Méthode de traitement du hook plxShowStaticListEnd
	 *
	 * @return stdio
	 * @author Thomas Ingles
	 **/
	public function plxShowStaticListEnd() {
		#	ajout du menu pour accèder à la page de contact
		if($this->getParam('mnuDisplay')) {
			echo '<?php ';?>
			$class = $this->plxMotor->mode=='<?php echo __CLASS__?>'?'active':'noactive';
			array_splice($menus, <?php echo ($this->getParam('mnuPos')-1)?>, 0, '<li class="static menu '.$class.'"><a class="static '.$class.'" href="'.$this->plxMotor->urlRewrite('?<?php echo (defined('PLX_MYMULTILINGUE')?$this->lang.'/':'').$this->getParam('url')?>').'" title="<?php echo $this->getParam('mnuName_'.$this->lang)?>"><?php echo $this->getParam('mnuName_'.$this->lang)?></a></li>');
?><?php
		}
	}
	/**
	 * Méthode qui ajoute le fichier css dans le fichier header.php du thème
	 *
	 * @return stdio
	 * @author Thomas Ingles
	 **/
	public function ThemeEndHead() {
//if (function_exists('cssCache')) return;
		echo '<?php ';?>
			if($plxMotor->mode == "<?php echo __CLASS__?>") {
<?php echo ' ?>'; ?>
	<link rel="stylesheet" href="<?php echo PLX_PLUGINS.__CLASS__.'/css/site.css?v='.$this->release ?>" media="screen" async />
<?php
		echo '<?php ';?>
			}
<?php echo ' ?>';
	}
	/**
	 * Méthode qui renseigne le titre de la page dans la balise html <title>
	 * Ajout du titre de la formation
	 * @return stdio
	 * @author Thomas Ingles
	 **/
	public function plxShowPageTitle() {
		$menuname = $this->getParam('mnuName_'.$this->lang);//Fix old server : Fatal error: Can't use method return value in write context
		$menuname = empty($menuname)?$this->lang("L_PAGE_TITLE"):$menuname;
		echo '<?php ';?>
			if($this->plxMotor->mode == "<?php echo __CLASS__?>") {
				echo "<?php echo $menuname ?> - ".$this->plxMotor->aConf['title']." - ".$this->plxMotor->aConf['description'];
				return true;
			}
?><?php
	}
	/**
	 * Méthode qui affiche un message si l'adresse email du contact n'est pas renseignée
	 *
	 * @return stdio
	 * @author Thomas Ingles
	 **/
	public function AdminTopBottom() {
		echo '<?php ';?>
		$mcmail = $plxAdmin->plxPlugins->aPlugins["<?php echo __CLASS__?>"]->getParam('email');
		if(empty($mcmail)) {
			echo '<p class="warning">Plugin <?php echo __CLASS__?><br /><?php echo $this->getLang('L_ERR_EMAIL')?></p>';
			plxMsg::Display();
		}
		unset($mcmail);
?><?php
	}
	/**
	 * Méthode qui référence la page de maxiContact dans le sitemap
	 *
	 * @return stdio
	 * @author Thomas Ingles
	 **/
	public function SitemapStatics() {
		echo '<?php ';?>
		echo "
\t<url>
\t\t<loc>".$plxMotor->urlRewrite("?<?php echo $this->getParam('url')?>")."</loc>
\t\t<changefreq>monthly</changefreq>
\t\t<priority>0.8</priority>
\t</url>".PHP_EOL;
?><?php
	}
	/**
	 * Méthode qui supprime un parametre du fichier parameters.xml
	 * Pour compatibilité de pluxml inferieur a 5.4 ;)
	 * @param	param	nom du parametre à supprimer
	 * @return	true si parametre supprimé, false sinon
	 * @author	Sebastien H
	 **/
	public function delParam($param) {
		if(isset($this->aParams[$param])) {
			unset($this->aParams[$param]);
			return true;
		} else {
			return false;
		}
	}

}#end class
	/* UNTESTÉD : used in sendme() (OLD PHP)
	 * To simulate this function in a horrid way for php <5.2, you can use something like this.
	 * php at joert dot net ¶ https://php.net/manual/fr/function.error-get-last.php#103539
	 **/
if( !function_exists('error_get_last') ) {
	set_error_handler(
		create_function(
			'$errno,$errstr,$errfile,$errline,$errcontext',
			'
				global $__error_get_last_retval__;
				$__error_get_last_retval__ = array(
					\'type\'   => $errno,
					\'message\'=> $errstr,
					\'file\'   => $errfile,
					\'line\'   => $errline
				);
				return false;
			'
		)
	);
	function error_get_last() {
		global $__error_get_last_retval__;
		if( !isset($__error_get_last_retval__) ) {
			return null;
		}
		return $__error_get_last_retval__;
	}
}
