# USER STORY 3 - Yêu cầu đổi trả hàng - ACCEPTANCE TEST CHECKLIST

## 1. Happy Path – Gửi yêu cầu đổi trả thành công

### Requirements:
- Given tôi đã đăng nhập vào hệ thống
- And tôi có đơn hàng đã nhận trong thời gian đổi trả hợp lệ
- When tôi chọn sản phẩm trong đơn hàng và nhấn "Yêu cầu đổi trả"
- And tôi nhập lý do đổi trả hợp lệ
- And tôi gửi yêu cầu
- Then hệ thống tạo yêu cầu đổi trả thành công
- And trạng thái yêu cầu là "Chờ xử lý"

### Implementation Status:
- ✅ Đăng nhập yêu cầu: Middleware auth() trên tất cả warranty routes
- ✅ Mock đơn hàng hợp lệ: 'valid_received' với received_at = 4 ngày trước
- ✅ Chọn sản phẩm: Form checkbox cho mỗi sản phẩm (products-grid)
- ✅ Nhập lý do: Textarea "Lý do yêu cầu đổi trả" (min: 10 ký tự)
- ✅ Gửi yêu cầu: Form POST /warranty/request
- ✅ Tạo thành công: WarrantyRequest::create() trong store()
- ✅ Trạng thái = "Chờ xử lý": Default status trong create()
- ✅ Test: test_valid_order_request_creates_warranty_record_and_redirects PASS

---

## 2. Unhappy Path – Đơn hàng không nằm trong thời gian đổi trả

### Requirements:
- Given tôi có đơn hàng đã quá thời gian đổi trả (>7 ngày từ nhận hàng)
- When tôi gửi yêu cầu đổi trả
- Then hệ thống hiển thị thông báo "Đơn hàng đã hết thời gian đổi trả"
- And yêu cầu không được tạo

### Implementation Status:
- ✅ Mock đơn hàng hết hạn: 'expired' với received_at = 15 ngày trước
- ✅ Kiểm tra quá hạn: orderIsExpired() method
- ✅ Hiển thị thông báo: withErrors(['order_key' => 'Đơn hàng đã hết thời gian đổi trả'])
- ✅ Không tạo yêu cầu: Redirect back nếu expired
- ✅ Test: test_expired_order_request_shows_error PASS

---

## 3. Unhappy Path – Đơn hàng không hợp lệ

### Requirements:
- Given đơn hàng của tôi chưa được giao hoặc đã bị hủy
- When tôi gửi yêu cầu đổi trả
- Then hệ thống hiển thị thông báo "Đơn hàng không đủ điều kiện đổi trả"
- And yêu cầu không được tạo

### Implementation Status:
- ✅ Mock đơn hàng không hợp lệ: 'invalid' với status = 'cancelled'
- ✅ Kiểm tra hợp lệ: orderIsInvalid() method - kiểm tra status !== 'delivered'
- ✅ Hiển thị thông báo: withErrors(['order_key' => 'Đơn hàng không đủ điều kiện đổi trả'])
- ✅ Không tạo yêu cầu: Redirect back nếu invalid
- ✅ Test: test_invalid_order_request_shows_error PASS

---

## 4. Unhappy Path – Chưa nhập lý do đổi trả

### Requirements:
- Given tôi mở form yêu cầu đổi trả
- When tôi không nhập lý do và nhấn gửi
- Then hệ thống hiển thị thông báo "Vui lòng nhập lý do đổi trả"
- And con trỏ focus vào ô lý do
- And yêu cầu không được tạo

### Implementation Status:
- ✅ Form reason textarea: id="reason" name="reason"
- ✅ Validation required: 'reason' => ['required', 'string', 'min:10']
- ✅ Error message: 'reason.required' => 'Vui lòng nhập lý do đổi trả'
- ✅ Auto-focus trên error: @error('reason') autofocus @enderror
- ✅ Không tạo yêu cầu: Validation reject
- ✅ Test: test_missing_reason_shows_validation_error PASS

---

## 5. Unhappy Path – Chưa chọn sản phẩm đổi trả

### Requirements:
- Given tôi có đơn hàng gồm nhiều sản phẩm
- When tôi không chọn sản phẩm nào và nhấn gửi yêu cầu
- Then hệ thống hiển thị thông báo "Vui lòng chọn sản phẩm cần đổi trả"
- And yêu cầu không được tạo

### Implementation Status:
- ✅ Form products: checkbox array với name="products[]"
- ✅ Mock sản phẩm: 'valid_received' có 3 sản phẩm
- ✅ Validation: 'products' => ['required', 'array', 'min:1']
- ✅ Error message: 'products.required' => 'Vui lòng chọn sản phẩm cần đổi trả'
- ✅ Không tạo yêu cầu: Validation reject
- ✅ Test: test_missing_products_shows_validation_error PASS

---

## ADDITIONAL FEATURES (Beyond Acceptance Tests)

### 6. Xem danh sách yêu cầu bảo hành
- ✅ Route: GET /warranty/requests → warranty.index
- ✅ Controller: index() method - lấy tất cả requests của user
- ✅ View: warranty/index.blade.php - hiển thị table
- ✅ Authorization: Filter by user_id
- ✅ Test: test_authenticated_user_can_view_warranty_requests_list PASS

### 7. Xem chi tiết yêu cầu bảo hành
- ✅ Route: GET /warranty/requests/{warrantyRequest} → warranty.show
- ✅ Controller: show() method - lấy single request
- ✅ View: warranty/show.blade.php - hiển thị chi tiết
- ✅ Authorization: Check user_id match (403 nếu khác)
- ✅ Test: test_authenticated_user_can_view_warranty_request_detail PASS
- ✅ Test: test_user_cannot_view_other_user_warranty_request PASS

### 8. Dashboard Integration
- ✅ Button "Yêu cầu bảo hành" → route('warranty.request')
- ✅ Button "Xem lịch sử" → route('warranty.index')
- ✅ Green theme styling (#198754)

---

## SUMMARY

### ✅ Hoàn thành đầy đủ (9/9 tests):
1. ✅ test_authenticated_user_can_view_warranty_request_form
2. ✅ test_valid_order_request_creates_warranty_record_and_redirects
3. ✅ test_expired_order_request_shows_error
4. ✅ test_invalid_order_request_shows_error
5. ✅ test_missing_products_shows_validation_error
6. ✅ test_missing_reason_shows_validation_error
7. ✅ test_authenticated_user_can_view_warranty_requests_list
8. ✅ test_authenticated_user_can_view_warranty_request_detail
9. ✅ test_user_cannot_view_other_user_warranty_request

### ✅ Tất cả acceptance test requirements đã được thỏa mãn:
1. ✅ Acceptance Test 1: Happy Path - Gửi yêu cầu thành công (status "Chờ xử lý")
2. ✅ Acceptance Test 2: Unhappy Path - Đơn hàng quá thời gian đổi trả
3. ✅ Acceptance Test 3: Unhappy Path - Đơn hàng không hợp lệ
4. ✅ Acceptance Test 4: Unhappy Path - Chưa nhập lý do (với autofocus)
5. ✅ Acceptance Test 5: Unhappy Path - Chưa chọn sản phẩm

### Acceptance Test Coverage:
- 5/5 User Story 3 acceptance test scenarios ✅ 100% COMPLETE
- 2/2 Additional feature scenarios ✅ PASS

---

## NOTES

- ✅ Tất cả validation rules khớp với yêu cầu acceptance tests
- ✅ Tất cả error messages khớp với yêu cầu acceptance tests
- ✅ Tất cả business logic (warranty window, order status check) đúng
- ✅ UX cải thiện: autofocus trên error field (reason)
- ✅ Feature hoàn chỉnh: submit → list → detail workflow
