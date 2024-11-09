// Gestion du caroussel

// Récupération des éléments du DOM
let carouselSlides = [...document.querySelectorAll('.slide')];
let carouselRightButton = document.querySelector('.btn--right');
let carouselLeftButton = document.querySelector('.btn--left');
let carouselDots = [...document.querySelectorAll('.dot')];

// Création d'un objet Carousel
const carousel = {
  // Index du slide courant 
  slideIndex: 0, 
  // Nombre total de slides du caroussel
  totalSlides: carouselSlides.length,
  // Valeur du timer de défilement automatique du caroussel
  timer: 5000,
  // Index du point indicateur courant
  dotIndex: 0
}

// Attribution par défaut de la classe 'active' au 1er slide du carousel
carouselSlides[0].classList.add('slide--active');

// Attribution par défaut de la classe 'dot--fill' au premier point indicateur du caroussel
carouselDots[0].classList.add('dot--fill');

// Réinitialisation des classes 'active'
function resetActive() {
  // Suppression de la classe 'active' pour tous les slides 
  carouselSlides.forEach(slide => slide.classList.remove('slide--active'));
  // Attribution de la classe 'active' uniquement à la slide courante
  carouselSlides[carousel.slideIndex].classList.add('slide--active');
}

// Réinitialisation des classes 'dot--fill'
function resetDotFill() {
  // Suppression de la classe 'dot--fill' pour tous les points indicateurs 
  carouselDots.forEach(carouselDot => carouselDot.classList.remove('dot--fill'));
  // Attribution de la classe 'dot--fill' uniquement au point indicateur courant
  carouselDots[carousel.slideIndex].classList.add('dot--fill');
}

// Défilement du slider vers la droite
function moveSliderToRight() {
  // On augmente l'index de caroussel de 1
  carousel.slideIndex = carousel.slideIndex + 1;
  // On augmente l'index du point indicateur de 1
  carousel.dotIndex = carousel.dotIndex + 1;
  // A la fin du caroussel, on retourne au 1er slide
  if(carousel.slideIndex > carousel.totalSlides - 1) {
    carousel.slideIndex = 0;
    carousel.dotIndex = 0;
  }
}

// Défilement du slider vers la gauche
function moveSliderToLeft() {
  // On diminue l'index de caroussel de 1
  carousel.slideIndex = carousel.slideIndex - 1;
  // On diminue l'index du point indicateur de 1
  carousel.dotIndex = carousel.dotIndex - 1;
  // Au début du caroussel, on retourne au dernière slide
  if(carousel.slideIndex < 0) {
    carousel.slideIndex = carousel.totalSlides -1 ;
    carousel.dotIndex = carousel.totalSlides -1 ;
  }
}

// Gestion du click sur bouton droit du caroussel
carouselRightButton.addEventListener("click", function(e){
  // Défilement du slider vers la droite
  moveSliderToRight();
  // Réinitialisation des classes 'active'
  resetActive();
  // Réinitialisation des classes 'dot--fill'
  resetDotFill();
});

// Gestion du click sur bouton gauche du caroussel
carouselLeftButton.addEventListener("click", function(e){
  // Défilement du slider vers la gauche
  moveSliderToLeft();
  // Réinitialisation des classes 'active'
  resetActive();
  // Réinitialisation des classes 'dot--fill'
  resetDotFill();
});

// Gestion du click sur les points indicateurs
carouselDots.forEach((carouselDot, currentDotIndex) => {
  carouselDot.addEventListener("click", function(e){
    // Suppression de la classe 'active' pour tous les slides 
    carouselSlides.forEach(slide => slide.classList.remove('slide--active'));
    // Attribution par défaut de la classe 'active' au slide du carousel dont l'index est égal à l'index courant du point indicateur
    carousel.slideIndex = currentDotIndex;
    carouselSlides[carousel.slideIndex].classList.add('slide--active');
    // Réinitialisation des classes 'dot--fill'
    resetDotFill();
  });
});
  
// Défilement automatique du slider
setInterval(function() {
  moveSliderToRight();
  resetActive();
  resetDotFill();
}, carousel.timer);
