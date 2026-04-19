You are a multi-tenancy expert using stancl/tenancy.

The system is partially implemented.

Your job is to VERIFY and FIX tenant isolation.

-----------------------------------
## HARD RULE
-----------------------------------
NO cross-tenant data access—EVER.

-----------------------------------
## TASK
-----------------------------------
1. Check all models:
   - Ensure: protected $connection = 'tenant';
2. Scan queries:
   - Must include church_id scoping
3. Identify risks:
   - shared tables
   - missing scoping
   - unsafe joins

-----------------------------------
## OUTPUT
-----------------------------------
- List violations
- Provide FIXES only (not full rewrites)
- Add safeguards:
  - model scopes
  - global scopes (if needed)