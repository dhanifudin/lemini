# Bahasa Indonesia Subject Implementation Plan

This document outlines the plan to adapt the BiologyDemoSeeder for Bahasa Indonesia (Indonesian Language) content tailored for senior high school (SMA/SMK) students in Indonesia, with three difficulty levels.

---

## Overview

**Target Audience:** Senior High School students (Kelas X, XI, XII / Grade 10, 11, 12)  
**Subject:** Bahasa Indonesia  
**Curriculum Reference:** Kurikulum Merdeka / Kurikulum 2013  
**Difficulty Levels:**
- **Dasar (Basic)** - Foundation level, suitable for Grade 10 or struggling students
- **Menengah (Intermediate)** - Standard level for Grade 11
- **Lanjut (Advanced)** - Advanced level for Grade 12 or gifted students

---

## 1. Learning Objectives (Capaian Pembelajaran)

Replace the biology objectives with Bahasa Indonesia competencies based on the Indonesian national curriculum:

### Dasar (Basic Level)
| Code | Title | Description | Standards |
|------|-------|-------------|-----------|
| **BI.TEKS.101** | Mengidentifikasi struktur teks deskripsi | Siswa dapat mengidentifikasi ciri-ciri dan struktur teks deskripsi serta unsur kebahasaannya | KD 3.1 Kelas X |
| **BI.KATA.102** | Menggunakan kata baku dan tidak baku | Siswa dapat membedakan dan menggunakan kata baku sesuai KBBI dalam kalimat efektif | KD 3.2 Kelas X |
| **BI.KALIMAT.103** | Menyusun kalimat efektif | Siswa dapat menyusun kalimat dengan struktur subjek-predikat yang benar dan efektif | KD 4.1 Kelas X |

### Menengah (Intermediate Level)
| Code | Title | Description | Standards |
|------|-------|-------------|-----------|
| **BI.ARGUMEN.201** | Menganalisis struktur teks argumentasi | Siswa dapat menganalisis struktur argumentasi (tesis, argumen, penegasan) dan mengevaluasi kualitas argumen | KD 3.3 Kelas XI |
| **BI.MAJAS.202** | Mengidentifikasi majas dan gaya bahasa | Siswa dapat mengidentifikasi berbagai jenis majas (metafora, personifikasi, hiperbola) dalam teks sastra | KD 3.4 Kelas XI |
| **BI.DISKUSI.203** | Menyampaikan pendapat dalam diskusi | Siswa dapat menyampaikan pendapat secara logis dan santun dalam forum diskusi dengan struktur yang sistematis | KD 4.3 Kelas XI |

### Lanjut (Advanced Level)
| Code | Title | Description | Standards |
|------|-------|-------------|-----------|
| **BI.KRITIK.301** | Menulis esai kritik sastra | Siswa dapat menulis esai kritik yang menganalisis unsur intrinsik dan ekstrinsik karya sastra dengan pendekatan teoretis | KD 3.5 Kelas XII |
| **BI.RETORIKA.302** | Menganalisis teknik retorika dalam pidato | Siswa dapat mengidentifikasi dan menganalisis teknik persuasi, diksi, dan struktur retorika dalam teks pidato | KD 3.6 Kelas XII |
| **BI.PENELITIAN.303** | Menyusun karya tulis ilmiah | Siswa dapat menyusun karya tulis ilmiah dengan metodologi penelitian yang benar dan sitasi akademik | KD 4.5 Kelas XII |

---

## 2. Rubrics (Rubrik Penilaian)

Replace biology rubrics with Bahasa Indonesia assessment criteria:

### Rubrik 1: Analisis Teks (Text Analysis)
**Name:** Rubrik Analisis Teks Bahasa Indonesia  
**Criteria:**
- **struktur_teks** - Mengidentifikasi struktur teks (orientasi, isi, penutup) dengan tepat
- **unsur_kebahasaan** - Menganalisis unsur kebahasaan (diksi, konjungsi, kalimat efektif)
- **isi_makna** - Memahami makna tersurat dan tersirat dalam teks
- **kesimpulan** - Menyimpulkan pesan atau amanat dari teks dengan argumentasi yang kuat

**Levels:**
- **kurang** - Pemahaman sangat terbatas, banyak kesalahan analisis
- **cukup** - Pemahaman dasar ada tetapi analisis kurang mendalam
- **baik** - Analisis tepat dengan alasan yang logis dan contoh yang relevan
- **sangat baik** - Analisis mendalam dengan sintesis ide dan pendekatan kritis

### Rubrik 2: Penulisan Kreatif (Creative Writing)
**Name:** Rubrik Penulisan Kreatif  
**Criteria:**
- **organisasi** - Struktur tulisan runtut dengan paragraf yang koheren
- **diksi** - Pemilihan kata yang tepat, variatif, dan sesuai konteks
- **ejaan_tata_bahasa** - Penggunaan EYD (Ejaan Yang Disempurnakan) dan tata bahasa yang benar
- **kreativitas** - Originalitas ide dan pengembangan gagasan yang menarik

**Levels:**
- **pemula** - Struktur belum terorganisir, banyak kesalahan EYD
- **berkembang** - Struktur cukup jelas, masih ada kesalahan minor
- **mahir** - Tulisan terstruktur baik dengan kesalahan minimal
- **unggul** - Tulisan sempurna dengan gaya bahasa yang matang dan kreatif

### Rubrik 3: Presentasi Lisan (Oral Presentation)
**Name:** Rubrik Presentasi dan Diskusi  
**Criteria:**
- **kejelasan** - Penyampaian jelas, intonasi tepat, volume suara memadai
- **struktur_argumen** - Argumen disusun logis dengan bukti atau contoh pendukung
- **bahasa_formal** - Menggunakan bahasa Indonesia formal dan santun
- **interaksi** - Merespon pertanyaan dengan relevan dan mampu berargumentasi

**Levels:**
- **dasar** - Penyampaian kurang jelas, argumen lemah
- **sedang** - Cukup jelas tetapi argumen perlu diperkuat
- **lanjut** - Jelas dan argumentatif dengan bukti yang kuat
- **mahir** - Sangat persuasif, argumentasi kuat, dan interaksi dinamis

---

## 3. Items (Soal dan Tugas)

Create items for each objective with appropriate difficulty levels:

### 3.1 Dasar (Basic) - Item Examples

#### Item 1: Struktur Teks Deskripsi (MCQ)
```php
[
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
]
```

#### Item 2: Kata Baku (MCQ)
```php
[
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
]
```

#### Item 3: Kalimat Efektif (SAQ)
```php
[
    'objective_code' => 'BI.KALIMAT.103',
    'stem' => 'Perbaiki kalimat berikut agar menjadi kalimat efektif: "Bagi yang berminat untuk mengikuti lomba karya tulis dapat mendaftar di sekretariat."',
    'type' => 'SAQ',
    'answer' => 'Yang berminat mengikuti lomba karya tulis dapat mendaftar di sekretariat.',
    'rationale' => 'Kalimat efektif menghilangkan kata "bagi" dan "untuk" yang berlebihan. Subjek "yang berminat" sudah jelas tanpa kata depan.',
    'meta' => [
        'topic' => 'Kalimat Efektif',
        'expected_length' => '1 kalimat',
        'keywords' => ['subjek', 'predikat', 'efisiensi'],
    ],
]
```

### 3.2 Menengah (Intermediate) - Item Examples

#### Item 4: Teks Argumentasi (MCQ)
```php
[
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
]
```

#### Item 5: Majas (MCQ)
```php
[
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
]
```

#### Item 6: Diskusi (SAQ)
```php
[
    'objective_code' => 'BI.DISKUSI.203',
    'stem' => 'Tuliskan pendapat Anda tentang "Apakah pembelajaran daring lebih efektif daripada pembelajaran tatap muka?" Sertakan minimal dua argumen yang mendukung pendapat Anda.',
    'type' => 'SAQ',
    'answer' => 'Pembelajaran tatap muka lebih efektif karena: 1) Interaksi langsung memungkinkan diskusi lebih mendalam, 2) Guru dapat langsung mengetahui kesulitan siswa dan memberikan bantuan.',
    'rationale' => 'Jawaban mengandung pendapat jelas dengan dua argumen yang logis dan relevan.',
    'meta' => [
        'topic' => 'Argumentasi dan Diskusi',
        'expected_length' => '3-5 kalimat',
        'keywords' => ['pendapat', 'argumen', 'bukti'],
    ],
]
```

### 3.3 Lanjut (Advanced) - Item Examples

#### Item 7: Kritik Sastra (SAQ)
```php
[
    'objective_code' => 'BI.KRITIK.301',
    'stem' => 'Bacalah cerpen "Robohnya Surau Kami" karya A.A. Navis. Analisislah tema dan amanat cerpen tersebut dengan menggunakan pendekatan struktural. Sertakan bukti dari teks.',
    'type' => 'SAQ',
    'answer' => 'Tema: Kritik terhadap kemunafikan beragama. Amanat: Agama bukan hanya ritual tetapi harus diamalkan dalam kehidupan. Bukti: Karakter Kakek yang rajin beribadah tetapi mengabaikan keadilan sosial.',
    'rationale' => 'Analisis menggunakan pendekatan struktural dengan mengidentifikasi tema, amanat, dan bukti tekstual.',
    'meta' => [
        'topic' => 'Kritik Sastra',
        'expected_length' => '1 paragraf (5-7 kalimat)',
        'keywords' => ['tema', 'amanat', 'struktural', 'bukti'],
    ],
]
```

#### Item 8: Teknik Retorika (MCQ)
```php
[
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
]
```

#### Item 9: Karya Tulis Ilmiah (SAQ)
```php
[
    'objective_code' => 'BI.PENELITIAN.303',
    'stem' => 'Buatlah kerangka karya tulis ilmiah dengan tema "Pengaruh Media Sosial terhadap Minat Baca Siswa SMA". Tuliskan: 1) Rumusan masalah, 2) Hipotesis, 3) Metode penelitian yang akan digunakan.',
    'type' => 'SAQ',
    'answer' => '1) Rumusan: Bagaimana pengaruh penggunaan media sosial terhadap minat baca siswa? 2) Hipotesis: Penggunaan media sosial berpengaruh negatif terhadap minat baca. 3) Metode: Survey kuantitatif dengan kuesioner kepada 100 siswa SMA.',
    'rationale' => 'Kerangka mencakup rumusan masalah yang jelas, hipotesis yang dapat diuji, dan metode penelitian yang sesuai.',
    'meta' => [
        'topic' => 'Karya Tulis Ilmiah',
        'expected_length' => '1 paragraf atau poin-poin',
        'keywords' => ['rumusan masalah', 'hipotesis', 'metodologi'],
    ],
]
```

---

## 4. User Data Adjustments

### 4.1 Teacher Names (Indonesian Context)
Replace with Indonesian teacher names:
```php
[
    'Ibu Dr. Siti Nurhaliza',     // Senior teacher, Literature specialist
    'Bapak Drs. Ahmad Hidayat, M.Pd.',  // Language and composition expert
]
```

### 4.2 Student Names (Indonesian Context)
Replace with common Indonesian student names:
```php
[
    'Aisyah Putri',      // Female, Kelas XII
    'Budi Santoso',      // Male, Kelas XI
    'Citra Dewi',        // Female, Kelas X
    'Dimas Prakoso',     // Male, Kelas XII
    'Eka Saputra',       // Male, Kelas XI
]
```

---

## 5. Attempt Data Adjustments

### 5.1 Response Examples
Update student responses to reflect Bahasa Indonesia content:

**For MCQ:**
- Store selected option (A, B, C, D)
- Score: 100 for correct, 0 for incorrect

**For SAQ (Dasar):**
```
"Kata baku yang benar adalah 'aktivitas' karena sesuai dengan KBBI dan tidak menggunakan huruf 'f'."
```

**For SAQ (Menengah):**
```
"Saya setuju dengan pembelajaran tatap muka karena interaksi langsung membangun komunikasi yang lebih baik. Selain itu, guru dapat langsung menilai pemahaman siswa melalui ekspresi dan respons mereka."
```

**For SAQ (Lanjut):**
```
"Tema cerpen 'Robohnya Surau Kami' adalah kritik sosial terhadap praktik keagamaan yang semu. Penulis menggunakan tokoh Kakek sebagai simbol masyarakat yang rajin beribadah tetapi mengabaikan aspek keadilan dan kepedulian sosial. Amanatnya adalah pentingnya mengamalkan nilai-nilai agama dalam kehidupan nyata, bukan sekadar ritual formal."
```

### 5.2 Metadata
Update metadata to reflect Indonesian education context:
```php
'metadata' => [
    'submitted_at' => $attemptedAt->toDateTimeString(),
    'duration_minutes' => 15, // Typical time for Indonesian language assignments
    'teacher_reviewer' => 'Ibu Dr. Siti Nurhaliza',
    'kelas' => 'XII IPA 1', // Add class information
    'semester' => 'Ganjil', // Odd/Even semester
]
```

---

## 6. Feedback Adjustments

### 6.1 AI Feedback in Indonesian
Replace English AI summaries with Indonesian:

**Dasar Level:**
```php
'ai_text' => [
    'summary' => 'Siswa menunjukkan pemahaman dasar tentang struktur teks deskripsi.',
    'strengths' => 'Mampu mengidentifikasi bagian identifikasi dengan tepat.',
    'action_steps' => [
        'Pelajari lebih lanjut tentang ciri kebahasaan teks deskripsi.',
        'Latihan menulis teks deskripsi dengan objek yang berbeda.',
    ],
]
```

**Menengah Level:**
```php
'ai_text' => [
    'summary' => 'Analisis struktur teks argumentasi cukup baik, argumen didukung dengan alasan logis.',
    'strengths' => 'Mampu mengidentifikasi tesis dan argumentasi dengan tepat.',
    'action_steps' => [
        'Perkuat argumentasi dengan menambahkan data atau statistik pendukung.',
        'Pelajari berbagai jenis majas untuk memperkaya gaya bahasa.',
    ],
]
```

**Lanjut Level:**
```php
'ai_text' => [
    'summary' => 'Kritik sastra menunjukkan analisis mendalam dengan pendekatan struktural yang tepat.',
    'strengths' => 'Menggunakan bukti tekstual yang kuat dan analisis yang sistematis.',
    'action_steps' => [
        'Eksplorasi pendekatan kritik lain seperti sosiologi sastra atau psikologi.',
        'Bandingkan tema cerpen ini dengan karya sastra Indonesia lainnya.',
    ],
]
```

### 6.2 Teacher Revision Examples
```php
'human_revision' => 'Sudah bagus! Coba tambahkan contoh konkret dari teks untuk memperkuat analisis Anda. - Ibu Siti'
```

---

## 7. Mastery Levels for Bahasa Indonesia

Adjust mastery levels to reflect Indonesian language proficiency:

| Level | Score Range | Description (Indonesian Context) |
|-------|-------------|----------------------------------|
| **pemula** (emerging) | 0-40 | Pemahaman dasar sangat terbatas, perlu bimbingan intensif |
| **berkembang** (developing) | 41-60 | Mulai memahami konsep tetapi masih banyak kesalahan |
| **cukup** (competent) | 61-75 | Pemahaman cukup baik, dapat mengerjakan tugas standar |
| **baik** (proficient) | 76-85 | Pemahaman baik, dapat menganalisis dan menerapkan konsep |
| **sangat baik** (strong) | 86-93 | Pemahaman mendalam dengan kemampuan sintesis yang baik |
| **mahir** (mastery) | 94-100 | Penguasaan sempurna dengan kemampuan berpikir kritis tinggi |

---

## 8. Recommendation Adjustments

Update recommendations to reflect Bahasa Indonesia learning paths:

**Example 1 (Dasar):**
```php
'payload' => [
    'focus_area' => 'Perkuat pemahaman struktur teks deskripsi',
    'suggested_resource' => 'Baca buku "Bahasa Indonesia untuk SMA/MA Kelas X" halaman 15-30',
    'next_check_in' => Carbon::now()->addDays(3)->toDateString(),
]
```

**Example 2 (Menengah):**
```php
'payload' => [
    'focus_area' => 'Tingkatkan kemampuan argumentasi dalam diskusi',
    'suggested_resource' => 'Tonton video tentang teknik debat dan diskusi formal di portal Rumah Belajar Kemdikbud',
    'next_check_in' => Carbon::now()->addDays(5)->toDateString(),
]
```

**Example 3 (Lanjut):**
```php
'payload' => [
    'focus_area' => 'Dalami analisis kritik sastra dengan berbagai pendekatan',
    'suggested_resource' => 'Baca jurnal kritik sastra Indonesia dan analisis karya Pramoedya Ananta Toer',
    'next_check_in' => Carbon::now()->addWeek()->toDateString(),
]
```

---

## 9. Quiz Eligibility

Mark items as `is_quiz_eligible` based on type:
- **MCQ**: All eligible for quick quizzes
- **SAQ (Dasar & Menengah)**: Eligible for short quizzes (5-10 minutes)
- **SAQ (Lanjut)**: Some eligible, but may require more time (essay-type)

---

## 10. Implementation Checklist

### Phase 1: Content Preparation
- [ ] Create 15-20 learning objectives (5-7 per difficulty level)
- [ ] Design 3 rubrics tailored to Bahasa Indonesia assessment
- [ ] Prepare 30-50 items (mix of MCQ and SAQ)
  - [ ] 15 items for Dasar level
  - [ ] 15 items for Menengah level
  - [ ] 15 items for Lanjut level
- [ ] Write Indonesian AI feedback templates

### Phase 2: Seeder Modification
- [ ] Create new file: `database/seeders/BahasaIndonesiaDemoSeeder.php`
- [ ] Copy structure from `BiologyDemoSeeder.php`
- [ ] Replace all content with Bahasa Indonesia materials
- [ ] Update user names to Indonesian context
- [ ] Adjust metadata fields (add 'kelas', 'semester')

### Phase 3: Testing
- [ ] Run fresh migration: `php artisan migrate:fresh`
- [ ] Seed database: `php artisan db:seed --class=BahasaIndonesiaDemoSeeder`
- [ ] Verify all objectives appear correctly
- [ ] Test quiz generation with different difficulty levels
- [ ] Verify mastery scoring works with Indonesian content

### Phase 4: Integration
- [ ] Update frontend labels to support Indonesian (if needed)
- [ ] Test quiz flow with Bahasa Indonesia items
- [ ] Verify teacher review workflow with SAQ items
- [ ] Test export functionality with Indonesian characters (UTF-8)

---

## 11. Sample Data Distribution

### Items per Objective
- **Dasar (3 objectives × 5 items)**: 15 items
- **Menengah (3 objectives × 5 items)**: 15 items  
- **Lanjut (3 objectives × 5 items)**: 15 items
- **Total**: 45 items

### Item Type Distribution
- **MCQ**: 60% (27 items) - Quick assessment
- **SAQ**: 40% (18 items) - Deeper understanding

### Difficulty Distribution per Level
- **Dasar**: 70% easy, 30% medium
- **Menengah**: 20% easy, 60% medium, 20% hard
- **Lanjut**: 10% medium, 70% hard, 20% very hard

---

## 12. Indonesian Education Context

### Alignment with Kurikulum Merdeka
- Focus on competency-based learning (Capaian Pembelajaran)
- Emphasize critical thinking and literacy skills
- Include project-based assessments (tugas proyek)
- Support differentiated learning paths

### Cultural Adaptations
- Use Indonesian literature references (Chairil Anwar, Pramoedya, etc.)
- Include local context examples (NKRI, Pancasila, etc.)
- Reference Indonesian media and social issues
- Use formal Indonesian language register (bahasa baku)

### Assessment Standards
- Follow Kriteria Ketuntasan Minimal (KKM) typically 70-75
- Use 0-100 scale aligned with Indonesian grading system
- Include both individual and group assessments
- Emphasize process as well as product

---

## 13. Future Enhancements

### Content Expansion
- Add poetry analysis (puisi)
- Include traditional literature (sastra klasik)
- Add modern literature genres (cerpen, novel kontemporer)
- Include debate and public speaking assessments

### Adaptive Features
- Difficulty adjustment based on student performance
- Personalized learning paths by genre preference
- Integration with UNBK/AKM question formats
- Support for regional language integration (bahasa daerah)

### Collaboration Features
- Peer review for creative writing
- Group discussion forums
- Collaborative story writing
- Class-wide literature circles

---

## Conclusion

This plan provides a comprehensive framework to adapt the demo seeder for Bahasa Indonesia content suitable for Indonesian senior high schools. The three-tiered difficulty system (Dasar, Menengah, Lanjut) ensures appropriate challenge levels for students across different grades and abilities while maintaining alignment with the national curriculum.

The implementation maintains the technical structure of the existing seeder while ensuring all content is culturally and educationally relevant to the Indonesian context.

---

## Implementation Status

### ✅ Completed (November 1, 2025)

The `BahasaIndonesiaDemoSeeder` has been successfully implemented with the following:

**Database Seeded:**
- 8 users (1 admin, 2 teachers, 5 students) with Indonesian names
- 9 learning objectives (3 per difficulty level)
- 3 rubrics (Analisis Teks, Penulisan Kreatif, Presentasi Lisan)
- 15 items (5 per difficulty level: 2 for BI.TEKS, 2 for BI.KATA, 1 for BI.KALIMAT, 2 for BI.ARGUMEN, 2 for BI.MAJAS, 1 for BI.DISKUSI, 2 for BI.KRITIK, 2 for BI.RETORIKA, 1 for BI.PENELITIAN)
- 75 attempts (5 students × 15 items)
- 75 feedback entries (with Indonesian language feedback)
- 45 mastery records (5 students × 9 objectives)
- 10 recommendations

**Files Created:**
- `database/seeders/BahasaIndonesiaDemoSeeder.php`
- Updated `database/seeders/DatabaseSeeder.php` to use BahasaIndonesiaDemoSeeder

**User Credentials:**
- Admin: `admin@sekolah.sch.id` / `password`
- Teachers: `siti.nurhaliza@sekolah.sch.id`, `ahmad.hidayat@sekolah.sch.id` / `password`
- Students: `aisyah.putri@siswa.sch.id`, `budi.santoso@siswa.sch.id`, etc. / `password`

**To run the seeder:**
```bash
php artisan migrate:fresh --seed
```

**Verified Data:**
- All learning objectives use Indonesian language competencies
- Items include both MCQ and SAQ types
- Feedback uses Indonesian language templates
- User names are culturally appropriate (Aisyah, Budi, Citra, Dimas, Eka)
- Email domains use Indonesian school format (@sekolah.sch.id, @siswa.sch.id)
- Metadata includes Indonesian school context (kelas X/XI/XII IPA, semester Ganjil)

