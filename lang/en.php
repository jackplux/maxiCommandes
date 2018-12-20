<?php

$LANG = array(

'L_PAGE_TITLE'         => 'Contact',
'L_LANG_UNAVAILABLE'   => 'Language of plugin unavailable : %s',
'L_FORCE_LANG'         => 'Force unavailable translate',
'L_SEE_PAGE'           => 'See public page',
'L_SEE'                => 'See',
# config.php
'L_MAIN'               => 'Main',
'L_MAIN2'              => 'Pre-programed',
'L_MAIL_AVAILABLE'     => 'Mail sending function available',
'L_MAIL_NOT_AVAILABLE' => 'Mail sending function unavailable',
'L_URL'                => 'Url parameter',
'L_OPTIONNEL'          => 'Optional',
'L_MENU_DISPLAY'       => 'Display the menu of the contact page',
'L_MENU_TITLE'         => 'Menu title',
'L_MENU_POS'           => 'Menu position',
'L_REDIRECT_URL'       => 'Redirected Url in case of success',
'L_SUCCESS_INC_URL'    => 'PHP Include instead of redirect',
'L_SUCCESS_INC_HINT'   => 'If activate, use redirect relative url',
'L_EMAIL'              => 'Recipients email',
'L_EMAIL_CC'           => 'Carbon copy of the email recipients',
'L_EMAIL_BCC'          => 'Blind carbon copy of the email recipients',
'L_EMAIL_FROM'         => 'From',
'L_EMAIL_SUBJECT'      => 'E-mail subject',
'L_APPEND_EMAIL_SUBJECT'=> 'Allow user to append his own subject to the one above',
'L_FAKE_SEND'          => 'Fake sends',
'L_FAKE_SEND_HINT'     => 'Send only confirmation email.',
'L_DEL_TMP'            => 'Delete saved emails folder on deactivate',
'L_DEL_TMP_HINT'       => 'Erase the folder of saved emails when the plugin is disabled or deleted.',
'L_THANKYOU_MESSAGE'   => 'Thank you message',
'L_TEMPLATE'           => 'Template',
'L_CAPTCHA'            => 'Enable anti spam-capcha',
'L_SAVE'               => 'Save',
'L_COMMA'              => 'Seperated by comma',
'L_ANSWER'             => 'Response',
'L_ANSWERS'            => 'Responses',
'L_MESSAGE'            => 'Message',
'L_DEFAULT_MENU_NAME'  => 'Contact',
'L_DEFAULT_OBJECT'     => '## Pluxml ## new contact from your website',
'L_DEFAULT_THANKYOU'   => 'Thank you for contacting me. I will reply as soon as possible.',
'L_NOMBRE_ADES'        => 'Number of customized question',
'L_AFFICHER'           => 'Show',
'L_DEBUT'              => 'At begining of the form',//Au début du formulaire
'L_PROG'               => 'After Pre-programed',
'L_AQCM'               => 'After MCQ',
'L_MSG'                => 'After message (end)',
'L_DESC_CONF'          => 'Parameters of email form',
'L_ERROR_EMAIL'        => 'Please enter a valid e-mail',
'L_ERROR_LLANG'        => 'Our PluXml is Old, maybe update it.',
'L_ACTIVATE'           => 'Activate',
'L_ACTIVE'             => 'Active',
'L_INACTIVE'           => 'Inactive',
'L_ATTIBUTS'           => 'Field HTML attributes',
'L_ATTR_TTL'           => 'Parameters or style attributes',
'L_H_MDN'              => 'Attributes',#Browser_compatibility

#help
'L_HELP_DESC'          => 'Contact page with most options without loose email',

#feed+admin
'L_FEED_EMAIL'         => 'Emails',
'L_DOWNLOAD_EML'       => 'Download this email (source)',
'L_FROM'               => 'From',
'L_TO'                 => 'To',
'L_TOUS'               => 'All',// messages
'L_EN'                 => 'In Box',
'L_HORS'               => 'Out of Box',

#admin.php
'L_DESCRIPTION'        => 'Emails in cache',
'L_NOSCRIPT'           => 'Javascript off, possibilities downgraded',
'L_REFRESH'            => 'Reload',
'L_SENDED_EMAIL'       => 'Sended emails',
'L_UNSENDED_EMAIL'     => 'Unsended emails',
'L_RESPOND_TO'         => '(or more) with PluXml and',
'L_MAIL_TO'            => 'with your local mail program',
'L_WRITE_TO'           => 'Write to',
'L_TOT'                => 'Total',
'L_NOT_FOUND'          => 'Error, file %s not found',
'L_COPYOF'             => 'Copy of',//senndme() subject prefix
'L_SENDME'             => 'Send email to',
'L_SWITCHME'            => 'Switch to',
'L_CACHE_ZIP'          => 'Save to zip',
'L_CACHE_ZIPDEL'       => 'Delete backup file',
'L_CACHE_ZIPPED'       => 'Cache folder zipped',
'L_CACHE_ZIP_PB'       => 'An error is occurred',
'L_CACHE_ZIP_PC'       => 'Failed to create archive',
'L_CACHE_ZIP_PW'       => 'Failed to write files to zip',
'L_ZIP_SERVER'         => 'zip backup on server',
'L_CACHE_LIST'         => 'Mail Files list',
'L_CLEAN_CACHE'        => 'Trash',
'L_CACHE_CLEANED'      => 'Cache folder cleared',
'L_FILE_CLEANED'       => 'File cache cleared',
'L_EML_CONFIRM_DEL'    => 'Would you remove this eml',
'L_EML_CONFIRM_SENDME' => 'Would you send one copy of this eml to',
'L_CACHE_CONFIRM_DEL'  => 'Would you cleaned all eml files ?',
'L_SEE_EMAIL_TITLE'    => 'See and hide this email',
'L_HIDE_EMAIL'         => 'Hide email',
'L_SEE_EMAIL'          => 'See email',

# form.contact.php
'L_MSG_WELCOME'        => 'Please complete this form',
'L_ERR_NAME'           => 'Please enter your name',
'L_ERR_EMAIL'          => 'Please enter a valid e-mail',
'L_ERR_SUBJECT'        => 'Please enter the subject of your message',
'L_ERR_CONTENT'        => 'Please enter the content of your message',
'L_ERR_ANTISPAM'       => 'Anti-spam verification failed',
'L_ERR_SENDMAIL'       => 'An error has occurred while sending your message',
'L_SENDMAIL_OK'        => 'Message send with success',
'L_ERR_SENDMAIL_PLUS'  => 'Server error! Your message not send! Resend later',

'L_FORM_NAME'          => 'Your name',
'L_FORM_MAIL'          => 'Your e-mail address',
'L_FORM_SUBJECT'       => 'Subject of your message',
'L_FORM_CONTENT'       => 'Content of your message',
'L_FORM_ANTISPAM'      => 'Anti-spam verification',
'L_FORM_BTN_SEND'      => 'Send',
'L_FORM_BTN_RESET'     => 'Reset',

'L_PREAMB_MAIL'        => 'Contact of',
'L_PREAMB_SUBJECT'     => 'About of',
'L_MAIL'               => 'Email',
'L_PERSO'              => 'Customized fields',
'L_QUEST'              => 'Settings of customized question',//Activate this customized question
'L_QUESTION_PERSO'     => 'Question',

'L_OBLIGATOIRE'        => 'Required',

'L_CHAMPS_OBLIGATOIRES'=> 'Required fields',
'L_ADRS'               => 'Address',

'L_SEXE'               => 'Ask the civility',
'L_SEXE2'              => 'Civility',
'L_MR'                 => 'Mr',
'L_MM'                 => 'Ms',
'L_ERR_SEXE'           => 'Choose your civility',

'L_PRENOM'             => 'Ask for a surname',
'L_PRENOM2'            => 'Your surname',
'L_ERR_PRENOM'         => 'Please enter your surname',

'L_TEL'                => 'Ask for a number phone',
'L_TEL2'               => 'Your number phone',
'L_TEL3'               => 'Phone',
'L_ERR_TEL'            => 'Please enter your phone number',

'L_ENTREPRISE'         => 'Ask for the company',
'L_ENTREPRISE2'        => 'Your company',
'L_ENTREPRISE3'        => 'Company',
'L_ERR_ENTREPRISE'     => 'Please enter your company',

'L_SITE'               => 'Ask for the website',
'L_SITE2'              => 'http://your-website.net',
'L_SITE3'              => 'Website',
'L_ERR_SITE'           => 'Please enter your website',

'L_FAX'                => 'Ask for the fax number',
'L_FAX2'               => 'Your fax number',
'L_FAX3'               => 'Fax',
'L_ERR_FAX'            => 'Please enter your fax number',

'L_PROFESSION'         => 'Ask for the profession',
'L_PROFESSION2'        => 'Your profession',
'L_PROFESSION3'        => 'Profession',
'L_ERR_PROFESSION'     => 'Please enter your profession',

'L_MOTIF'              => 'Ask for the raison',
'L_MOTIF2'             => 'Raison',
'L_MOTIF3'             => 'Raison',
'L_ERR_MOTIF'          => 'Please enter the raison',

'L_ADRESSE'            => 'Ask for an address',
'L_RUE'                => 'Your street',
'L_CP'                 => 'Your postal number',
'L_VILLE'              => 'Your town',
'L_ERR_RUE'            => 'Please enter your street',
'L_ERR_CP'             => 'Please enter your postal code',
'L_ERR_VILLE'          => 'Please enter your town',

'L_QCM'                => 'MCQ',//Poll
'L_QCM_TITLE'          => 'Title of the MCQ',
'L_TYPE'               => 'Type',
'L_COMMENT'            => 'Settings of the MCQ',//Enable Multiple Choice Questionnaire Ask how they knew us
'L_COMMENT2'           => 'You knew us by',
'L_COMMENT3'           => 'Answer to the question',//Known by
'L_ERR_COMMENT'        => 'Please give an response to the question',
'L_NOMBRE_QCM'         => 'Number of polls',
'L_NOMBRE_QRM'         => 'Number of personalized responses',
'L_REPONSE_PERSO'      => 'Possible responses',
'L_REPONSE_AUTRE'      => 'Other',
'L_REPONSE_PRECIS'     => 'please specify',
'L_AUTRE'              => 'Enable the Choice Other',//config

'L_NO_REPONSE'         => 'Not responding.',
'L_ERR_REPONSE'        => 'Please respond to the answer',

'L_EXTENSIONS_OK'      => 'File extentions ok to upload',
'L_PIECE_SIZE'         => 'Max size of file upload',
'L_POST_SIZE'          => 'Max size of data upload',
'L_POST_SIZE_HINT'     => 'G : Giga, M : Mega, K : Kilo (Bytes)',
'L_PIECE'              => 'File upload',
'L_PIECE2'             => 'Attach',
'L_PIECE3'            => 'Attachment, the file %s is sent',#
'L_ERR_PIECE'          => 'Please attach a file',
'L_ERR_PIECE2'         => 'Information : File type of "%s" is forbidden!',#type
'L_ERR_PIECE3'         => 'Information : Attached %s file too large, makes over "%s", unattached file',#est trop volumineux
'L_MAX_UPLOAD_FILE'    => 'Maximum file size',
'L_MAX_UPLOAD_NBFILE'  => 'Max number of files per upload',
'L_MAX_POST_SIZE'      => 'Maximum data size',
'L_SCROLLTOTOP'        => 'Scroll to top page',
'L_LIBERAPAY'          => 'Donate with LiberaPay',

#datatable
'L_LABEL_JSDTABLE_PLACEHOLDE'=> 'Search...',
'L_LABEL_JSDTABLE_PERPGS'    => '{select} email per page',//{select} entries per page
'L_LABEL_JSDTABLE_NODATA'    => 'No email found',
'L_LABEL_JSDTABLE_INFO'      => 'Showing {start} to {end} of {rows} email',

#class mail
'L_CLASS_MAIL_ARRAY'   => array(
'checkMail' => "Class Mail, method Mail: Invalid Address",
'checkExp_ok_msg' => "Your email, to %s, has be send!",
'checkExp_ok_sbj' => "Your email subject: %s...",
'checkExp_unknown' => " - [Unknown address (%s)!]",
'From_error' => "Error Class Mail: Bad form of From"
 )//L_CLASS_MAIL_ARRAY lang
);