# Graph Report - .  (2026-08-15)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 376 nodes · 640 edges · 65 communities (56 shown, 9 thin omitted)
- Extraction: 85% EXTRACTED · 15% INFERRED · 0% AMBIGUOUS · INFERRED: 97 edges (avg confidence: 0.8)
- Token cost: 1,689 input · 622 output

## Community Hubs (Navigation)
- Composer Configuration
- Authentication Controllers
- Activity and Assignment Management
- Core Models and Posters
- Assessment and Group Management
- Composer Scripts
- Frontend Dependencies
- System Settings and Factories
- App Prototype and Gallery
- Document Management
- Route Middleware
- App Service Providers
- Feature Testing
- Unit Testing
- Laravel Framework Core
- About Section Assets
- Hero Section Assets
- Primary Logo
- Text Logo
- Robots Configuration

## God Nodes (most connected - your core abstractions)
1. `ScfProgram` - 30 edges
2. `User` - 28 edges
3. `Controller` - 26 edges
4. `ScfGroup` - 21 edges
5. `ScfAssessment` - 17 edges
6. `ScfActivityLog` - 15 edges
7. `ScfSetting` - 14 edges
8. `ScfDocument` - 12 edges
9. `ScfPoster` - 12 edges
10. `ScfAssignment` - 9 edges

## Surprising Connections (you probably didn't know these)
- `Gallery Image 1 - Food Presentation` --conceptually_related_to--> `Science Food Festival (SFF)`  [INFERRED]
  public/foto/gallery-1.jpg → sinfo_prototype.html
- `Gallery Image 2 - Students with Food` --conceptually_related_to--> `Science Food Festival (SFF)`  [INFERRED]
  public/foto/gallery-2.jpg → sinfo_prototype.html
- `Gallery Image 4 - Group Presentation` --conceptually_related_to--> `Science Food Festival (SFF)`  [INFERRED]
  public/foto/gallery-4.jpg → sinfo_prototype.html
- `Gallery Image 5 - Group Photo` --conceptually_related_to--> `Science Food Festival (SFF)`  [INFERRED]
  public/foto/gallery-5.jpg → sinfo_prototype.html
- `Gallery Image 3 - Assessment Process` --conceptually_related_to--> `SINFO Assessment Modal`  [INFERRED]
  public/foto/gallery-3.jpg → sinfo_prototype.html

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **SINFO UI Components** — sinfo_login_view, sinfo_app_shell, sinfo_assessment_modal [EXTRACTED 1.00]
- **SFF Event Documentation** — public_foto_gallery_1_jpg, public_foto_gallery_2_jpg, public_foto_gallery_3_jpg, public_foto_gallery_4_jpg, public_foto_gallery_5_jpg [INFERRED 0.90]

## Communities (65 total, 9 thin omitted)

### Community 0 - "Composer Configuration"
Cohesion: 0.05
Nodes (42): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+34 more)

### Community 1 - "Authentication Controllers"
Cohesion: 0.17
Nodes (11): LoginController, LogoutController, Controller, DashboardController, DashboardController, KepsekController, DashboardController, PosterController (+3 more)

### Community 2 - "Activity and Assignment Management"
Cohesion: 0.07
Nodes (12): AssignmentController, DashboardController, ScfActivityLog, ScfAssignment, User, DatabaseSeeder, ScfSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents (+4 more)

### Community 3 - "Core Models and Posters"
Cohesion: 0.09
Nodes (9): Guru, Kelas, Role, ScfAssessmentDetail, ScfGroupMember, ScfPoster, Siswa, Illuminate\Database\Eloquent\Model (+1 more)

### Community 4 - "Assessment and Group Management"
Cohesion: 0.12
Nodes (7): AssessmentController, GroupController, AssessmentController, ScfAssessment, ScfGroup, ScfProgram, Illuminate\Database\Eloquent\Relations\HasMany

### Community 5 - "Composer Scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 6 - "Frontend Dependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 7 - "System Settings and Factories"
Cohesion: 0.19
Nodes (5): ScfSetting, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, self, static

### Community 8 - "App Prototype and Gallery"
Cohesion: 0.18
Nodes (11): Gallery Image 1 - Food Presentation, Gallery Image 2 - Students with Food, Gallery Image 3 - Assessment Process, Gallery Image 4 - Group Presentation, Gallery Image 5 - Group Photo, Science Food Festival (SFF), SINFO Main App Shell, SINFO Assessment Modal (+3 more)

### Community 10 - "Route Middleware"
Cohesion: 0.42
Nodes (4): RoleMiddleware, SystemOpenMiddleware, Closure, Symfony\Component\HttpFoundation\Response

### Community 12 - "Feature Testing"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Testing\TestCase, ExampleTest, TestCase

## Knowledge Gaps
- **71 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+66 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **9 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `Activity and Assignment Management` to `Authentication Controllers`, `Core Models and Posters`, `Assessment and Group Management`, `Document Management`?**
  _High betweenness centrality (0.041) - this node is a cross-community bridge._
- **Why does `ScfProgram` connect `Assessment and Group Management` to `Authentication Controllers`, `Activity and Assignment Management`, `Core Models and Posters`, `System Settings and Factories`, `Document Management`, `Route Middleware`?**
  _High betweenness centrality (0.033) - this node is a cross-community bridge._
- **Why does `scripts` connect `Composer Scripts` to `Composer Configuration`?**
  _High betweenness centrality (0.019) - this node is a cross-community bridge._
- **Are the 22 inferred relationships involving `ScfProgram` (e.g. with `.index()` and `.store()`) actually correct?**
  _`ScfProgram` has 22 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `User` (e.g. with `.login()` and `.store()`) actually correct?**
  _`User` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _71 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Composer Configuration` be split into smaller, more focused modules?**
  _Cohesion score 0.046511627906976744 - nodes in this community are weakly interconnected._