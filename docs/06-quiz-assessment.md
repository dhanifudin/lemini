# Quiz Assessment Enhancement Plan

This plan expands the student practice experience by layering a structured quiz workflow on top of the adaptive engine. The aim is to capture formative assessment data, provide real-time insight into mastery, and loop teachers into review when needed.

---

## Goals
- Offer curated quiz sessions that draw from adaptive recommendations while respecting pacing.
- Provide immediate, student-friendly feedback with clear remediation paths.
- Surface quiz outcomes to teachers for moderation and targeted support.

---

## Functional Enhancements

### 1. Quiz Session Builder
- Generate 5–10 question quizzes pulled from `AdaptivePracticeEngine` recommendations (mix of high-priority objectives).
- Allow students to select focus objectives or let the engine choose.
- Persist session metadata: assigned items, start/end time, variant used.

### 2. In-Quiz Experience
- Timer support (optional per quiz) with progress indicator.
- Question navigation (prev/next, flag for review).
- Autosave responses to avoid data loss.

### 3. Feedback & Results
- Instant feedback for each question after submission:
  - Correct/incorrect indicator.
  - Explanation/rationale (pull from item metadata).
  - Suggested follow-up practice if incorrect.
- Summary page highlighting objectives, a mastery delta estimate, and reflection prompt.

### 4. Teacher Oversight
- Log quiz sessions as adaptive events (`event_type = quiz_submitted`) with score deltas.
- Notify teachers when students repeatedly miss the same objective (>2 quizzes).
- Integrate into Teacher Command Center queue with quick review notes.

---

## Technical Tasks
1. **Backend**
   - New `quiz_sessions` table with child `quiz_session_items` storing user responses, score, timestamps.
   - Extend adaptive engine to support “bundled” recommendation requests.
   - Services to evaluate quiz results, update mastery score, and generate follow-up recommendations.
2. **Frontend**
   - Student UI: quiz launcher modal, quiz player (question card layout), results view.
   - State management (Pinia) for in-progress quizzes, autosave, submission.
3. **Telemetry & Experiments**
   - Experiment assignments for quiz flow variants (e.g., immediate vs delayed feedback).
   - Event logging for question-level actions (flagged, skipped, revisited).
4. **Teacher Dashboard**
   - Widget summarizing recent quiz outcomes by objective.
   - Detailed quiz session view linked to student record.

---

## Integration Considerations
- Mastery updates must remain idempotent; leverage `MasteryScoringService`.
- Ensure quiz mode respects existing practice queue (no duplication of recently attempted items within a window).
- Prepare seed data (Biology demo) with quiz-ready items (e.g., mark certain items as “quiz eligible”).

---

## Rollout Steps
1. Build storage schema + API endpoints.
2. Implement student quiz UI with controlled release (feature flag per class).
3. Hook in teacher dashboards + notifications.
4. Capture telemetry and run A/B test to tune quiz lengths and feedback timing.
5. Document workflow for students and teachers; update onboarding guides.
