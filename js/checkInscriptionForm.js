
//------------------------------------------------------------------------------
// * Vérifie si le champ spécifié est rempli
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
	document.getElementById("error-" + element).innerHTML = "";
	return true;
}

//------------------------------------------------------------------------------
// * Vérifie la longueur de la chaîne de caractères du champ spécifié
// Si non, insère le message d'erreur correspondant
//------------------------------------------------------------------------------
function checkStringLength(element,maxLength,errorMessage) {
	var content = document.getElementById(element).value;
	if(content.length > maxLength) {
		document.getElementById("error-" + element).innerHTML = errorMessage;
		document.getElementById("error-" + element).style.display = "block";
		return false;
	}
	document.getElementById("error-" + element).style.display = "none";
	document.getElementById("error-" + element).innerHTML = "";
	return true;
}

//------------------------------------------------------------------------------
// * [AJAX/Saisie] Vérifie si le login est déjà pris
// Renvoie toujours vrai.
// Exemple AJAX: http://www.w3schools.com/xml/ajax_database.asp
//------------------------------------------------------------------------------
function checkUserLoginAlreadyUsed(element)
{
	var xhttp;
	var content = document.getElementById(element).value;
	if (content == null || content == "") {
		document.getElementById("error-" + element).style.display = "none";
		document.getElementById("error-" + element).innerHTML = "";
		return true;
	}
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if(this.responseText === "true") {
				document.getElementById("error-" + element).innerHTML = "Ce pseudo est déjà pris";
				document.getElementById("error-" + element).style.display = "block";
			} else if(this.responseText === "false") {
				document.getElementById("error-" + element).style.display = "none";
				document.getElementById("error-" + element).innerHTML = "";
			}
		}
	};
	xhttp.open("GET","php/is_login_used.php?login="+content,true); // requête GET asynchrone
	xhttp.send();
	return true;
}

//------------------------------------------------------------------------------
// * [Saisie] Vérifie le pseudo (longueur & déjà utilisé)
//------------------------------------------------------------------------------
function checkLogin()
{
	return checkStringLength("login",15,"Le pseudo doit faire moins de 15 caractères") &&
	       checkUserLoginAlreadyUsed("login");
}

//------------------------------------------------------------------------------
// * [Saisie] Vérifie le mot de passe (longueur)
//------------------------------------------------------------------------------
function checkPassword()
{
	return checkStringLength("password",32,"Le mot de passe doit faire moins de 32 caractères");
}

//------------------------------------------------------------------------------
// * [Saisie] Vérifie le prénom (longueur)
//------------------------------------------------------------------------------
function checkFirstname()
{
	return checkStringLength("firstname",15,"Le prénom doit faire moins de 15 caractères");
}

//------------------------------------------------------------------------------
// * [Saisie] Vérifie le nom (longueur)
//------------------------------------------------------------------------------
function checkName()
{
	return checkStringLength("name",15,"Le nom doit faire moins de 15 caractères");
}

//------------------------------------------------------------------------------
// * Vérifie le formulaire à la fin du chargement de la page
//------------------------------------------------------------------------------
function checkFormOnLoad()
{
	checkLogin();
	checkPassword();
	checkFirstname();
	checkName();
}

//------------------------------------------------------------------------------
// * Vérifie que le formulaire est correctement rempli
// ! Attention à l'ordre des fonctions !
//------------------------------------------------------------------------------
function checkForm() {
	return (checkEmptyField("login","Veuillez indiquer un pseudo d'utilisateur") &&
	        checkLogin() &&
	        checkEmptyField("password","Veuillez saisir un mot de passe") &&
	        checkPassword() &&
	        checkEmptyField("firstname","Veuillez indiquer votre prénom") &&
	        checkFirstname() &&
	        checkEmptyField("name","Veuillez indiquer votre nom") &&
	        checkName() &&
	        checkEmptyField("avatar","Veuillez sélectionner une image"));
}

//------------------------------------------------------------------------------
// * Envoi le formulaire s'il est correctement rempli
//------------------------------------------------------------------------------
function sendForm() {
	if(checkForm()) document.getElementById("form-inscription").submit();
}
