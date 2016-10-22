
//------------------------------------------------------------------------------
// * Vérifie si les champs spécifié est rempli
// Si non, insère le message d'erreur correspondant
//------------------------------------------------------------------------------
function checkEmptyField(element,errorMessage) {
	var content = document.getElementById(element).value;
	if(content == null || content == "") {
		document.getElementById("error-" + element).innerHTML = errorMessage;
		document.getElementById("error-" + element).style.display = "block";
		return false;
	}
	document.getElementById("error-" + element).style.display = "none";
	return true;
}

//------------------------------------------------------------------------------
// * Vérifie si les champs sont remplis
// Si non, affiche le message d'erreur correspondant au premier champ vide.
//------------------------------------------------------------------------------
function checkEmptyFields() {
	return checkEmptyField("login","Veuillez indiquer un pseudo d'utilisateur") &&
		checkEmptyField("password","Veuillez saisir un mot de passe") &&
		checkEmptyField("firstname","Veuillez indiquer votre prénom") &&
		checkEmptyField("name","Veuillez indiquer votre nom") &&
		checkEmptyField("avatar","Veuillez sélectionner une image");
}

//------------------------------------------------------------------------------
// * Vérifie que le formulaire est correctement rempli
//------------------------------------------------------------------------------
function checkForm() {
	return checkEmptyFields();
	//if(document.referrer.indexOf("inscription.php") == -1) return false;
}

//------------------------------------------------------------------------------
// * Vérifie que le formulaire est correctement rempli avant de l'envoyer
//------------------------------------------------------------------------------
function sendForm() {
	if(checkForm()) {
		document.getElementById("form-inscription").submit();
	}
}
