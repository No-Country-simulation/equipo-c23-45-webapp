document.addEventListener("DOMContentLoaded", () => {
  const btnHamburguesa = document.querySelector('.hamburguesa');
  const menu = document.querySelector('.menu');
  const loginNavItem = document.getElementById('login-nav-item');
  const form = document.getElementById('product-form');
  const imageInput = document.getElementById('product-image');
  const previewContainer = document.getElementById('preview-container');
  const imagePreview = document.getElementById('image-preview');
  const removeImageButton = document.getElementById('remove-image');
  const productContainer = document.createElement("productos-container"); 

  productContainer.classList.add("productos-container");
  document.body.appendChild(productContainer); 

  // Menú hamburguesa
  btnHamburguesa.addEventListener("click", () => {
    menu.style.display = menu.style.display === "flex" ? "none" : "flex";
  });

  // Función para mostrar el mensaje de bienvenida
  const showWelcomeMessage = () => {
    const username = localStorage.getItem('username');
    if (username && loginNavItem) {
      loginNavItem.innerHTML = `¡Bienvenido(a), ${username}!`;
    }
  };

  showWelcomeMessage(); // Mostrar mensaje de bienvenida al cargar la página

  // Vista previa de imagen
  imageInput.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        imagePreview.src = e.target.result;
        previewContainer.classList.remove('hidden');
      };
      reader.readAsDataURL(file);
    }
  });

  // Eliminar imagen de vista previa
  removeImageButton.addEventListener('click', () => {
    imageInput.value = '';
    previewContainer.classList.add('hidden');
    imagePreview.src = '';
  });

  // Envío del formulario para agregar productos
  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const formData = new FormData(form);
    try {
      const response = await fetch('http://localhost/agregarProducto.php', { // se ajusta a disposición del backend
        method: 'POST',
        body: formData,
      });
      const data = await response.json();
      console.log('Producto guardado:', data);
      form.reset();
      previewContainer.classList.add('hidden');
      fetchProductos(); // Vuelve a cargar productos después de agregar uno nuevo
    } catch (error) {
      console.error('Error al guardar el producto:', error);
    }
  });

  // Obtener y mostrar productos desde el backend
  const fetchProductos = async () => {
    try {
      const response = await fetch('http://localhost/tu-ruta/getProductos.php'); // Ajusta la ruta del backend
      const productos = await response.json();
      mostrarProductos(productos);
    } catch (error) {
      console.error('Error al obtener productos:', error);
    }
  };

  // Mostrar productos en cards
  const mostrarProductos = (productos) => {
    productContainer.innerHTML = ''; 
    productos.forEach((producto) => {
      const card = document.createElement("div");
      card.classList.add("producto-card");
      card.innerHTML = `
        <img src="${producto.imagen}" alt="${producto.nombre_producto}">
        <h3>${producto.nombre_producto}</h3>
        <p>Variedad: ${producto.variedad_producto}</p>
        <p>Categoría: ${producto.categoria_producto}</p>
        <p>Cantidad: ${producto.cantidad_disponible} ${producto.unidad_venta}</p>
        <p>Precio: $${producto.precio}</p>
      `;
      productContainer.appendChild(card);
    });
  };

  fetchProductos(); // Cargar productos al cargar la página
});
