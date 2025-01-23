
const btnHamburguesa =document.querySelector(".hamburguesa");
const menu = document.querySelector(".menu");

btnHamburguesa.addEventListener("click", () => {
  menu.style.display = menu.style.display === "flex" ? "none" : "flex";
});

const tabButtons = document.querySelectorAll('.tab-button');
const forms = document.querySelectorAll('.form');

tabButtons.forEach(button => {
  button.addEventListener('click', () => {
    // Elimina la clase activa de todos los botones y formularios
    tabButtons.forEach(btn => btn.classList.remove('active'));
    forms.forEach(form => form.classList.remove('active'));

    // Activa el botón y el formulario correspondiente
    button.classList.add('active');
    document.getElementById(button.dataset.target).classList.add('active');
  });
});