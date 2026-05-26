# Symfony 6.4 + Doctrine ORM: relationships guide (attributes)
## entity names
- filieres -> Filiere
- parcours -> Parcours
- countries -> Country
- cities -> City
- insatien -> Insatien
- users -> User
- jobs -> Job
- recommandations -> Recommendation
- experience -> Experience
- skills -> Skill
- user_skills -> Many-to-many User <-> Skill
- projects -> Project
- project_skills -> Many-to-many Project <-> Skill
- achievements -> Achievement
- posts -> Post
- contact_messages -> ContactMessage

## Relationship mapping (detailed)

### Filiere <-> Parcours
- Parcours has filiere_id (nullable)
- Doctrine:
  - Filiere: OneToMany to Parcours (mappedBy: filiere)
  - Parcours: ManyToOne to Filiere (inversedBy: parcours)
  - JoinColumn: onDelete SET NULL, nullable true

### Country <-> City
- City has country_id (not null)
- Doctrine:
  - Country: OneToMany to City
  - City: ManyToOne to Country
  - JoinColumn: onDelete CASCADE, nullable false

### Parcours <-> Insatien
- Insatien has parcours_id (nullable)
- Doctrine:
  - Parcours: OneToMany to Insatien
  - Insatien: ManyToOne to Parcours
  - JoinColumn: onDelete SET NULL, nullable true

### Insatien <-> User (one-to-one)
- User has insatien_id (unique)
- Doctrine:
  - User: OneToOne to Insatien
  - Insatien: OneToOne mappedBy user
  - JoinColumn: onDelete CASCADE

### User <-> Job (creator)
- Job has created_by (nullable)
- Doctrine:
  - User: OneToMany to Job (createdBy)
  - Job: ManyToOne to User (createdBy)
  - JoinColumn: onDelete SET NULL, nullable true

### Job <-> Country / City
- Job has country_id, city_id (nullable)
- Doctrine:
  - Job: ManyToOne to Country, ManyToOne to City
  - JoinColumn: onDelete SET NULL

### Recommendation (from_user, to_user)
- Recommendation has two FKs to users
- Doctrine:
  - Recommendation: ManyToOne fromUser, ManyToOne toUser
  - User: OneToMany sentRecommendations, OneToMany receivedRecommendations
  - JoinColumn: onDelete CASCADE
  - Add validation to prevent fromUser == toUser (app-level)

### User <-> Experience
- Experience has user_id
- Doctrine:
  - User: OneToMany to Experience
  - Experience: ManyToOne to User
  - JoinColumn: onDelete CASCADE

### User <-> Skill (many-to-many)
- Join table: user_skills
- Doctrine:
  - User: ManyToMany to Skill, JoinTable user_skills
  - Skill: ManyToMany mappedBy users

### User <-> Project
- Project has user_id
- Doctrine:
  - User: OneToMany to Project
  - Project: ManyToOne to User
  - JoinColumn: onDelete CASCADE

### Project <-> Skill (many-to-many)
- Join table: project_skills
- Doctrine:
  - Project: ManyToMany to Skill, JoinTable project_skills
  - Skill: ManyToMany mappedBy projects

### User <-> Achievement
- Achievement has user_id
- Doctrine:
  - User: OneToMany to Achievement
  - Achievement: ManyToOne to User
  - JoinColumn: onDelete CASCADE

### User <-> Post
- Post has user_id
- Doctrine:
  - User: OneToMany to Post
  - Post: ManyToOne to User
  - JoinColumn: onDelete CASCADE

## Enum fields
- jobs.job_type: part-time | full-time | internship
- jobs.job_mode: remote | onsite | hybrid
- experience.experience_type: skill | job | certification
- achievements.achievement_type: award | honour | publication | competition | other

Recommendation: use PHP enums and map them as string columns.

## Timestamp fields
- Use DateTimeImmutable in entities
- Map to datetime_immutable

## Indexes and unique constraints
- Keep unique: users.email, insatien.email, skills.name, countries.name, cities (country_id + name)
- Remove unique: jobs.contact_email

## Generation workflow (suggested)
1) Create Symfony 6.4 project
2) Configure Doctrine DB connection
3) Create entities manually with attributes based on this guide
4) Run doctrine:migrations:diff to generate migration
5) Apply migration and import data if needed
