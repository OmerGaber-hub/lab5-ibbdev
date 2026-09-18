# 🚀 IBBDev — منصة تبادل الخبرات البرمجية

منصة تفاعلية لطلاب علوم الحاسوب مبنية بـ Laravel 13 + Blade
**Blade · Service Layer · SOLID · Pest · Alpine.js**

[✨ المميزات](#-المميزات) · [🏗️ المعمارية](#️-المعمارية--solid) · [🗄️ قاعدة البيانات](#️-قاعدة-البيانات) · [🚀 التثبيت](#-التثبيت-السريع) · [🛣️ المسارات](#️-المسارات) · [🎨 الواجهات](#-الواجهات)

---

## 📖 ما هي IBBDev؟
IBBDev هي منصة عربية لطلاب علوم الحاسوب لطرح الأسئلة البرمجية، تقديم الإجابات، وكسب نقاط السمعة (Reputation) عند اعتماد الحل. بُنيت كتطبيق نهائي لمادة هندسة البرمجيات — المستوى الرابع لتطبيق كل ما تعلمناه من المعامل 1→4 في مشروع واحد حقيقي.

---

## ✨ المميزات
| الميزة | التفاصيل |
| :--- | :--- |
| **إدارة الحسابات** | تسجيل بـ `name` + `username` فريد (`@ahmed_dev`) + `avatar_path` + `reputation_points` |
| **الأسئلة (posts)** | عنوان، وصف، `image_path` لتوضيح الخطأ، `is_solved` تلقائي عند الاعتماد |
| **الإجابات (answers)** | `post_id` + `user_id` + `body` + `is_accepted` — لا يمكن الإجابة على سؤالك الخاص |
| **السمعة (reputation)** | `+10` نقاط عند الاعتماد عبر `ReputationService` + سجل شفاف في `reputation_logs` (points + reason) |
| **البحث والفلترة** | بحث بالعنوان/المحتوى، فلتر (الكل/محلولة/غير محلولة/أسئلتي)، ترتيب (الأحدث/الأقدم/الأكثر إجابات)، ترقيم 9/صفحة |
| **الوسائط** | رفع صور الأسئلة و Avatar عبر `Storage::disk('public')` + `storage:link` + `asset('storage/...')` |
| **الصلاحيات** | `PostPolicy` + `AnswerPolicy` — لا تعديل إلا للمالك، لا اعتماد إلا لصاحب السؤال |
| **الواجهة** | Blade + Tailwind 3 + Alpine.js — RTL، Noto Kufi Arabic، متجاوبة 320→1440، Toast يختفي بعد 5s |
| **الصفحات العامة** | `welcome` + `about` + `contact` + `faqs` + `users` بتصميم عصري |

---

## 🧰 التقنيات
| الطبقة | التقنية | الإصدار |
| :--- | :--- | :--- |
| **Backend** | Laravel | 13.x |
| **Language** | PHP | 8.3+ |
| **Frontend** | Blade + Vite + Alpine.js | 3.4 |
| **CSS** | Tailwind CSS | 3.x |
| **Auth** | Laravel Breeze (Blade) | 2.x |
| **DB** | SQLite (افتراضي) / MySQL | - |
| **Testing** | Pest + PHPUnit | 4 / 12 |
| **Quality** | Pint | 1.x |

---

## 🏗️ المعمارية — SOLID

`Browser` → `Route` → `Controller` (نحيف) → `Service` (Interface) → `Model` → `DB`
↓
`View` (Blade)

**لماذا Service Layer؟**
* **SRP:** الـ Controller لا يعرف كيف تُحسب النقاط — فقط يستدعي `ReputationServiceInterface::awardForAcceptedAnswer()`.
* **DIP:** الـ Controller يعتمد على Interface وليس class محددة — مربوط في `AppServiceProvider::register()` عبر `$this->app->bind(...)`.

```php
// AppServiceProvider.php
$this->app->bind(PostServiceInterface::class, PostService::class);
$this->app->bind(ReputationServiceInterface::class, ReputationService::class);
Gate::policy(Answer::class, AnswerPolicy::class);
```

| المفهوم | التطبيق |
| :--- | :--- |
| **MVC** | `PostController` + `Post` + `posts/index.blade.php` |
| **DI** | `__construct(PostServiceInterface $service)` |
| **Policy** | `AnswerPolicy::accept()` → `post.user_id === user.id` |
| **Form Request**| `StorePostRequest` (title/body/image_path) برسائل عربية |
| **Factory/Seeder**| `PostFactory` + `PostSeeder` (saherqaid + 4 fake + 23 answer) |

---

## 🗄️ قاعدة البيانات

### ERD
```text
users 1──∞ posts 1──∞ answers
  │          │
  │          └────∞ answers └── is_accepted → is_solved + reputation_logs
  └────∞ reputation_logs
```

### الجداول المطلوبة بالضبط
*   **users**
    `id PK, name VARCHAR, email VARCHAR UNIQUE, email_verified_at DATETIME, password VARCHAR, remember_token VARCHAR, username VARCHAR UNIQUE, -- @ahmed_dev avatar_path VARCHAR NULL, -- storage/avatars/... reputation_points INTEGER DEFAULT 0, created_at, updated_at`
*   **posts (جدول الأسئلة)**
    `id PK, user_id FK→users CASCADE, title VARCHAR, body TEXT, image_path VARCHAR NULL, -- storage/posts/... is_solved BOOLEAN DEFAULT false, created_at, updated_at`
*   **answers**
    `id PK, post_id FK→posts CASCADE, user_id FK→users CASCADE, body TEXT, is_accepted BOOLEAN DEFAULT false, created_at, updated_at`
*   **reputation_logs (سجل الشفافية)**
    `id PK, user_id FK→users CASCADE, points INTEGER (10), reason VARCHAR, created_at, updated_at -- مثال: reason = "تم اعتماد إجابته كحل لسؤال: كيف أحل NullPointer؟"`
*   أخرى (Laravel): `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens`, `migrations`

> **التحقق:** `sqlite3 database/database.sqlite "SELECT sql FROM sqlite_master WHERE type='table' AND name IN ('users','posts','answers','reputation_logs');"`

---

## 🚀 التثبيت السريع

**المتطلبات:** PHP 8.3 + Composer 2 + Node 20.19+/22.12+ + npm + Git

### 1. نسخ المشروع
```bash
git clone https://github.com/OmerGaber-hub/lab5-ibbdev.git
cd lab5-ibbdev
```

### 2. PHP
```bash
composer install
# Windows
Copy-Item .env.example .env
# Linux/macOS
cp .env.example .env
php artisan key:generate
```

### 3. قاعدة البيانات (SQLite افتراضي)
```bash
# Windows
New-Item database/database.sqlite -ItemType File -Force
# Linux/macOS
touch database/database.sqlite

php artisan migrate --seed 
# ينشئ saherqaid / saherqaid2020@gmail.com / password123 + 3 fake users + 9 posts + 23 answers + 6 logs
# بديل: php artisan migrate:fresh --seed (يحذف ويعيد)
```
> `.env` افتراضي:
> `DB_CONNECTION=sqlite`, `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=database`

### 4. الواجهة
```bash
npm install
npm run build # إنتاج
# أو للتطوير مع hot-reload:
composer run dev # يشغل serve + queue:listen + vite معاً
```
ثم افتح `http://127.0.0.1:8000`

> **التخزين:** `php artisan storage:link` (تم تنفيذه — يربط `public/storage` → `storage/app/public` للصور)

---

### حسابات تجريبية
| المستخدم | البريد | كلمة المرور | النقاط |
| :--- | :--- | :--- | :--- |
| **OmerGaber** | omer1234@gmail.com| 12345678 | 10+ |

---

## 🛣️ المسارات
`php artisan route:list`

| الطريقة | المسار | الاسم | الحماية | الوظيفة |
| :--- | :--- | :--- | :--- | :--- |
| **GET** | `/` | `welcome` | عام | الرئيسية |
| **GET** | `/welcome`, `/about`, `/contact`, `/faqs` | - | عام | صفحات عامة |
| **GET** | `/users` | `users.index` | عام | قائمة المطورين (بحث + ترقيم 12) |
| **GET** | `/users/{user}` | `users.show` | عام | ملف مستخدم + posts + reputation_logs |
| **GET** | `/posts` | `posts.index` | عام | قائمة الأسئلة (بحث + فلتر + ترتيب + 9/صفحة) |
| **GET** | `/posts/create` | `posts.create` | auth | نموذج سؤال جديد |
| **POST** | `/posts` | `posts.store` | auth | حفظ (StorePostRequest + image_path) |
| **GET** | `/posts/{post}` | `posts.show` | عام | تفاصيل + إجابات + اعتماد |
| **GET** | `/posts/{post}/edit` | `posts.edit` | auth+owner | تعديل |
| **PUT/PATCH** | `/posts/{post}` | `posts.update` | auth+owner | تحديث |
| **DELETE** | `/posts/{post}` | `posts.destroy` | auth+owner | حذف (يحذف الصورة) |
| **POST** | `/posts/{post}/answers` | `answers.store` | auth | إضافة إجابة (StoreAnswerRequest + منع الذات) |
| **POST** | `/answers/{answer}/accept` | `answers.accept` | auth+owner | اعتماد → is_accepted + is_solved + +10 + log |
| **GET/PATCH/DELETE** | `/profile` | `profile.*` | auth | تعديل الملف (name/username/email/avatar_path) |

> **الصور:** `<img src="{{ asset('storage/'.$post->image_path) }}">` ، **الأفاتار:** `<img src="{{ asset('storage/'.$user->avatar_path) }}">`

---

## 🎨 الواجهات

```text
resources/views/
├── layouts/app.blade.php        (RTL, Noto Kufi, navigation + toast + footer)
├── components/toast.blade.php   (Alpine, 5s auto-dismiss, shrink bar)
├── welcome.blade.php            (Hero + stats + 6 features + 3 steps + latest 3 posts + CTA)
├── about.blade.php              (Hero + رسالة + قيم + stack + timeline 1→5)
├── contact.blade.php            (3 info cards + form + map)
├── faqs.blade.php               (search + categories + accordion Alpine)
├── users/index.blade.php        (search + grid 3col + pagination 12)
├── users/show.blade.php         (cover + avatar + 3 stats + posts + logs)
├── posts/index.blade.php        (search + filter pills + sort + grid 3col + pagination 9 + solved badge + "كيف حصل على النقاط؟")
├── posts/create.blade.php       (drag&drop image_path preview + tips)
├── posts/edit.blade.php         (current image + is_solved toggle + danger zone)
└── posts/show.blade.php         (author + image_path + answers + accept + add answer)
```

> **Toast:** `layouts/app.blade.php` يحوي حاوية `fixed top-4 end-4` تعرض `session('success')/error` عبر `<x-toast type>` — يختفي بعد 5 ثوان مع `x-transition` وشريط shrink.

---

## 🔄 تدفق السمعة
1. `POST /posts/{post}/answers` → `AnswerController@store` → `AnswerService::createAnswer()` → يمنع إذا `post.user_id == auth.id`
2. `POST /answers/{answer}/accept` → `AnswerController@accept` → `Gate::authorize('accept')` → `ReputationService::awardForAcceptedAnswer(answer)` → `answer.is_accepted = true` → `post.is_solved = true` → `user.increment('reputation_points', 10)` → `ReputationLog::create(user_id, points:10, reason: "تم اعتماد إجابته...")`

> **عرضه في:** `posts/show` (زر "اعتماد كحل +10")، `posts/index` (بطاقة "محلول ✓" + `@username` حصل على 10 نقاط → رابط `users.show`)، `users/show` + `profile/edit` (سجل `reputation_logs`).

---

## 🧪 الاختبارات
```bash
php artisan test --compact       # كل الاختبارات
php artisan test --filter=Auth   # فئة محددة
vendor/bin/pint --format agent   # تنسيق
npm run build                    # بناء الواجهة
```
*   **Pest 4** — 25 اختبار (Auth + Profile + Example) — `phpunit.xml` يستخدم `sqlite :memory:`.
*   **Factory:** `UserFactory` (username/avatar_path) + `PostFactory` (image_path/is_solved)
*   **Seeder:** `PostSeeder` — firstOrCreate(saherqaid) + 3 fake + 9 posts (5 عربية) + 23 answers + logs.

---

## 📁 هيكل المشروع
```text
app/
├── Contracts/PostServiceInterface.php
├── Services/{PostService,AnswerService,ReputationService}.php
├── Http/{Controllers/PostController,AnswerController,UserController,ProfileController, Auth/*}
├── Http/Requests/{StorePostRequest,UpdatePostRequest,StoreAnswerRequest,ProfileUpdateRequest}
├── Models/{User,Post,Answer,ReputationLog}.php     # User: posts/answers/reputationLogs
└── Policies/{PostPolicy,AnswerPolicy}
database/{factories,migrations,seeders/PostSeeder.php}
resources/views/{posts,users,profile,auth,welcome,about,contact,faqs}
routes/web.php
tests/Feature/{Auth,Profile,Example}
```

---

## 🤝 المساهمة
```bash
git checkout -b feature/اسم-الميزة
# ... code + tests ...
vendor/bin/pint --format agent
php artisan test --compact
git commit -m "feat: وصف واضح"
git push origin feature/اسم-الميزة
# افتح Pull Request
```

---

## 📄 الترخيص
**MIT** — مشروع تعليمي لمقرر هندسة البرمجيات.
