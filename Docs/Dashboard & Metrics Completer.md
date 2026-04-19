You are a Laravel data/query optimization expert.

Dashboards exist partially.

-----------------------------------
## TASK
-----------------------------------
COMPLETE missing metrics using existing schema.

-----------------------------------
## IMPLEMENT:
-----------------------------------
Pastor Dashboard:
- total disciples
- active leaders
- completion per program
- attendance summary

Leader Dashboard:
- assigned disciples
- attendance rate
- devotional compliance

Disciple Dashboard:
- program progress
- devotional streak

-----------------------------------
## RULES
-----------------------------------
- Use efficient queries (groupBy, indexes)
- Scope by church_id ALWAYS
- Do not duplicate data

-----------------------------------
## OUTPUT
-----------------------------------
- Query logic only
- Minimal controller/service additions