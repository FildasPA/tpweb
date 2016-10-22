
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
// * Vérifie la longueur de la chaines de caractères
//------------------------------------------------------------------------------
function checkStringLength(element,maxLength,errorMessage) {
	var content = document.getElementById(element).value;
	if(content.length > maxLength) {
		document.getElementById("error-" + element).innerHTML = errorMessage;
		document.getElementById("error-" + element).style.display = "block";
		return false;
	}
	document.getElementById("error-" + element).style.display = "none";
	return true;
}

//------------------------------------------------------------------------------
// * Vérifie que le formulaire est correctement rempli
//------------------------------------------------------------------------------
function checkForm() {
	return (checkEmptyField("login","Veuillez indiquer un pseudo d'utilisateur") &&
	        checkStringLength("login",15,"Le pseudo doit faire moins de 15 caractères") &&
	        checkEmptyField("password","Veuillez saisir un mot de passe") &&
	        checkStringLength("password",32,"Le mot de passe doit faire moins de 32 caractères") &&
	        checkEmptyField("firstname","Veuillez indiquer votre prénom") &&
	        checkStringLength("firstname",15,"Le prénom doit faire moins de 15 caractères") &&
	        checkEmptyField("name","Veuillez indiquer votre nom") &&
	        checkStringLength("name",15,"Le nom doit faire moins de 15 caractères") &&
	        checkEmptyField("avatar","Veuillez sélectionner une image"));
}

//------------------------------------------------------------------------------
// * Vérifie que le formulaire est correctement rempli avant de l'envoyer
//------------------------------------------------------------------------------
function sendForm() {
	if(checkForm()) document.getElementById("form-inscription").submit();
}

//------------------------------------------------------------------------------
// * [Saisie] Vérifie le pseudo
//------------------------------------------------------------------------------
function checkLogin()
{
	return checkStringLength("login",15,"Le pseudo doit faire moins de 15 caractères");
}

//------------------------------------------------------------------------------
// * [Saisie] Vérifie le mot de passe
//------------------------------------------------------------------------------
function checkPassword()
{
	return checkStringLength("password",32,"Le mot de passe doit faire moins de 32 caractères");
}

//------------------------------------------------------------------------------
// * [Saisie] Vérifie le prénom
//------------------------------------------------------------------------------
function checkFirstname()
{
	return checkStringLength("firstname",15,"Le prénom doit faire moins de 15 caractères");
}

//------------------------------------------------------------------------------
// * [Saisie] Vérifie le nom
//------------------------------------------------------------------------------
function checkName()
{
	return checkStringLength("name",15,"Le nom doit faire moins de 15 caractères");
}
