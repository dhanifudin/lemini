# Biology Test Seeder Plan

This document outlines the plan for generating seed data to support local testing of the learning platform with a focus on Biology content. The seeder will populate the database with coherent records across Users, Rubrics, Items, Attempts, Feedback, Mastery, and Recommendation entities so that Filament resources and domain workflows can be exercised end to end.

## 1. Seeder Structure

1. Publish a dedicated seeder class `BiologyDemoSeeder`.
2. Register the seeder within `DatabaseSeeder` behind a local/testing guard to prevent accidental production execution.
3. Group model creation into helper methods for clarity:
   - `seedUsers()`
   - `seedRubrics()`
   - `seedItems()`
   - `seedAttemptsAndFeedback()`
   - `seedMasteryAndRecommendations()`

## 2. Domain Assumptions

- Biology is the primary subject; align objectives and descriptions accordingly.
- Roles:
  - 1 admin (e.g., department head).
  - 2 teachers (biology instructors).
  - 5 students.
- Rubrics target biology lab reports, quiz assessments, and short-answer conceptual questions.
- Items cover topics such as cellular respiration, genetics, and human anatomy.

## 3. Data Generation Details

### 3.1 Users
- Admin user with `role = admin`, verified email, and secure password.
- Teacher users with `role = teacher`, verified emails, distinct names.
- Student users with `role = student`, unique emails, optional verification timestamps.

### 3.2 Rubrics
- Lab report rubric with criteria for hypothesis, methodology, data analysis, conclusion.
- Quiz rubric with multiple-choice grading levels.
- Short-answer rubric focused on clarity, accuracy, and depth.

### 3.3 Items
- Multiple-choice question on cell structure linked to quiz rubric.
- Short-answer question on DNA replication linked to short-answer rubric.
- Lab prompt asking students to analyze photosynthesis data linked to lab report rubric.
- Store options, answers, rationales, and metadata as JSON strings where needed.

### 3.4 Attempts & Feedback
- Each student submits at least one attempt per item.
- Score fields reflect rubric expectations (e.g., 0–100 scale or descriptive levels).
- Feedback entries include AI-generated text and optional human revisions with `status` cycling between `draft` and `published`.
- Release a subset of feedback records via `released_at`.

### 3.5 Mastery & Recommendations
- Mastery records track objective codes like `BIO.CELL.101` with levels such as `emerging`, `proficient`, `mastery`.
- Recommendation payloads advise next study topics or remediation steps, stored as JSON structures (stringified).
- Flag one recommendation per student as `chosen`.

## 4. Implementation Steps

1. **Create Seeder Class**
   - Run `php artisan make:seeder BiologyDemoSeeder`.
   - Inject calls to helper methods in `run()`.
2. **Populate Models**
   - Use factories where available; otherwise, call `Model::create()` with explicit arrays.
   - Ensure relational integrity by capturing created IDs and passing them forward.
3. **Update DatabaseSeeder**
   - Wrap invocation with `if (app()->environment('local', 'testing')) { $this->call(BiologyDemoSeeder::class); }`.
4. **Migrate & Seed**
   - Run `php artisan migrate:fresh --seed` in a local/testing environment.
5. **Verification Checklist**
   - Confirm Filament lists show populated data across all resources.
   - Validate relationships (e.g., attempts display user and item names).
   - Spot-check password hashing (no plain text in database).

## 5. Future Enhancements

- Expand items to cover upcoming biology units (e.g., ecology, evolution).
- Introduce randomized data generation with factories once domain fixtures stabilize.
- Add scenario-specific seeders (e.g., assessment retake workflows) derived from this baseline.
