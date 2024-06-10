document.addEventListener('DOMContentLoaded', function() {
    fetchDashboardData();

    function fetchDashboardData() {
        fetch('fetch_dashboard_data.php')
            .then(response => response.json())
            .then(data => {
                // Update sales stats
                document.getElementById('total-sales').innerText = `Total de Vendas: $${data.sales_stats.total_sales}`;
                document.getElementById('number-of-orders').innerText = `Número de Pedidos: ${data.sales_stats.number_of_orders}`;
                document.getElementById('products-sold').innerText = `Produtos Vendidos: ${data.sales_stats.products_sold}`;

                // Update product list
                const productList = document.getElementById('product-list');
                productList.innerHTML = '';
                data.products.forEach(product => {
                    const productElement = document.createElement('li');
                    productElement.innerHTML = `
                        <span>Nome do Produto: ${product.name}</span>
                        <span>Preço: $${product.price}</span>
                        <span>Disponível: ${product.stock}</span>
                        <button class="edit-product">Editar</button>
                        <button class="delete-product">Eliminar</button>
                    `;
                    productList.appendChild(productElement);
                });

                // Update order list
                const orderList = document.getElementById('order-list');
                orderList.innerHTML = '';
                data.orders.forEach(order => {
                    const orderElement = document.createElement('li');
                    orderElement.innerHTML = `
                        <span>Número da Encomenda: #${order.id}</span>
                        <span>Data: ${order.created_at}</span>
                        <span>Total: $${order.total}</span>
                        <button class="process-order">Processar</button>
                        <button class="view-details">Detalhes</button>
                    `;
                    orderList.appendChild(orderElement);
                });
            });
    }
});
