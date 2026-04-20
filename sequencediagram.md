# Class Diagram

Tai lieu nay mo ta so do class theo source code hien tai cua project Laravel.
No tap trung vao cac class trong `app/` va cac quan he nghiep vu thuc te dang ton tai.

Pham vi:
- Controllers
- Models
- Middleware
- Service Provider

Khong dua framework internals vao chi tiet, chi ghi cac quan he can thiet de hieu codebase.

======================================================================
1. TONG QUAN KHOI LOP
======================================================================

```text
                                      +----------------------+
                                      |      Controller      |
                                      | Laravel base class   |
                                      +----------+-----------+
                                                 ^
                 +-------------------------------+-------------------------------+
                 |               |                    |               |           |
                 |               |                    |               |           |
        +--------+--------+ +----+-----------+ +------+--------+ +----+------+ +--+----------------------+
        | AuthController  | | DashboardCtrl  | | AdminCtrl     | | AdminUser  | | AdminWarrantyRequestCtrl|
        +-----------------+ +----------------+ +---------------+ +-----------+ +-------------------------+
                 |                    |                 |                |                    |
                 |                    |                 |                |                    |
                 |                    |                 |                |                    |
                 v                    v                 v                v                    v
           +-----------+      +----------------+                 +-----------+          +------------------+
           |   User    |      | WarrantyRequest|<----------------|   User    |          | WarrantyRequest  |
           +-----------+      +----------------+                 +-----------+          +------------------+


      +------------------+                  +------------------+
      |  CheckRole       |                  | AdminMiddleware  |
      +------------------+                  +------------------+

      +----------------------+
      | AppServiceProvider   |
      +----------------------+
```

======================================================================
2. MODEL LAYER
======================================================================

## 2.1 User

File: `app/Models/User.php`

```text
+----------------------------------------------------------------------------------+
| User                                                                            |
| extends Authenticatable                                                         |
+----------------------------------------------------------------------------------+
| - fillable: name, email, password, role                                         |
| - hidden: password, remember_token                                              |
| - casts(): email_verified_at => datetime, password => hashed                    |
+----------------------------------------------------------------------------------+
```

Vai tro:
- Dai dien cho tai khoan dang nhap.
- Dung cho ca user thuong va admin thong qua thuoc tinh `role`.

Quan he trong source code:
- Duoc tao moi trong `AuthController::register()`
- Duoc CRUD trong `AdminUserController`
- Duoc doc trong middleware `CheckRole`
- Duoc lien ket voi `WarrantyRequest` qua:
  - `user_id`: chu so huu yeu cau
  - `processed_by`: admin xu ly yeu cau

## 2.2 WarrantyRequest

File: `app/Models/WarrantyRequest.php`

```text
+--------------------------------------------------------------------------------------------------+
| WarrantyRequest                                                                                 |
| extends Model                                                                                   |
+--------------------------------------------------------------------------------------------------+
| - fillable:                                                                                     |
|   user_id, order_number, order_status, products, reason, status,                               |
|   processed_by, processed_at, rejection_reason                                                  |
| - casts:                                                                                        |
|   products => array                                                                             |
|   processed_at => datetime                                                                      |
+--------------------------------------------------------------------------------------------------+
| + user(): BelongsTo                                                                             |
| + processor(): BelongsTo                                                                        |
+--------------------------------------------------------------------------------------------------+
```

Vai tro:
- Dai dien cho mot yeu cau bao hanh/doi tra do nguoi dung gui.
- Luu trang thai xu ly nghiep vu:
  - `Chờ xử lý`
  - `Đã chấp nhận`
  - `Đã từ chối`

Quan he:

```text
WarrantyRequest
  |-- belongsTo --> User      (owner)      by user_id
  |-- belongsTo --> User      (processor)  by processed_by
```

======================================================================
3. CONTROLLER LAYER
======================================================================

Tat ca controller ben duoi deu ke thua:

```text
Controller
  ^
  |-- AuthController
  |-- DashboardController
  |-- WarrantyRequestController
  |-- AdminController
  |-- AdminUserController
  |-- AdminWarrantyRequestController
```

## 3.1 AuthController

File: `app/Http/Controllers/AuthController.php`

```text
+--------------------------------------------------------------------------------------------------+
| AuthController                                                                                  |
| extends Controller                                                                              |
+--------------------------------------------------------------------------------------------------+
| + showRegister()                                                                                |
| + register(Request $request)                                                                    |
| + showLogin()                                                                                   |
| + login(Request $request)                                                                       |
| + logout()                                                                                      |
+--------------------------------------------------------------------------------------------------+
Dependencies:
  - User
  - Request
  - Auth facade
  - Hash facade
  - RateLimiter facade
```

Quan he/chuc nang:
- Tao `User` moi khi dang ky.
- Dang nhap bang `Auth::attempt`.
- Phan nhanh dieu huong theo `User.role`.
- Dung `RateLimiter` de gioi han so lan dang nhap sai.

## 3.2 DashboardController

File: `app/Http/Controllers/DashboardController.php`

```text
+------------------------------------------------------------------------+
| DashboardController                                                    |
| extends Controller                                                     |
+------------------------------------------------------------------------+
| + index()                                                              |
+------------------------------------------------------------------------+
Dependencies:
  - WarrantyRequest
  - Auth facade
```

Quan he/chuc nang:
- Doc `Auth::user()`
- Truy van `WarrantyRequest` de tong hop:
  - tong so yeu cau
  - so yeu cau cho xu ly
  - so yeu cau da xu ly
  - 4 yeu cau moi nhat

## 3.3 WarrantyRequestController

File: `app/Http/Controllers/WarrantyRequestController.php`

```text
+--------------------------------------------------------------------------------------------------+
| WarrantyRequestController                                                                       |
| extends Controller                                                                              |
+--------------------------------------------------------------------------------------------------+
| - mockOrders(): array                                                                           |
| - getSelectedOrder(Request $request): array                                                     |
| - orderIsExpired(array $order): bool                                                            |
| - orderIsInvalid(array $order): bool                                                            |
| + create(Request $request)                                                                      |
| + store(Request $request)                                                                       |
| + success()                                                                                     |
| + index()                                                                                       |
| + show(WarrantyRequest $warrantyRequest)                                                        |
+--------------------------------------------------------------------------------------------------+
Dependencies:
  - WarrantyRequest
  - Request
  - Carbon
  - Auth facade
  - Redirect facade
  - Validation Rule
```

Quan he/chuc nang:
- Tao nguon du lieu don hang gia lap bang `mockOrders()`.
- Validate va tao `WarrantyRequest`.
- Chi cho phep user xem request cua chinh ho.

Quan he voi model:

```text
WarrantyRequestController
  |-- creates -----> WarrantyRequest
  |-- queries -----> WarrantyRequest
  |-- checks owner-> WarrantyRequest.user_id vs Auth::id()
```

## 3.4 AdminController

File: `app/Http/Controllers/AdminController.php`

```text
+--------------------------------------------------+
| AdminController                                  |
| extends Controller                               |
+--------------------------------------------------+
| + index()                                        |
+--------------------------------------------------+
Dependencies:
  - Auth facade
```

Vai tro:
- Controller don gian de render trang tong quan admin.

## 3.5 AdminUserController

File: `app/Http/Controllers/AdminUserController.php`

```text
+--------------------------------------------------------------------------------------------------+
| AdminUserController                                                                             |
| extends Controller                                                                              |
+--------------------------------------------------------------------------------------------------+
| + index()                                                                                       |
| + create()                                                                                      |
| + store(Request $request)                                                                       |
| + edit(User $user)                                                                              |
| + update(Request $request, User $user)                                                          |
| + destroy(User $user)                                                                           |
+--------------------------------------------------------------------------------------------------+
Dependencies:
  - User
  - Request
  - Auth facade
  - Validation Rule
```

Quan he/chuc nang:
- Quan ly CRUD cho `User`.
- Dung `config('roles')` de gioi han role hop le.
- Chan admin tu xoa chinh minh.

Quan he voi model:

```text
AdminUserController
  |-- reads -----> User
  |-- creates ---> User
  |-- updates ---> User
  |-- deletes ---> User
```

## 3.6 AdminWarrantyRequestController

File: `app/Http/Controllers/AdminWarrantyRequestController.php`

```text
+--------------------------------------------------------------------------------------------------+
| AdminWarrantyRequestController                                                                  |
| extends Controller                                                                              |
+--------------------------------------------------------------------------------------------------+
| + index(): View                                                                                 |
| + show(WarrantyRequest $warrantyRequest): View                                                  |
| + approve(WarrantyRequest $warrantyRequest): RedirectResponse                                   |
| + reject(Request $request, WarrantyRequest $warrantyRequest): RedirectResponse                  |
| - requestAlreadyProcessed(WarrantyRequest $warrantyRequest): bool                               |
+--------------------------------------------------------------------------------------------------+
Dependencies:
  - WarrantyRequest
  - Request
  - Auth facade
```

Quan he/chuc nang:
- Xem tat ca warranty request cho admin.
- Load `user` va `processor` relation.
- Chap nhan hoac tu choi request.
- Chi xu ly request neu trang thai la `Chờ xử lý`.

Quan he voi model:

```text
AdminWarrantyRequestController
  |-- queries ----> WarrantyRequest
  |-- loads -----> WarrantyRequest.user()
  |-- loads -----> WarrantyRequest.processor()
  |-- updates ---> WarrantyRequest.status
  |-- updates ---> WarrantyRequest.processed_by
  |-- updates ---> WarrantyRequest.processed_at
  |-- updates ---> WarrantyRequest.rejection_reason
```

======================================================================
4. MIDDLEWARE LAYER
======================================================================

## 4.1 CheckRole

File: `app/Http/Middleware/CheckRole.php`

```text
+--------------------------------------------------------------------------------------------------+
| CheckRole                                                                                       |
+--------------------------------------------------------------------------------------------------+
| + handle(Request $request, Closure $next, ...$roles): Response                                  |
+--------------------------------------------------------------------------------------------------+
Dependencies:
  - Request
  - Auth facade
  - Closure
  - Response
```

Vai tro:
- Neu chua dang nhap -> redirect `/login`
- Neu role khong hop le -> abort 403
- Neu hop le -> goi `$next($request)`

Quan he:

```text
Route
  -> alias "role"
  -> CheckRole
  -> Controller
```

## 4.2 AdminMiddleware

File: `app/Http/Middleware/AdminMiddleware.php`

```text
+--------------------------------------------------------------+
| AdminMiddleware                                              |
+--------------------------------------------------------------+
| + handle($request, Closure $next)                            |
+--------------------------------------------------------------+
Dependencies:
  - Auth facade
```

Trang thai trong source code:
- Class nay van ton tai.
- Hien tai khong thay duoc alias hoac route nao dang dung no.
- Middleware dang duoc dung thuc te cho admin la `CheckRole`.

======================================================================
5. SERVICE PROVIDER
======================================================================

## 5.1 AppServiceProvider

File: `app/Providers/AppServiceProvider.php`

```text
+--------------------------------------------------------------+
| AppServiceProvider                                           |
| extends ServiceProvider                                      |
+--------------------------------------------------------------+
| + register(): void                                           |
| + boot(): void                                               |
+--------------------------------------------------------------+
```

Trang thai hien tai:
- Chua co custom service registration hay boot logic dang dung.

======================================================================
6. CLASS RELATIONSHIP CHI TIET
======================================================================

## 6.1 Inheritance

```text
Authenticatable
  ^
  |-- User

Model
  ^
  |-- WarrantyRequest

Controller
  ^
  |-- AuthController
  |-- DashboardController
  |-- WarrantyRequestController
  |-- AdminController
  |-- AdminUserController
  |-- AdminWarrantyRequestController

ServiceProvider
  ^
  |-- AppServiceProvider
```

## 6.2 Association

```text
WarrantyRequest "many" ---> "one" User
  y nghia: mot user co the co nhieu warranty request
  source field: warranty_requests.user_id

WarrantyRequest "many" ---> "one" User (processor)
  y nghia: mot admin co the xu ly nhieu warranty request
  source field: warranty_requests.processed_by
```

## 6.3 Dependency

```text
AuthController --------------------------> User
DashboardController ---------------------> WarrantyRequest
WarrantyRequestController ---------------> WarrantyRequest
AdminUserController ---------------------> User
AdminWarrantyRequestController ----------> WarrantyRequest
CheckRole -------------------------------> User role via Auth::user()
```

======================================================================
7. CLASS DIAGRAM DANG TEXT
======================================================================

```text
                                         +----------------------------------+
                                         |          ServiceProvider         |
                                         +----------------------------------+
                                                         ^
                                                         |
                                         +----------------------------------+
                                         |       AppServiceProvider         |
                                         +----------------------------------+


 +----------------------------------+                    +----------------------------------+
 |        Authenticatable           |                    |              Model               |
 +----------------------------------+                    +----------------------------------+
               ^                                                      ^
               |                                                      |
 +----------------------------------+                    +----------------------------------+
 |               User               |<-------------------|         WarrantyRequest          |
 +----------------------------------+    owner by user_id +----------------------------------+
 | name                             |    processor by      | user_id                         |
 | email                            |<-------------------  | order_number                    |
 | password                         |     processed_by     | order_status                    |
 | role                             |                      | products                        |
 +----------------------------------+                      | reason                          |
                                                          | status                          |
                                                          | processed_by                    |
                                                          | processed_at                    |
                                                          | rejection_reason                |
                                                          +----------------------------------+
                                                          | + user()                         |
                                                          | + processor()                    |
                                                          +----------------------------------+


                                         +----------------------------------+
                                         |            Controller            |
                                         +----------------------------------+
                                                         ^
              +---------------------------+--------------+----------------------------+--------------------------+------------------------------+------------------------------+
              |                           |                                           |                          |                              |
              |                           |                                           |                          |                              |
 +---------------------------+ +---------------------------+ +-----------------------+ +------------------------+ +----------------------------+ +-----------------------------+
 |      AuthController       | |   DashboardController    | | AdminController       | | AdminUserController    | | WarrantyRequestController  | | AdminWarrantyRequestController |
 +---------------------------+ +---------------------------+ +-----------------------+ +------------------------+ +----------------------------+ +-----------------------------+
 | showRegister()            | | index()                   | | index()               | | index()                | | mockOrders()               | | index()                     |
 | register()                | +---------------------------+ +-----------------------+ | create()               | | getSelectedOrder()         | | show()                      |
 | showLogin()               |            |                                       |    | store()                | | orderIsExpired()           | | approve()                   |
 | login()                   |            | queries                               |    | edit()                 | | orderIsInvalid()           | | reject()                    |
 | logout()                  |            v                                       |    | update()               | | create()                   | | requestAlreadyProcessed()   |
 +---------------------------+   +---------------------------+                     |    | destroy()              | | store()                    | +-----------------------------+
             |                  |      WarrantyRequest       |<--------------------+    +------------------------+ | success()                  |                |
             | creates          +---------------------------+      CRUD User                 |                       | index()                    |                | updates
             v                                                                                 |                       | show()                     |                v
   +---------------------------+                                                                v                       +----------------------------+   +---------------------------+
   |           User            |                                                        +-------------------+                                       |      WarrantyRequest      |
   +---------------------------+                                                        |       User        |                                       +---------------------------+


 +----------------------------------+         middleware for admin routes
 |            CheckRole             |<------------------------------------ Route alias "role"
 +----------------------------------+
 | handle(request, next, ...roles)  |
 +----------------------------------+

 +----------------------------------+
 |         AdminMiddleware          |
 +----------------------------------+
 | handle(request, next)            |
 +----------------------------------+
 | note: currently unused           |
 +----------------------------------+
```

======================================================================
8. NHAN XET KIEN TRUC TU SO DO CLASS
======================================================================

- `User` la trung tam cua auth va phan quyen.
- `WarrantyRequest` la model nghiep vu trung tam cua he thong hien tai.
- Controllers dang xu ly ca HTTP flow va nghiep vu, chua tach service layer rieng.
- `AdminWarrantyRequestController` va `WarrantyRequestController` deu thao tac truc tiep len `WarrantyRequest`.
- `CheckRole` la diem chan phan quyen thuc te cho admin routes.
- `AdminMiddleware` dang bi trung vai tro va hien tai khong duoc noi vao route.

======================================================================
9. FILE THAM CHIEU
======================================================================

- `app/Models/User.php`
- `app/Models/WarrantyRequest.php`
- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/WarrantyRequestController.php`
- `app/Http/Controllers/AdminController.php`
- `app/Http/Controllers/AdminUserController.php`
- `app/Http/Controllers/AdminWarrantyRequestController.php`
- `app/Http/Middleware/CheckRole.php`
- `app/Http/Middleware/AdminMiddleware.php`
- `app/Providers/AppServiceProvider.php`
