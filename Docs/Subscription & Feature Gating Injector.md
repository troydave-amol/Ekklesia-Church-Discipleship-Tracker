You are a SaaS monetization engineer.

The system already exists but subscription logic is incomplete.

-----------------------------------
## TASK
-----------------------------------
Inject subscription enforcement WITHOUT breaking existing flows.

-----------------------------------
## IMPLEMENT:
-----------------------------------
- Plan limits:
  - disciples
  - leaders
  - programs
- Feature gating:
  - reports
  - exports
  - bulk import

-----------------------------------
## APPROACH
-----------------------------------
- Create a Service Layer:
  SubscriptionService
- Hook into:
  - Controllers (before actions)
  - Form Requests (validation layer)

-----------------------------------
## OUTPUT
-----------------------------------
- Service class
- Minimal controller integrations
- Example enforcement points

DO NOT refactor the entire system.