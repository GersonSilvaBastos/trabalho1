document.querySelectorAll('.remove-item').forEach(button => {
    button.addEventListener('click', () => {
        const item = button.parentElement;
        item.remove();
        updateTotal();
    });
});

document.querySelectorAll('.item input').forEach(input => {
    input.addEventListener('change', updateTotal);
});

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.item').forEach(item => {
        const price = parseFloat(item.querySelector('span:nth-child(2)').innerText.replace('$', ''));
        const quantity = parseInt(item.querySelector('input').value);
        total += price * quantity;
    });
    document.querySelector('.total').innerText = `Total: $${total.toFixed(2)}`;
}

