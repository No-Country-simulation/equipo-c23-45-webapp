
const btnHamburguesa =document.querySelector(".hamburguesa");
const menu = document.querySelector(".menu");

btnHamburguesa.addEventListener("click", () => {
  menu.style.display = menu.style.display === "flex" ? "none" : "flex";
});