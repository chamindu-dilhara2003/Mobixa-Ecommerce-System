# 📱 Mobixa-Ecommerce-System

<p align="center">
  <img src="https://shields.io" alt="PHP" />
  <img src="https://shields.io" alt="MySQL" />
  <img src="https://shields.io" alt="HTML5" />
  <img src="https://shields.io" alt="CSS3" />
  <img src="https://shields.io" alt="JavaScript" />
</p>

An elegant, fully-featured **Online Mobile Accessories Selling System** built to provide a seamless shopping experience for customers and a robust management system for store administrators.

---

## 🌟 Key Features

### 🛒 Client/Public Side
* **User Authentication:** Secure registration, login, and profile management.
* **Product Catalog:** Smart filtering and searching for mobile accessories (cases, chargers, earphones, etc.).
* **Shopping Cart System:** Smooth experience adding, removing, or updating product quantities in real-time.
* **Checkout Process:** Secure order placement workflow with delivery detail collection.
* **Order Tracking:** Customers can view past transaction histories and real-time order statuses.

### 🛡️ Admin Dashboard
* **Product Management:** Complete CRUD functionality (Create, Read, Update, Delete) for products and inventory categories.
* **Order Processing:** Oversee customer orders, update shipment tracking, and process returns.
* **User Control:** View and manage user accounts securely.
* **Sales Reports:** Generate date-wise printable performance metrics and analytical overviews.

---

## 🛠️ Architecture & Tech Stack

* **Frontend:** HTML5, CSS3 (Bootstrap framework for responsive styling), and pure JavaScript (including AJAX for smooth, asynchronous updates).
* **Backend:** PHP (OOP principles for secure request routing).
* **Database:** SQL / MySQL (Structured relational design for optimal performance).

---

## 📁 File Structure Overview

```text
Mobixa-Ecommerce-System/
│
├── assets/               # CSS, JS, and global application images
├── config/               # Database connection scripts (db.php)
├── includes/             # Shared UI components (header.php, footer.php)
├── admin/                # Specialized dashboard controls and interfaces
│   ├── index.php         # Admin base route
│   └── manage-products.php
│
├── index.php             # Landing and catalog homepage
├── cart.php              # Dynamic shopping cart layout
├── checkout.php          # Order validation form
└── database.sql          # Primary relational database structure file
```

---

## 🚀 Installation & Setup Guide

Get this application running locally in just a few steps:

### Prerequisites
Make sure you have a local web server tool installed, such as **XAMPP**, **WAMP**, or **MAMP**.

### Step-by-Step Instructions

1. **Clone the repository:**
   ```bash
   git clone https://github.com
   ```

2. **Move files to your server directory:**
   Copy the cloned project folder and move it inside your server's public root directory (e.g., `C:/xampp/htdocs/` for XAMPP).

3. **Import the Database Schema:**
   * Start Apache and MySQL via your local server control panel.
   * Open a browser and navigate to `http://localhost/phpmyadmin/`.
   * Create a new database named **`mobixa_db`**.
   * Click on the **Import** tab, choose the `database.sql` file located in the root of this project, and click **Go**.

4. **Update Connection Parameters:**
   Navigate to the database configuration file (typically in `config/db.php` or `config.php`) and make sure the server credentials match your local setup:
   ```php
   $host = "localhost";
   $user = "root";
   $password = "";
   $dbname = "mobixa_db";
   ```

5. **Launch the Storefront:**
   Open your browser and visit: `http://localhost/Mobixa-Ecommerce-System/`

---

## 🔒 Security Practices Implemented
* Relational database integrity using clean SQL joins.
* SQL injection preventative measures via parameterized queries.
* Input validation protocols handled securely across client and backend environments.

---

## 🤝 Contributing

Contributions make the open-source community an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.
