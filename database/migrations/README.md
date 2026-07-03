# Migrations

**21 migrations** — full JMC attendance schema on `php artisan migrate` or `migrate:fresh`.

| # | File | Tables |
|---|------|--------|
| 000001 | `create_users_table` | `users` |
| 000002 | `create_password_reset_tokens_table` | `password_reset_tokens` |
| 000003 | `create_sessions_table` | `sessions` |
| 000004 | `create_cache_table` | `cache`, `cache_locks` |
| 000005 | `create_jobs_table` | `jobs`, `job_batches`, `failed_jobs` |
| 000006 | `create_roles_table` | `roles` |
| 000007 | `create_programs_table` | `programs` |
| 000008 | `create_program_years_and_courses_tables` | `program_years`, `program_courses` |
| 000009 | `create_students_table` | `students` (incl. RFID, face, section, sex) |
| 000010 | `create_pending_students_table` | `pending_students` |
| 000011 | `create_employees_table` | `employees` |
| 000012 | `create_pending_employees_table` | `pending_employees` |
| 000013 | `create_attendance_logs_table` | `attendance_logs` |
| 000014 | `create_attendance_feedback_table` | `attendance_feedback` |
| 000015 | `create_settings_table` | `settings` |
| 000016 | `create_grade_sections_table` | `grade_sections` |
| 000017 | `create_school_strands_table` | `school_strands` |
| 000018 | `create_sf2_reports_tables` | `sf2_reports`, `sf2_report_students` |
| 000019 | `create_visitors_table` | `visitors` |
| 000020 | `create_visitor_logs_table` | `visitor_logs` |
| 000021 | `create_files_table` | `files` |

Legacy library tables (books, ebooks, rooms, prospectus) are **not** included.

## Local setup

```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=AdminUserSeeder
```

Use `migrate:fresh` only on a new or throwaway database.
