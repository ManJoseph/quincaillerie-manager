# 🛠️ StoreMinder: Professional Hardware Store Manager

[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)](https://github.com/ManJoseph/quincaillerie-manager)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ed.svg?logo=docker)](https://www.docker.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2-777bb4.svg?logo=php)](https://www.php.net/)

**StoreMinder** is a robust, multi-tenant inventory and sales management system specifically engineered for hardware stores. It streamlines complex operations like unit-based stock tracking (Boxes/Items/Loose), automated sales recording, and detailed financial reporting.

---

## ✨ Key Features

- 🏢 **Multi-Tenancy Architecture:** Host multiple stores on a single platform with complete data isolation.
- 📦 **Smart Inventory:** Track stock using a flexible unit system (Boxes, Items per Box, and Loose Units) with automatic total quantity calculations.
- 💸 **Sales & Transactions:** Link multiple sales to a single parent transaction for better tracking of buyers and payment statuses.
- 📊 **Insightful Dashboard:** Real-time metrics on total products, low-stock alerts, monthly income, and sales volume.
- 📄 **Professional PDF Receipts:** Automated receipt generation using FPDF.
- 📧 **Email Notifications:** Instant digital receipts sent via PHPMailer (SMTP).
- 🔐 **Role-Based Access:** Secure authentication with Admin and User roles.
- 🌓 **Modern UI:** Responsive design with support for both Light and Dark themes.

---

## 🚀 Tech Stack

- **Backend:** PHP 8.2 (Procedural)
- **Database:** MySQL 8.0
- **Frontend:** Vanilla CSS (Modern Design System), JavaScript (AJAX)
- **Libraries:** [PHPMailer](https://github.com/PHPMailer/PHPMailer), [FPDF](http://www.fpdf.org/)
- **Infrastructure:** Docker & Docker Compose

---

## 🛠️ Installation & Setup

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop) installed.

### Quick Start (Docker)

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/ManJoseph/quincaillerie-manager.git
    cd quincaillerie-manager
    ```

2.  **Spin up the containers:**
    ```bash
    docker-compose up -d
    ```

3.  **Access the app:**
    Open your browser and navigate to `http://localhost:8085`.

*Note: The database is automatically initialized using the script in `setup/database.sql`.*

---

## 📂 Project Structure

```text
├── app/          # Core application modules (Dashboard, Inventory, Sales)
├── auth/         # Authentication logic (Login, Register, Password Recovery)
├── utils/        # Shared utilities (Database connection, Mailer helper)
├── setup/        # Database initialization scripts
├── docs/         # Project documentation and design guides
├── css/          # Global styles and design system
├── img/          # Visual assets and icons
└── fpdf186/      # PDF generation library
```

---

## 🤝 Contributing

We welcome contributions! Whether it's fixing bugs, adding features, or improving documentation:

1.  **Fork** the project.
2.  **Create** your feature branch (`git checkout -b feature/AmazingFeature`).
3.  **Commit** your changes (`git commit -m 'Add some AmazingFeature'`).
4.  **Push** to the branch (`git push origin feature/AmazingFeature`).
5.  **Open** a Pull Request.

---

## 📞 Contact

**Man.Joseph** - [GitHub Profile](https://github.com/ManJoseph)

Project Link: [https://github.com/ManJoseph/quincaillerie-manager](https://github.com/ManJoseph/quincaillerie-manager)
