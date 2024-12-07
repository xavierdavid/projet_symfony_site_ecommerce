// Gestion de la validation 'frontend' du formulaire d'inscription


// Récupération des éléments du DOM

// Icônes de validation
let inputSuccessIcons = document.querySelectorAll('.input-success-icon');
let inputAlertIcons = document.querySelectorAll('.input-alert-icon');
let passwordSuccessIconFirst = document.querySelector('.password-success-icon-first');
let passwordAlertIconFirst = document.querySelector('.password-alert-icon-first');
let passwordSuccessIconSecond = document.querySelector('.password-success-icon-second');
let passwordAlertIconSecond = document.querySelector('.password-alert-icon-second');
// Messages d'erreurs
let inputErrorMessages = document.querySelectorAll('.input-error-msg');
let passwordInfoMessage = document.querySelector('.password-info-msg');
// Formulaire d'inscription
let registerForm = document.querySelector('form');
let registerFormContainer = document.querySelector('.register-container');
// Champs du formulaire d'inscription
let registerFirstname = document.querySelector('#register_firstname');
let registerLastname = document.querySelector('#register_lastname');
let registerEmail = document.querySelector('#register_email');
let registerPasswordFirst = document.querySelector('#register_password_first');
let registerPasswordSecond = document.querySelector('#register_password_second');
// Lignes indicatrices de force du mot de passe
let strengthLines = document.querySelectorAll('.password-lines div');
// Bouton de validation
let registerButton = document.querySelector('.btn');


// Création d'un objet de vérification de la validité des champs lors de la soumission du formulaire
const formSubmitValidation = {
  firstname: false,
  lastname: false,
  email: false,
  password: false,
  passwordConfirmation: false
}

// Gestionnaire d'évènement pour la soumission du formulaire
registerButton.addEventListener('click', handleRegisterForm)

// Initialisation d'une animation sur le formulaire
let isAnimated = false;

/**
 * Gestion de la soumission du formulaire
 */
function handleRegisterForm(e) {
  
  // On récupère dans un tableau l'ensemble des propriétés de l'objet formSubmitValidation
  const keys = Object.keys(formSubmitValidation);
  const failedInputs = keys.filter(key => !formSubmitValidation[key])
  // Création d'une animation s'il reste des erreurs
  if(failedInputs.length && !isAnimated) {
    // On neutralise le comportement par défaut de l'envoi de formulaire
    e.preventDefault();
    isAnimated = true;
    registerFormContainer.classList.add('shake');
    setTimeout(()=>{
      registerFormContainer.classList.remove('shake');
      isAnimated = false;
    }, 400)
  }
}

// Gestionnaire d'évènement pour la saisie du prénom de l'utilisateur
registerFirstname.addEventListener('blur', firstnameValidation)
registerFirstname.addEventListener('input', firstnameValidation)

/**
 * Validation de la saisie du prénom de l'utilisateur
 */
function firstnameValidation() {
  if(registerFirstname.value.length >= 3) {
    // On gère l'affichage de l'icône et du message de succès de validation
    showValidation({index: 0, validation: true})
    // On valide le champ firstname pour la soumission
    formSubmitValidation.firstname = true;
  } else {
    // On gère l'affichage de l'icône et du message d'erreur de validation
    showValidation({index: 0, validation: false})
    // On ne valide pas le champ firstname pour la soumission
    formSubmitValidation.firstname = false;
  }
}

// Gestionnaire d'évènement pour la saisie du nom de l'utilisateur
registerLastname.addEventListener('blur', lastnameValidation)
registerLastname.addEventListener('input', lastnameValidation)

/**
 * Validation de la saisie du nom de l'utilisateur
 */
function lastnameValidation() {
  if(registerLastname.value.length >= 2) {
    showValidation({index: 1, validation: true})
    formSubmitValidation.lastname = true;
  } else {
    showValidation({index: 1, validation: false})
    formSubmitValidation.lastname = false;
  }
}

// Gestionnaire d'évènement pour la saisie de l'email de l'utilisateur
registerEmail.addEventListener('blur', emailValidation)
registerEmail.addEventListener('input', emailValidation)

// Définition d'un modèle de chaîne caractères (Objet Regex et sa méthode 'test') pour l'email
const regexEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/

/**
 * Validation de la saisie de l'email de l'utilisateur
 */
function emailValidation() {

  if(regexEmail.test(registerEmail.value)) {
    showValidation({index: 2, validation: true})
    formSubmitValidation.email = true;
  } else {
    showValidation({index: 2, validation: false})
    formSubmitValidation.email = false;
  }
}

/**
 * Gestion de l'affichage des icônes et messages des champs de formulaire à valider 
 * Paramètres : index du champ et booléen de validation
 * @param {*} param0 
 */
function showValidation({index, validation}){
  if(validation) {
    // On affiche l'icône de succès du champ à valider
    inputSuccessIcons[index].style.display = "inline";
    // On masque l'icône d'alerte' du champ à valider
    inputAlertIcons[index].style.display = "none";
    // On masque le message d'erreur du champ à valider
    inputErrorMessages[index].style.display = "none";
  }
  else {
    // On masque l'icône de succès du champ à valider
    inputSuccessIcons[index].style.display = "none";
    // On affiche l'icône d'alerte' du champ à valider
    inputAlertIcons[index].style.display = "inline";
    // On affiche le message d'erreur du champ à valider
    inputErrorMessages[index].style.display = "block";
  }
}

// Gestionnaire d'évènement pour la saisie du champ principal dédié au mot de passe de l'utilisateur
registerPasswordFirst.addEventListener('blur', passwordValidation)
registerPasswordFirst.addEventListener('input', passwordValidation)

// Création d'un objet de vérification du mot de passe
const passwordVerification = {
  // Propriétés de l'objet et valeurs par défaut avant vérification
  length: false,
  specialCharacter: false,
  numbers: false
}

// Définition d'un objet Regex pour le mot de passe
const regexPassword = {
  // Un caracère spécial
  specialCharacter: /[^a-zA-Z0-9\s]/,
  // 2 chiffres
  numbers: /[0-9]{2}/,
}

// Initialisation de la valeur du mot de passe
let passwordValue;

/**
 * Validation de la saisie du mot de passe de l'utilisateur
 */
function passwordValidation(e) {
  passwordValue = registerPasswordFirst.value;
  let validationResult = 0;
  // On boucle sur les propriétés de l'objet passwordVerification
  for (const prop in passwordVerification) {
    // Vérification de la longueur du mot de passe
    if(prop === 'length') {
      if(passwordValue.length < 11) {
        passwordVerification.length = false;
      }
      else {
        passwordVerification.length = true;
        validationResult++;
      }
      continue;
    }
    // Vérification des regex des propriétés 'specialCharacter' et 'numbers' de l'objet regexPassword
    if (regexPassword[prop].test(passwordValue)) {
      passwordVerification[prop] = true;
      validationResult++;
    }
    else {
      passwordVerification[prop] = false;
    }
  }
  // Appel de la fonction gérant l'affichage des icônes de validation du champ principal dédié au mot de passe
  if(validationResult !== 3) {
    showPasswordFirstValidation({validation: false})
    formSubmitValidation.password = false;
  }
  else {
    showPasswordFirstValidation({validation: true})
    formSubmitValidation.password = true;
  }
  // Appel de la fonction gérant la force du mot de passe
  passwordStrength()
}

/**
 * Gestion de la force du mot de passe
 */
function passwordStrength() {
  const passwordLength = registerPasswordFirst.value.length;
  // Si la longueur du mot de passe est à zéro
  if(!passwordLength) {
    // Force nulle : on affiche aucune ligne 
    addLines(0);
  }
  else if (passwordLength > 10 && passwordVerification.specialCharacter && passwordVerification.numbers) {
    // Force élevée : on affiche 3 lignes 
    addLines(3)
  }
  else if(passwordLength > 10 && passwordVerification.specialCharacter || passwordVerification.numbers) {
    // Force moyenne : on affiche 2 lignes 
    addLines(2)
  }
  else {
    // Force faible : on affiche 1 ligne 
    addLines(1)
  }
}

/**
 * Gestion de l'affichage des lignes indicatrices de force du mot de passe
 * @param {*} numberOfLines 
 */
function addLines(numberOfLines) {
  strengthLines.forEach((line, index) => {
    if(index < numberOfLines) {
      line.style.display = 'block'
    }
    else {
      line.style.display = 'none'
    }
  })
  if(passwordSuccessIconSecond.style.display = "inline") {
    confirmPassword()
  }
}

/**
 * Gestion de l'affichage des icônes de validation du champ principal dédié au mot de passe 
 * Paramètre : booléen de validation
 * @param {*} param0 
 */
function showPasswordFirstValidation({validation}){
  if(validation) {
    // On affiche l'icône de succès du champ 'password first'
    passwordSuccessIconFirst.style.display = "inline";
    // On masque l'icône d'alerte' du champ 'password first'
    passwordAlertIconFirst.style.display = "none";
  }
  else {
    // On masque l'icône de succès du champ 'password first'
    passwordSuccessIconFirst.style.display = "none";
    // On affiche l'icône d'alerte' du champ 'password first'
    passwordAlertIconFirst.style.display = "inline";
  }
}

// Gestionnaire d'évènement pour la validation du champ dédié à la confirmation du mot de passe de l'utilisateur
registerPasswordSecond.addEventListener('blur', confirmPassword)
registerPasswordSecond.addEventListener('input', confirmPassword)

function confirmPassword(){
  let confirmedValue = registerPasswordSecond.value;
  if(!confirmedValue && !passwordValue) {
    // On masque l'icône de succès du champ 'password second'
    passwordSuccessIconSecond.style.display = "none";
  }
  else if(confirmedValue !== passwordValue) {
    showPasswordSecondValidation({validation: false})
    formSubmitValidation.passwordConfirmation = false;
  }
  else {
    showPasswordSecondValidation({validation: true})
    formSubmitValidation.passwordConfirmation = true;
  }
}

/**
 * Gestion de l'affichage des icônes de validation du champ dédié à la confirmation du mot de passe 
 * Paramètre : booléen de validation
 * @param {*} param0 
 */
function showPasswordSecondValidation({validation}){
  if(validation) {
    // On affiche l'icône de succès du champ 'password second'
    passwordSuccessIconSecond.style.display = "inline";
    // On masque l'icône d'alerte' du champ 'password second'
    passwordAlertIconSecond.style.display = "none";
  }
  else {
    // On masque l'icône de succès du champ 'password second'
    passwordSuccessIconSecond.style.display = "none";
    // On affiche l'icône d'alerte' du champ 'password second'
    passwordAlertIconSecond.style.display = "inline";
  }
}
