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
        if (text.includes(filter)) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
});

