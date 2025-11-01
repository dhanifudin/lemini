# Remaining Feature Implementation Plan

This document breaks the roadmap into actionable epics, with the first set of stories sized for upcoming sprints. Each epic references the original roadmap section and outlines milestones, core tasks, and dependencies.

---

## Epic T1: Teacher Command Center (Roadmap §1)

**Milestone T1.1 — Foundations**
- Introduce `Role::TEACHER` guardrails and seed demo teachers.
- Craft Filament reporting queries for attempt volume, average mastery, outstanding feedback counts.
- Build API endpoints powering the teacher dashboard widgets.

**Milestone T1.2 — Feedback Review Queue**
- Design teacher dashboard Inertia page with cards + charts.
- Implement moderation queue UI: list pending feedback with quick approve/request-revision actions.
- Persist audit events (who approved, status transitions) via dedicated model/table.

**Milestone T1.3 — Communication Panel**
- Add “nudge” workflow: teachers select students lacking reflections, send templated reminder (email/notification stub).
- Log reminder history in audit trail.
- UX polish + accessibility pass.

Dependencies: student hub APIs, feedback reflections.

---

## Epic O1: Learning Objective Builder (Roadmap §2)

**Milestone O1.1 — Objective CRUD**
- New `learning_objectives` table/model with code, description, standards metadata.
- Filament resource for admin CRUD, including soft deletes and version tags.
- Enforce objective association on items (database constraint + model layer validation).

**Milestone O1.2 — Alignment UX**
- Build alignment UI (Filament relation manager or bespoke page) to connect objectives ↔ rubrics/items via drag-and-drop or selection lists.
- Surface warnings on items/rubrics when objectives change.
- Export aligned data as CSV for audits.

**Milestone O1.3 — Versioning & Notifications**
- Record change history (objective revisions) with diff view.
- Notify owners when objectives tied to active items/rubrics change.

Dependencies: existing item/rubric models; teacher dashboards consume aligned data.

---

## Epic A1: Adaptive Practice Engine (Roadmap §3)

**Milestone A1.1 — Scoring Model**
- Define mastery scoring formula (weights for score, recency, reflections).
- Batch job or observer to recompute scores per attempt/feedback.
- Persist normalized mastery score per objective/student.

**Milestone A1.2 — Recommendation Engine**
- Service that assembles practice set candidates using mastery scores + item metadata.
- Explainability payload (why each recommendation surfaced).
- Feature flag to toggle engine for selected users.

**Milestone A1.3 — Experimentation Harness**
- A/B testing scaffolding (assignment buckets, result logging).
- Telemetry collection (completion rate, score delta, time on task).
- Dashboard card summarizing experiment outcomes.

Dependencies: mastery data, objective alignment, analytics pipeline.

---

## Epic P1: Parent & Guardian Portal (Roadmap §4)

**Milestone P1.1 — Access & Linking**
- Guardian user model + invitation flow.
- Role-based middleware ensuring guardians view only linked students.
- Admin UI to manage guardian-student relationships.

**Milestone P1.2 — Portal Experience**
- Guardian dashboard summarizing mastery trends, feedback acknowledgments, next steps.
- Weekly digest template (blade/Inertia) reusable for email + PDF download.
- Resource library page with filters per objective/topic.

**Milestone P1.3 — Compliance & Logging**
- Audit logs for guardian access.
- Consent management (record when guardians accept terms).
- Privacy review checklist.

Dependencies: mastery/feedback APIs, resources metadata.

---

## Epic N1: Analytics & Insights Layer (Roadmap §5)

**Milestone N1.1 — Data Modeling**
- Define event schema (attempt, feedback, reflection, recommendation) and implement write hooks.
- Configure queue/cron job to persist daily aggregates.

**Milestone N1.2 — Dashboards**
- Filament dashboard widgets for mastery growth, rubric hotspots.
- Optional integration with external BI (embed mode or data export API).

**Milestone N1.3 — Alerting**
- Threshold configuration UI (per metric/role).
- Notification pipeline (email/webhook) for anomalies (e.g., drop in acknowledgments).

Dependencies: instrumentation across other epics; may use same audit tables.

---

## Epic I1: Infrastructure & QA Hardening (Roadmap §6)

**Milestone I1.1 — Testing Baseline**
- Set up Pest/PHPUnit for backend models/controllers; seed with fixture data.
- Add Playwright or Vitest for critical front-end flows (student dashboard, feedback submission).
- Enforce coverage thresholds in CI.

**Milestone I1.2 — CI/CD Pipeline**
- GitHub Actions (or preferred CI) running tests, static analysis, lint.
- Deploy script for staging with database migrations + seeds + health check ping.
- Smoke tests post-deploy.

**Milestone I1.3 — Security & Ops**
- Password policy enforcement, rate limiting, audit logging.
- Automated dependency updates (Dependabot) with security review workflow.
- Data retention policy + anonymized staging refresh job.

Dependencies: none; runs in parallel with epics above.

---

## Suggested Sequencing
1. Kick off Epic T1 & I1 in parallel (teacher center + infra baseline).
2. Start Epic O1 once objective schemas stabilized; feed data to adaptive engine later.
3. Bring in Epic N1 telemetry as soon as key events exist; ensures data for adaptive engine and dashboards.
4. Prioritize A1 after O1 foundations; run experiments with seeded data first.
5. Launch P1 after core teacher/student workflows stabilize.
