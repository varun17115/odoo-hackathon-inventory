# CoreInventory – Inventory Management System

## Overview

CoreInventory is a modular **Inventory Management System (IMS)** designed to digitize and streamline stock operations within a business.
It replaces manual registers and Excel-based tracking with a **centralized, real-time inventory platform**.

The system enables organizations to efficiently manage:

* Product catalogs
* Warehouse locations
* Incoming stock
* Outgoing deliveries
* Internal stock transfers
* Inventory adjustments
* Stock movement tracking

All inventory changes are recorded through a **stock movement ledger**, ensuring transparency and traceability of every operation.

---

# Problem Statement

Many businesses still rely on spreadsheets or manual logs to manage inventory, which often leads to:

* Data inconsistencies
* Stock mismatches
* Lack of real-time visibility
* Operational inefficiencies

CoreInventory solves this by providing a **structured inventory workflow** that tracks all stock operations through a centralized application.

---

# Key Features

### Product Management

* Create and manage products
* SKU-based identification
* Product categorization
* Unit of measure support
* Reorder level tracking

### Multi-Warehouse Support

* Create multiple warehouses
* Define storage locations
* Track inventory per location

### Receipts (Incoming Stock)

* Register stock received from vendors
* Update inventory automatically
* Record receipt history

### Delivery Orders

* Manage outgoing shipments
* Pick and pack workflow
* Reduce stock automatically upon validation

### Internal Transfers

* Move stock between warehouses or locations
* Maintain accurate location-based inventory

### Inventory Adjustments

* Correct stock discrepancies
* Record adjustments for damaged or missing items

### Stock Ledger

Every stock change is logged in a **stock movement ledger**, providing a complete audit trail of inventory operations.

### Dashboard Analytics

The dashboard provides real-time insights including:

* Total products in stock
* Low stock alerts
* Pending receipts
* Pending deliveries
* Scheduled transfers

---

# Technology Stack

Backend
Laravel

Frontend
Blade + TailwindCSS

Database
MySQL

Authentication
Laravel Breeze

Version Control
Git & GitHub

---

# System Architecture

The system follows a **stock movement-based inventory model**.

Inventory changes never occur directly.
Every operation generates a stock movement entry.

```
Operation → Stock Movement → Stock Update → Ledger Record
```

Supported operations:

* Receipts
* Deliveries
* Transfers
* Adjustments

---

# Database Schema

Core tables used in the system:

```
users
categories
products
warehouses
locations
stocks

receipts
receipt_items

deliveries
delivery_items

transfers
transfer_items

adjustments
adjustment_items

stock_movements
stock_movement_items
```

These tables ensure accurate tracking of stock levels and movement history.

---

# Installation Guide

### Clone Repository

```
git clone https://github.com/varun17115/odoo-hackathon-inventory.git
cd odoo-hackathon-inventory
```

### Install Dependencies

```
composer install
npm install
npm run dev
```

### Environment Setup

```
cp .env.example .env
php artisan key:generate
```

Update database credentials in `.env`.

### Database Setup

Import the provided database file:

```
database.sql
```

Or run migrations if provided:

```
php artisan migrate
```

### Run the Application

```
php artisan serve
```

Open:

```
http://localhost:8000
```

---

# Admin Login Credentials

```
Email: admin@example.com
Password: password
```

---

# Project Structure

```
app
 ├ Models
 ├ Http/Controllers
 ├ Services

database
 ├ migrations
 ├ seeders

resources
 ├ views
 ├ css
 ├ js

routes
 ├ web.php
```

---

# Screenshots

Screenshots of the system interface are available in the `/screenshots` folder.

Examples include:

* Dashboard
* Product Management
* Receipts
* Transfers
* Stock Ledger

---

# Demo Video

A complete walkthrough of the system is available here:

Demo Video Link

*(Replace with your uploaded video link)*

---

# Future Improvements

Potential enhancements include:

* Barcode / QR code scanning for products
* Mobile inventory management application
* AI-based demand forecasting
* Automated purchase order generation
* Advanced inventory analytics

---

# Contributors

Varun Bardia
Hackathon Participant

---

# License

This project is licensed under the MIT License.
