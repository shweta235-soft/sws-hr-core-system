# 🎯 SWS HR Core Enterprise System

A high-performance, production-ready **22-Layer HRMS Architecture** engineered using the Laravel REST API ecosystem and a strict dependency-ordered MySQL relational database storage engine. This system provides a single source of truth accessible across **Web UI portals, Windows Desktop tracking clients, and Mobile applications** via secure stateful bearer tokens.

---

## ⚡ Core Acceptance Workflow Flowchart

---

## 🏗️ System Architecture & Data Layer Sequence

To guarantee absolute database structural integrity and prevent relational foreign-key collision constraints, the system compiles schemas in the following strict dependency sequence layout:

1. **Foundational Authentication Layer:** `users`, Sanctum Access Tokens
2. **Access Control (RBAC):** Dynamic security role mapping matrix (`Admin`, `HR`, `Accounts`, `Manager`, `Employee`)
3. **Organizational Taxonomy:** Teams allocation, self-referencing hierarchical supervisor reporting links (`manager_id`)
4. **Shift Operations Engine:** Multi-device attendance state logs, automated Gross vs Net hours tracking, micro-activity break increments
5. **Project Tracking Core:** Desktop widget background synchronized timer channels mapping task execution logs
6. **Financial Settle & Compliance Gate:** Cross-module validation calculating dynamic late-arrival deductions, draft ledgers, and automated payment closures generating real-time downloadable PDF payslips

---

## 🛡️ Enterprise Security Blueprint (P0 / P1 Tests Passed)

- 🔑 **Token-State Isolation:** Standardized on `Laravel Sanctum` stateful bearer tokens.
- 🕒 **Automated Session Expire:** Enforced global token invalidation timeout locked tightly at **15 minutes** for maximum machine terminal safety.
- 🛑 **Inactive Employee Lock:** Instant access cancellation gate checks individual database profile state active flags during authentication.
- 🚷 **Horizontal Intrusion Guard:** Strict workspace parameter validation enforces query execution ownership blocks (`where('user_id', auth_id)`), preventing employees from altering or reading peer records.

---

## 🔌 Core API Unified Endpoints Matrix

### 🟢 Public Channels (No Token Mandatory)

- `POST /api/register` - Employee registration entry page setting individual secure access credentials.
- `POST /api/login` - Core authorization engine returning unique state tokens.

### 🔒 Protected System Workspace (Bearer Token Authorization Header Required)

- `POST /api/attendance/check-in` - Shift clocking initialization tracking real-time latency statuses.
- `POST /api/attendance/break-start` - Registers micro-break interval start markers; features double-trigger blockers.
- `POST /api/attendance/break-end` - Ends break intervals, calculates time deltas, and logs break time accumulation.
- `POST /api/attendance/check-out` - Concludes shifts, processes gross-vs-net mathematical totals, and locks tracking inputs.
- `POST /api/tasks/{id}/sync-timer` - Background connection gateway matching synced seconds from Windows task clients.
- `POST /api/payroll/generate` - _(Admin/HR Exclusive)_ Processes work cycle entries to compile statement drafts.
- `POST /api/payroll/{id}/pay` - _(Admin/Accounts Exclusive)_ Releases bank transfers and locks downstream document states.
- `GET /download-payslip/{id}` - Publicly visible secure route generating dynamic, corporate-formatted printable PDF payslips.

---

## 🚀 Environment Initialization & Boot Steps

Follow these sequential parameters to spin up the integrated environment locally:

### 1. Clone & Set Environment Context

```bash
git clone https://github.com
cd sws-hr-core-system
cp .env.example .env
```

### 2. Match Database Config Envelopes (`.env`)

Ensure your MySQL environment properties align with the application engine boundaries:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sws_hr
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Deploy Workspace Dependencies & Seeding

```bash
composer install
php artisan key:generate
php artisan config:clear
php artisan migrate:fresh --seed
```

### 4. Fire Up the Server Engine Core

```bash
php artisan serve
```

Open **`http://localhost:8000`** inside your preferred browser. Access the centralized deployment console using `admin@hrms.com` / `password123` to observe live data streams dynamically.
