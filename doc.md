A) Caso de Uso em PlantUML (cola e exporta)
Use em: https://www.plantuml.com/plantuml/ ou https://www.planttext.com/

------------------------------

@startuml
left to right direction
actor "Usuário" as User

rectangle "Sistema de Estoque de Ferramentas" {
  usecase "Register account" as UC1
  usecase "Login" as UC2
  usecase "Logout" as UC3
  usecase "Create tool" as UC4
  usecase "Edit tool" as UC5
  usecase "Delete tool" as UC6
  usecase "List tools / View dashboard" as UC7
  usecase "Create stock move (IN/OUT)" as UC8
  usecase "View stock move history" as UC9
}

User --> UC1
User --> UC2
User --> UC3
User --> UC4
User --> UC5
User --> UC6
User --> UC7
User --> UC8
User --> UC9
@enduml

-------------------------

B) DER em dbdiagram.io (gera ERD automaticamente)
Cole em: https://dbdiagram.io/

-------------------------

Table users {
  id int [pk, increment]
  name varchar [not null]
  email varchar [not null, unique]
  password_hash varchar [not null]
  status boolean [not null, default: true]
  created_at datetime [not null]
}

Table tools {
  id int [pk, increment]
  name varchar [not null]
  category varchar [not null]
  unit varchar [not null]
  min_stock int [not null]
  status boolean [not null, default: true]
  created_at datetime [not null]
}

Table stock_moves {
  id int [pk, increment]
  tool_id int [not null]
  user_id int [not null]
  move_type varchar [not null]
  quantity int [not null]
  note varchar
  moved_at datetime [not null]
}

Ref: stock_moves.user_id > users.id
Ref: stock_moves.tool_id > tools.id

-------------------------