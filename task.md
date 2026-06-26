# Implementasi E-Learning

## Tahap A: Database, Models, Guru
- `[x]` Buat migrasi (teachers, subjects, materials, assignments, submissions)
- `[x]` Buat models (Teacher, Subject, Material, Assignment, Submission)
- `[x]` Update User model (relasi teacher)
- `[x]` Update LoginController (redirect teacher)
- `[x]` Update RoleMiddleware (fallback teacher)
- `[x]` Buat Admin\TeacherController (CRUD)
- `[x]` Buat admin teacher views (index, create, edit)
- `[x]` Update sidebar (menu guru)
- `[x]` Tambah routes
- `[x]` Buat Admin\SubjectController (CRUD)
- `[x]` Buat admin subject views (index, create, edit)

## Tahap B: Portal Guru
- `[x]` Buat Teacher\DashboardController
- `[x]` Buat Teacher\SubjectController
- `[x]` Buat Teacher\MaterialController
- `[x]` Buat Teacher\AssignmentController
- `[x]` Buat teacher views (dashboard, subjects, materials, assignments)
- `[x]` Update sidebar (menu guru portal)

## Tahap C: Portal Siswa E-Learning
- `[x]` Buat Student\SubjectController
- `[x]` Buat Student\MaterialController
- `[x]` Buat Student\AssignmentController
- `[x]` Buat student e-learning views
- `[x]` Buat Teacher\SubmissionController (penilaian)
- `[x]` Update sidebar siswa (menu e-learning)
