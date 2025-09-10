# Connect HR Constitution

## Core Principles

### I. Compliance-First Architecture (NON-NEGOTIABLE)
All payroll calculations, tax mappings, and statutory reporting must comply with South African Revenue Service (SARS), UIF, and SDL regulations. Every feature must validate against official SARS BRS and UIF E03 specifications before implementation. No hard-coded tax rules - all compliance logic must be configurable via lookup tables to accommodate regulatory changes.

### II. Multi-Tenant Isolation
Complete data segregation between companies is mandatory. Every database query, API endpoint, and service must enforce tenant context. Company A data must never be accessible to Company B users under any circumstance. All models must implement `HasTenant` trait with global scopes.

### III. Specification-Driven Development (NON-NEGOTIABLE)
Every feature must have a complete specification before any code is written. Specifications must include: user scenarios, functional requirements, business rules, compliance mappings, and acceptance criteria. All exports must be validated against official SARS/UIF contract specifications.

### IV. Audit Trail Completeness
Full audit logging is required for all data changes, especially payroll calculations, leave adjustments, and compliance exports. Every transaction must be traceable with user attribution, timestamps, and change history. POPIA compliance mandatory for all personal data handling.

### V. Test-First Development
TDD is mandatory for all payroll calculations, tax computations, and compliance exports. Tests must validate against known SARS scenarios and edge cases. Integration tests required for multi-tenant isolation, leave accrual calculations, and statutory report generation.

### VI. Modular Service Architecture
Payroll, Leave, SARS, and UIF services must remain independent and configurable. No cross-service dependencies that would prevent individual testing or regulatory updates. Each service exposes clear interfaces and maintains its own data integrity.

## Compliance Requirements

### SARS Code Mappings (Critical)
- Paid leave → **3601** (basic income) or **3615** (if split reporting required)
- Leave encashment/termination payout → **3605** (lump sum payments)
- Unpaid leave reduces 3601 base but creates no separate code line
- ETI calculations must exclude leave hours but include leave remuneration
- UIF and SDL must include paid leave in contribution bases

### Leave Management Rules
- Annual leave: 21 days/year pro-rata from start date, maximum 21-day carryover
- Sick leave: 30 days per 3-year cycle, medical certificate required >2 days
- Family responsibility: 3 days/year, unpaid after entitlement exhausted
- Leave balances cannot go negative; accrual stops for terminated employees
- Replacement employees accrue pro-rata from effective start date

### Payroll Calculation Standards
- Daily rates calculated to cents precision with SARS-compliant rounding
- UIF: 1% employee + 1% employer contributions with legislative ceiling
- SDL: 1% on total remuneration including paid leave
- PAYE calculated using current SARS tax tables with rebates
- ETI thresholds and adjustments applied according to current legislation

## Technical Standards

### Laravel Architecture Requirements
- Framework: Laravel 12 with PHP 8.3+
- Database: PostgreSQL 15+ with tenant partitioning
- Frontend: Blade templates with Tailwind CSS and Alpine.js/Livewire
- Queuing: Redis-backed Laravel Queue for payroll processing
- Authentication: Laravel Sanctum for mobile API access

### Performance & Scalability
- Page load times <3 seconds
- Bulk operations (1000+ records) complete in <30 seconds  
- 99.5% uptime during business hours
- Support 500 concurrent users per tenant
- 10,000+ learners per tenant capacity

### Security & Data Protection
- AES-256 encryption at rest for sensitive payroll data
- TLS encryption in transit for all communications
- Multi-factor authentication mandatory for administrative users
- Role-based access control with principle of least privilege
- POPIA compliance for all personal information processing

## Development Workflow

### Feature Development Process
1. Create specification using `/specify` command and template
2. Validate business rules and compliance requirements
3. Write comprehensive tests covering all scenarios
4. Implement feature with full tenant isolation
5. Validate against SARS/UIF specifications
6. Conduct compliance review before merge

### Quality Gates
- All payroll calculations must pass SARS validation tests
- Multi-tenant isolation verified for every data access point  
- Audit trail completeness validated for all transactions
- Performance benchmarks met for bulk operations
- Security review completed for sensitive data handling

### Export Validation Requirements
- EMP201 monthly returns must validate against e@syFile format
- EMP501 annual reconciliation must match IRP5 certificate totals
- UIF declarations must use correct reason codes for leave types
- All exports validated against official SARS BRS specifications
- Test exports generated and verified before production deployment

## Governance

### Constitution Authority
This constitution supersedes all other development practices and guidelines. All code reviews, feature approvals, and deployment decisions must verify compliance with these principles. Any deviation requires documented justification and constitutional amendment.

### Compliance Oversight
Monthly reviews of SARS/UIF regulatory changes with immediate lookup table updates as required. All compliance-related changes must be reviewed by domain experts familiar with South African payroll legislation.

### Amendment Process
Constitutional changes require:
1. Documented business justification
2. Impact analysis on existing compliance features
3. Migration plan for affected systems
4. Stakeholder approval from legal/compliance team

**Version**: 1.0.0 | **Ratified**: 2025-09-09 | **Last Amended**: 2025-09-09