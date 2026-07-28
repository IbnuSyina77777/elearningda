$erdFullCode = @"
@startuml
skinparam BackgroundColor white
skinparam RoundCorner 10
skinparam DefaultFontName Arial
skinparam DefaultFontSize 12
skinparam Shadowing false
skinparam Entity {
  BackgroundColor #e1f5fe
  BorderColor #0288d1
  BorderThickness 2
}

entity "users" as user {
  * **id** : BigInt <<PK>>
  --
  name : Varchar(255)
  email : Varchar(255) <<Unique>>
  password : Varchar(255)
  role : Enum (admin, teacher, student)
  created_at : Timestamp
  updated_at : Timestamp
}

entity "students" as student {
  * **id** : BigInt <<PK>>
  --
  # user_id : BigInt <<FK>>
  # classroom_id : BigInt <<FK>>
  nisn : Varchar(50)
  phone : Varchar(20)
}

entity "teachers" as teacher {
  * **id** : BigInt <<PK>>
  --
  # user_id : BigInt <<FK>>
  nip : Varchar(50)
  phone : Varchar(20)
}

entity "majors" as major {
  * **id** : BigInt <<PK>>
  --
  name : Varchar(100)
}

entity "classrooms" as classroom {
  * **id** : BigInt <<PK>>
  --
  # major_id : BigInt <<FK>>
  name : Varchar(100)
  grade : Integer
}

entity "subjects" as subject {
  * **id** : BigInt <<PK>>
  --
  name : Varchar(150)
}

entity "schedules" as schedule {
  * **id** : BigInt <<PK>>
  --
  # classroom_id : BigInt <<FK>>
  # subject_id : BigInt <<FK>>
  # teacher_id : BigInt <<FK>>
  day : Varchar(20)
  start_time : Time
  end_time : Time
}

entity "materials" as material {
  * **id** : BigInt <<PK>>
  --
  # schedule_id : BigInt <<FK>>
  title : Varchar(255)
  file_path : Varchar(255)
}

entity "assignments" as assignment {
  * **id** : BigInt <<PK>>
  --
  # schedule_id : BigInt <<FK>>
  title : Varchar(255)
  due_date : DateTime
}

entity "submissions" as submission {
  * **id** : BigInt <<PK>>
  --
  # assignment_id : BigInt <<FK>>
  # student_id : BigInt <<FK>>
  file_path : Varchar(255)
  grade : Float
}

entity "bills" as bill {
  * **id** : BigInt <<PK>>
  --
  # student_id : BigInt <<FK>>
  amount : Decimal(15,2)
  status : Enum (unpaid, paid)
}

entity "transactions" as transaction {
  * **id** : BigInt <<PK>>
  --
  # bill_id : BigInt <<FK>>
  order_id : Varchar(100)
  payment_type : Varchar(50)
  status : Varchar(50)
}

user ||--o| student : "1:1"
user ||--o| teacher : "1:1"
major ||--o{ classroom : "1:N"
classroom ||--o{ student : "1:N"
classroom ||--o{ schedule : "1:N"
subject ||--o{ schedule : "1:N"
teacher ||--o{ schedule : "1:N"
schedule ||--o{ material : "1:N"
schedule ||--o{ assignment : "1:N"
assignment ||--o{ submission : "1:N"
student ||--o{ submission : "1:N"
student ||--o{ bill : "1:N"
bill ||--o{ transaction : "1:N"
@enduml
"@

[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

Write-Host "Generating ERD (Full Detail)..."
try {
    Invoke-WebRequest -Uri "https://kroki.io/plantuml/png" -Method Post -Body $erdFullCode -ContentType "text/plain" -OutFile "c:\web\elearning\diagram\erd_database_full.png" -ErrorAction Stop
    Write-Host "Success!"
} catch {
    Write-Host "Error: $_"
}
Write-Host "Done!"
