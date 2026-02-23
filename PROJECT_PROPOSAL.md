## Med-Nova Healthcare Platform – Project Proposal

**Med-Nova** is a multi-tenant, SaaS-based healthcare platform designed for **hospitals**, **clinics**, and **specialized centers**. It centralizes appointments, patient records, diet plans, and blood donation activities, while allowing each organization to maintain its own configuration and workflows.

---

### 1. Problem Statement

Traditional hospital and clinic systems are often:

- **Fragmented**: Separate tools for appointments, records, and donations.
- **Paper-based**: Manual tracking of patients, diet plans, and blood stock.
- **Not specialized**: Mental health centers and nutrition services are usually treated as generic modules.

Med-Nova addresses these gaps with a unified, web-based platform offering both **general hospital management** and **specialized modules** (psychological centers, diet planning, and blood donation).

---

### 2. Project Objectives

- **Provide a SaaS-based hospital and clinic system** for appointments, patient management, and basic billing.
- **Offer a separate, privacy-aware workflow for psychological centers** (mental health).
- **Support diet and nutrition planning** linked to patient records.
- **Digitize and manage blood donation and requests** across hospitals and clinics.
- **Enable multi-tenant usage**, so multiple hospitals/centers can use the same platform safely.

---

### 3. Major Modules

- **Hospital & Clinic Management**
  - Outpatient and inpatient appointments (booking, rescheduling, cancellations).
  - Doctor and department assignments.
  - Basic billing support and operational reporting.

- **Psychological & Mental Health Centers**
  - Separate module with its own appointments and schedules.
  - Case notes and records with stricter role-based access.
  - Better privacy for sensitive psychological data.

- **Diet & Nutrition Plans**
  - Create personalized diet plans for patients.
  - Link plans to clinical visits or diagnoses.
  - Track updates to diet recommendations over time.

- **Blood Donation & Requests**
  - Donor registration and donation history.
  - Blood stock management by type and quantity.
  - Handling blood requests from hospitals/clinics.

---

### 4. System Workflow (High-Level)

#### 4.1 Appointment & Treatment Flow (Hospitals / Clinics)

1. **Patient / Receptionist**
   - Select hospital/clinic and department.
   - Choose available doctor and time slot.
   - Submit appointment request.
2. **System**
   - Validates slot availability.
   - Saves appointment to the database.
   - Notifies doctor dashboard.
3. **Doctor**
   - Views daily schedule.
   - Opens patient record, updates diagnosis and notes.
   - (Optional) Creates or updates diet plan.
4. **Admin / Reception**
   - Manages check-in, status (pending, in-progress, completed), and basic billing.

#### 4.2 Psychological Center Workflow

1. **Psychological Center Reception**
   - Registers or selects existing patient.
   - Books session with psychologist/therapist.
2. **Psychologist**
   - Accesses only authorized psychological records.
   - Records session notes and follow-up plans.
3. **System**
   - Ensures sensitive data is restricted by role and center.

#### 4.3 Diet Plan Workflow

1. **Doctor / Dietitian**
   - Opens patient profile from appointment.
   - Creates or edits a diet plan (meals, timings, restrictions).
2. **System**
   - Saves diet plan and links it to patient and visit.
3. **Patient / Staff**
   - Views assigned diet plan during future visits.

#### 4.4 Blood Donation & Request Workflow

1. **Donor / Staff**
   - Registers donor details and eligibility.
   - Records donation event with blood group and quantity.
2. **System**
   - Updates blood stock.
3. **Hospital / Clinic**
   - Raises blood request with required blood type and units.
4. **Admin / Blood Bank**
   - Matches request with available stock.
   - Approves or rejects request and updates inventory.

---

### 5. Architecture Overview

- **Frontend**
  - Static HTML pages using **Bootstrap 5**, **custom CSS**, and basic **JavaScript**.
  - Public-facing pages and basic interaction forms for appointments, contact, etc.

- **Backend**
  - Custom **PHP MVC** architecture.
  - REST-like endpoints and server-rendered views.
  - Controllers for authentication, hospital admin, doctor, patient, and API access.

- **Database**
  - **MySQL**, normalized schema supporting:
    - Multiple hospitals/clinics (multi-tenancy).
    - Role-based access (admin, doctor, receptionist, patient, etc.).
    - Core entities: users, patients, doctors, appointments, hospitals, departments, regions, donors, and diet plans.

---

### 6. Project Layout

- `frontend/` – Public-facing site and basic interfaces (HTML, CSS, JS).
- `backend/` – PHP MVC backend, database configuration, controllers, and API endpoints.

Developer-oriented details and internal conventions are available in `.github/copilot-instructions.md`.

