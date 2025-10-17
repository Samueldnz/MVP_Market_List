# 🥗 Optimized Meal Plan Management System

## 🎯 Project Overview

The goal of this project is to develop a **comprehensive web-based system** for managing **custom meal plans**, including **meals, food items, substitutions, and price optimization**.  
The system allows users to create detailed dietary plans, manage food prices, and automatically generate a **monthly optimized shopping list** that minimizes total cost while maintaining nutritional equivalence.

---

## ⚙️ Main Objectives

1. Register **complete meal plans**, composed of **meals and substitutions**.  
2. **View, edit, and delete** each plan, meal, food item, or substitution.  
3. Register and update the **price of foods** (including measurement units).  
4. Automatically generate an **optimized monthly shopping list** calculating:
   - Total cost per meal;  
   - Cheapest substitution combinations;  
   - Total monthly quantities of each ingredient;  
   - Estimated total cost of the plan.

---

## 🗃️ Database Structure (MySQL)

The database `plano_alimentar` is composed of the following tables:

### `planos`
| Field | Type | Description |
|-------|------|--------------|
| id | INT (PK, AI) | Plan identifier |
| nome | VARCHAR(100) | Plan name (e.g., “Bulking Samuel”) |
| descricao | TEXT | Short description |
| data_criacao | DATETIME | Creation date |

### `refeicoes`
| Field | Type | Description |
|-------|------|-------------|
| id | INT (PK, AI) |  |
| plano_id | INT (FK → planos.id) | Linked plan |
| nome | VARCHAR(100) | Meal name (e.g., “Breakfast”) |
| horario | TIME | Approximate time |

### `alimentos`
| Field | Type | Description |
|-------|------|-------------|
| id | INT (PK, AI) |  |
| refeicao_id | INT (FK → refeicoes.id) | Linked meal |
| nome | VARCHAR(100) | Food name |
| quantidade | DECIMAL(10,2) | Quantity |
| unidade | VARCHAR(20) | Unit (g, ml, slice, unit, etc.) |

### `substituicoes`
| Field | Type | Description |
|-------|------|-------------|
| id | INT (PK, AI) |  |
| refeicao_id | INT (FK → refeicoes.id) | Linked meal |
| nome | VARCHAR(100) | Substitution name (e.g., “Banana shake with whey”) |

### `alimentos_substituicao`
| Field | Type | Description |
|-------|------|-------------|
| id | INT (PK, AI) |  |
| substituicao_id | INT (FK → substituicoes.id) | Linked substitution |
| nome | VARCHAR(100) | Food name |
| quantidade | DECIMAL(10,2) | Quantity |
| unidade | VARCHAR(20) | Unit |

### `precos`
| Field | Type | Description |
|-------|------|-------------|
| id | INT (PK, AI) |  |
| alimento_nome | VARCHAR(100) | Food name |
| preco | DECIMAL(10,2) | Price |
| unidade | VARCHAR(20) | Unit (e.g., “kg”, “L”, “unit”) |
| data_atualizacao | DATETIME | Last update |

---

## 🧩 Core Functionalities

### 1. 📋 Meal Plan Management
- Create and edit plans with name and description.
- Redirects to the **meal editing page** upon creation.

### 2. 🍽️ Meal and Substitution Management
- Add multiple meals per plan, each containing:
  - Name (e.g., “Lunch”)
  - Time of day
  - List of foods (name, quantity, unit)
  - List of substitutions (each with their own foods)
- Supports **dynamic inputs via JavaScript** for adding, editing, and deleting items without page reload.

### 3. 💰 Food Price Management
- Dedicated page `/precos.php` for managing food prices.
- Displays all unique registered foods.
- Allows updating:
  - Unit price  
  - Measurement unit (g, kg, L, unit, etc.)

### 4. 🧾 Optimized Shopping List Generation
- “Generate Shopping List” button triggers:
  - Calculation of costs for all meals and substitutions;
  - Automatic selection of the **cheapest substitution** for each meal;
  - Aggregation of total food quantities for 30 days;
  - Display of:
    - Food name  
    - Total monthly quantity  
    - Unit price and total cost  
    - Associated meal  
  - Shows the **total monthly cost** of the plan.

> 📝 The result is displayed in HTML (no PDF generation required).

---

## 💻 Page Structure

| Page | Purpose |
|------|----------|
| `index.php` | Dashboard for managing all meal plans |
| `criar_plano.php` | Form to create a new meal plan |
| `editar_plano.php?id=1` | Edit meals and substitutions |
| `precos.php` | Manage and edit food prices |
| `lista_compras.php` | Display the optimized shopping list |

---

## 🎨 Front-End

- Built with **HTML5 + CSS3 (Flexbox/Grid)** for a clean, responsive design.  
- Uses **cards** to display plans and meals.  
- **JavaScript** handles dynamic input fields for meals and substitutions.  
- **Modals** are used for editing and deleting items, improving user experience.

---

## ⚙️ Back-End (PHP)

- Developed in **PHP** with **PDO** for secure MySQL database interaction.  
- Uses **prepared statements** to prevent SQL injection.  
- Centralized connection via `db_connect.php`.  
- Simple REST-like CRUD routes:
  - `add_plano.php`  
  - `add_refeicao.php`  
  - `add_alimento.php`  
  - `add_substituicao.php`  
  - `add_preco.php`  
  - `gerar_lista.php`

---

## 📊 Optimization Logic

1. For each meal:
   - Compute base cost = Σ (quantity × unit price).  
   - Compute each substitution’s cost similarly.  
   - Choose the **lowest total cost** option.
2. Aggregate total quantities and costs for 30 days.  
3. Display a summary table:

| Meal | Food | Monthly Quantity | Unit | Unit Price | Total Cost |
|------|------|------------------|-------|-------------|-------------|

4. Show the **final monthly total cost** of the meal plan.

---

## 🚀 Summary

This project integrates **nutritional planning, price management, and cost optimization** into a single intuitive platform.  
It provides a **complete and data-driven solution** for nutritionists, trainers, or individuals who want to **automate the financial and logistical aspects of meal planning**, ensuring efficiency, flexibility, and transparency.
