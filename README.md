## Mục tiêu
- Cho phép người dùng mới đăng ký và người dùng hiện hữu đăng nhập nhanh, rõ ràng, hạn chế gián đoạn.

## Phạm vi
- Web app (màn hình desktop + mobile).
- Form đăng ký và đăng nhập, thông báo lỗi, điều hướng sau khi thành công.

---

## USER STORY 1 — REGISTER (Đăng ký tài khoản)
**As**: người dùng mới  
**I want**: đăng ký tài khoản nhanh chóng, dễ dàng  
**So that**: tôi có thể bắt đầu sử dụng dịch vụ ngay mà không bị gián đoạn

### Acceptance Tests
1) Happy Path  
   - Given tôi là người dùng mới và truy cập trang đăng ký  
   - When tôi nhập họ tên hợp lệ, email hợp lệ, mật khẩu hợp lệ và đăng ký
   - Then tôi được tạo tài khoản thành công

2) Unhappy Path – Email đã tồn tại  
   - Given tôi nhập email đã được đăng ký  
   - When tôi đăng ký  
   - Then hệ thống thông báo rõ ràng email đã tồn tại  
   - And trỏ đến trường email để tôi nhập lại

3) Unhappy Path – Xác nhận mật khẩu không hợp lệ (không trùng nhau)  
   - Given tôi bỏ trống mật khẩu hoặc nhập sai thông tin  
   - When tôi đăng ký  
   - Then hệ thống hiển thị lỗi “mật khẩu không trùng khớp”  
   - And trỏ đến trường mật khẩu để tôi nhập lại

### Luồng màn hình
1. Mở trang `Đăng ký`.
2. Nhập Họ tên, Email, Mật khẩu, Xác nhận mật khẩu.
3. Bấm `Đăng ký`.
4a. Hợp lệ → tạo tài khoản, chuyển tới trang login
4b. Không hợp lệ → hiển thị lỗi inline tại trường sai, focus vào trường sai.

### Quy tắc nhập liệu
- Họ tên: không rỗng, tối thiểu 2 ký tự.
- Email: định dạng email; kiểm tra tồn tại.
- Mật khẩu: tối thiểu 8 ký tự, gồm chữ và số (tùy policy); xác nhận phải trùng.

### UI/UX gợi ý
- Nút chính: “Đăng ký” (primary).  
- Link phụ: “Đã có tài khoản? Đăng nhập”.  
- Hiển thị spinner trên nút khi submit.  
- Thông báo lỗi rõ ràng, ngắn gọn ngay dưới trường; màu cảnh báo (đỏ).  
- Sau thành công: banner chào mừng + auto-redirect sau 1–2s.

---

## USER STORY 2 — LOGIN (Đăng nhập)
**As**: người dùng đã có tài khoản  
**I want**: đăng nhập nhanh và dễ dàng  
**So that**: tôi có thể tiếp tục sử dụng dịch vụ mà không bị gián đoạn

### Acceptance Tests
1)  Happy Path  
   - Given tôi đã có tài khoản hợp lệ  
   - When tôi nhập đúng email và mật khẩu  
   - Then tôi đăng nhập thành công và được chuyển vào hệ thống

2)  Unhappy Path – Sai mật khẩu  
   - Given tôi nhập sai mật khẩu  
   - When tôi đăng nhập  
   - Then hệ thống hiển thị lỗi “không đúng tài khoản hoặc mật khẩu”

3) Unhappy Path – Nhập sai nhiều lần  
   - Given tôi nhập sai nhiều lần liên tiếp  
   - When tôi tiếp tục đăng nhập  
   - Then hệ thống tạm thời khóa (thông báo thời gian mở khóa hoặc hướng dẫn khôi phục)

### Luồng màn hình
1. Mở trang `Đăng nhập`.
2. Nhập Email, Mật khẩu.
3. Bấm `Đăng nhập`.
4a. Hợp lệ → vào hệ thống / dashboard.  
4b. Sai → hiển thị lỗi chung, không lộ chi tiết; focus về trường mật khẩu.  
4c. Sai nhiều lần → hiển thị trạng thái khóa, hướng dẫn mở khóa (email reset hoặc chờ X phút).

### Quy tắc nhập liệu
- Email: định dạng email, yêu cầu không rỗng.
- Mật khẩu: không rỗng.

### UI/UX gợi ý
- Nút chính: “Đăng nhập” (primary).  
- Link phụ: “Quên mật khẩu?” → flow reset.  
- Hiển thị trạng thái khóa bằng banner cảnh báo + timer đếm ngược (nếu áp dụng).  
- Giới hạn số lần thử và thông báo rõ ràng sau mỗi lần thất bại.

---

## Phi chức năng
- Hiệu năng: phản hồi form < 300ms phía client; hiển thị loader khi gọi API.  
- Khả dụng: thông báo lỗi tiếng Việt rõ ràng; không tiết lộ lý do chi tiết (an toàn).  
- Truy cập: hỗ trợ focus outline, enter để submit, aria-label cho input & button.

## Chuyển hướng & trạng thái
- Đăng ký thành công → tự động đăng nhập (nếu policy cho phép) hoặc dẫn tới trang đăng nhập kèm banner “Tạo tài khoản thành công”.
- Đăng nhập thành công → chuyển tới trang chính; lưu session/token an toàn.
- Form nhớ email tùy chọn “Ghi nhớ tôi” (opt-in).

---

## Sơ đồ tuần tự

### Register - Happy Path
```text
Người dùng          Form đăng ký          Auth API                CSDL
    |                    |                    |                    |
    | Truy cập trang     |                    |                    |
    |------------------->|                    |                    |
    | Nhập họ tên, email, mật khẩu, xác nhận  |                    |
    |------------------->|                    |                    |
    |                    | Kiểm tra dữ liệu   |                    |
    |                    |------------------->|                    |
    |                    | Gửi yêu cầu đăng ký|                    |
    |                    |------------------->|                    |
    |                    |                    | Kiểm tra email     |
    |                    |                    |------------------->|
    |                    |                    | Email chưa tồn tại |
    |                    |                    |<-------------------|
    |                    |                    | Tạo tài khoản mới  |
    |                    |                    |------------------->|
    |                    |                    | Tạo thành công     |
    |                    |                    |<-------------------|
    |                    | Nhận kết quả thành công                 |
    |                    |<-------------------|                    |
    | Thông báo thành công và chuyển trang    |                    |
    |<-------------------|                    |                    |
```

chưa có control để API gọi đến

### Register - Unhappy Path: Email đã tồn tại
```text
Người dùng          Form đăng ký          Auth API                CSDL
    |                    |                    |                    |
    | Nhập email đã đăng ký                   |                    |
    |------------------->|                    |                    |
    |                    | Kiểm tra dữ liệu   |                    |
    |                    |------------------->|                    |
    |                    | Gửi yêu cầu đăng ký|                    |
    |                    |------------------->|                    |
    |                    |                    | Kiểm tra email     |
    |                    |                    |------------------->|
    |                    |                    | Email đã tồn tại   |
    |                    |                    |<-------------------|
    |                    | Trả lỗi email đã tồn tại                |
    |                    |<-------------------|                    |
    | Hiển thị lỗi tại ô email                |                    |
    |<-------------------|                    |                    |
    | Focus vào ô email để nhập lại           |                    |
    |<-------------------|                    |                    |
```

### Register - Unhappy Path: Mật khẩu không trùng khớp
```text
Người dùng          Form đăng ký
    |                    |
    | Nhập mật khẩu và xác nhận không trùng
    |------------------->|
    |                    | Kiểm tra dữ liệu phía client
    |                    |------------------->|
    | Hiển thị lỗi mật khẩu không trùng khớp
    |<-------------------|
    | Focus vào trường mật khẩu/xác nhận
    |<-------------------|
```

### Login - Happy Path
```text
Người dùng          Form đăng nhập         Auth API                CSDL
    |                    |                    |                    |
    | Truy cập trang     |                    |                    |
    |------------------->|                    |                    |
    | Nhập email và mật khẩu đúng             |                    |
    |------------------->|                    |                    |
    |                    | Gửi yêu cầu đăng nhập                  |
    |                    |------------------->|                    |
    |                    |                    | Tìm tài khoản      |
    |                    |                    |------------------->|
    |                    |                    | Trả thông tin TK   |
    |                    |                    |<-------------------|
    |                    |                    | Xác thực mật khẩu  |
    |                    |                    |------------------->|
    |                    | Nhận kết quả thành công                 |
    |                    |<-------------------|                    |
    | Chuyển vào hệ thống                    |                    |
    |<-------------------|                    |                    |
```

### Login - Unhappy Path: Sai mật khẩu
```text
Người dùng          Form đăng nhập         Auth API                CSDL
    |                    |                    |                    |
    | Nhập email đúng nhưng mật khẩu sai     |                    |
    |------------------->|                    |                    |
    |                    | Gửi yêu cầu đăng nhập                  |
    |                    |------------------->|                    |
    |                    |                    | Tìm tài khoản      |
    |                    |                    |------------------->|
    |                    |                    | Trả thông tin TK   |
    |                    |                    |<-------------------|
    |                    |                    | Xác thực thất bại  |
    |                    |                    |------------------->|
    |                    | Trả lỗi tài khoản/mật khẩu             |
    |                    |<-------------------|                    |
    | Hiển thị lỗi đăng nhập                 |                    |
    |<-------------------|                    |                    |
```

### Login - Unhappy Path: Nhập sai nhiều lần
```text
Người dùng          Form đăng nhập         Auth API                CSDL
    |                    |                    |                    |
    | Nhập sai nhiều lần |                    |                    |
    |------------------->|                    |                    |
    |                    | Gửi yêu cầu đăng nhập                  |
    |                    |------------------->|                    |
    |                    |                    | Kiểm tra failedCount
    |                    |                    |------------------->|
    |                    |                    | Trả số lần sai     |
    |                    |                    |<-------------------|
    |                    |                    | Tăng failedCount   |
    |                    |                    |------------------->|
    |                    |                    | Đặt lock tạm thời  |
    |                    |                    |------------------->|
    |                    | Trả thông báo bị khóa                 |
    |                    |<-------------------|                    |
    | Hiển thị khóa tạm thời và hướng dẫn     |                    |
    |<-------------------|                    |                    |
```



### Sơ đồ tuần tự (text) — Phân quyền & Admin
Login – Happy Path (phân nhánh role)

Người dùng          Form đăng nhập         AuthController        Session/Auth        CSDL
    |                    |                    |                      |                  |
    | Truy cập /login     |                    |                      |                  |
    |------------------->|                    |                      |                  |
    | Nhập email, mật khẩu|                    |                      |                  |
    |------------------->|                    |                      |                  |
    |                    | Gửi yêu cầu login  |                      |                  |
    |                    |------------------->|                      |                  |
    |                    |                    | Kiểm tra throttle    |                  |
    |                    |                    |--------------------->|                  |
    |                    |                    |<---------------------| ok               |
    |                    |                    | Lấy user theo email  |                  |
    |                    |                    |--------------------->|                  |
    |                    |                    |<---------------------| user             |
    |                    |                    | Auth::attempt        |                  |
    |                    |                    |--------------------->|                  |
    |                    |                    |<---------------------| success          |
    |                    |                    | regenerate session   |                  |
    |                    |                    |                      |                  |
    |                    | Nhận redirect      |                      |                  |
    |                    |<-------------------|                      |                  |
    | Chuyển trang:      |                    |                      |                  |
    | - nếu role=admin -> /admin                                   |                  |
    | - nếu role=user  -> /dashboard                               |                  |



Login – Sai mật khẩu
Người dùng          Form đăng nhập         AuthController        Session/Auth        CSDL
    |                    |                    |                      |                  |
    | Nhập email đúng nhưng mật khẩu sai     |                      |                  |
    |------------------->|                    |                      |                  |
    |                    | Gửi yêu cầu login  |                      |                  |
    |                    |------------------->|                      |                  |
    |                    |                    | Kiểm tra throttle    |                  |
    |                    |                    |--------------------->|                  |
    |                    |                    |<---------------------| ok               |
    |                    |                    | Lấy user theo email  |                  |
    |                    |                    |--------------------->|                  |
    |                    |                    |<---------------------| user             |
    |                    |                    | Auth::               |                  |
    |                    |                    |--------------------->|                  |
    |                    |                    |<---------------------| fail             |
    |                    |                    | RateLimiter::hit     |                  |
    |                    | Nhận lỗi/email     |                      |                  |
    |                    |<-------------------|                      |                  |
    | Hiển thị lỗi đăng nhập (email)          |                      |                  |
    |<-------------------|                    |                      |                  |


Admin – Danh sách user

Admin               Browser               Web routes           CheckRole           AdminUserController        DB
    |                   |                      |                    |                       |                  |
    | Mở /admin/users   |                      |                    |                       |                  |
    |------------------>|                      |                    |                       |                  |
    |                   | Resolve route        |                    |                       |                  |
    |                   |--------------------->|                    |                       |                  |
    |                   |                      | role:admin?        |                       |                  |
    |                   |                      |------------------->|                       |                  |
    |                   |                      |<-------------------| allow/403             |                  |
    |                   |                      | call index()       |                       |                  |
    |                   |                      |------------------------------------------->|                  |
    |                   |                      |                       select * from users  |----------------->|
    |                   |                      |                                            |<-----------------|
    |                   |                      |<-------------------------------------------| render list      |
    | Nhận bảng user    |                      |                    |                       |                  |
    |<------------------|                      |                    |                       |                  |


Admin – Tạo user

Admin               Browser               Web routes           CheckRole           AdminUserController        DB
    |                   |                      |                    |                       |                  |
    | Submit POST /admin/users (name,email,pwd,role)                                            |
    |------------------>|                      |                    |                       |                  |
    |                   |--------------------->|                    |                       |                  |
    |                   |                      | role:admin?        |                       |                  |
    |                   |                      |------------------->|                       |                  |
    |                   |                      |<-------------------| allow/403             |                  |
    |                   |                      | call store()       |                       |                  |
    |                   |                      |------------------------------------------->|                  |
    |                   |                      |                       INSERT user          |----------------->|
    |                   |                      |                                            |<-----------------|
    |                   |                      |<-------------------------------------------| redirect /admin/users |
    | Nhận redirect + flash                    |                    |                       |                  |
    |<------------------|                      |                    |                       |                  |


Admin – Cập nhật user (không đổi mật khẩu nếu để trống)

Admin               Browser               Web routes           CheckRole           AdminUserController        DB
    |                   |                      |                    |                       |                  |
    | PUT /admin/users/{id} (name,email,role,password optional)                           |
    |------------------>|                      |                    |                       |                  |
    |                   |--------------------->|                    |                       |                  |
    |                   |                      | role:admin?        |                       |                  |
    |                   |                      |------------------->|                       |                  |
    |                   |                      |<-------------------| allow/403             |                  |
    |                   |                      | call update()      |                       |                  |
    |                   |                      |-------------------------------------------->|                  |
    |                   |                      |                       UPDATE user           |---------------->|
    |                   |                      |                                              |<---------------|
    |                   |                      |<---------------------------------------------| redirect /admin/users |
    | Nhận redirect     |                      |                    |                       |                  |
    |<------------------|                      |                    |                       |                  |


Admin – Xóa user (chặn tự xóa)
Admin               Browser               Web routes           CheckRole           AdminUserController        DB
    |                   |                      |                    |                       |                  |
    | DELETE /admin/users/{id}                 |                    |                       |                  |
    |------------------>|                      |                    |                       |                  |
    |                   |--------------------->|                    |                       |                  |
    |                   |                      | role:admin?        |                       |                  |
    |                   |                      |------------------->|                       |                  |
    |                   |                      |<-------------------| allow/403             |                  |
    |                   |                      | call destroy()     |                       |                  |
    |                   |                      |-------------------------------------------->|                  |
    |                   |                      |   if id == current admin -> block           |                  |
    |                   |                      |   else DELETE user                         |---------------->|
    |                   |                      |                                              |<---------------|
    |                   |                      |<---------------------------------------------| redirect + status/error |
    | Nhận redirect     |                      |                    |                       |                  |
    |<------------------|                      |                    |                       |                  |
