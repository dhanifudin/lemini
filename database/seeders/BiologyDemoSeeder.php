<?php

namespace Database\Seeders;

use App\Models\Attempt;
use App\Models\Feedback;
use App\Models\Item;
use App\Models\LearningObjective;
use App\Models\Mastery;
use App\Models\Recommendation;
use App\Models\Rubric;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BiologyDemoSeeder extends Seeder
{
    protected User $admin;

    /** @var Collection<int, User> */
    protected Collection $teachers;

    /** @var Collection<int, User> */
    protected Collection $students;

    /** @var Collection<string, Rubric> */
    protected Collection $rubrics;

    /** @var Collection<string, Item> */
    protected Collection $items;

    /** @var Collection<string, LearningObjective> */
    protected Collection $objectives;

    /** @var Collection<int, Attempt> */
    protected Collection $attempts;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguarded(function (): void {
            $this->seedUsers();
            $this->seedRubrics();
            $this->seedLearningObjectives();
            $this->seedItems();
            $this->seedAttemptsAndFeedback();
            $this->seedMasteryAndRecommendations();
        });
    }

    protected function seedUsers(): void
    {
        $this->admin = User::create([
            'name' => 'Dr. Ada Kingsley',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
            'email_verified_at' => now()->subDays(14),
            'remember_token' => Str::random(20),
        ]);

        $teacherData = [
            [
                'name' => 'Prof. Naomi Chen',
                'email' => 'naomi.chen@example.com',
                'password' => 'password',
                'role' => 'teacher',
                'email_verified_at' => now()->subDays(10),
            ],
            [
                'name' => 'Prof. Malik Hassan',
                'email' => 'malik.hassan@example.com',
                'password' => 'password',
                'role' => 'teacher',
                'email_verified_at' => now()->subDays(9),
            ],
        ];

        $this->teachers = collect($teacherData)->map(function (array $attributes): User {
            $attributes['remember_token'] = Str::random(20);

            return User::create($attributes);
        });

        $studentData = [
            [
                'name' => 'Aria Patel',
                'email' => 'aria.patel@example.com',
                'password' => 'password',
                'role' => 'student',
                'email_verified_at' => now()->subDays(5),
            ],
            [
                'name' => 'Jonah Rivers',
                'email' => 'jonah.rivers@example.com',
                'password' => 'password',
                'role' => 'student',
                'email_verified_at' => now()->subDays(4),
            ],
            [
                'name' => 'Sofia Morales',
                'email' => 'sofia.morales@example.com',
                'password' => 'password',
                'role' => 'student',
                'email_verified_at' => null,
            ],
            [
                'name' => 'Elias Novak',
                'email' => 'elias.novak@example.com',
                'password' => 'password',
                'role' => 'student',
                'email_verified_at' => now()->subDays(3),
            ],
            [
                'name' => 'Mira Tan',
                'email' => 'mira.tan@example.com',
                'password' => 'password',
                'role' => 'student',
                'email_verified_at' => null,
            ],
        ];

        $this->students = collect($studentData)->map(function (array $attributes): User {
            $attributes['remember_token'] = Str::random(20);

            return User::create($attributes);
        });
    }

    protected function seedRubrics(): void
    {
        $this->rubrics = collect([
            'lab_report' => Rubric::create([
                'name' => 'Biology Lab Report',
                'criteria' => [
                    'hypothesis' => 'States a clear, testable hypothesis grounded in biology concepts.',
                    'methodology' => 'Describes procedures with sufficient detail for replication.',
                    'data_analysis' => 'Interprets observations with accurate biological reasoning.',
                    'conclusion' => 'Summarizes findings and reflects on experimental limitations.',
                ],
                'levels' => [
                    'beginning' => 'Minimal understanding demonstrated.',
                    'developing' => 'Partial understanding with notable gaps.',
                    'proficient' => 'Solid biological reasoning with minor errors.',
                    'exemplary' => 'Insightful analysis with advanced synthesis.',
                ],
            ]),
            'quiz' => Rubric::create([
                'name' => 'Biology Quiz Assessment',
                'criteria' => [
                    'accuracy' => 'Correctness of selected options.',
                    'reasoning' => 'Evidence of understanding the biological process.',
                ],
                'levels' => [
                    'poor' => 'Few correct responses or major misconceptions.',
                    'fair' => 'Partial accuracy with limited explanations.',
                    'good' => 'Mostly accurate answers with sound rationale.',
                    'excellent' => 'Fully accurate with detailed biological insight.',
                ],
            ]),
            'short_answer' => Rubric::create([
                'name' => 'Short-Answer Biology Response',
                'criteria' => [
                    'clarity' => 'Communicates response succinctly and clearly.',
                    'accuracy' => 'Biological facts presented without errors.',
                    'depth' => 'Connects concepts and explains underlying mechanisms.',
                ],
                'levels' => [
                    'emerging' => 'Idea present but unclear or inaccurate.',
                    'competent' => 'Mostly accurate with limited elaboration.',
                    'strong' => 'Accurate and explains key relationships.',
                    'mastery' => 'Rich detail that extends beyond expectation.',
                ],
            ]),
        ]);
    }

    protected function seedLearningObjectives(): void
    {
        $objectiveData = [
            'BIO.CELL.101' => [
                'title' => 'Explain cellular respiration organelles',
                'description' => 'Students differentiate organelles responsible for energy production and explain ATP synthesis pathways.',
                'standards' => ['NGSS HS-LS1-7'],
            ],
            'BIO.GEN.201' => [
                'title' => 'Describe DNA replication enzymes',
                'description' => 'Students summarize polymerase directionality and proofreading to justify genomic fidelity.',
                'standards' => ['NGSS HS-LS3-1'],
            ],
            'BIO.PS.305' => [
                'title' => 'Analyze photosynthesis limiting factors',
                'description' => 'Students interpret data to identify how light intensity impacts photosynthetic rate.',
                'standards' => ['NGSS HS-LS1-5'],
            ],
        ];

        $this->objectives = collect($objectiveData)->map(function (array $data, string $code) {
            return LearningObjective::create([
                'code' => $code,
                'title' => $data['title'],
                'description' => $data['description'],
                'standards' => $data['standards'],
                'version' => 'v1',
            ]);
        });
    }

    protected function seedItems(): void
    {
        $this->items = collect([
            'cell_structure' => Item::create([
                'rubric_id' => $this->rubrics->get('quiz')->id,
                'learning_objective_id' => $this->objectives->get('BIO.CELL.101')->id,
                'objective_code' => 'BIO.CELL.101',
                'stem' => 'Which organelle is primarily responsible for producing ATP during cellular respiration?',
                'type' => 'MCQ',
                'options' => [
                    'A' => 'Mitochondrion',
                    'B' => 'Golgi apparatus',
                    'C' => 'Nucleus',
                    'D' => 'Chloroplast',
                ],
                'answer' => 'A',
                'rationale' => 'Mitochondria generate ATP through oxidative phosphorylation, making them the cell’s powerhouses.',
                'meta' => [
                    'topic' => 'Cellular respiration',
                    'difficulty' => 'medium',
                    'source' => 'Unit 3 Quiz',
                ],
                'is_quiz_eligible' => true,
            ]),
            'dna_replication' => Item::create([
                'rubric_id' => $this->rubrics->get('short_answer')->id,
                'learning_objective_id' => $this->objectives->get('BIO.GEN.201')->id,
                'objective_code' => 'BIO.GEN.201',
                'stem' => 'Explain the role of DNA polymerase during DNA replication and describe why its proofreading ability is important.',
                'type' => 'SAQ',
                'options' => null,
                'answer' => 'DNA polymerase synthesizes new DNA strands in the 5’ to 3’ direction and corrects mistakes via exonuclease activity to maintain fidelity.',
                'rationale' => 'Highlights enzyme directionality and proofreading, ensuring genomic stability.',
                'meta' => [
                    'topic' => 'Genetics',
                    'expected_length' => '3-4 sentences',
                    'keywords' => ['polymerase', 'proofreading', 'fidelity'],
                ],
                'is_quiz_eligible' => true,
            ]),
            'photosynthesis_lab' => Item::create([
                'rubric_id' => $this->rubrics->get('lab_report')->id,
                'learning_objective_id' => $this->objectives->get('BIO.PS.305')->id,
                'objective_code' => 'BIO.PS.305',
                'stem' => 'Analyze the provided data set to determine how light intensity affects the rate of photosynthesis in Elodea plants.',
                'type' => 'SAQ',
                'options' => null,
                'answer' => 'As light intensity increases up to a threshold, the rate of photosynthesis rises before plateauing due to enzyme saturation.',
                'rationale' => 'Connects experimental data to limiting factors in photosynthesis.',
                'meta' => [
                    'topic' => 'Photosynthesis',
                    'lab_focus' => 'Data interpretation',
                    'data_set' => 'photosynthesis_light_curve.csv',
                ],
                'is_quiz_eligible' => true,
            ]),
        ]);
    }

    protected function seedAttemptsAndFeedback(): void
    {
        $this->attempts = collect();
        $statusCycle = ['draft', 'published'];
        $aiSummaries = [
            'cell_structure' => [
                'summary' => 'Demonstrated solid understanding of ATP production.',
                'action_steps' => [
                    'Review how mitochondria and chloroplasts differ in plant cells.',
                    'Connect ATP production to cellular energy demands.',
                ],
            ],
            'dna_replication' => [
                'summary' => 'Explained DNA polymerase function with minor gaps.',
                'action_steps' => [
                    'Clarify the directionality terminology (leading vs lagging).',
                    'Study how mismatch repair complements polymerase proofreading.',
                ],
            ],
            'photosynthesis_lab' => [
                'summary' => 'Data interpretation captures the light intensity relationship.',
                'action_steps' => [
                    'Discuss potential limiting factors after the plateau.',
                    'Relate results to chloroplast structure and enzyme activity.',
                ],
            ],
        ];

        $attemptCounter = 0;

        foreach ($this->students as $studentIndex => $student) {
            foreach ($this->items as $key => $item) {
                $score = match ($item->type) {
                    'MCQ' => 70 + ($studentIndex * 5) + ($attemptCounter % 10),
                    default => 65 + ($studentIndex * 6) + ($attemptCounter % 8),
                };

                $attemptedAt = now()->subDays(6 - $studentIndex)->setTime(14, 0)->addMinutes($attemptCounter * 3);

                $attempt = Attempt::create([
                    'user_id' => $student->id,
                    'item_id' => $item->id,
                    'response' => $item->type === 'MCQ'
                        ? ($score >= 80 ? 'A' : 'D')
                        : 'Student response discussing key biology concepts for ' . $item->objective_code,
                    'score' => min($score, 100),
                    'metadata' => [
                        'submitted_at' => $attemptedAt->toDateTimeString(),
                        'duration_minutes' => 12 + ($attemptCounter % 7),
                        'teacher_reviewer' => $this->teachers->get($studentIndex % $this->teachers->count())->name,
                    ],
                    'created_at' => $attemptedAt,
                    'updated_at' => $attemptedAt,
                ]);

                $this->attempts->push($attempt);

                $status = $statusCycle[$attemptCounter % count($statusCycle)];
                $releasedAt = $status === 'published'
                    ? $attemptedAt->copy()->addDay()
                    : null;

                $feedbackPayload = $aiSummaries[$key];

                Feedback::create([
                    'attempt_id' => $attempt->id,
                    'ai_text' => [
                        'summary' => $feedbackPayload['summary'],
                        'strengths' => 'Highlights accurate references to ' . $item->objective_code,
                        'action_steps' => $feedbackPayload['action_steps'],
                    ],
                    'human_revision' => $status === 'published'
                        ? 'Reviewed by ' . $this->teachers->get($attemptCounter % $this->teachers->count())->name
                        : null,
                    'status' => $status,
                    'released_at' => $releasedAt,
                    'created_at' => $attemptedAt->copy()->addHours(2),
                    'updated_at' => $attemptedAt->copy()->addHours(2),
                ]);

                $attemptCounter++;
            }
        }
    }

    protected function seedMasteryAndRecommendations(): void
    {
        $objectiveCodes = [
            ['code' => 'BIO.CELL.101', 'focus' => 'Cellular respiration'],
            ['code' => 'BIO.GEN.201', 'focus' => 'Genetics and replication'],
            ['code' => 'BIO.PS.305', 'focus' => 'Photosynthesis pathways'],
        ];

        $masteryLevels = ['emerging', 'proficient', 'mastery'];
        $levelScores = [
            'emerging' => 35,
            'developing' => 50,
            'competent' => 65,
            'proficient' => 80,
            'strong' => 90,
            'mastery' => 97,
        ];

        foreach ($this->students as $studentIndex => $student) {
            foreach ($objectiveCodes as $objectiveIndex => $info) {
                $level = $masteryLevels[($studentIndex + $objectiveIndex) % count($masteryLevels)];
                $lastSeen = Carbon::now()
                    ->subDays(2 + $studentIndex + $objectiveIndex)
                    ->setTime(16, 0);

                Mastery::updateOrCreate(
                    [
                        'user_id' => $student->id,
                        'objective_code' => $info['code'],
                    ],
                    [
                        'level' => $level,
                        'score' => $levelScores[$level] ?? 50,
                        'last_seen_at' => $lastSeen,
                    ],
                );
            }

            $chosenRecommendation = Recommendation::create([
                'user_id' => $student->id,
                'payload' => [
                    'focus_area' => 'Deepen understanding of ' . $objectiveCodes[$studentIndex % count($objectiveCodes)]['focus'],
                    'suggested_resource' => 'Watch the interactive simulation on enzyme kinetics.',
                    'next_check_in' => Carbon::now()->addDays(3)->toDateString(),
                ],
                'chosen' => true,
            ]);

            Recommendation::create([
                'user_id' => $student->id,
                'payload' => [
                    'focus_area' => 'Prepare for upcoming lab on plant physiology.',
                    'suggested_resource' => 'Complete virtual lab: Measuring photosynthetic rate.',
                    'related_recommendation_id' => $chosenRecommendation->id,
                ],
                'chosen' => false,
            ]);
        }
    }
}
