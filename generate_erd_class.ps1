$erdCode = @"
@startuml
skinparam BackgroundColor white
skinparam RoundCorner 10
skinparam DefaultFontName Arial
skinparam Entity {
  BackgroundColor #e1f5fe
  BorderColor #0288d1
}

entity "User" as user {
  * id : bigint
  --
  name : varchar
  email : varchar
  password : varchar
  role : enum (admin, teacher, student)
}

entity "Student" as student {
  * id : bigint
  --
  user_id : bigint <<FK>>
  classroom_id : bigint <<FK>>
  nisn : varchar
  phone : varchar
}

entity "Teacher" as teacher {
  * id : bigint
  --
  user_id : bigint <<FK>>
  nip : varchar
  phone : varchar
}

entity "Classroom" as class {
  * id : bigint
  --
  major_id : bigint <<FK>>
  name : varchar
  grade : int
}

entity "Major" as major {
  * id : bigint
  --
  name : varchar
}

entity "Subject" as subject {
  * id : bigint
  --
  name : varchar
}

entity "Schedule" as schedule {
  * id : bigint
  --
  classroom_id : bigint <<FK>>
  subject_id : bigint <<FK>>
  teacher_id : bigint <<FK>>
  day : varchar
  start_time : time
  end_time : time
}

entity "Material" as material {
  * id : bigint
  --
  schedule_id : bigint <<FK>>
  title : varchar
  file_path : varchar
}

entity "Assignment" as assignment {
  * id : bigint
  --
  schedule_id : bigint <<FK>>
  title : varchar
  due_date : datetime
}

entity "Submission" as submission {
  * id : bigint
  --
  assignment_id : bigint <<FK>>
  student_id : bigint <<FK>>
  file_path : varchar
  grade : float
}

entity "Bill" as bill {
  * id : bigint
  --
  student_id : bigint <<FK>>
  amount : decimal
  status : enum (unpaid, paid)
}

entity "Transaction" as transaction {
  * id : bigint
  --
  bill_id : bigint <<FK>>
  order_id : varchar
  payment_type : varchar
  status : varchar
}

user ||--o| student
user ||--o| teacher
major ||--o{ class
class ||--o{ student
class ||--o{ schedule
subject ||--o{ schedule
teacher ||--o{ schedule
schedule ||--o{ material
schedule ||--o{ assignment
assignment ||--o{ submission
student ||--o{ submission
student ||--o{ bill
bill ||--o{ transaction
@enduml
"@

$classCode = @"
@startuml
skinparam BackgroundColor white
skinparam ClassBackgroundColor #e8f3fa
skinparam ClassBorderColor #1c4a6b
skinparam ArrowColor #1c4a6b
skinparam DefaultFontName Arial

class User {
  +id: int
  +name: string
  +role: string
  +student(): HasOne
  +teacher(): HasOne
}

class Student {
  +user_id: int
  +classroom_id: int
  +user(): BelongsTo
  +classroom(): BelongsTo
  +bills(): HasMany
  +submissions(): HasMany
}

class Teacher {
  +user_id: int
  +user(): BelongsTo
  +schedules(): HasMany
}

class Classroom {
  +major_id: int
  +students(): HasMany
  +schedules(): HasMany
}

class Schedule {
  +classroom_id: int
  +teacher_id: int
  +subject_id: int
  +materials(): HasMany
  +assignments(): HasMany
}

class Material {
  +schedule_id: int
  +title: string
}

class Assignment {
  +schedule_id: int
  +submissions(): HasMany
}

class Submission {
  +assignment_id: int
  +student_id: int
  +grade: float
}

class Bill {
  +student_id: int
  +amount: decimal
  +status: string
  +transactions(): HasMany
}

class Transaction {
  +bill_id: int
  +status: string
}

User "1" -- "0..1" Student
User "1" -- "0..1" Teacher
Classroom "1" -- "*" Student
Classroom "1" -- "*" Schedule
Teacher "1" -- "*" Schedule
Schedule "1" -- "*" Material
Schedule "1" -- "*" Assignment
Assignment "1" -- "*" Submission
Student "1" -- "*" Submission
Student "1" -- "*" Bill
Bill "1" -- "*" Transaction
@enduml
"@

[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

Write-Host "Generating ERD..."
try {
    Invoke-WebRequest -Uri "https://kroki.io/plantuml/png" -Method Post -Body $erdCode -ContentType "text/plain" -OutFile "c:\web\elearning\diagram\erd_database.png" -ErrorAction Stop
    Write-Host "Success ERD!"
} catch {
    Write-Host "Error generating ERD: $_"
}

Start-Sleep -Seconds 2

Write-Host "Generating Class Diagram..."
try {
    Invoke-WebRequest -Uri "https://kroki.io/plantuml/png" -Method Post -Body $classCode -ContentType "text/plain" -OutFile "c:\web\elearning\diagram\class_diagram.png" -ErrorAction Stop
    Write-Host "Success Class Diagram!"
} catch {
    Write-Host "Error generating Class Diagram: $_"
}
Write-Host "Done!"
