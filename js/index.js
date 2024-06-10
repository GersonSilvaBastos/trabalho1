document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    document.getElementById('search').addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        const products = document.querySelectorAll('.product');

        products.forEach(product => {
            const text = product.textContent.toLowerCase();
            product.style.display = text.includes(filter) ? 'block' : 'none';
        });
    });

    fetchProducts();
});

function fetchProducts() {
    fetch('fetch_products.php')
        .then(response => response.json())
        .then(data => {
            const productContainer = document.getElementById('product-list');
            productContainer.innerHTML = '';
            data.forEach(product => {
                const productElement = document.createElement('div');
                productElement.className = 'product';
                productElement.innerHTML = `
                    <img src="${product.image}" alt="${product.name}">
                    <h3>${product.name}</h3>
                    <p>${product.description}</p>
                    <span>$${product.price}</span>
                    <a href="checkout.html">Adicionar ao Carrinho</a>
                `;
                productContainer.appendChild(productElement);
            });
        });
}
