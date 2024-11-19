document.getElementById('brand').addEventListener('change', function() {
    const brandId = this.value;
    const categoryId = document.getElementById('category').value;
    
    fetch(`/api/items/filter?brand_id=${brandId}&category_id=${categoryId}`)
        .then(response => response.json())
        .then(data => {
            const itemSelect = document.getElementById('item');
            itemSelect.innerHTML = '<option value="">Select Item</option>'; // Reset options

            data.forEach(item => {
                itemSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
            });
        });
});

document.getElementById('category').addEventListener('change', function() {
    const categoryId = this.value;
    const brandId = document.getElementById('brand').value;

    fetch(`/api/items/filter?brand_id=${brandId}&category_id=${categoryId}`)
        .then(response => response.json())
        .then(data => {
            const itemSelect = document.getElementById('item');
            itemSelect.innerHTML = '<option value="">Select Item</option>'; // Reset options

            data.forEach(item => {
                itemSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
            });
        });
});
