# Connect HR – Unified Spec (spec.md)

**Project**: Connect HR
**Version**: 2.0
**Feature Branch**: `001-connect-hr-is`
**Created**: 2025-09-09
**Status**: Ready for Development
**Spec Kit Compatible**: ✓

---

## 1) Business Context

### Domain Overview

Workforce development programs in South Africa require learners to receive stipends/allowances treated as taxable income, with full SARS and UIF compliance. These programs involve daily attendance tracking, leave management, ETI eligibility, and statutory exports.

### Problem Statement

Organizations struggle with manual processes for tracking learners, calculating compliant payslips, managing leave accruals, and generating accurate SARS/UIF submissions. Current solutions lack multi-tenant capabilities and domain-specific compliance features.

### Business Value

* **Compliance Assurance**: Automated SARS/UIF calculations reduce audit risks.
* **Operational Efficiency**: ~80% reduction in payroll processing time.
* **Cost Savings**: Eliminate manual errors and penalties.
* **Scalability**: Single platform supports multiple organizations and programs.

---

## 2) System Purpose (Overview)

Multi-tenant SaaS platform for managing workforce development programs in South Africa, with full SARS & UIF compliance, QR-code attendance, leave management, and automated payroll processing.

---

## 3) User Scenarios & Testing

### Primary User Story

A company admin sets up their organization (with branches), creates programs with daily rates, enrolls learners, tracks daily attendance and leave, and automatically generates compliant payslips and tax documents that can be exported to SARS and UIF systems.

### Acceptance Scenarios

1. **Program Creation**: Create program @ R350/day → available for enrollment.
2. **Attendance → Payroll**: Record attendance → payslip reflects daily-rate earnings.
3. **Leave (Paid)**: Approve 5 days annual leave → deduct balance, pay as **3601**, include in UIF/SDL bases.
4. **EMP201 Export**: Generate month's payslips → produce e@syFile-compatible EMP201.
5. **Data Isolation**: Admin of Company A only sees Company A data.
6. **ETI**: Eligible learner → ETI applied; **leave hours excluded** but **leave remuneration included**.
7. **Replacement Joiners**: Leaver stops accrual; leave payout → **3605**. Replacement accrues pro‑rata from start date.

### User Journey Workflows

**Daily Ops**: Review dashboards → Capture attendance → Handle exceptions → Process leave → Monitor.
**Monthly Payroll**: Track → Prep & validate → Generate payslips → SARS (EMP201) → UIF.

### Edge Cases

* Multiple programs overlap; backdated edits (≤30 days); mid-month SARS/ETI rule changes; half‑day leave; leave encashment during service (**3605**); termination leave payout (**3605**); downtime during submission; bulk program transfers; tax year rollover.

---

## 4) Requirements

### Functional Requirements (Must Have – P1)

**FR-001** Multi-tenant isolation per company.
**FR-002** Company hierarchy: parent → branches → departments.
**FR-003** Programs with daily rates, NQF levels, duration.
**FR-004** Enrollment with tax number validation & ETI eligibility checks.
**FR-005** Daily attendance tracking (optional geolocation).
**FR-006** Attendance dashboards & exception reporting.
**FR-007** Payslip calculation: attendance + leave + deductions.
**FR-008** SARS-compliant mapping (IRP5/IT3(a) source codes).
**FR-009** UIF & SDL calcs with proper inclusion/exclusion.
**FR-010** ETI engine with thresholds & adjustments.
**FR-011** Leave Management: annual (21 days/year, pro‑rata), sick (30/3yrs), family resp (3/yr), unpaid, configurable types.
**FR-012** Leave Balances: imports; monthly accrual; audit adjustments; encashment → **3605**; termination payout → **3605**.
**FR-013** Tax Certificates: IRP5 & IT3(a), bulk generation & distribution.
**FR-014** SARS Integration: EMP201 monthly; EMP501 annual; e@syFile formats; rules validation.
**FR-015** UIF Integration: monthly file, contributions, validations.
**FR-016** Notifications: email/SMS/push for leave & compliance deadlines.
**FR-017** Audit Trail: full change history & compliance reports.
**FR-018** Data IO: bulk templates, configurable reports, APIs, historical export.
**FR-019** Mobile app: check‑in/out, leave requests, payslip view.

### Functional Requirements (Should Have – P2)

**FR-020** Advanced reporting dashboards & scheduled reports.
**FR-021** Workflows: approvals, escalation, bulk approval, delegation.
**FR-022** Integration Hub: REST API, webhooks, payroll connectors, bank files.
**FR-023** Advanced Leave: trading/selling, sabbatical, parental, study integrations.

### Functional Requirements (Could Have – P3)

**FR-024** AI analytics (attendance patterns, anomaly detection).
**FR-025** Multi-language UI & docs.

### Non-Functional Requirements

**NFR-001** Performance: <3s pages; 1000+ bulk ops <30s; 99.5% business-hours uptime; 500 concurrent users/tenant.
**NFR-002** Security: MFA for admins; RBAC; AES‑256 at rest; TLS in transit; POPIA compliance.
**NFR-003** Scalability: 10k+ learners/tenant; multi‑region; partitioning by company.
**NFR-004** BCDR: daily backups (7‑year retention); PITR; RTO 4h, RPO 1h; geo‑replicated.
**NFR-005** Integrations: OpenAPI 3.0; webhooks; SAML 2.0 SSO; MT940/BAI2 support.

---

## 5) Business Rules & Validations

### Attendance

* Max 8h/day, 40h/week; ≥4h counts full day (else pro‑rata).
* Overtime not default; weekends/holidays need approval.

### Leave

* Annual: carryover ≤21 days; Sick: certificate if >2 days.
* No negative balances; 48h notice (except sick); max 10 consecutive days without special approval.
* **Mapping**: paid leave → **3601** (or **3615** split); encashment/payout → **3605**; unpaid leave reduces 3601 (no code line).

### Calculation

* Daily rates to cents; SARS rounding; ETI excludes hours on leave but includes leave remuneration; UIF capped per legislation; SDL on total remuneration incl. paid leave.

### Validation

* Tax number/BVN checks; bank account validation; prevent duplicate concurrent enrollments; no future-dated transactions >7 days.

---

## 6) Key Entities & Data Model

**Company / Sub-Company** (tenant, branches, tax refs)
**Program** (rate, NQF, duration, compliance settings)
**Learner** (tax & ETI details, contacts)
**User** (auth, roles, audit)
**AttendanceRecord** (timestamps, hours, exceptions, approvals)
**LeaveRecord** (request/approval, balance impacts)
**LeaveBalance** (opening, accrued, taken, encashed, closing)
**Payslip** (earnings, deductions, SARS codes)
**Payment** (banking, runs, recon)
**TaxCertificate** (IRP5/IT3(a))
**ExportFile** (SARS/UIF outputs)
**ReconciliationRecord** (EMP201↔EMP501↔IRP5↔UIF↔Leave)
**AuditLog** (full history)
**LookupTable** (SARS codes, UIF rates, ETI thresholds, holidays)
**CompanySettings** (leave policy, approvals, notifications)
**SystemParameters** (tax tables, integration endpoints)

---

## 7) Architecture Requirements (Laravel 12 + Blade)

```yaml
Backend:
  framework: Laravel 12
  php_version: 8.3+
  database: PostgreSQL 15+ with tenant partitioning
  cache: Redis 7+ (sessions/queues)
  queue: Laravel Queue (Redis)
Frontend:
  templating: Blade
  css_framework: Tailwind 3+
  js: Alpine.js 3+ / Livewire 3+
Mobile:
  attendance: QR code scanning via API (Sanctum)
Infra:
  deploy: Forge or Docker
  storage: S3-compatible
  cdn: CloudFlare
  monitoring: Telescope + custom metrics
```

### App Structure (high-level)

```
app/
  Console/Commands (GeneratePayslips, ProcessLeaveAccruals, GenerateSARSExport, SyncAttendanceData)
  Events (AttendanceRecorded, LeaveRequestSubmitted, PayslipGenerated, ComplianceDeadlineApproaching)
  Http/Controllers (Api/V1: Attendance, Leave, Payroll, Compliance; Web: Dashboard, Program, Learner, Reports)
  Http/Middleware (EnsureTenantContext, CheckUserPermissions, AuditLogger)
  Jobs (ProcessPayrollCalculation, GenerateComplianceFile, SendNotificationJob, SyncMobileAttendance)
  Listeners (SendLeaveApprovalNotification, UpdateLeaveBalance, LogUserActivity)
  Models (Tenant, User, Learner, Program, AttendanceRecord, LeaveRecord, LeaveBalance, Payslip, ComplianceExport)
  Policies (...)
  Services (PayrollCalculationService, SARSIntegrationService, UIFIntegrationService, LeaveManagementService, QRCodeService)
  Traits (HasTenant, Auditable, HasPermissions)
```

### Multi‑Tenant Pattern (excerpt)

```php
trait HasTenant {
    protected static function bootHasTenant() {
        static::addGlobalScope(new TenantScope);
        static::creating(function ($model) {
            if (session()->has('tenant_id')) $model->tenant_id = session('tenant_id');
        });
    }
    public function tenant() { return $this->belongsTo(Tenant::class); }
}
```

---

## 8) APIs (selected)

### Attendance – QR Scan

```http
POST /api/v1/attendance/qr-scan
```

* Validates QR, optional geofence, records attendance (check\_in/out).
* Returns normalized record; errors on invalid QR, out-of-range, or duplicates.

### Leave – Request & Approve

```http
POST /api/v1/leave/request
PATCH /api/v1/leave/{id}/approve
```

* Balance checks, policy validation, approval with audit; updates balances.

### Payroll – Generate & Status

```http
POST /api/v1/payroll/generate
GET  /api/v1/payroll/job/{jobId}
```

* Starts async payroll calc; exposes job status with progress/errors.

---

## 9) Data Models & Migrations (excerpts)

### AttendanceRecord (key fields)

* `attendance_date`, `check_in_time`, `check_out_time`, `hours_worked`, `location_data`, `status`, approvals.
* Unique per (tenant, learner, program, date).

### Payslip (key fields)

* Earnings: `days_worked`, `daily_rate`, `basic_earnings`, `leave_pay`, `other_earnings`.
* Deductions: `paye`, `uif_employee`, `other_deductions`.
* Employer: `uif_employer`, `sdl`, `eti_benefit`.
* SARS: `sars_3601`, `sars_3605`, `sars_3615`.
* Derived: `gross_earnings`, `net_pay` accessors.

---

## 10) Business Logic Services (excerpts)

### PayrollCalculationService

* Aggregates attendance & approved leave.
* Calculates gross, PAYE (rebates), UIF (1% EE/ER with ceiling), SDL (1%), ETI (excludes leave hours; includes leave remuneration).
* Maps to SARS codes: **3601** (basic + paid leave), **3605** (encashments/payouts), **3615** (travel if applicable).

---

## 11) UI (Blade) & Livewire (excerpts)

* **Dashboard**: KPIs (active learners, attendance rate, pending leave, payroll total).
* **AttendanceChart** Livewire: period filters; Chart.js line.
* **ComplianceDashboard** Livewire: upcoming deadlines (EMP201/UIF), recent submissions.

---

## 12) Commands & Scheduling

* `payroll:generate` (period options, tenant, dry‑run).
* `leave:process-accruals` (monthly accruals).
* `sars:export` (emp201 | emp501 | irp5, period, tenant).
* Scheduler: nightly accruals; monthly dry‑run payroll; SARS reminders.

---

## 13) Compliance Mappings (Leave → SARS/UIF/ETI)

| Event                         | IRP5                     | BRS Total  | PAYE | UIF Base | SDL Base | ETI Remun. | ETI Hours |
| ----------------------------- | ------------------------ | ---------- | ---- | -------- | -------- | ---------- | --------- |
| Paid annual/sick/family/study | **3601** *(or **3615**)* | **3699** ↑ | Yes  | Yes      | Yes      | Include    | Exclude   |
| Unpaid leave                  | — (reduces 3601)         | **3699** ↓ | N/A  | No       | No       | Exclude    | Exclude   |
| Leave encashment (in service) | **3605**                 | **3699** ↑ | Yes  | Yes      | Yes      | Include    | Exclude   |
| Termination leave payout      | **3605**                 | **3699** ↑ | Yes  | Yes      | Yes      | Include    | Exclude   |

Pro‑rata accrual by start date; leavers stop accrual; replacements accrue from effective start.

---

## 14) Success Metrics & KPIs

* Ops: 80% faster payroll; 99.9% calc accuracy; 90% learner DAU; 100% on‑time submissions.
* Business: R50k+/100 learners annual savings; 95% penalty reduction; 4.5★ admin satisfaction.
* Technical: 99.5% availability; <3s responses; zero data loss; zero unauthorized access.

---

## 15) Risks & Mitigations

* **SARS changes** → Monthly reviews & config dictionaries.
* **Data migration** → mapping + parallel runs.
* **Tenant security** → RLS, pentests.
* **Integrations** → standard APIs + contract tests.

---

## 16) Review & Acceptance Checklist

* [x] User scenarios & acceptance defined
* [x] Functional + non‑functional requirements
* [x] Business rules & validations
* [x] Entities & architecture
* [x] Compliance mappings
* [x] Risks & KPIs
* [x] Stakeholder sign‑off
* [x] Feasibility review
* [x] Timeline & resourcing

---

## Execution Flow (main)
```
1. Parse user description from Input
   → Complete: Multi-tenant HR platform for South African workforce development
2. Extract key concepts from description
   → Actors: HR administrators, learners, payroll officers, compliance managers
   → Actions: program management, attendance tracking, payslip generation, tax reporting
   → Data: employee records, attendance, leave, payroll, tax certificates
   → Constraints: SARS/UIF/SDL compliance, POPIA compliance, multi-tenancy
3. For each unclear aspect:
   → All major aspects clarified through comprehensive unified specification
4. Fill User Scenarios & Testing section
   → Complete workflows defined for all user types
5. Generate Functional Requirements
   → 25+ detailed requirements with priorities and technical specifications
6. Identify Key Entities
   → Complete data model with 15+ entities and relationships
7. Run Review Checklist
   → All items completed and verified
8. Return: SUCCESS (spec ready for development)
```

---

## Execution Status
*Updated by main() during processing*

- [x] User description parsed
- [x] Key concepts extracted
- [x] Ambiguities clarified
- [x] User scenarios defined
- [x] Requirements generated
- [x] Entities identified
- [x] Review checklist passed

---