# Graph Report - SDA-Kas-Web  (2026-10-02)

## Corpus Check
- 126 files · ~113,291 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 24 file(s) not represented in the graph (top: (none) 18, .example 1, .conf 1)

## Summary
- 509 nodes · 1009 edges · 57 communities (17 shown, 40 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Eloquent Models & Schema
- HTTP Controllers & Routing
- Database Migrations & Seeders
- HTTP Controllers & Routing
- HTTP Controllers & Routing
- Eloquent Models & Schema
- Eloquent Models & Schema
- HTTP Controllers & Routing
- Views & Presentation Layer
- Module 9
- Module 10
- Module 11
- Business Services & Calculations
- HTTP Controllers & Routing
- Database Migrations & Seeders
- Module 16
- Test Suite & Quality Assurance
- Test Suite & Quality Assurance
- Module 22
- Module 23
- Views & Presentation Layer
- Views & Presentation Layer
- Eloquent Models & Schema
- Eloquent Models & Schema
- Eloquent Models & Schema
- Eloquent Models & Schema
- Eloquent Models & Schema
- Eloquent Models & Schema
- Eloquent Models & Schema
- Eloquent Models & Schema
- Eloquent Models & Schema
- Eloquent Models & Schema
- Eloquent Models & Schema
- Eloquent Models & Schema
- Eloquent Models & Schema
- Eloquent Models & Schema
- Eloquent Models & Schema
- Eloquent Models & Schema
- Views & Presentation Layer
- Views & Presentation Layer
- Views & Presentation Layer
- Views & Presentation Layer

## God Nodes (most connected - your core abstractions)
1. `Group` - 65 edges
2. `User` - 60 edges
3. `Period` - 58 edges
4. `Member` - 33 edges
5. `Income` - 27 edges
6. `Expense` - 24 edges
7. `ExpenseCategory` - 17 edges
8. `TestCase` - 16 edges
9. `ArrearsService` - 15 edges
10. `Controller` - 14 edges

## Surprising Connections (you probably didn't know these)
- `{closure#1}()` --references--> `User`  [EXTRACTED]
  app/Providers/AppServiceProvider.php → app/Models/User.php
- `AutomationController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Api/AutomationController.php → app/Http/Controllers/Controller.php
- `DashboardController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/DashboardController.php → app/Http/Controllers/Controller.php
- `IncomeController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/IncomeController.php → app/Http/Controllers/Controller.php
- `MemberController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/MemberController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (57 total, 40 thin omitted)

### Community 0 - "Eloquent Models & Schema"
Cohesion: 0.06
Nodes (11): Group, User, PeriodFactory, AdminCrudTest, ArrearsCalculationTest, AuthTest, AutomationApiTest, ExampleTest (+3 more)

### Community 1 - "HTTP Controllers & Routing"
Cohesion: 0.06
Nodes (8): AuthController, Controller, ExpenseCategoryController, ExpenseController, GroupController, PeriodController, UserController, ExpenseCategory

### Community 2 - "Database Migrations & Seeders"
Cohesion: 0.07
Nodes (16): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}() (+8 more)

### Community 3 - "HTTP Controllers & Routing"
Cohesion: 0.08
Nodes (7): AutomationController, DashboardController, MemberController, PublicKasController, Member, ArrearsService, GET /me()

### Community 4 - "HTTP Controllers & Routing"
Cohesion: 0.06
Nodes (3): IncomeController, ReportController, Period

### Community 5 - "Eloquent Models & Schema"
Cohesion: 0.09
Nodes (4): Expense, ExpenseAttachment, Income, DatabaseSeeder

### Community 6 - "Eloquent Models & Schema"
Cohesion: 0.09
Nodes (6): ExpenseCategoryFactory, ExpenseFactory, GroupFactory, IncomeFactory, MemberFactory, UserFactory

### Community 7 - "HTTP Controllers & Routing"
Cohesion: 0.15
Nodes (5): EnsureAdminRole, EnsureSuperAdminApi, {closure#1}(), {closure#2}(), {closure#3}()

### Community 8 - "Views & Presentation Layer"
Cohesion: 0.20
Nodes (9): background_color, description, display, icons, name, orientation, short_name, start_url (+1 more)

### Community 9 - "Module 9"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 10 - "Module 10"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/boost, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 11 - "Module 11"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 14 - "HTTP Controllers & Routing"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 15 - "Database Migrations & Seeders"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 16 - "Module 16"
Cohesion: 0.40
Nodes (5): require, laravel/framework, laravel/sanctum, laravel/tinker, php

### Community 21 - "Test Suite & Quality Assurance"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 22 - "Module 22"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

## Knowledge Gaps
- **70 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+65 more)
  These have ≤1 connection - possible missing edges. (Counts symbols only; 213 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **40 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `Eloquent Models & Schema` to `HTTP Controllers & Routing`, `Business Services & Calculations`, `Eloquent Models & Schema`, `Eloquent Models & Schema`?**
  _High betweenness centrality (0.097) - this node is a cross-community bridge._
- **Why does `Group` connect `Eloquent Models & Schema` to `HTTP Controllers & Routing`, `HTTP Controllers & Routing`, `HTTP Controllers & Routing`, `Eloquent Models & Schema`, `Eloquent Models & Schema`, `HTTP Controllers & Routing`?**
  _High betweenness centrality (0.086) - this node is a cross-community bridge._
- **Why does `Period` connect `HTTP Controllers & Routing` to `Eloquent Models & Schema`, `HTTP Controllers & Routing`, `HTTP Controllers & Routing`, `Eloquent Models & Schema`, `Eloquent Models & Schema`, `HTTP Controllers & Routing`?**
  _High betweenness centrality (0.069) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _70 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Eloquent Models & Schema` be split into smaller, more focused modules?**
  _Cohesion score 0.06057945566286216 - nodes in this community are weakly interconnected._
- **Should `HTTP Controllers & Routing` be split into smaller, more focused modules?**
  _Cohesion score 0.06451612903225806 - nodes in this community are weakly interconnected._
- **Should `Database Migrations & Seeders` be split into smaller, more focused modules?**
  _Cohesion score 0.06561085972850679 - nodes in this community are weakly interconnected._