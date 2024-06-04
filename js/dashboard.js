document.querySelectorAll('.edit-product').forEach(button => {
    button.addEventListener('click', () => {
        const productDetails = button.parentElement;
        const name = productDetails.querySelector('span:nth-child(1)').innerText;
        const price = productDetails.querySelector('span:nth-child(2)').innerText;
        const available = productDetails.querySelector('span:nth-child(3)').innerText;
        alert(`Edit Product:\nName: ${name}\nPrice: ${price}\nAvailable: ${available}`);
    });
});

document.querySelectorAll('.process-order').forEach(button => {
    button.addEventListener('click', () => {
        const orderDetails = button.parentElement;
        const orderId = orderDetails.querySelector('span:nth-child(1)').innerText;
        const date = orderDetails.querySelector('span:nth-child(2)').innerText;
        const total = orderDetails.querySelector('span:nth-child(3)').innerText;
        alert(`Process Order:\nOrder ID: ${orderId}\nDate: ${date}\nTotal: ${total}`);
    });
});

