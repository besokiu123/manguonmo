Pawsome — Minimal PHP shop (frontend + backend)

Quick start (Windows PowerShell):

1. Open PowerShell and run inside project folder:

```powershell
cd 'c:\Users\abc\OneDrive\Máy tính\test\shop'
php -S localhost:9000
```

2. Open browser:
- Home: http://localhost:9000/index.html
- Product detail: http://localhost:9000/product.html?id=1
- Cart: http://localhost:9000/cart.html

APIs (for reference):
- `/shop/api/products.php` — GET list of products
- `/shop/api/product.php?id=...` — GET product detail
- `/shop/api/cart.php` — GET view cart, POST add/remove/clear (session)
- `/shop/api/order.php` — POST create order (reads session cart + customer info)

Data is stored in `data/orders.json` (appends new orders).

Notes:
- This is a minimal demo for local development using PHP built-in server. Do not use in production without securing inputs and restricting CORS.
