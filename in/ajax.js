// Requette AJAX
function makeRequest(url,id_niveau,id_ecrire){
				obj = document.getElementById(id_ecrire); 
				obj.innerHTML = '<h5 class=""><img src="../../plugins/maxiContact/img/wait.gif" title="..." /></h5>';
	var http_request = false;
		//créer une instance (un objet) de la classe désirée fonctionnant sur plusieurs navigateurs
		if (window.XMLHttpRequest) { // Mozilla, Safari,...
			http_request = new XMLHttpRequest();
			if (http_request.overrideMimeType) {
				http_request.overrideMimeType('text/xml');//un appel de fonction supplémentaire pour écraser l'en-tête envoyé par le serveur, juste au cas où il ne s'agit pas de text/xml, pour certaines versions de navigateurs Mozilla
			}
		} else if (window.ActiveXObject) { // IE
			try {
				http_request = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try {
					http_request = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {}
			}
		}

		if (!http_request) {
			alert('Abandon :( Impossible de créer une instance XMLHTTP');
			return false;
		}
		
		http_request.onreadystatechange = function() { traitementReponse(http_request,id_ecrire); } //affectation fonction appelée qd on recevra la reponse
		// lancement de la requete  v0.1
		http_request.open('POST', url, true);
		//changer le type MIME de la requête pour envoyer des données avec la méthode POST ,  !!!! cette ligne doit etre absolument apres http_request.open('POST'....
		http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		obj=document.getElementById(id_niveau);
		data="val_sel="+obj.value;
		http_request.send(data);
}

function traitementReponse(http_request,id_ecrire) {
	var affich="";
	if (http_request.readyState == 4) {
		if (http_request.status == 200) {
			// cas avec reponse de PHP en mode texte:
			//chargement des elements reçus dans la liste
			var affich_list=http_request.responseText;
				obj = document.getElementById(id_ecrire); 
				obj.innerHTML = affich_list;
		} 
		else {
				obj = document.getElementById(id_ecrire); 
				obj.innerHTML = '<img src="../../plugins/maxiContact/img/attention.gif" title="Un probleme est survenu avec la requete! Verifier la connexion et relancer..." />' + obj.innerHTML;
				//alert('Un probleme est survenu avec la requete.');
		}
	}
}
