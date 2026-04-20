## Mục tiêu
- Cho phép người dùng mới đăng ký và người dùng hiện hữu đăng nhập nhanh, rõ ràng, hạn chế gián đoạn.

## Phạm vi
- Web app (màn hình desktop + mobile).
- Form đăng ký và đăng nhập, thông báo lỗi, điều hướng sau khi thành công.

---

## USER STORY 1 - REGISTER (Đăng ký tài khoản)
As: Khách hàng chưa có tài khoản trên hệ thống
I want: đăng ký tài khoản bằng họ tên, email và mật khẩu
So that: tôi có thể đăng nhập và sử dụng các chức năng mua hàng

### Acceptance Tests
1. Happy Path – Đăng ký thành công
Given tôi đang ở trang đăng ký
When tôi nhập họ tên hợp lệ
And tôi nhập email chưa tồn tại trong hệ thống
And tôi nhập mật khẩu và xác nhận mật khẩu trùng khớp
And tôi nhấn "Đăng ký"
Then hệ thống tạo tài khoản mới
And chuyển tôi đến trang đăng nhập

2. Unhappy Path – Email đã tồn tại
Given tôi đang ở trang đăng ký
And email tôi nhập đã tồn tại trong hệ thống
When tôi nhấn "Đăng ký"
Then hệ thống hiển thị thông báo "Email đã tồn tại"
And con trỏ được focus vào trường email
And tài khoản không được tạo

3. Unhappy Path – Mật khẩu không trùng khớp
Given tôi nhập mật khẩu và xác nhận mật khẩu không giống nhau
When tôi nhấn "Đăng ký"
Then hệ thống hiển thị thông báo "Mật khẩu không trùng khớp"
And con trỏ focus vào trường xác nhận mật khẩu
And tài khoản không được tạo

4. Unhappy Path – Thiếu thông tin bắt buộc
Given tôi bỏ trống họ tên hoặc email hoặc mật khẩu
When tôi nhấn "Đăng ký"
Then hệ thống hiển thị lỗi "Vui lòng nhập đầy đủ thông tin"
And highlight các trường bị thiếu
And tài khoản không được tạo






## USER STORY 2 - LOGIN (Đăng nhập)
As: Khách hàng đã đăng ký tài khoản trên hệ thống
I want: đăng nhập bằng email và mật khẩu
So that: tôi có thể truy cập tài khoản và thực hiện mua hàng

### Acceptance Tests
1. Happy Path – Đăng nhập thành công
Given tôi có tài khoản đã đăng ký
When tôi nhập email và mật khẩu hợp lệ
And tôi nhấn "Đăng nhập"
Then hệ thống xác thực thành công
And tôi được chuyển đến trang chủ

2. Unhappy Path – Sai mật khẩu
Given tôi nhập email hợp lệ
And tôi nhập sai mật khẩu
When tôi nhấn "Đăng nhập"
Then hệ thống hiển thị thông báo "Không đúng tài khoản hoặc mật khẩu"
And người dùng vẫn ở trang đăng nhập

3. Unhappy Path – Tài khoản không tồn tại
Given email tôi nhập chưa được đăng ký
When tôi đăng nhập
Then hệ thống hiển thị "Không đúng tài khoản hoặc mật khẩu"
And đăng nhập không thành công

4. Unhappy Path – Nhập sai nhiều lần
Given tôi nhập sai mật khẩu nhiều lần liên tiếp
When tôi tiếp tục đăng nhập
Then hệ thống tạm thời khóa đăng nhập
And hiển thị thông báo thời gian thử lại

5. Unhappy Path – Thiếu thông tin đăng nhập
Given tôi bỏ trống email hoặc mật khẩu
When tôi nhấn đăng nhập
Then hệ thống hiển thị lỗi "Vui lòng nhập đầy đủ thông tin"


## Sơ đồ tuần tự (text) - Phân quyền & Admin

### Login - Happy Path (phân nhánh role)
```text
Người dùng          Form đăng nhập         AuthController        Session/Auth        CSDL
    |                    |                    |                      |                  |
    | Truy cập /login    |                    |                      |                  |
    |------------------->|                    |                      |                  |
    | Nhập email, mật khẩu|                   |                      |                  |
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
```

### Login - Sai mật khẩu
```text
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
    |                    |                    | Auth::attempt        |                  |
    |                    |                    |--------------------->|                  |
    |                    |                    |<---------------------| fail             |
    |                    |                    | RateLimiter::hit     |                  |
    |                    | Nhận lỗi/email     |                      |                  |
    |                    |<-------------------|                      |                  |
    | Hiển thị lỗi đăng nhập (email)          |                      |                  |
    |<-------------------|                    |                      |                  |
```

### Admin - Danh sách user
```text
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
```

### Admin - Tạo user
```text
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
```

### Admin - Cập nhật user (không đổi mật khẩu nếu để trống)
```text
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
```

### Admin - Xóa user (chặn tự xóa)
```text
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
```













=================================================================================




## USER STORY 3 - Yêu cầu đổi trả hàng
**As**: Khách hàng đã nhận đơn hàng hợp lệ trong thời gian quy định 
**I want**: gửi yêu cầu đổi trả sản phẩm  
**So that**: tôi có thể được hỗ trợ đổi sang sản phẩm khác hoặc hoàn tiền đơn hàng

### Acceptance Tests
## 1. Happy Path – Gửi yêu cầu đổi trả thành công
Given tôi đã đăng nhập vào hệ thống
And tôi có đơn hàng đã nhận trong thời gian đổi trả hợp lệ
When tôi chọn sản phẩm trong đơn hàng và nhấn "Yêu cầu đổi trả"
And tôi nhập lý do đổi trả hợp lệ
And tôi gửi yêu cầu
Then hệ thống tạo yêu cầu đổi trả thành công
And trạng thái yêu cầu là "Chờ xử lý"

## 2. Unhappy Path – Đơn hàng không nằm trong thời gian đổi trả
Given tôi có đơn hàng đã quá thời gian đổi trả
When tôi gửi yêu cầu đổi trả
Then hệ thống hiển thị thông báo "Đơn hàng đã hết thời gian đổi trả"
And yêu cầu không được tạo

## 3. Unhappy Path – Đơn hàng không hợp lệ
Given đơn hàng của tôi chưa được giao hoặc đã bị hủy
When tôi gửi yêu cầu đổi trả
Then hệ thống hiển thị thông báo "Đơn hàng không đủ điều kiện đổi trả"
And yêu cầu không được tạo

## 4. Unhappy Path – Chưa nhập lý do đổi trả
Given tôi mở form yêu cầu đổi trả
When tôi không nhập lý do và nhấn gửi
Then hệ thống hiển thị thông báo "Vui lòng nhập lý do đổi trả"
And con trỏ focus vào ô lý do
And yêu cầu không được tạo

## 5. Unhappy Path – Chưa chọn sản phẩm đổi trả
Given tôi có đơn hàng gồm nhiều sản phẩm
When tôi không chọn sản phẩm nào và nhấn gửi yêu cầu
Then hệ thống hiển thị thông báo "Vui lòng chọn sản phẩm cần đổi trả"
And yêu cầu không được tạo

=========================================================================
## USER STORY 4 – Admin chấp nhận yêu cầu đổi trả hàng
As: Nhân viên quản trị xử lý yêu cầu đổi trả
I want: chấp nhận yêu cầu đổi trả đang chờ xử lý
So that: yêu cầu hợp lệ được tiến hành đổi trả

## Acceptance Tests
## 1. Happy Path – Admin chấp nhận yêu cầu đổi trả
Given tôi là admin đã đăng nhập
And có yêu cầu đổi trả ở trạng thái "Chờ xử lý"
When tôi chọn "Chấp nhận" yêu cầu
Then hệ thống cập nhật trạng thái thành "Đã chấp nhận"
And hệ thống lưu thời gian xử lý

## 2. Unhappy Path – Người dùng không phải admin truy cập
Requirements:
Given tôi là người dùng thường đã đăng nhập
When tôi truy cập trang quản lý yêu cầu đổi trả
Then hệ thống từ chối truy cập
And hiển thị lỗi "Không có quyền truy cập"

## 3. Unhappy Path – Xử lý yêu cầu đã được xử lý
Requirements:
Given yêu cầu đổi trả đã có trạng thái "Đã chấp nhận" hoặc "Đã từ chối"
When admin tiếp tục xử lý yêu cầu
Then hệ thống không cho phép xử lý lại
And hiển thị thông báo "Yêu cầu đã được xử lý"

#### USER STORY 5 – Admin từ chối yêu cầu đổi trả hàng
As: Nhân viên quản trị xử lý yêu cầu đổi trả
I want: từ chối yêu cầu đổi trả không hợp lệ
So that: chỉ các yêu cầu đúng chính sách được thực hiện

## Acceptance Tests

1. Happy Path – Từ chối yêu cầu thành công
Given tôi là admin đã đăng nhập
And yêu cầu đang ở trạng thái "Chờ xử lý"
When tôi chọn "Từ chối"
And tôi nhập lý do từ chối
Then hệ thống cập nhật trạng thái thành "Đã từ chối"
And hệ thống lưu lý do từ chối

2. Unhappy Path – Chưa nhập lý do từ chối
Given tôi chọn "Từ chối" yêu cầu
When tôi không nhập lý do và nhấn xác nhận
Then hệ thống hiển thị "Vui lòng nhập lý do từ chối"
And trạng thái không thay đổi

3. Unhappy Path – Yêu cầu đã xử lý
Given yêu cầu đã có trạng thái "Đã từ chối"
When tôi tiếp tục từ chối
Then hệ thống hiển thị "Yêu cầu đã được xử lý"
And hệ thống không cập nhật lại trạng thái


### USER STORY 6 – Xem yêu cầu bảo hành / đổi trả
As: Nhân viên quản trị phụ trách xử lý đổi trả
I want: xem danh sách yêu cầu bảo hành của khách hàng
So that: tôi có thể theo dõi và lựa chọn yêu cầu cần xử lý

## Acceptance Tests

1. Happy Path – Xem danh sách yêu cầu bảo hành
Requirements:
Given tôi là admin đã đăng nhập
When tôi truy cập trang quản lý yêu cầu bảo hành
Then hệ thống hiển thị danh sách yêu cầu bảo hành
And mỗi yêu cầu hiển thị thông tin cơ bản (mã đơn hàng, khách hàng, trạng thái, ngày tạo)

2. Happy Path – Xem chi tiết yêu cầu bảo hành
Requirements:
Given tôi là admin đã đăng nhập
And danh sách có yêu cầu bảo hành
When tôi chọn một yêu cầu bảo hành
Then hệ thống hiển thị chi tiết yêu cầu
And hiển thị sản phẩm cần đổi trả
And hiển thị lý do yêu cầu
And hiển thị trạng thái yêu cầu

3. Unhappy Path – Người dùng không phải admin truy cập
Requirements:
Given tôi là người dùng thường đã đăng nhập
When tôi truy cập trang quản lý yêu cầu bảo hành
Then hệ thống từ chối truy cập
And hiển thị thông báo "Không có quyền truy cập"

## Business Rules ( User yêu cầu bảo hành)
- Người dùng phải đăng nhập trước khi gửi yêu cầu đổi trả
- Đơn hàng phải thuộc về người dùng hiện tại
- Đơn hàng phải đã giao thành công
- Sản phẩm phải nằm trong thời gian đổi trả quy định
- Người dùng phải chọn sản phẩm trong đơn hàng để đổi trả


## Business Logic ( User yêu cầu bảo hành )
- Hệ thống kiểm tra người dùng đã đăng nhập
- Hệ thống kiểm tra đơn hàng thuộc về người dùng
- Hệ thống kiểm tra trạng thái đơn hàng là delivered
- Hệ thống kiểm tra thời gian đổi trả trong vòng 7 ngày
- Người dùng chọn sản phẩm cần đổi trả
- Người dùng nhập lý do đổi trả tối thiểu 10 ký tự
- Hệ thống validate dữ liệu
- Nếu hợp lệ, hệ thống tạo yêu cầu đổi trả với trạng thái "Chờ xử lý"
=======================================================


## Business Rules (Admin xử lý yêu cầu bảo hành)
- Chỉ admin mới được truy cập trang quản lý yêu cầu bảo hành
- Admin có thể xem tất cả yêu cầu đổi trả của người dùng
- Admin chỉ được xử lý các yêu cầu có trạng thái "Chờ xử lý"
- Admin có thể chấp nhận hoặc từ chối yêu cầu đổi trả
- Khi admin xử lý, trạng thái yêu cầu phải được cập nhật
- Mỗi yêu cầu chỉ được xử lý một lần
- Admin có thể xem chi tiết sản phẩm trong yêu cầu đổi trả

## Business Logic (Admin xử lý yêu cầu bảo hành)
- Admin đăng nhập vào hệ thống quản trị
- Hệ thống hiển thị danh sách yêu cầu đổi trả
- Admin chọn một yêu cầu cần xử lý
- Hệ thống hiển thị chi tiết yêu cầu (đơn hàng, sản phẩm, lý do)
- Admin chọn hành động "Chấp nhận" hoặc "Từ chối"
- Nếu admin chọn "Chấp nhận"
- Hệ thống cập nhật trạng thái thành "Đã chấp nhận"
- Nếu admin chọn "Từ chối"
- Admin nhập lý do từ chối
- Hệ thống cập nhật trạng thái thành "Đã từ chối"
- Hệ thống lưu thông tin người xử lý và thời gian xử lý
- Sau khi xử lý, yêu cầu không được chỉnh sửa lại



