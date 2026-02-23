# 🏥 Med Nova: Healthcare Platform SaaS
> **"Healthcare Onboarding & Operations Made Simple"**

Med Nova is a modern, multi-tenant healthcare platform built to digitize hospitals, clinics, and specialized medical centers. It centralizes appointment scheduling, donor management, and nutrition planning into a single, sleek solution.

---

### 🚀 Quick Stats & Tech Stack
![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![Status](https://img.shields.io/badge/Status-Development-orange?style=for-the-badge)

---

### ✨ Core Features

#### 🏷️ Hospital & Clinic Management
- **Smart Booking**: Comprehensive appointment flow (Search → Select → Confirm).
- **Staff Workflows**: Management for Doctors, Receptionists, and Admins.
- **Reporting**: Operational snapshots for facility performance.

#### 🧠 Mental Health Centers
- **Privacy First**: Logically separate modules for psychological case notes.
- **Dedicated Scheduling**: Specific workflows for therapy and counseling.

#### 🍎 Diet & Nutrition Planning
- **Personalized Plans**: Create tailored nutrition guides based on patient profiles.
- **Goal Tracking**: Manage dietary health goals and clinical follow-ups.

#### 🩸 Blood Donation Registry
- **Donor Hub**: Register and track donation history.
- **Urgent Requests**: Broadcast blood requests across the entire network.

#### 📝 Dynamic News & Blog
- **Admin Managed**: Create and categorize blogs via a Rich Text Editor (CKEditor).
- **Dynamic Frontend**: Real-time news updates on the landing page.

---

### 📂 Project Structure
```bash
├── 📁 frontend/     # Public Landing Page & Patient Interface (HTML/CSS/JS)
├── 📁 backend/      # PHP MVC Core, Models, Controllers, and API
├── 📁 database/     # SQL Schemas and Migration Scripts
└── 📁 .github/      # CI/CD and Developer Instructions
```

---

### 🛠️ Local Setup Guide

1. **Environment Ready?**  
   Ensure you are using **XAMPP** (PHP 7.4+ and MySQL).
2. **Installation**  
   Clone/Move the project to `C:\xampp\htdocs\FYP`.
3. **Database Auto-Setup**  
   Navigate to `http://localhost/FYP/backend/`. The system will automatically detect and import the `schema.sql`.
4. **Seed Initial Content**  
   Run `http://localhost/FYP/backend/seed_blogs.php` to populate the homepage News section.
5. **Accessing the App**  
   - **User Frontend**: `http://localhost/FYP/frontend/index.html`
   - **Admin Portal**: `http://localhost/FYP/backend/?route=auth/login`

---

### 🗺️ Future Roadmap
- [ ] Implement advanced privacy encryption for Mental Health case notes.
- [ ] Add Telegram/WhatsApp notifications for appointment reminders.
- [ ] Expand the Patient Dashboard for history & reports.
- [ ] Real-time inventory tracking for Hospital Blood Stock.

---
**Med Nova** – *Bridging the gap between technology and healthcare.*

