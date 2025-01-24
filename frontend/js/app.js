
const btnHamburguesa =document.querySelector('.hamburguesa');
const menu = document.querySelector('.menu');
const tabButtons = document.querySelectorAll('.tab-button');
const forms = document.querySelectorAll('.form');
const loginButton = document.getElementById('login-button');
const usernameInput = document.getElementById('username');
const welcomeMessage = document.getElementById('login-nav-item');
const imageInput = document.getElementById('product-image');
const previewContainer = document.getElementById('preview-container');
const imagePreview = document.getElementById('image-preview');
const removeImageButton = document.getElementById('remove-image');


//Menú
btnHamburguesa.addEventListener("click", () => {
  menu.style.display = menu.style.display === "flex" ? "none" : "flex";
});

//Vista de formularios
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

// Función para manejar el login
if (loginButton) {
  loginButton.addEventListener('click', () => {
    const username = usernameInput.value.trim(); 
    if (username) {
      localStorage.setItem('username', username);

      // Redirigimos a productor.html
      window.location.href = '../productor.html';
    } else {
      alert('Por favor, ingresa un nombre de usuario.');
    }
  });
}

// Función para mostrar el mensaje de bienvenida
const showWelcomeMessage = () => {
  // Recuperamos el nombre de usuario desde localStorage
  const username = localStorage.getItem('username');

  // Si existe un usuario y el elemento del menú está presente
  if (username && loginNavItem) {
    loginNavItem.innerHTML = `¡Bienvenido(a), ${username}!`; 
  }
};

// Vista previa de imagen
imageInput.addEventListener('change', (event) => {
  const file = event.target.files[0]; // Obtén el archivo cargado

  if (file) {
    const reader = new FileReader(); // Crea un lector de archivos

    // Cuando el archivo se carga correctamente
    reader.onload = (e) => {
      imagePreview.src = e.target.result; // Muestra la imagen en el contenedor
      previewContainer.classList.remove('hidden'); // Muestra la línea de vista previa
    };

    reader.readAsDataURL(file); // Lee el archivo como una URL de datos
  }
});

// Maneja la eliminación de la imagen cargada
removeImageButton.addEventListener('click', () => {
  imageInput.value = ''; // Resetea el input de archivo
  previewContainer.classList.add('hidden'); // Oculta la línea de vista previa
  imagePreview.src = ''; // Elimina la imagen de la vista previa
});


//Envio de productos
const form = document.getElementById('product-form');
form.addEventListener('submit', (event) => {
  event.preventDefault();

  const formData = new FormData(form); 
  fetch('/ruta-del-servidor', {
    method: 'POST',
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log('Producto guardado:', data);
    })
    .catch((error) => {
      console.error('Error al guardar el producto:', error);
    });
});



// Llamamos a la función al cargar la página
showWelcomeMessage();