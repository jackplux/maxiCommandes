<?php

$LANG = array(

'L_PAGE_TITLE'         => 'Contact',
'L_LANG_UNAVAILABLE'   => 'Langue du plugin indisponible : %s',
'L_FORCE_LANG'         => 'Forcer malgré les traductions manquante',
'L_SEE_PAGE'           => 'Voir la page publiée',
'L_SEE'                => 'Voir',
'L_HELP'               => 'Aide',//si <= 5.3.1
# config.php
'L_MAIN'               => 'Globaux',
'L_MAIN2'              => 'Champs pré-programmés',
'L_MAIL_AVAILABLE'     => 'Fonction d’envoi de mail disponible',
'L_MAIL_NOT_AVAILABLE' => 'Fonction d’envoi de mail non disponible',
'L_URL'                => 'Paramètre de l’url',
'L_OPTIONNEL'          => 'Optionnel',
'L_MENU_DISPLAY'       => 'Afficher le menu de la page de contact',
'L_MENU_TITLE'         => 'Titre du menu',
'L_MENU_POS'           => 'Position du menu',
'L_REDIRECT_URL'       => 'Url de redirection en cas de succès',
'L_SUCCESS_INC_URL'    => 'Inclure en PHP au lieu de rediriger',
'L_SUCCESS_INC_HINT'   => 'Si activé, mettre une url de redirection relative',
'L_EMAIL'              => 'Destinataire(s) du courriel',
'L_EMAIL_CC'           => 'Destinataire(s) en copie du courriel',
'L_EMAIL_BCC'          => 'Destinataire(s) en copie cachée du courriel',
'L_EMAIL_FROM'         => 'Expediteur du courriel',
'L_EMAIL_SUBJECT'      => 'Objet du mail',
'L_APPEND_EMAIL_SUBJECT'=> 'Permettre à l’utilisateur d’ajouter son propre objet à celui ci-dessus',
'L_FAKE_SEND'          => 'Envois factices',
'L_FAKE_SEND_HINT'     => 'Aucun envoi de courriel, sauf ceux de confirmation.',
'L_DEL_TMP'            => 'Effacer les courriels sauvés à la désactivation',
'L_DEL_TMP_HINT'       => 'Détruire le dossier des courriels sauvés lorsque le plugin est désactivé ou supprimé.',
'L_THANKYOU_MESSAGE'   => 'Message de remerciement',
'L_TEMPLATE'           => 'Template',
'L_CAPTCHA'            => 'Activer le captcha anti-spam',
'L_SAVE'               => 'Enregistrer',
'L_COMMA'              => 'Séparé par une virgule',
'L_ANSWER'             => 'Réponse',
'L_ANSWERS'            => 'Réponses',
'L_MESSAGE'            => 'Message',
'L_DEFAULT_MENU_NAME'  => 'Contact',
'L_DEFAULT_OBJECT'     => '## Pluxml ## nouveau contact depuis votre site internet',
'L_DEFAULT_THANKYOU'   => 'Merci de m’avoir contacté. Je vous répondrai le plus rapidement possible.',
'L_NOMBRE_ADES'        => 'Nombre de question personnalisée',
'L_AFFICHER'           => 'Afficher',
'L_DEBUT'              => 'Au début du formulaire',
'L_PROG'               => 'Après les champs pré-programmés',
'L_AQCM'               => 'Après le QCM',
'L_MSG'                => 'Après le message (a la fin)',
'L_DESC_CONF'          => 'Paramètres du formulaire de courriel',
'L_ERROR_EMAIL'        => 'Veuillez saisir une adresse courriel valide',
'L_ERROR_LLANG'        => 'Ce PluXml est ancien, peut être une mise a jour',
'L_ACTIVATE'           => 'Activer',
'L_ACTIVE'             => 'Activée',
'L_INACTIVE'           => 'Désactivée',
'L_ATTIBUTS'           => 'Attributs personnalisés du champ',
'L_ATTR_TTL'           => 'Paramètres du champ “inpuṭ“ à personnaliser.'.PHP_EOL.'Style, attributs HTML(5) et javascript ...'.PHP_EOL.'Èviter le paramètre “required“ (déja géré)',
'L_H_MDN'              => 'Attributs',#Compatibilité_des_navigateurs

#help
'L_HELP_DESC'          => 'Page de contact personnalisable sans perte de courriel',

#feed+admin
'L_FEED_EMAIL'         => 'Courriels',
'L_DOWNLOAD_EML'       => 'Télécharger le Courriel (source)',
'L_FROM'               => 'De',
'L_TO'                 => 'Pour',
'L_TOUS'               => 'Tous',// les messages
'L_EN'                 => 'Envoyés',
'L_HORS'               => 'Non envoyés',

#admin
'L_DESCRIPTION'        => 'Courriels en mémore',
'L_NOSCRIPT'           => 'Javascript désactivé, possibilités dégradés',
'L_REFRESH'            => 'Recharger',
'L_SENDED_EMAIL'       => 'Courriel envoyé',
'L_UNSENDED_EMAIL'     => 'Courriel non envoyé',
'L_RESPOND_TO'         => '(ou plus) avec PluXml et',
'L_MAIL_TO'            => 'avec votre programme de messagerie local',
'L_WRITE_TO'           => 'Écrire à',
'L_TOT'                => 'Total',
'L_NOT_FOUND'          => 'Erreur, Fichier %s introuvable',
'L_COPYOF'             => 'Copie de',//préfixe du sujet senndme()
'L_SENDME'             => 'Envoyer le courriel a',
'L_SWITCHME'           => 'Basculer en',
'L_CACHE_ZIP'          => 'Sauvegarder',
'L_CACHE_ZIPDEL'       => 'Supprimer le fichier de sauvegarde',
'L_CACHE_ZIPPED'       => 'Dossier eml sauvegardé',
'L_CACHE_ZIP_PB'       => 'Une erreur est survenue',
'L_CACHE_ZIP_PC'       => 'Impossible de créer le fichier de sauvegarde',
'L_CACHE_ZIP_PW'       => 'Impossible de placer le fichier dans la sauvegarde',
'L_ZIP_SERVER'         => 'Fichier zip de sauvegarde présent dans le serveur',
'L_CACHE_LIST'         => 'Liste des Courriels en mémoire',
'L_CLEAN_CACHE'        => 'Vider',
'L_CACHE_CLEANED'      => 'Dossier eml vidé',
'L_FILE_CLEANED'       => 'Fichier effacé',
'L_EML_CONFIRM_DEL'    => 'Voulez vous effacer l’eml',
'L_EML_CONFIRM_SENDME' => 'Voulez vous envoyer une copie de l’eml a',
'L_CACHE_CONFIRM_DEL'  => 'Voulez vous vidé le dossier des eml?',
'L_SEE_EMAIL_TITLE'    => 'Voir et cacher ce courriel',
'L_HIDE_EMAIL'         => 'Cacher ce courriel',
'L_SEE_EMAIL'          => 'Voir ce courriel',

# form.contact.php
'L_MSG_WELCOME'        => 'Merci de remplir le formulaire ci-dessous',
'L_ERR_NAME'           => 'Veuillez saisir votre nom',
'L_ERR_EMAIL'          => 'Veuillez saisir une adresse de courriel valide',
'L_ERR_SUBJECT'        => 'Veuillez saisir l’object du message',
'L_ERR_CONTENT'        => 'Veuillez saisir le contenu de votre message',
'L_ERR_ANTISPAM'       => 'La vérification anti-spam a échoué',
'L_ERR_SENDMAIL'       => 'Une erreur est survenue pendant l’envoi de votre message',
'L_SENDMAIL_OK'        => 'Message envoyé avec succés',
'L_ERR_SENDMAIL_PLUS'  => 'Problème de serveur! Votre message n’a pu être envoyé! Veuillez Réessayer plus tard.',

'L_FORM_NAME'          => 'Votre nom',
'L_FORM_MAIL'          => 'Votre adresse courriel',
'L_FORM_SUBJECT'       => 'Objet de votre message',
'L_FORM_CONTENT'       => 'Contenu de votre message',
'L_FORM_ANTISPAM'      => 'Vérification anti-spam',
'L_FORM_BTN_SEND'      => 'Envoyer',
'L_FORM_BTN_RESET'     => 'Effacer',

'L_PREAMB_MAIL'        => 'Contact de',
'L_PREAMB_SUBJECT'     => 'A propos de',//Au sujet de
'L_MAIL'               => 'Courriel',
'L_PERSO'              => 'Champs Personnalisés',
'L_QUEST'              => 'Réglages de la question',//Activer cette question personnalisée
'L_QUESTION_PERSO'     => 'Question',

'L_OBLIGATOIRE'        => 'Requis',//Obligatoire

'L_CHAMPS_OBLIGATOIRES'=> 'Champs obligatoires',
'L_ADRS'               => 'Adresse',

'L_SEXE'               => 'Demander la civilité',
'L_SEXE2'              => 'Votre civilité',
'L_MR'                 => 'Mr',
'L_MM'                 => 'Mme',
'L_ERR_SEXE'           => 'Choisissez votre intitulé',

'L_PRENOM'             => 'Demander un prénom',
'L_PRENOM2'            => 'Votre prénom',
'L_ERR_PRENOM'         => 'Veuillez saisir votre prénom',

'L_TEL'                => 'Demander un numero de telephone',
'L_TEL2'               => 'Votre numéro de téléphone',
'L_TEL3'               => 'Téléphone',
'L_ERR_TEL'            => 'Veuillez saisir votre numero de téléphone',

'L_ENTREPRISE'         => 'Demander le nom de leur entreprise',
'L_ENTREPRISE2'        => 'Votre entreprise',
'L_ENTREPRISE3'        => 'Entreprise',
'L_ERR_ENTREPRISE'     => 'Veuillez saisir le nom de votre entreprise',

'L_SITE'               => 'Demander leur site web',
'L_SITE2'              => 'https://votre-site-web.net',
'L_SITE3'              => 'Site web',
'L_ERR_SITE'           => 'Veuillez saisir votre site web',

'L_FAX'                => 'Demander le numéro de fax',
'L_FAX2'               => 'Votre numéro de fax',
'L_FAX3'               => 'Fax',
'L_ERR_FAX'            => 'Veuillez saisir le numéro de fax',

'L_PROFESSION'         => 'Demander la profession',
'L_PROFESSION2'        => 'Votre profession',
'L_PROFESSION3'        => 'Profession',
'L_ERR_PROFESSION'     => 'Veuillez saisir votre profession',

'L_MOTIF'              => 'Demander le motif',
'L_MOTIF2'             => 'Motif',
'L_MOTIF3'             => 'Motif',
'L_ERR_MOTIF'          => 'Veuillez saisir le motif',

'L_ADRESSE'            => 'Demander une adresse',
'L_RUE'                => 'Votre rue',
'L_CP'                 => 'Votre code postal',
'L_VILLE'              => 'Votre ville',
'L_ERR_RUE'            => 'Veuillez saisir votre rue',
'L_ERR_CP'             => 'Veuillez saisir votre code postal',
'L_ERR_VILLE'          => 'Veuillez saisir votre ville',

'L_QCM'                => 'QCM',
'L_QCM_TITLE'          => 'Intitulé du QCM',
'L_TYPE'               => 'Type',
'L_COMMENT'            => 'Réglages du QCM',//Activer ce questionnaire à Choix Multiple, Demander comment il nous ont connus
'L_COMMENT2'           => 'Vous nous avez connus par',
'L_COMMENT3'           => 'Réponse a la question',//Connu par
'L_ERR_COMMENT'        => 'Veuillez répondre a la question',//sélectionner
'L_NOMBRE_QCM'         => 'Nombre de QCM personnalisés',
'L_NOMBRE_QRM'         => 'Nombre de réponses personnalisées',
'L_REPONSE_PERSO'      => 'Réponse possible',
'L_REPONSE_AUTRE'      => 'Autre',
'L_REPONSE_PRECIS'     => 'veuillez préciser',
'L_AUTRE'              => 'Activer le Choix Autre',//config

'L_NO_REPONSE'         => 'Non répondu.',
'L_ERR_REPONSE'        => 'Veuillez répondre a la question',

'L_EXTENSIONS_OK'      => 'Extensions de fichiers autorisées au téléversement',
'L_PIECE_SIZE'         => 'Poids Maxi des fichiers téléversés',
'L_POST_SIZE'          => 'Poids Maxi des données téléversés',
'L_POST_SIZE_HINT'     => 'G : Giga, M : Méga, K : Kilo (octets)',
'L_PIECE'              => 'Permettre de téléverser des fichiers',
'L_PIECE2'             => 'Pièce a joindre',
'L_PIECE3'             => 'Pièce jointe, le fichier "%s" est envoyé.',#
'L_ERR_PIECE'          => 'Veuillez joindre un fichier.',
'L_ERR_PIECE2'         => 'Information : Le type du fichier "%s" est interdit!',#type
'L_ERR_PIECE3'         => 'Information : Le fichier "%s" fait plus de "%s", pièce non jointe.',#est trop volumineux
'L_MAX_UPLOAD_FILE'    => 'Taille maxi par fichier',
'L_MAX_UPLOAD_NBFILE'  => 'Nombre maxi de fichiers par envoi',
'L_MAX_POST_SIZE'      => 'Taille maxi des données',
'L_SCROLLTOTOP'        => 'Remonter en début de page',
'L_LIBERAPAY'          => 'Dons Réguliers simplifiés avec LiberaPay',

#datatable
'L_LABEL_JSDTABLE_PLACEHOLDE'=> 'Chercher...',//Search...
'L_LABEL_JSDTABLE_PERPGS'    => '{select} courriels par page',//{select} entries per page
'L_LABEL_JSDTABLE_NODATA'    => 'Aucun(e) courriels trouvé(e)',//No entries found
'L_LABEL_JSDTABLE_INFO'      => 'Affichage de {start} à {end} sur {rows} courriels',//Showing {start} to {end} of {rows} entries

#class mail
'L_CLASS_MAIL_ARRAY'   => array(
'checkMail' => "Class Mail, method Mail : Adresse Invalide",
'checkExp_ok_msg' => "Votre courriel, destiné à %s, a été envoyé!",
'checkExp_ok_sbj' => "Votre courriel au sujet de: %s...",
'checkExp_unknown' => " - [Adresse (%s) non reconnue!]",
'From_error' => "Class Mail: Erreur, From n'est pas de la bonne forme"
 )//L_CLASS_MAIL_ARRAY default lang
);