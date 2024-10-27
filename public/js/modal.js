// Gestion de l'affichage de la fenêtre modale de confirmation de suppression

window.onload = () => {
  // Récupération de la modale
  let modalContainer = document.querySelector('.modal-container');
  // Récupération de tous les liens de suppression ayant un déclencheur de modale
  let modalTriggers = document.querySelectorAll('.modal-trigger');
  // On boucle sur les liens
  for(modalTrigger of modalTriggers) {
    // On définit un gestionnaire d'événement
    modalTrigger.addEventListener('click', function(event){
        // On désactive le lien pour éviter la navigation
        event.preventDefault();
        // On déclenche l'ouverture de la modale de suppression
        modalContainer.classList.toggle("active"); 
        // On récupère la valeur de l'attribut 'href' du lien contenant la route de suppression
        let deletePath = this.getAttribute('href');
        // On récupère la valeur de l'attribut 'data-token' du lien contenant le CSRF Token de suppression
        let deleteToken = this.getAttribute('data-token');
        // On récupère la valeur de l'attribut 'data-message' du lien contenant le message de suppression
        let deleteMessage = this.getAttribute('data-message');
        // On récupère le titre 'h2' de la modale ayant pour classe 'modal-title'
        let modalTitle = document.querySelector('.modal-title');
        // On récupère le formulaire de la modale ayant la classe 'modal-delete-form' 
        let modalDeleteForm = document.querySelector('.modal-delete-form');
        // On récupère le champ caché de la modale ayant la classe 'modal-input' 
        let hiddenInputForm = document.querySelector('.modal-input');
        // On copie le deletePath dans l'attribut 'action' du formulaire la modale
        modalDeleteForm.setAttribute('action',deletePath);
        // On copie le deleteToken dans l'attribut 'value' du champ caché de la modale
        hiddenInputForm.setAttribute('value',deleteToken);
        // On copie le deleteMessage dans le contenu du titre h2 de la modale
        modalTitle.textContent = deleteMessage;
    })
  }
}