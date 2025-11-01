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

class BahasaIndonesiaDemoSeeder extends Seeder
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
            'name' => 'Dr. Retno Wulandari',
            'email' => 'admin@sekolah.sch.id',
            'password' => 'password',
            'role' => 'admin',
            'email_verified_at' => now()->subDays(30),
            'remember_token' => Str::random(20),
        ]);

        $teacherData = [
            [
                'name' => 'Ibu Dr. Siti Nurhaliza',
                'email' => 'siti.nurhaliza@sekolah.sch.id',
                'password' => 'password',
                'role' => 'teacher',
                'email_verified_at' => now()->subDays(25),
            ],
            [
                'name' => 'Bapak Drs. Ahmad Hidayat, M.Pd.',
                'email' => 'ahmad.hidayat@sekolah.sch.id',
                'password' => 'password',
                'role' => 'teacher',
                'email_verified_at' => now()->subDays(24),
            ],
        ];

        $this->teachers = collect($teacherData)->map(function (array $attributes): User {
            $attributes['remember_token'] = Str::random(20);

            return User::create($attributes);
        });

        $studentData = [
            [
                'name' => 'Aisyah Putri',
                'email' => 'aisyah.putri@siswa.sch.id',
                'password' => 'password',
                'role' => 'student',
                'email_verified_at' => now()->subDays(10),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@siswa.sch.id',
                'password' => 'password',
                'role' => 'student',
                'email_verified_at' => now()->subDays(9),
            ],
            [
                'name' => 'Citra Dewi',
                'email' => 'citra.dewi@siswa.sch.id',
                'password' => 'password',
                'role' => 'student',
                'email_verified_at' => null,
            ],
            [
                'name' => 'Dimas Prakoso',
                'email' => 'dimas.prakoso@siswa.sch.id',
                'password' => 'password',
                'role' => 'student',
                'email_verified_at' => now()->subDays(8),
            ],
            [
                'name' => 'Eka Saputra',
                'email' => 'eka.saputra@siswa.sch.id',
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
            'analisis_teks' => Rubric::create([
                'name' => 'Rubrik Analisis Teks Bahasa Indonesia',
                'criteria' => [
                    'struktur_teks' => 'Mengidentifikasi struktur teks (orientasi, isi, penutup) dengan tepat',
                    'unsur_kebahasaan' => 'Menganalisis unsur kebahasaan (diksi, konjungsi, kalimat efektif)',
                    'isi_makna' => 'Memahami makna tersurat dan tersirat dalam teks',
                    'kesimpulan' => 'Menyimpulkan pesan atau amanat dari teks dengan argumentasi yang kuat',
                ],
                'levels' => [
                    'kurang' => 'Pemahaman sangat terbatas, banyak kesalahan analisis',
                    'cukup' => 'Pemahaman dasar ada tetapi analisis kurang mendalam',
                    'baik' => 'Analisis tepat dengan alasan yang logis dan contoh yang relevan',
                    'sangat_baik' => 'Analisis mendalam dengan sintesis ide dan pendekatan kritis',
                ],
            ]),
            'penulisan_kreatif' => Rubric::create([
                'name' => 'Rubrik Penulisan Kreatif',
                'criteria' => [
                    'organisasi' => 'Struktur tulisan runtut dengan paragraf yang koheren',
                    'diksi' => 'Pemilihan kata yang tepat, variatif, dan sesuai konteks',
                    'ejaan_tata_bahasa' => 'Penggunaan EYD (Ejaan Yang Disempurnakan) dan tata bahasa yang benar',
                    'kreativitas' => 'Originalitas ide dan pengembangan gagasan yang menarik',
                ],
                'levels' => [
                    'pemula' => 'Struktur belum terorganisir, banyak kesalahan EYD',
                    'berkembang' => 'Struktur cukup jelas, masih ada kesalahan minor',
                    'mahir' => 'Tulisan terstruktur baik dengan kesalahan minimal',
                    'unggul' => 'Tulisan sempurna dengan gaya bahasa yang matang dan kreatif',
                ],
            ]),
            'presentasi_lisan' => Rubric::create([
                'name' => 'Rubrik Presentasi dan Diskusi',
                'criteria' => [
                    'kejelasan' => 'Penyampaian jelas, intonasi tepat, volume suara memadai',
                    'struktur_argumen' => 'Argumen disusun logis dengan bukti atau contoh pendukung',
                    'bahasa_formal' => 'Menggunakan bahasa Indonesia formal dan santun',
                    'interaksi' => 'Merespon pertanyaan dengan relevan dan mampu berargumentasi',
                ],
                'levels' => [
                    'dasar' => 'Penyampaian kurang jelas, argumen lemah',
                    'sedang' => 'Cukup jelas tetapi argumen perlu diperkuat',
                    'lanjut' => 'Jelas dan argumentatif dengan bukti yang kuat',
                    'mahir' => 'Sangat persuasif, argumentasi kuat, dan interaksi dinamis',
                ],
            ]),
        ]);
    }

    protected function seedLearningObjectives(): void
    {
        $objectiveData = [
            // Dasar (Basic Level)
            'BI.TEKS.101' => [
                'title' => 'Mengidentifikasi struktur teks deskripsi',
                'description' => 'Siswa dapat mengidentifikasi ciri-ciri dan struktur teks deskripsi serta unsur kebahasaannya',
                'standards' => ['KD 3.1 Kelas X'],
            ],
            'BI.KATA.102' => [
                'title' => 'Menggunakan kata baku dan tidak baku',
                'description' => 'Siswa dapat membedakan dan menggunakan kata baku sesuai KBBI dalam kalimat efektif',
                'standards' => ['KD 3.2 Kelas X'],
            ],
            'BI.KALIMAT.103' => [
                'title' => 'Menyusun kalimat efektif',
                'description' => 'Siswa dapat menyusun kalimat dengan struktur subjek-predikat yang benar dan efektif',
                'standards' => ['KD 4.1 Kelas X'],
            ],
            // Menengah (Intermediate Level)
            'BI.ARGUMEN.201' => [
                'title' => 'Menganalisis struktur teks argumentasi',
                'description' => 'Siswa dapat menganalisis struktur argumentasi (tesis, argumen, penegasan) dan mengevaluasi kualitas argumen',
                'standards' => ['KD 3.3 Kelas XI'],
            ],
            'BI.MAJAS.202' => [
                'title' => 'Mengidentifikasi majas dan gaya bahasa',
                'description' => 'Siswa dapat mengidentifikasi berbagai jenis majas (metafora, personifikasi, hiperbola) dalam teks sastra',
                'standards' => ['KD 3.4 Kelas XI'],
            ],
            'BI.DISKUSI.203' => [
                'title' => 'Menyampaikan pendapat dalam diskusi',
                'description' => 'Siswa dapat menyampaikan pendapat secara logis dan santun dalam forum diskusi dengan struktur yang sistematis',
                'standards' => ['KD 4.3 Kelas XI'],
            ],
            // Lanjut (Advanced Level)
            'BI.KRITIK.301' => [
                'title' => 'Menulis esai kritik sastra',
                'description' => 'Siswa dapat menulis esai kritik yang menganalisis unsur intrinsik dan ekstrinsik karya sastra dengan pendekatan teoretis',
                'standards' => ['KD 3.5 Kelas XII'],
            ],
            'BI.RETORIKA.302' => [
                'title' => 'Menganalisis teknik retorika dalam pidato',
                'description' => 'Siswa dapat mengidentifikasi dan menganalisis teknik persuasi, diksi, dan struktur retorika dalam teks pidato',
                'standards' => ['KD 3.6 Kelas XII'],
            ],
            'BI.PENELITIAN.303' => [
                'title' => 'Menyusun karya tulis ilmiah',
                'description' => 'Siswa dapat menyusun karya tulis ilmiah dengan metodologi penelitian yang benar dan sitasi akademik',
                'standards' => ['KD 4.5 Kelas XII'],
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
            // DASAR LEVEL (Basic)
            'teks_deskripsi_1' => Item::create([
                'rubric_id' => $this->rubrics->get('analisis_teks')->id,
                'learning_objective_id' => $this->objectives->get('BI.TEKS.101')->id,
                'objective_code' => 'BI.TEKS.101',
                'stem' => 'Perhatikan kutipan berikut: "Candi Borobudur merupakan candi Buddha terbesar di dunia. Candi ini memiliki sembilan tingkat dengan ribuan relief yang menceritakan ajaran Buddha." Bagian teks di atas termasuk dalam struktur...',
                'type' => 'MCQ',
                'options' => [
                    'A' => 'Identifikasi',
                    'B' => 'Deskripsi bagian',
                    'C' => 'Penutup',
                    'D' => 'Argumentasi',
                ],
                'answer' => 'A',
                'rationale' => 'Kutipan tersebut merupakan bagian identifikasi karena memperkenalkan objek (Candi Borobudur) secara umum.',
                'meta' => [
                    'topic' => 'Teks Deskripsi',
                    'difficulty' => 'dasar',
                    'bloom_level' => 'mengingat',
                ],
                'is_quiz_eligible' => true,
            ]),
            'teks_deskripsi_2' => Item::create([
                'rubric_id' => $this->rubrics->get('analisis_teks')->id,
                'learning_objective_id' => $this->objectives->get('BI.TEKS.101')->id,
                'objective_code' => 'BI.TEKS.101',
                'stem' => 'Ciri kebahasaan yang paling dominan dalam teks deskripsi adalah...',
                'type' => 'MCQ',
                'options' => [
                    'A' => 'Menggunakan kata penghubung temporal',
                    'B' => 'Menggunakan kata sifat dan keterangan',
                    'C' => 'Menggunakan kata kerja aksi',
                    'D' => 'Menggunakan kata penghubung kausal',
                ],
                'answer' => 'B',
                'rationale' => 'Teks deskripsi menggunakan banyak kata sifat dan keterangan untuk menggambarkan objek secara detail.',
                'meta' => [
                    'topic' => 'Teks Deskripsi',
                    'difficulty' => 'dasar',
                    'bloom_level' => 'memahami',
                ],
                'is_quiz_eligible' => true,
            ]),
            'kata_baku_1' => Item::create([
                'rubric_id' => $this->rubrics->get('penulisan_kreatif')->id,
                'learning_objective_id' => $this->objectives->get('BI.KATA.102')->id,
                'objective_code' => 'BI.KATA.102',
                'stem' => 'Manakah penulisan kata yang BAKU menurut KBBI?',
                'type' => 'MCQ',
                'options' => [
                    'A' => 'aktifitas',
                    'B' => 'aktivitas',
                    'C' => 'aktipitas',
                    'D' => 'aktifitaz',
                ],
                'answer' => 'B',
                'rationale' => 'Penulisan yang baku adalah "aktivitas" sesuai dengan Kamus Besar Bahasa Indonesia (KBBI).',
                'meta' => [
                    'topic' => 'Kata Baku',
                    'difficulty' => 'dasar',
                    'bloom_level' => 'memahami',
                ],
                'is_quiz_eligible' => true,
            ]),
            'kata_baku_2' => Item::create([
                'rubric_id' => $this->rubrics->get('penulisan_kreatif')->id,
                'learning_objective_id' => $this->objectives->get('BI.KATA.102')->id,
                'objective_code' => 'BI.KATA.102',
                'stem' => 'Kata yang penulisannya TIDAK BAKU adalah...',
                'type' => 'MCQ',
                'options' => [
                    'A' => 'standar',
                    'B' => 'analisis',
                    'C' => 'sistim',
                    'D' => 'praktik',
                ],
                'answer' => 'C',
                'rationale' => 'Penulisan yang baku adalah "sistem" bukan "sistim" sesuai KBBI.',
                'meta' => [
                    'topic' => 'Kata Baku',
                    'difficulty' => 'dasar',
                    'bloom_level' => 'memahami',
                ],
                'is_quiz_eligible' => true,
            ]),
            'kalimat_efektif_1' => Item::create([
                'rubric_id' => $this->rubrics->get('penulisan_kreatif')->id,
                'learning_objective_id' => $this->objectives->get('BI.KALIMAT.103')->id,
                'objective_code' => 'BI.KALIMAT.103',
                'stem' => 'Perbaiki kalimat berikut agar menjadi kalimat efektif: "Bagi yang berminat untuk mengikuti lomba karya tulis dapat mendaftar di sekretariat."',
                'type' => 'SAQ',
                'options' => null,
                'answer' => 'Yang berminat mengikuti lomba karya tulis dapat mendaftar di sekretariat.',
                'rationale' => 'Kalimat efektif menghilangkan kata "bagi" dan "untuk" yang berlebihan. Subjek "yang berminat" sudah jelas tanpa kata depan.',
                'meta' => [
                    'topic' => 'Kalimat Efektif',
                    'expected_length' => '1 kalimat',
                    'keywords' => ['subjek', 'predikat', 'efisiensi'],
                    'difficulty' => 'dasar',
                ],
                'is_quiz_eligible' => true,
            ]),

            // MENENGAH LEVEL (Intermediate)
            'teks_argumentasi_1' => Item::create([
                'rubric_id' => $this->rubrics->get('analisis_teks')->id,
                'learning_objective_id' => $this->objectives->get('BI.ARGUMEN.201')->id,
                'objective_code' => 'BI.ARGUMEN.201',
                'stem' => 'Perhatikan paragraf: "Pendidikan karakter sangat penting diterapkan di sekolah. Dengan pendidikan karakter, siswa tidak hanya cerdas secara akademik tetapi juga memiliki moral yang baik. Oleh karena itu, setiap sekolah harus mengintegrasikan pendidikan karakter dalam kurikulum." Struktur paragraf tersebut adalah...',
                'type' => 'MCQ',
                'options' => [
                    'A' => 'Tesis - Argumentasi - Penegasan',
                    'B' => 'Orientasi - Komplikasi - Resolusi',
                    'C' => 'Pembuka - Isi - Penutup',
                    'D' => 'Identifikasi - Deskripsi - Simpulan',
                ],
                'answer' => 'A',
                'rationale' => 'Paragraf diawali tesis (pendapat), dilanjutkan argumen pendukung, dan ditutup dengan penegasan.',
                'meta' => [
                    'topic' => 'Teks Argumentasi',
                    'difficulty' => 'menengah',
                    'bloom_level' => 'menganalisis',
                ],
                'is_quiz_eligible' => true,
            ]),
            'teks_argumentasi_2' => Item::create([
                'rubric_id' => $this->rubrics->get('analisis_teks')->id,
                'learning_objective_id' => $this->objectives->get('BI.ARGUMEN.201')->id,
                'objective_code' => 'BI.ARGUMEN.201',
                'stem' => 'Dalam teks argumentasi, bagian yang berisi pendapat atau pernyataan penulis disebut...',
                'type' => 'MCQ',
                'options' => [
                    'A' => 'Argumen',
                    'B' => 'Tesis',
                    'C' => 'Penegasan',
                    'D' => 'Fakta',
                ],
                'answer' => 'B',
                'rationale' => 'Tesis adalah bagian yang berisi pendapat atau pernyataan penulis tentang suatu masalah.',
                'meta' => [
                    'topic' => 'Teks Argumentasi',
                    'difficulty' => 'menengah',
                    'bloom_level' => 'memahami',
                ],
                'is_quiz_eligible' => true,
            ]),
            'majas_1' => Item::create([
                'rubric_id' => $this->rubrics->get('analisis_teks')->id,
                'learning_objective_id' => $this->objectives->get('BI.MAJAS.202')->id,
                'objective_code' => 'BI.MAJAS.202',
                'stem' => 'Kalimat "Angin berbisik di telinga, menceritakan rahasia malam" menggunakan majas...',
                'type' => 'MCQ',
                'options' => [
                    'A' => 'Metafora',
                    'B' => 'Personifikasi',
                    'C' => 'Hiperbola',
                    'D' => 'Simile',
                ],
                'answer' => 'B',
                'rationale' => 'Personifikasi memberikan sifat manusia (berbisik, menceritakan) kepada benda mati (angin).',
                'meta' => [
                    'topic' => 'Majas dan Gaya Bahasa',
                    'difficulty' => 'menengah',
                    'bloom_level' => 'menganalisis',
                ],
                'is_quiz_eligible' => true,
            ]),
            'majas_2' => Item::create([
                'rubric_id' => $this->rubrics->get('analisis_teks')->id,
                'learning_objective_id' => $this->objectives->get('BI.MAJAS.202')->id,
                'objective_code' => 'BI.MAJAS.202',
                'stem' => 'Kalimat "Suaranya menggelegar membelah langit" mengandung majas...',
                'type' => 'MCQ',
                'options' => [
                    'A' => 'Personifikasi',
                    'B' => 'Metafora',
                    'C' => 'Hiperbola',
                    'D' => 'Alegori',
                ],
                'answer' => 'C',
                'rationale' => 'Hiperbola adalah majas yang melebih-lebihkan sesuatu untuk memberikan kesan dramatis.',
                'meta' => [
                    'topic' => 'Majas dan Gaya Bahasa',
                    'difficulty' => 'menengah',
                    'bloom_level' => 'menganalisis',
                ],
                'is_quiz_eligible' => true,
            ]),
            'diskusi_1' => Item::create([
                'rubric_id' => $this->rubrics->get('presentasi_lisan')->id,
                'learning_objective_id' => $this->objectives->get('BI.DISKUSI.203')->id,
                'objective_code' => 'BI.DISKUSI.203',
                'stem' => 'Tuliskan pendapat Anda tentang "Apakah pembelajaran daring lebih efektif daripada pembelajaran tatap muka?" Sertakan minimal dua argumen yang mendukung pendapat Anda.',
                'type' => 'SAQ',
                'options' => null,
                'answer' => 'Pembelajaran tatap muka lebih efektif karena: 1) Interaksi langsung memungkinkan diskusi lebih mendalam, 2) Guru dapat langsung mengetahui kesulitan siswa dan memberikan bantuan.',
                'rationale' => 'Jawaban mengandung pendapat jelas dengan dua argumen yang logis dan relevan.',
                'meta' => [
                    'topic' => 'Argumentasi dan Diskusi',
                    'expected_length' => '3-5 kalimat',
                    'keywords' => ['pendapat', 'argumen', 'bukti'],
                    'difficulty' => 'menengah',
                ],
                'is_quiz_eligible' => true,
            ]),

            // LANJUT LEVEL (Advanced)
            'kritik_sastra_1' => Item::create([
                'rubric_id' => $this->rubrics->get('analisis_teks')->id,
                'learning_objective_id' => $this->objectives->get('BI.KRITIK.301')->id,
                'objective_code' => 'BI.KRITIK.301',
                'stem' => 'Bacalah cerpen "Robohnya Surau Kami" karya A.A. Navis. Analisislah tema dan amanat cerpen tersebut dengan menggunakan pendekatan struktural. Sertakan bukti dari teks.',
                'type' => 'SAQ',
                'options' => null,
                'answer' => 'Tema: Kritik terhadap kemunafikan beragama. Amanat: Agama bukan hanya ritual tetapi harus diamalkan dalam kehidupan. Bukti: Karakter Kakek yang rajin beribadah tetapi mengabaikan keadilan sosial.',
                'rationale' => 'Analisis menggunakan pendekatan struktural dengan mengidentifikasi tema, amanat, dan bukti tekstual.',
                'meta' => [
                    'topic' => 'Kritik Sastra',
                    'expected_length' => '1 paragraf (5-7 kalimat)',
                    'keywords' => ['tema', 'amanat', 'struktural', 'bukti'],
                    'difficulty' => 'lanjut',
                ],
                'is_quiz_eligible' => true,
            ]),
            'kritik_sastra_2' => Item::create([
                'rubric_id' => $this->rubrics->get('analisis_teks')->id,
                'learning_objective_id' => $this->objectives->get('BI.KRITIK.301')->id,
                'objective_code' => 'BI.KRITIK.301',
                'stem' => 'Unsur intrinsik dalam karya sastra yang paling berperan dalam membangun konflik adalah...',
                'type' => 'MCQ',
                'options' => [
                    'A' => 'Latar',
                    'B' => 'Tema',
                    'C' => 'Tokoh dan penokohan',
                    'D' => 'Amanat',
                ],
                'answer' => 'C',
                'rationale' => 'Tokoh dan penokohan adalah unsur yang paling berperan dalam membangun konflik melalui karakter dan tindakan para tokoh.',
                'meta' => [
                    'topic' => 'Kritik Sastra',
                    'difficulty' => 'lanjut',
                    'bloom_level' => 'menganalisis',
                ],
                'is_quiz_eligible' => true,
            ]),
            'retorika_1' => Item::create([
                'rubric_id' => $this->rubrics->get('presentasi_lisan')->id,
                'learning_objective_id' => $this->objectives->get('BI.RETORIKA.302')->id,
                'objective_code' => 'BI.RETORIKA.302',
                'stem' => 'Dalam pidato Bung Tomo, "Merdeka atau mati!" merupakan contoh penggunaan teknik retorika...',
                'type' => 'MCQ',
                'options' => [
                    'A' => 'Anafora (pengulangan kata di awal kalimat)',
                    'B' => 'Antitesis (pertentangan dua hal)',
                    'C' => 'Klimaks (peningkatan gagasan)',
                    'D' => 'Aliterasi (pengulangan bunyi)',
                ],
                'answer' => 'B',
                'rationale' => 'Antitesis menyajikan dua pilihan yang berlawanan (merdeka vs mati) untuk memperkuat pesan.',
                'meta' => [
                    'topic' => 'Retorika',
                    'difficulty' => 'lanjut',
                    'bloom_level' => 'mengevaluasi',
                ],
                'is_quiz_eligible' => true,
            ]),
            'retorika_2' => Item::create([
                'rubric_id' => $this->rubrics->get('presentasi_lisan')->id,
                'learning_objective_id' => $this->objectives->get('BI.RETORIKA.302')->id,
                'objective_code' => 'BI.RETORIKA.302',
                'stem' => 'Teknik persuasi dalam pidato yang menggunakan emosi pendengar disebut...',
                'type' => 'MCQ',
                'options' => [
                    'A' => 'Logos (logika)',
                    'B' => 'Ethos (kredibilitas)',
                    'C' => 'Pathos (emosi)',
                    'D' => 'Kairos (waktu yang tepat)',
                ],
                'answer' => 'C',
                'rationale' => 'Pathos adalah teknik persuasi yang menggunakan emosi pendengar untuk meyakinkan mereka.',
                'meta' => [
                    'topic' => 'Retorika',
                    'difficulty' => 'lanjut',
                    'bloom_level' => 'memahami',
                ],
                'is_quiz_eligible' => true,
            ]),
            'penelitian_1' => Item::create([
                'rubric_id' => $this->rubrics->get('penulisan_kreatif')->id,
                'learning_objective_id' => $this->objectives->get('BI.PENELITIAN.303')->id,
                'objective_code' => 'BI.PENELITIAN.303',
                'stem' => 'Buatlah kerangka karya tulis ilmiah dengan tema "Pengaruh Media Sosial terhadap Minat Baca Siswa SMA". Tuliskan: 1) Rumusan masalah, 2) Hipotesis, 3) Metode penelitian yang akan digunakan.',
                'type' => 'SAQ',
                'options' => null,
                'answer' => '1) Rumusan: Bagaimana pengaruh penggunaan media sosial terhadap minat baca siswa? 2) Hipotesis: Penggunaan media sosial berpengaruh negatif terhadap minat baca. 3) Metode: Survey kuantitatif dengan kuesioner kepada 100 siswa SMA.',
                'rationale' => 'Kerangka mencakup rumusan masalah yang jelas, hipotesis yang dapat diuji, dan metode penelitian yang sesuai.',
                'meta' => [
                    'topic' => 'Karya Tulis Ilmiah',
                    'expected_length' => '1 paragraf atau poin-poin',
                    'keywords' => ['rumusan masalah', 'hipotesis', 'metodologi'],
                    'difficulty' => 'lanjut',
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
            'dasar' => [
                'summary' => 'Siswa menunjukkan pemahaman dasar tentang materi.',
                'strengths' => 'Mampu mengidentifikasi konsep dasar dengan tepat.',
                'action_steps' => [
                    'Pelajari lebih lanjut tentang ciri kebahasaan teks.',
                    'Latihan dengan contoh soal yang lebih variatif.',
                ],
            ],
            'menengah' => [
                'summary' => 'Analisis cukup baik, argumen didukung dengan alasan logis.',
                'strengths' => 'Mampu menganalisis struktur dan memberikan argumentasi.',
                'action_steps' => [
                    'Perkuat argumentasi dengan menambahkan data pendukung.',
                    'Pelajari berbagai jenis majas untuk memperkaya analisis.',
                ],
            ],
            'lanjut' => [
                'summary' => 'Analisis menunjukkan pemahaman mendalam dengan pendekatan yang tepat.',
                'strengths' => 'Menggunakan bukti tekstual yang kuat dan analisis yang sistematis.',
                'action_steps' => [
                    'Eksplorasi pendekatan kritik lain seperti sosiologi sastra.',
                    'Bandingkan dengan karya sastra Indonesia lainnya.',
                ],
            ],
        ];

        $attemptCounter = 0;

        foreach ($this->students as $studentIndex => $student) {
            foreach ($this->items as $key => $item) {
                // Determine difficulty level from item meta
                $difficulty = $item->meta['difficulty'] ?? 'menengah';
                
                $baseScore = match ($difficulty) {
                    'dasar' => 75,
                    'menengah' => 70,
                    'lanjut' => 65,
                    default => 70,
                };

                $score = $baseScore + ($studentIndex * 4) + ($attemptCounter % 10);
                $score = min($score, 100);

                $attemptedAt = now()->subDays(8 - $studentIndex)->setTime(14, 0)->addMinutes($attemptCounter * 3);

                // Generate appropriate response based on item type
                $response = $item->type === 'MCQ'
                    ? ($score >= 80 ? $item->answer : collect(['A', 'B', 'C', 'D'])->random())
                    : $this->generateSAQResponse($item, $score);

                $attempt = Attempt::create([
                    'user_id' => $student->id,
                    'item_id' => $item->id,
                    'response' => $response,
                    'score' => $score,
                    'metadata' => [
                        'submitted_at' => $attemptedAt->toDateTimeString(),
                        'duration_minutes' => 10 + ($attemptCounter % 8),
                        'teacher_reviewer' => $this->teachers->get($studentIndex % $this->teachers->count())->name,
                        'kelas' => $this->getKelas($difficulty),
                        'semester' => 'Ganjil',
                    ],
                    'created_at' => $attemptedAt,
                    'updated_at' => $attemptedAt,
                ]);

                $this->attempts->push($attempt);

                $status = $statusCycle[$attemptCounter % count($statusCycle)];
                $releasedAt = $status === 'published'
                    ? $attemptedAt->copy()->addDay()
                    : null;

                $feedbackPayload = $aiSummaries[$difficulty];

                Feedback::create([
                    'attempt_id' => $attempt->id,
                    'ai_text' => [
                        'summary' => $feedbackPayload['summary'],
                        'strengths' => $feedbackPayload['strengths'],
                        'action_steps' => $feedbackPayload['action_steps'],
                    ],
                    'human_revision' => $status === 'published'
                        ? 'Sudah bagus! Teruskan belajar. - '.$this->teachers->get($attemptCounter % $this->teachers->count())->name
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

    protected function generateSAQResponse(Item $item, float $score): string
    {
        $quality = match (true) {
            $score >= 85 => 'excellent',
            $score >= 70 => 'good',
            default => 'fair',
        };

        $responses = [
            'BI.KALIMAT.103' => [
                'excellent' => 'Yang berminat mengikuti lomba karya tulis dapat mendaftar di sekretariat.',
                'good' => 'Yang berminat untuk mengikuti lomba karya tulis dapat mendaftar di sekretariat.',
                'fair' => 'Bagi yang berminat lomba karya tulis bisa mendaftar di sekretariat.',
            ],
            'BI.DISKUSI.203' => [
                'excellent' => 'Pembelajaran tatap muka lebih efektif karena interaksi langsung memungkinkan diskusi mendalam dan guru dapat langsung membantu siswa yang kesulitan.',
                'good' => 'Menurut saya pembelajaran tatap muka lebih baik karena bisa bertemu langsung dengan guru.',
                'fair' => 'Saya setuju dengan pembelajaran tatap muka karena lebih enak.',
            ],
            'BI.KRITIK.301' => [
                'excellent' => 'Tema cerpen adalah kritik terhadap kemunafikan beragama. Amanatnya adalah pentingnya mengamalkan nilai agama dalam kehidupan nyata. Bukti: karakter Kakek yang rajin beribadah tetapi mengabaikan keadilan sosial.',
                'good' => 'Tema cerpen adalah tentang agama dan kehidupan. Amanatnya adalah pentingnya beribadah dengan benar.',
                'fair' => 'Cerpen ini bertemakan kehidupan sosial dan agama.',
            ],
            'BI.PENELITIAN.303' => [
                'excellent' => '1) Rumusan: Bagaimana pengaruh media sosial terhadap minat baca siswa? 2) Hipotesis: Media sosial berpengaruh negatif terhadap minat baca. 3) Metode: Survey kuantitatif dengan kuesioner kepada 100 siswa.',
                'good' => '1) Rumusan: Apakah media sosial mempengaruhi minat baca? 2) Hipotesis: Ada pengaruh negatif. 3) Metode: Survey dengan angket.',
                'fair' => '1) Media sosial dan minat baca 2) Media sosial menurunkan minat baca 3) Survey',
            ],
        ];

        return $responses[$item->objective_code][$quality] ?? 'Jawaban siswa untuk '.$item->objective_code;
    }

    protected function getKelas(string $difficulty): string
    {
        return match ($difficulty) {
            'dasar' => 'X IPA '.rand(1, 3),
            'menengah' => 'XI IPA '.rand(1, 3),
            'lanjut' => 'XII IPA '.rand(1, 3),
            default => 'XI IPA 1',
        };
    }

    protected function seedMasteryAndRecommendations(): void
    {
        $masteryLevels = ['pemula', 'berkembang', 'cukup', 'baik', 'sangat_baik', 'mahir'];
        $levelScores = [
            'pemula' => 35,
            'berkembang' => 55,
            'cukup' => 70,
            'baik' => 80,
            'sangat_baik' => 90,
            'mahir' => 97,
        ];

        foreach ($this->students as $studentIndex => $student) {
            foreach ($this->objectives as $code => $objective) {
                $level = $masteryLevels[($studentIndex + $objective->id) % count($masteryLevels)];
                $lastSeen = Carbon::now()
                    ->subDays(3 + $studentIndex)
                    ->setTime(15, 0);

                Mastery::updateOrCreate(
                    [
                        'user_id' => $student->id,
                        'objective_code' => $code,
                    ],
                    [
                        'level' => $level,
                        'score' => $levelScores[$level] ?? 70,
                        'last_seen_at' => $lastSeen,
                    ],
                );
            }

            // Create recommendations
            $objectiveCodes = ['BI.TEKS.101', 'BI.ARGUMEN.201', 'BI.KRITIK.301'];
            $chosenCode = $objectiveCodes[$studentIndex % count($objectiveCodes)];

            $recommendations = [
                'BI.TEKS.101' => [
                    'focus_area' => 'Perkuat pemahaman struktur teks deskripsi',
                    'suggested_resource' => 'Baca buku "Bahasa Indonesia untuk SMA/MA Kelas X" halaman 15-30 atau tonton video pembelajaran di Rumah Belajar Kemdikbud',
                ],
                'BI.ARGUMEN.201' => [
                    'focus_area' => 'Tingkatkan kemampuan argumentasi dalam diskusi',
                    'suggested_resource' => 'Tonton video tentang teknik debat dan diskusi formal, latihan dengan teman sebaya',
                ],
                'BI.KRITIK.301' => [
                    'focus_area' => 'Dalami analisis kritik sastra dengan berbagai pendekatan',
                    'suggested_resource' => 'Baca jurnal kritik sastra Indonesia dan analisis karya Pramoedya Ananta Toer atau Chairil Anwar',
                ],
            ];

            $chosenRecommendation = Recommendation::create([
                'user_id' => $student->id,
                'payload' => array_merge(
                    $recommendations[$chosenCode],
                    ['next_check_in' => Carbon::now()->addDays(5)->toDateString()]
                ),
                'chosen' => true,
            ]);

            // Alternative recommendation
            $alternativeCodes = array_diff($objectiveCodes, [$chosenCode]);
            $altCode = collect($alternativeCodes)->random();

            Recommendation::create([
                'user_id' => $student->id,
                'payload' => array_merge(
                    $recommendations[$altCode],
                    [
                        'related_recommendation_id' => $chosenRecommendation->id,
                        'next_check_in' => Carbon::now()->addWeek()->toDateString(),
                    ]
                ),
                'chosen' => false,
            ]);
        }
    }
}
