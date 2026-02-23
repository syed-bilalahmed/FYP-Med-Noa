## Med-Nova Healthcare Platform

Med-Nova is a multi-tenant, SaaS-based healthcare platform designed for hospitals, clinics, and specialized centers. It centralizes appointments, patient records, and operational workflows while allowing each organization to maintain its own data and configuration.

### Key Modules

- **Hospital & Clinic Management**: End-to-end appointment flow (booking, rescheduling, cancellations), outpatient and inpatient management, basic billing support, and operational reporting for general hospitals and clinics.
- **Psychological & Mental Health Centers**: A logically separate module with its own schedules, case notes, and role-based access to protect highly sensitive records.
- **Diet & Nutrition Plans**: Creation and management of personalized diet plans that can be attached to patient profiles and clinical encounters.
- **Blood Donation & Requests**: Donor registration, donation history, blood stock management, and handling of blood requests from hospitals and clinics.

### Architecture Overview

- **Frontend**: Static HTML pages using Bootstrap 5, custom CSS, and basic JavaScript for user interactions.
- **Backend**: Custom PHP MVC architecture exposing REST-like endpoints and server-rendered views.
- **Database**: MySQL with a normalized schema supporting:
  - Multiple hospitals/clinics (multi-tenancy by hospital/clinic)
  - Role-based access (admin, doctor, receptionist, patient, etc.)
  - Core entities such as users, patients, doctors, appointments, hospitals, departments, and regions

### Project Layout

- `frontend/` – Public-facing site and basic interfaces (HTML, CSS, JS).
- `backend/` – PHP MVC backend, database configuration, controllers, and API endpoints.

The full Copilot/developer-oriented instructions and file reference live in `.github/copilot-instructions.md`.

### Getting Started (Local Development)

1. **Environment**
   - Windows with XAMPP (Apache, PHP 7.4+ with PDO MySQL, MySQL 5.7+).
2. **Clone / Copy Project**
   - Place the project in `c:\xampp\htdocs\FYP\`.
3. **Backend Setup**
   - Configure Apache to serve `backend/` (e.g., `http://localhost/FYP/backend/`).
   - On first connection, the backend auto-creates the main database (default: `fyp_mediqu`) using the provided schema.
4. **Frontend Access**
   - Serve `frontend/` via Apache (e.g., `http://localhost/FYP/frontend/index.html`).

### Next Steps / Roadmap (High Level)

- Harden role-based access and privacy for psychological centers.
- Complete full integration of all frontend forms with backend APIs.
- Extend reporting and analytics for appointments, donations, and diet plans.

