# Sequence Diagram

Tai lieu nay viet lai cac luong chuc nang hien co trong source code duoi dang text thuần de de doc tren GitHub, de review diff, va de doi chieu voi code.

Pham vi duoc ve theo source code hien tai:
- `routes/web.php`
- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/WarrantyRequestController.php`
- `app/Http/Controllers/AdminController.php`
- `app/Http/Controllers/AdminUserController.php`
- `app/Http/Controllers/AdminWarrantyRequestController.php`
- `app/Http/Middleware/CheckRole.php`
- `app/Models/User.php`
- `app/Models/WarrantyRequest.php`

Quy uoc:
- Browser/User/Admin: tac nhan goi HTTP
- Route: Laravel web route
- Middleware: `auth`, `guest`, `role:admin`
- Controller: ham xu ly trong controller
- DB: bang du lieu thuc te hoac nguon du lieu gia lap
- View: Blade view tra ve

======================================================================
1. DANG KY TAI KHOAN
======================================================================

Source chinh:
- `routes/web.php` -> `GET /register`, `POST /register`
- `app/Http/Controllers/AuthController.php` -> `showRegister()`, `register()`

Text sequence:

Guest/Browser                Route (guest)                 AuthController                DB: users                 View
     |                             |                              |                           |                        |
     | GET /register               |                              |                           |                        |
     |---------------------------->|                              |                           |                        |
     |                             | showRegister()               |                           |                        |
     |                             |----------------------------->|                           |                        |
     |                             |                              | Auth::check()?            |                        |
     |                             |                              | neu da login -> redirect  |                        |
     |                             |                              | nguoc lai render view     |                        |
     |                             |<-----------------------------|                           | register.blade.php     |
     |<----------------------------|                              |                           |                        |
     | form dang ky                |                              |                           |                        |
     |                             |                              |                           |                        |
     | POST /register              |                              |                           |                        |
     | name, email, password,      |                              |                           |                        |
     | password_confirmation       |                              |                           |                        |
     |---------------------------->|                              |                           |                        |
     |                             | register(Request)            |                           |                        |
     |                             |----------------------------->|                           |                        |
     |                             |                              | validate():               |                        |
     |                             |                              | - name required           |                        |
     |                             |                              | - email required|email    |                        |
     |                             |                              | - email unique:users      | SELECT email           |
     |                             |                              | - password confirmed      |----------------------->|
     |                             |                              |                           | unique check           |
     |                             |                              | User::create(...)         | INSERT user            |
     |                             |                              |-------------------------->|----------------------->|
     |                             |                              | Hash::make(password)      |                        |
     |                             |<-----------------------------|                           |                        |
     |<----------------------------| redirect /login + success    |                           |                        |

Ghi chu thuc te theo code:
- Role khong truyen khi register, nen user moi mac dinh lay theo migration/model behavior.
- Neu validation fail, Laravel tu dong redirect back + session errors.

======================================================================
2. DANG NHAP
======================================================================

Source chinh:
- `routes/web.php` -> `GET /login`, `POST /login`
- `app/Http/Controllers/AuthController.php` -> `showLogin()`, `login()`

Text sequence:

User/Browser                 Route (guest)                 AuthController            RateLimiter          Auth/Session           DB: users              View
    |                              |                              |                        |                     |                     |                     |
    | GET /login                   |                              |                        |                     |                     |                     |
    |----------------------------->|                              |                        |                     |                     |                     |
    |                              | showLogin()                  |                        |                     |                     |                     |
    |                              |----------------------------->|                        |                     |                     |                     |
    |                              |                              | Auth::check()?         |                     |                     |                     |
    |                              |                              | neu da login -> /      |                     |                     |                     |
    |                              |<-----------------------------|                        |                     |                     | login.blade.php      |
    |<-----------------------------| render login view            |                        |                     |                     |                     |
    |                              |                              |                        |                     |                     |                     |
    | POST /login                  |                              |                        |                     |                     |                     |
    | email, password              |                              |                        |                     |                     |                     |
    |----------------------------->|                              |                        |                     |                     |                     |
    |                              | login(Request)               |                        |                     |                     |                     |
    |                              |----------------------------->|                        |                     |                     |                     |
    |                              |                              | validate():            |                     |                     |                     |
    |                              |                              | - email required|email |                     |                     |                     |
    |                              |                              | - password required    |                     |                     |                     |
    |                              |                              | build throttle key     |                     |                     |                     |
    |                              |                              | tooManyAttempts?       |-------------------->|                     |                     |
    |                              |                              |<-----------------------| bool                |                     |                     |
    |                              |                              | alt bi khoa tam thoi   |                     |                     |                     |
    |<-----------------------------| back + error email           |                        |                     |                     |                     |
    |                              |                              | else tiep tuc          |                     |                     |                     |
    |                              |                              | Auth::attempt(...)     |                     |-------------------->| SELECT user by email |
    |                              |                              |                        |                     | verify password     |-------------------->|
    |                              |                              | alt login thanh cong   |                     |                     |                     |
    |                              |                              | session()->regenerate  |                     | regenerate session  |                     |
    |                              |                              | RateLimiter::clear     |-------------------->| clear key           |                     |
    |                              |                              | lay Auth::user()->role |                     |                     |                     |
    |                              |                              | role=admin ? /admin    |                     |                     |                     |
    |                              |                              | role=user  ? /dashboard|                     |                     |                     |
    |<-----------------------------| redirect intended(target)    |                        |                     |                     |                     |
    |                              |                              | else login fail        |                     |                     |                     |
    |                              |                              | RateLimiter::hit       |-------------------->| hit 60s             |                     |
    |<-----------------------------| back + error email           |                        |                     |                     |                     |

======================================================================
3. DANG XUAT
======================================================================

Source chinh:
- `routes/web.php` -> `POST /logout`
- `app/Http/Controllers/AuthController.php` -> `logout()`

Text sequence:

User/Browser                 Route (auth)                  AuthController                Auth
    |                              |                              |                           |
    | POST /logout                 |                              |                           |
    |----------------------------->|                              |                           |
    |                              | logout()                     |                           |
    |                              |----------------------------->|                           |
    |                              |                              | Auth::logout()            |
    |                              |                              |-------------------------->|
    |                              |<-----------------------------|                           |
    |<-----------------------------| redirect /login              |                           |

======================================================================
4. DASHBOARD NGUOI DUNG
======================================================================

Source chinh:
- `routes/web.php` -> `GET /dashboard`
- `app/Http/Controllers/DashboardController.php` -> `index()`
- `app/Models/WarrantyRequest.php`

Text sequence:

User/Browser                 Route (auth)                  DashboardController            DB: warranty_requests             View
    |                              |                              |                                 |                               |
    | GET /dashboard               |                              |                                 |                               |
    |----------------------------->|                              |                                 |                               |
    |                              | index()                      |                                 |                               |
    |                              |----------------------------->|                                 |                               |
    |                              |                              | Auth::user()                    |                               |
    |                              |                              | count all requests              | SELECT count(*) by user_id    |
    |                              |                              |-------------------------------->|------------------------------>|
    |                              |                              | count pending                   | WHERE status='Chờ xử lý'      |
    |                              |                              |-------------------------------->|------------------------------>|
    |                              |                              | count completed                 | WHERE status<>'Chờ xử lý'     |
    |                              |                              |-------------------------------->|------------------------------>|
    |                              |                              | recentRequests latest take(4)   | ORDER BY created_at desc      |
    |                              |                              |-------------------------------->| LIMIT 4                       |
    |                              |<-----------------------------| compact(user, counts, list)     |                               |
    |<-----------------------------| render dashboard             |                                 | dashboard.blade.php           |

======================================================================
5. MO FORM YEU CAU BAO HANH
======================================================================

Source chinh:
- `routes/web.php` -> `GET /warranty/request`
- `app/Http/Controllers/WarrantyRequestController.php` -> `create()`
- `mockOrders()`, `getSelectedOrder()`

Text sequence:

User/Browser                 Route (auth)                  WarrantyRequestController           Mock Order Source               View
    |                              |                                   |                                 |                             |
    | GET /warranty/request        |                                   |                                 |                             |
    | ?order_key=...               |                                   |                                 |                             |
    |----------------------------->|                                   |                                 |                             |
    |                              | create(Request)                   |                                 |                             |
    |                              |---------------------------------->|                                 |                             |
    |                              |                                   | mockOrders()                    | build 3 mock orders          |
    |                              |                                   |-------------------------------->| delivered/expired/cancelled  |
    |                              |                                   | getSelectedOrder(request)       | select by old/query/default  |
    |                              |                                   |-------------------------------->|                             |
    |                              |<----------------------------------| orders + selectedOrder          |                             |
    |<-----------------------------| render warranty.request           |                                 | warranty/request.blade.php  |

Ghi chu:
- Don hang hien tai la du lieu gia lap trong controller, chua doc tu bang `orders`.
- `order_key` duoc lay theo thu tu: old input -> query string -> phan tu dau tien.

======================================================================
6. GUI YEU CAU BAO HANH
======================================================================

Source chinh:
- `routes/web.php` -> `POST /warranty/request`
- `app/Http/Controllers/WarrantyRequestController.php` -> `store()`
- `app/Models/WarrantyRequest.php`

Text sequence:

User/Browser                 Route (auth)                  WarrantyRequestController          Mock Order Source            DB: warranty_requests
    |                              |                                   |                                |                            |
    | POST /warranty/request       |                                   |                                |                            |
    | order_key, products[],       |                                   |                                |                            |
    | reason                       |                                   |                                |                            |
    |----------------------------->|                                   |                                |                            |
    |                              | store(Request)                    |                                |                            |
    |                              |---------------------------------->|                                |                            |
    |                              |                                   | mockOrders()                   |                            |
    |                              |                                   |-------------------------------->|                            |
    |                              |                                   | validate():                    |                            |
    |                              |                                   | - order_key in array_keys      |                            |
    |                              |                                   | - products required|array|min1 |                            |
    |                              |                                   | - products.* string            |                            |
    |                              |                                   | - reason required|min10|max500 |                            |
    |                              |                                   | lay order theo order_key       |                            |
    |                              |                                   | orderIsInvalid(order)?         | status != delivered         |
    |                              |                                   | alt invalid                    |                            |
    |<-----------------------------| redirect back + error order_key   |                                |                            |
    |                              |                                   | else orderIsExpired(order)?    | received_at > window        |
    |                              |                                   | alt expired                    |                            |
    |<-----------------------------| redirect back + error order_key   |                                |                            |
    |                              |                                   | else hop le                    |                            |
    |                              |                                   | WarrantyRequest::create        | INSERT row                  |
    |                              |                                   |--------------------------------|--------------------------->|
    |                              |                                   | fields:                        |                            |
    |                              |                                   | - user_id = Auth::id()         |                            |
    |                              |                                   | - order_number                 |                            |
    |                              |                                   | - order_status                 |                            |
    |                              |                                   | - products = array values      |                            |
    |                              |                                   | - reason                       |                            |
    |                              |                                   | - status = 'Chờ xử lý'         |                            |
    |<-----------------------------| redirect route warranty.success   |                                |                            |

======================================================================
7. XEM TRANG GUI THANH CONG
======================================================================

Source chinh:
- `routes/web.php` -> `GET /warranty/success`
- `app/Http/Controllers/WarrantyRequestController.php` -> `success()`

Text sequence:

User/Browser                 Route (auth)                  WarrantyRequestController           View
    |                              |                                   |                             |
    | GET /warranty/success        |                                   |                             |
    |----------------------------->|                                   |                             |
    |                              | success()                         |                             |
    |                              |---------------------------------->|                             |
    |                              |<----------------------------------|                             |
    |<-----------------------------| render success view               | warranty/success.blade.php  |

======================================================================
8. NGUOI DUNG XEM DANH SACH YEU CAU CUA MINH
======================================================================

Source chinh:
- `routes/web.php` -> `GET /warranty/requests`
- `app/Http/Controllers/WarrantyRequestController.php` -> `index()`

Text sequence:

User/Browser                 Route (auth)                  WarrantyRequestController          DB: warranty_requests             View
    |                              |                                   |                                 |                              |
    | GET /warranty/requests       |                                   |                                 |                              |
    |----------------------------->|                                   |                                 |                              |
    |                              | index()                           |                                 |                              |
    |                              |---------------------------------->|                                 |                              |
    |                              |                                   | Auth::id()                      |                              |
    |                              |                                   | query by user_id                | SELECT * WHERE user_id=?     |
    |                              |                                   | orderBy created_at desc         | ORDER BY created_at DESC     |
    |                              |                                   |-------------------------------->|----------------------------->|
    |                              |<----------------------------------| requests                        |                              |
    |<-----------------------------| render warranty.index             |                                 | warranty/index.blade.php    |

======================================================================
9. NGUOI DUNG XEM CHI TIET YEU CAU CUA MINH
======================================================================

Source chinh:
- `routes/web.php` -> `GET /warranty/requests/{warrantyRequest}`
- `app/Http/Controllers/WarrantyRequestController.php` -> `show()`

Text sequence:

User/Browser                 Route (auth)                 Route Model Binding         WarrantyRequestController       DB: warranty_requests         View
    |                              |                                |                             |                              |                           |
    | GET /warranty/requests/{id}  |                                |                             |                              |                           |
    |----------------------------->|                                |                             |                              |                           |
    |                              | resolve {warrantyRequest}      |                             | SELECT by id                 |                           |
    |                              |------------------------------->|---------------------------->|----------------------------->|                           |
    |                              |                                | warrantyRequest model        |                              |                           |
    |                              | show(warrantyRequest)          |                             |                              |                           |
    |                              |--------------------------------------------------------------->|                              |                           |
    |                              |                                |                             | compare user_id with Auth::id|
    |                              |                                |                             | alt khong phai chu so huu     |
    |<-----------------------------| abort 403                      |                             |                              |                           |
    |                              |                                |                             | else hop le                  |
    |<-----------------------------| render warranty.show           |                             |                              | warranty/show.blade.php  |

======================================================================
10. MIDDLEWARE ROLE:ADMIN
======================================================================

Source chinh:
- `bootstrap/app.php` -> alias `role`
- `app/Http/Middleware/CheckRole.php` -> `handle()`

Text sequence:

User/Browser                 Admin Route                    CheckRole Middleware                Auth/User
    |                              |                                  |                              |
    | request /admin/...           |                                  |                              |
    |----------------------------->|                                  |                              |
    |                              | handle(request, 'admin')         |                              |
    |                              |--------------------------------->|                              |
    |                              |                                  | Auth::check()?                |
    |                              |                                  | alt chua login                |
    |<-----------------------------| redirect guest /login            |                              |
    |                              |                                  | else lay user role            |
    |                              |                                  | allowedRoles = ['admin']      |
    |                              |                                  | in_array(userRole, roles)?    |
    |                              |                                  | alt sai role                  |
    |<-----------------------------| abort 403 "Bạn không có quyền..."|                              |
    |                              |                                  | else cho phep                 |
    |                              |<---------------------------------| next(request)                 |
    |                              | controller duoc goi tiep         |                              |

======================================================================
11. ADMIN VAO TRANG TONG QUAN
======================================================================

Source chinh:
- `routes/web.php` -> `GET /admin`
- `app/Http/Controllers/AdminController.php` -> `index()`

Text sequence:

Admin/Browser                Route + role:admin             AdminController                     View
    |                              |                               |                              |
    | GET /admin                   |                               |                              |
    |----------------------------->|                               |                              |
    |                              | index()                       |                              |
    |                              |------------------------------>|                              |
    |                              |                               | Auth::user()                  |
    |                              |<------------------------------| compact(user)                 |
    |<-----------------------------| render admin.index            | admin/index.blade.php        |

======================================================================
12. ADMIN XEM DANH SACH USER
======================================================================

Source chinh:
- `routes/web.php` -> resource `admin/users`
- `app/Http/Controllers/AdminUserController.php` -> `index()`

Text sequence:

Admin/Browser                Route + role:admin             AdminUserController                DB: users                      View
    |                              |                               |                                 |                            |
    | GET /admin/users             |                               |                                 |                            |
    |----------------------------->|                               |                                 |                            |
    |                              | index()                       |                                 |                            |
    |                              |------------------------------>|                                 |                            |
    |                              |                               | User::orderBy('id')->get()     | SELECT * ORDER BY id        |
    |                              |                               |-------------------------------->|--------------------------->|
    |                              |                               | $roles = config('roles')       |                            |
    |                              |<------------------------------| users + roles                  |                            |
    |<-----------------------------| render admin.users.index      |                                 | admin/users/index.blade.php|

======================================================================
13. ADMIN TAO USER
======================================================================

Source chinh:
- `app/Http/Controllers/AdminUserController.php` -> `create()`, `store()`

Text sequence:

Admin/Browser                Route + role:admin             AdminUserController                DB: users                      View
    |                              |                               |                                 |                            |
    | GET /admin/users/create      |                               |                                 |                            |
    |----------------------------->|                               |                                 |                            |
    |                              | create()                      |                                 |                            |
    |                              |------------------------------>|                                 |                            |
    |                              |                               | $roles = config('roles')       |                            |
    |                              |<------------------------------| roles                           |                            |
    |<-----------------------------| render create form           |                                 | admin/users/create.blade.php
    |                              |                               |                                 |                            |
    | POST /admin/users            |                               |                                 |                            |
    | name,email,password,role     |                               |                                 |                            |
    |----------------------------->|                               |                                 |                            |
    |                              | store(Request)                |                                 |                            |
    |                              |------------------------------>|                                 |                            |
    |                              |                               | validate():                     |                            |
    |                              |                               | - name required|max255         |                            |
    |                              |                               | - email unique:users,email     | SELECT email               |
    |                              |                               | - password min:6               |                            |
    |                              |                               | - role in config(roles)        |                            |
    |                              |                               | User::create(...)              | INSERT user                |
    |                              |                               | bcrypt(password)               |--------------------------->|
    |                              |<------------------------------| success                         |                            |
    |<-----------------------------| redirect admin.users.index    | + flash status                 |                            |

======================================================================
14. ADMIN SUA USER
======================================================================

Source chinh:
- `app/Http/Controllers/AdminUserController.php` -> `edit()`, `update()`

Text sequence:

Admin/Browser                Route + role:admin          Route Model Binding        AdminUserController              DB: users                 View
    |                              |                               |                           |                              |                        |
    | GET /admin/users/{id}/edit   |                               |                           |                              |                        |
    |----------------------------->|                               | load user by id           |                              |                        |
    |                              |------------------------------>|------------------------->|                              |                        |
    |                              | edit(user)                    |                           |                              |                        |
    |                              |----------------------------------------------------------->|                              |                        |
    |                              |                               |                           | $roles = config('roles')     |                        |
    |<-----------------------------| render edit view              |                           |                              | admin/users/edit.blade.php
    |                              |                               |                           |                              |                        |
    | PUT /admin/users/{id}        |                               |                           |                              |                        |
    | name,email,password?,role    |                               |                           |                              |                        |
    |----------------------------->|                               | load user by id           |                              |                        |
    |                              |------------------------------>|------------------------->|                              |                        |
    |                              | update(Request, user)         |                           |                              |                        |
    |                              |----------------------------------------------------------->|                              |                        |
    |                              |                               |                           | validate():                  |                        |
    |                              |                               |                           | - unique email ignore(id)    | SELECT email           |
    |                              |                               |                           | - password nullable|min:6    |                        |
    |                              |                               |                           | gan name/email/role          |                        |
    |                              |                               |                           | neu co password -> bcrypt    |                        |
    |                              |                               |                           | save()                       | UPDATE users           |
    |                              |                               |                           |----------------------------->|----------------------->|
    |<-----------------------------| redirect admin.users.index    |                           | + flash status               |                        |

======================================================================
15. ADMIN XOA USER
======================================================================

Source chinh:
- `app/Http/Controllers/AdminUserController.php` -> `destroy()`

Text sequence:

Admin/Browser                Route + role:admin          Route Model Binding        AdminUserController              DB: users
    |                              |                               |                           |                              |
    | DELETE /admin/users/{id}     |                               |                           |                              |
    |----------------------------->|                               | load user by id           |                              |
    |                              |------------------------------>|------------------------->|                              |
    |                              | destroy(user)                 |                           |                              |
    |                              |----------------------------------------------------------->|                              |
    |                              |                               |                           | Auth::id() === user->id ?    |
    |                              |                               |                           | alt dang xoa chinh minh      |
    |<-----------------------------| redirect back + error         |                           |                              |
    |                              |                               |                           | else delete()                |
    |                              |                               |                           |----------------------------->| DELETE row             |
    |<-----------------------------| redirect back + status        |                           |                              |

======================================================================
16. ADMIN XEM DANH SACH YEU CAU BAO HANH
======================================================================

Source chinh:
- `routes/web.php` -> `GET /admin/warranty-requests`
- `app/Http/Controllers/AdminWarrantyRequestController.php` -> `index()`
- `app/Models/WarrantyRequest.php` -> `user()`, `processor()`

Text sequence:

Admin/Browser                Route + role:admin          AdminWarrantyRequestController          DB: warranty_requests         DB: users                   View
    |                              |                                  |                                   |                           |                         |
    | GET /admin/warranty-requests |                                  |                                   |                           |                         |
    |----------------------------->|                                  |                                   |                           |                         |
    |                              | index()                          |                                   |                           |                         |
    |                              |--------------------------------->|                                   |                           |                         |
    |                              |                                  | WarrantyRequest::with(...)        |                           |                         |
    |                              |                                  | - with('user','processor')        | SELECT warranty_requests   | JOIN/eager load users    |
    |                              |                                  | - latest()                        | ORDER BY created_at DESC   | by user_id/processed_by  |
    |                              |                                  | - get()                           |--------------------------->|------------------------>|
    |                              |<---------------------------------| requests                          |                           |                         |
    |<-----------------------------| render admin.warranty list       |                                   |                           | admin/warranty-requests/index.blade.php

======================================================================
17. ADMIN XEM CHI TIET YEU CAU BAO HANH
======================================================================

Source chinh:
- `routes/web.php` -> `GET /admin/warranty-requests/{warrantyRequest}`
- `app/Http/Controllers/AdminWarrantyRequestController.php` -> `show()`

Text sequence:

Admin/Browser                Route + role:admin          Route Model Binding        AdminWarrantyRequestController      DB: users                     View
    |                              |                               |                           |                                  |                           |
    | GET /admin/warranty-requests/{id}                            |                           |                                  |                           |
    |----------------------------->|                               | load warrantyRequest      |                                  |                           |
    |                              |------------------------------>|------------------------->|                                  |                           |
    |                              | show(warrantyRequest)         |                           |                                  |                           |
    |                              |------------------------------------------------------------>|                                  |                           |
    |                              |                               |                           | load(['user','processor'])        | SELECT related users      |
    |                              |                               |                           |--------------------------------->|-------------------------->|
    |<-----------------------------| render detail view            |                           |                                  | admin/warranty-requests/show.blade.php

======================================================================
18. ADMIN CHAP NHAN YEU CAU BAO HANH
======================================================================

Source chinh:
- `routes/web.php` -> `PATCH /admin/warranty-requests/{warrantyRequest}/approve`
- `app/Http/Controllers/AdminWarrantyRequestController.php` -> `approve()`

Text sequence:

Admin/Browser                Route + role:admin          Route Model Binding        AdminWarrantyRequestController      DB: warranty_requests
    |                              |                               |                           |                                  |
    | PATCH /admin/warranty-requests/{id}/approve                  |                           |                                  |
    |----------------------------->|                               | load warrantyRequest      |                                  |
    |                              |------------------------------>|------------------------->|                                  |
    |                              | approve(warrantyRequest)      |                           |                                  |
    |                              |------------------------------------------------------------>|                                  |
    |                              |                               |                           | requestAlreadyProcessed()?        |
    |                              |                               |                           | status !== 'Chờ xử lý' ?          |
    |                              |                               |                           | alt da xu ly                      |
    |<-----------------------------| redirect detail + error       |                           |                                  |
    |                              |                               |                           | else update([...])                |
    |                              |                               |                           | - status = 'Đã chấp nhận'         |
    |                              |                               |                           | - processed_by = Auth::id()       |
    |                              |                               |                           | - processed_at = now()            |
    |                              |                               |                           | - rejection_reason = null         |
    |                              |                               |                           |--------------------------------->| UPDATE row               |
    |<-----------------------------| redirect detail + flash       |                           |                                  |

======================================================================
19. ADMIN TU CHOI YEU CAU BAO HANH
======================================================================

Source chinh:
- `routes/web.php` -> `PATCH /admin/warranty-requests/{warrantyRequest}/reject`
- `app/Http/Controllers/AdminWarrantyRequestController.php` -> `reject()`

Text sequence:

Admin/Browser                Route + role:admin          Route Model Binding        AdminWarrantyRequestController      DB: warranty_requests
    |                              |                               |                           |                                  |
    | PATCH /admin/warranty-requests/{id}/reject                   |                           |                                  |
    | rejection_reason             |                               |                           |                                  |
    |----------------------------->|                               | load warrantyRequest      |                                  |
    |                              |------------------------------>|------------------------->|                                  |
    |                              | reject(Request, warrantyRequest)                           |                                  |
    |                              |------------------------------------------------------------>|                                  |
    |                              |                               |                           | requestAlreadyProcessed()?        |
    |                              |                               |                           | alt da xu ly                      |
    |<-----------------------------| redirect detail + error       |                           |                                  |
    |                              |                               |                           | else validate():                  |
    |                              |                               |                           | - rejection_reason required       |
    |                              |                               |                           | - string|min:5|max:500            |
    |                              |                               |                           | alt validation fail               |
    |<-----------------------------| redirect back + session errors|                           |                                  |
    |                              |                               |                           | else update([...])                |
    |                              |                               |                           | - status = 'Đã từ chối'           |
    |                              |                               |                           | - processed_by = Auth::id()       |
    |                              |                               |                           | - processed_at = now()            |
    |                              |                               |                           | - rejection_reason = input        |
    |                              |                               |                           |--------------------------------->| UPDATE row               |
    |<-----------------------------| redirect detail + flash       |                           |                                  |

======================================================================
20. CAC QUAN HE DU LIEU CHINH
======================================================================

Source chinh:
- `app/Models/WarrantyRequest.php`
- `app/Models/User.php`

Text sequence:

Controller                    WarrantyRequest Model                 User Model / DB
    |                                  |                                   |
    | query warranty request           |                                   |
    |--------------------------------->|                                   |
    |                                  | belongsTo user() via user_id      |
    |                                  |---------------------------------->|
    |                                  | owner user                        |
    |<---------------------------------|-----------------------------------|
    |                                  | belongsTo processor() via processed_by
    |                                  |---------------------------------->|
    |                                  | admin processor                   |
    |<---------------------------------|-----------------------------------|

Bang du lieu `warranty_requests` hien tai co cac cot nghiep vu chinh:
- `user_id`
- `order_number`
- `order_status`
- `products`
- `reason`
- `status`
- `processed_by`
- `processed_at`
- `rejection_reason`

======================================================================
21. TONG KET CAC TRANG THAI BAO HANH
======================================================================

Theo source code hien tai, lifecycle cua `warranty_requests.status` la:

Nguoi dung gui yeu cau
    -> `Chờ xử lý`

Admin chap nhan
    -> `Đã chấp nhận`

Admin tu choi
    -> `Đã từ chối`

Rule thuc thi trong code:
- Chi request co `status = 'Chờ xử lý'` moi duoc admin xu ly tiep.
- Neu request da la `Đã chấp nhận` hoac `Đã từ chối` thi controller tra loi:
  `Yêu cầu đã được xử lý`

======================================================================
22. DANH MUC ROUTE CHUC NANG
======================================================================

Guest routes:
- `GET /login`
- `POST /login`
- `GET /register`
- `POST /register`

Authenticated user routes:
- `POST /logout`
- `GET /dashboard`
- `GET /warranty/request`
- `POST /warranty/request`
- `GET /warranty/success`
- `GET /warranty/requests`
- `GET /warranty/requests/{warrantyRequest}`

Admin routes:
- `GET /admin`
- `GET /admin/users`
- `GET /admin/users/create`
- `POST /admin/users`
- `GET /admin/users/{user}/edit`
- `PUT /admin/users/{user}`
- `DELETE /admin/users/{user}`
- `GET /admin/warranty-requests`
- `GET /admin/warranty-requests/{warrantyRequest}`
- `PATCH /admin/warranty-requests/{warrantyRequest}/approve`
- `PATCH /admin/warranty-requests/{warrantyRequest}/reject`

