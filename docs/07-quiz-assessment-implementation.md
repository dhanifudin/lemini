# Quiz Assessment Implementation Plan

This plan turns the high-level roadmap (docs/06-quiz-assessment.md) into incremental engineering work. It is structured as four phases, each with back-end, front-end, and integration tasks scoped for sprints.

---

## Phase 1 — Data Model & API Foundations

### Back-end
- Create `quiz_sessions` table (user_id, experiment_variant, status, started_at, submitted_at).
- Create `quiz_session_items` table (session_id, item_id, position, response, score, feedback_payload).
- Add `is_quiz_eligible` flag to items (nullable boolean default true).
- Extend `AdaptivePracticeEngine` with a method to fetch a bundled set given a desired size and objective filter.
- Add API endpoints:
  - `POST /api/quizzes` to start a session (select objectives, size, optional timer flag).
  - `POST /api/quizzes/{session}/responses` to autosave question responses.
  - `POST /api/quizzes/{session}/submit` to finalise.

### Front-end
- Pinia module `useQuizStore` to manage session state (items, answers, timer).
- Initial UI: quiz launcher modal (objective selection, number of questions).
- Basic quiz player layout with question stem, options, navigation, autosave hook.

### Integration
- Seed: mark a subset of demo items as quiz eligible, ensure each objective has >5 questions.
- Logging: record `AdaptiveRecommendationEvent` with event_type `quiz_started`.

---

## Phase 2 — Evaluation & Feedback Experience

### Back-end
- Implement quiz evaluator service:
  - Grade multiple-choice automatically (score = 100 or 0).
  - For SAQ, compute placeholder scoring (e.g., require teacher review, mark pending).
  - Invoke `MasteryScoringService` to adjust mastery score based on aggregate quiz result.
  - Surface per-question explanations (answer + rationale) for the score view payload.
- Store item-level feedback payload (correctness, rationale, follow-up recommendations).
- Emit `quiz_submitted` adaptive event with score delta.

### Front-end
- Quiz player:
  - Timer countdown (optional).
  - Flag for review, progress indicator.
  - Submission redirects to a score view that highlights correct/incorrect answers and explanations.
- Results view:
  - Per-question feedback (correct/incorrect, explanation/rationale, correct answer).
  - Summary cards (overall score, mastery delta, suggested practice items with links).
  - Reflection textarea feeding existing reflection endpoint (`student.api.feedback.reflect`).

### Integration
- Update teacher command center summary to include “quizzes submitted (7 days)”.
- Add new reminder type “Follow up on quiz performance” (stub using existing reminder controller).

---

## Phase 3 — Teacher Oversight & Moderation

### Back-end
- Create `quiz_reviews` table (quiz_session_id, teacher_id, status, notes).
- Extend API for teachers:
  - `GET /teacher/api/quizzes` list recent sessions with filters by objective, status.
  - `POST /teacher/api/quizzes/{session}/review` accept/update review notes, resolve pending SAQ.
- Ensure SAQ items set to `requires_review` status until teacher confirmation.

### Front-end
- Teacher dashboard widget summarising quiz outcomes by objective.
- Detailed quiz session drawer:
  - Student metadata, score, mastery delta.
  - Item-level responses with teacher note fields for SAQ.
- Badge/notification when student fails same objective in >=2 quizzes (pull from analytics).

### Integration
- Adaptive events: log `quiz_reviewed`.
- Notifications (deferred implementation): queue stub for teacher follow-up emails.

---

## Phase 4 — Experiments & Telemetry

### Back-end
- Experiment variants:
  - A: immediate per-question feedback.
  - B: delayed feedback (summary-only).
- Update `AdaptiveExperimentAssignment` to track quiz-specific variant.
- Instrument event logging for:
  - `quiz_question_flagged`
  - `quiz_question_skipped`
  - `quiz_time_expired`
  - `quiz_feedback_viewed`
- Aggregate nightly job to compute quiz performance metrics (avg score per objective, improvement after remedial practice).

### Front-end
- Respect assigned variant (toggle immediate feedback UI).
- Telemetry hooks: send events on flag, skip, time expiry, final feedback view.

### Integration
- Add analytics dashboard tiles for quiz metrics (Filament widgets).
- Define cohort export (CSV) for researchers (objective, score, variant, completion time).

---

## Rollout Checklist
1. Run migrations and update seeds (Phase 1).
2. Release quiz launcher/quiz player behind feature flag for pilot classes.
3. Turn on evaluation + results page (Phase 2) when QA passes.
4. Enable teacher oversight flows (Phase 3).
5. Roll out experiments/telemetry (Phase 4) once baseline experience stabilises.
6. Document workflows and update help guides for students/teachers.
