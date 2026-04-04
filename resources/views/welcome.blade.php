<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    @vite('resources/css/welcome.css')
</head>
<body>
    <div class="top-strip">THÁNG 4 | Siêu sale làm đẹp - Đăng ký/Đăng nhập để nhận voucher độc quyền!</div>

    <div class="nav">
        <div class="brand">
            <div class="brand-badge">H</div>
            Hasaki Style
        </div>
        <div class="search">
            <input type="text" placeholder="Tìm sản phẩm, thương hiệu...">
            <button>Tìm kiếm</button>
        </div>
        <div class="auth-links">
            @auth
                <span style="font-weight:700;">{{ auth()->user()->name }}</span>
                <form method="POST" action="/logout" style="margin:0;">
                    @csrf
                    <button class="btn btn-outline" type="submit">Đăng xuất</button>
                </form>
            @else
                <a class="btn btn-outline" href="/login">Đăng nhập</a>
                <a class="btn btn-solid" href="/register">Đăng ký</a>
            @endauth
        </div>
    </div>

    @auth
        <div class="profile-float">
            <div class="profile-name">{{ auth()->user()->name }}</div>
            <div class="profile-email">{{ auth()->user()->email }}</div>
            <form method="POST" action="/logout" style="margin:0;">
                @csrf
                <button class="btn logout-btn" type="submit">Đăng xuất</button>
            </form>
        </div>
    @endauth

    <div class="hero">
        <div class="banner">
            <div class="ribbon">🎉 Ưu đãi 4.4 – Giảm tới 70%</div>
            <h2>Ở đâu rẻ hơn, Hasaki rẻ hơn!</h2>
            <p>Miễn phí giao nhanh nội thành, freeship toàn quốc đơn từ 249k. Mua mỹ phẩm chính hãng, thanh toán an toàn.</p>
            <div class="banner-cards">
                <div class="mini-card">
                    <div>🚚</div>
                    <strong>Giao 2H</strong>
                    <small style="color:var(--muted)">TP.HCM & Cần Thơ</small>
                </div>
                <div class="mini-card">
                    <div>🏬</div>
                    <strong>Hệ thống 200+ cửa hàng</strong>
                    <small style="color:var(--muted)">Mở cửa 8h-22h</small>
                </div>
                <div class="mini-card">
                    <div>🛡️</div>
                    <strong>Bảo hành chính hãng</strong>
                    <small style="color:var(--muted)">Hoàn tiền nếu giả</small>
                </div>
                <div class="mini-card">
                    <div>🎁</div>
                    <strong>Voucher thành viên</strong>
                    <small style="color:var(--muted)">Đăng nhập để nhận</small>
                </div>
            </div>
        </div>
        <div class="side">
            <div class="side-card">
                <h4>Mua lần đầu?</h4>
                <p style="margin:0 0 10px;color:var(--muted)">Tạo tài khoản để lưu đơn, theo dõi vận chuyển và nhận mã giảm giá.</p>
                <div class="chips">
                    <span class="chip">Freeship</span>
                    <span class="chip">Hoàn xu</span>
                    <span class="chip">Ưu tiên xử lý</span>
                </div>
                <div style="margin-top:12px; display:flex; gap:8px;">
                    @guest
                        <a class="btn btn-solid" style="flex:1" href="/register">Đăng ký ngay</a>
                        <a class="btn btn-outline" style="flex:1" href="/login">Đăng nhập</a>
                    @else
                        <a class="btn btn-solid" style="flex:1" href="/dashboard">Vào dashboard</a>
                    @endguest
                </div>
            </div>
            <div class="side-card">
                <h4>Ứng dụng Hasaki</h4>
                <p style="margin:0 0 10px;color:var(--muted)">Quét mã để tải app, nhận thông báo khuyến mãi sớm nhất.</p>
                <div class="chips">
                    <span class="chip">iOS</span>
                    <span class="chip">Android</span>
                    <span class="chip">Ưu đãi app</span>
                </div>
            </div>
        </div>
    </div>

    <div class="cats">
        <div class="cat">🧴<span>Chăm sóc da</span></div>
        <div class="cat">💄<span>Trang điểm</span></div>
        <div class="cat">🧖‍♀️<span>Clinic & SPA</span></div>
        <div class="cat">💅<span>Makeup</span></div>
        <div class="cat">🧼<span>Tắm gội</span></div>
        <div class="cat">🍼<span>Mẹ & bé</span></div>
        <div class="cat">🦷<span>Chăm sóc răng</span></div>
        <div class="cat">🏋️‍♀️<span>Thực phẩm chức năng</span></div>
    </div>

    <div class="flash">
        <div class="section-title">
            <h3>Flash Deals</h3>
            @guest
                <a href="/login" style="color:var(--green);font-weight:700;">Đăng nhập để lưu deal</a>
            @else
                <span style="color:var(--green);font-weight:700;">Deal dành riêng cho bạn</span>
            @endguest
        </div>
        <div class="grid">
            <div class="product">
                <div class="img-placeholder">CeraVe</div>
                <strong>Sữa rửa mặt Cerave 236ml</strong>
                <div class="tag">-23%</div>
                <div class="price">268.000 đ</div>
                <small class="muted">Giao nhanh 2H</small>
            </div>
            <div class="product">
                <div class="img-placeholder" style="background:linear-gradient(135deg,#d1f4ff,#f0fbff);color:#0284c7;">La Roche-Posay</div>
                <strong>Kem chống nắng Anthelios</strong>
                <div class="tag">-30%</div>
                <div class="price">325.000 đ</div>
                <small class="muted">HSD xa</small>
            </div>
            <div class="product">
                <div class="img-placeholder" style="background:linear-gradient(135deg,#ffe2d5,#fff4ec);color:#e11d48;">Anessa</div>
                <strong>Chống nắng Anessa 60ml</strong>
                <div class="tag">New</div>
                <div class="price">417.000 đ</div>
                <small class="muted">Deal độc quyền</small>
            </div>
            <div class="product">
                <div class="img-placeholder" style="background:linear-gradient(135deg,#e0f2fe,#e0fce5);color:#16a34a;">Cocoon</div>
                <strong>Combo Cocoon Sen Hậu Giang</strong>
                <div class="tag">-25%</div>
                <div class="price">246.000 đ</div>
                <small class="muted">Tặng kèm quà mini</small>
            </div>
        </div>
    </div>

    <footer>Đăng nhập hoặc đăng ký để xem giá ưu đãi & tích điểm nhanh hơn.</footer>
</body>
</html>
