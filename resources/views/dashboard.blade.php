<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasaki Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/dashboard.css')
</head>
<body>
    <div class="dashboard-shell">
        <header class="dashboard-header">
            <div class="brand-block">
                <div class="brand-logo">HASAKI</div>
                <p class="brand-subtitle">Giao diện quản lý đổi/trả và bảo hành đẹp mắt, dễ dùng.</p>
            </div>

            <form class="dashboard-search" method="GET" action="/dashboard">
                <label class="sr-only" for="search">Tìm kiếm</label>
                <input id="search" type="search" name="q" placeholder="Tìm đơn hàng, sản phẩm hoặc trạng thái...">
                <button type="submit">🔎</button>
            </form>

            <div class="profile-card">
                <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <div>
                    <p class="profile-name">{{ $user->name }}</p>
                    <p class="profile-role">{{ ucfirst($user->role) }}</p>
                </div>
            </div>
        </header>

        <section class="hero-panel">
            <div class="hero-copy">
                <span class="hero-tag">DASHBOARD KHÁCH HÀNG</span>
                <h1>Quản lý hành trình yêu cầu của bạn thật nhanh, thật trong.</h1>
                <p>Trang dashboard mới dành cho bạn, kết hợp phong cách Hasaki với trải nghiệm rõ ràng, trực quan và thân thiện trên mọi thiết bị.</p>

                <div class="hero-actions">
                    <a class="button button-primary" href="{{ route('warranty.request') }}">Tạo yêu cầu mới</a>
                    <a class="button button-secondary" href="{{ route('warranty.index') }}">Xem lịch sử bảo hành</a>
                </div>
            </div>

            <aside class="hero-summary">
                <div class="summary-title">Tổng quan nhanh</div>
                <div class="summary-count">{{ $requestCount ?? 0 }}</div>
                <p class="summary-note">Yêu cầu bảo hành hiện tại của bạn</p>
                <div class="summary-stats">
                    <div>
                        <span>{{ $pendingCount ?? 0 }}</span>
                        <small>Đang chờ</small>
                    </div>
                    <div>
                        <span>{{ $completedCount ?? 0 }}</span>
                        <small>Đã xử lý</small>
                    </div>
                </div>
                <div class="summary-chip">Thời gian xử lý mục tiêu: <strong>1-2 ngày</strong></div>
            </aside>
        </section>

        <section class="metric-grid">
            <article class="metric-card accent">
                <p>Tổng yêu cầu</p>
                <h2>{{ $requestCount ?? 0 }}</h2>
            </article>
            <article class="metric-card">
                <p>Đang chờ xử lý</p>
                <h2>{{ $pendingCount ?? 0 }}</h2>
            </article>
            <article class="metric-card">
                <p>Yêu cầu đã xử lý</p>
                <h2>{{ $completedCount ?? 0 }}</h2>
            </article>
            <article class="metric-card">
                <p>Mục tiêu phản hồi</p>
                <h2>1-2 ngày</h2>
            </article>
        </section>

        <section class="dashboard-grid">
            <article class="panel panel-actions">
                <div class="panel-header">
                    <div>
                        <h2>Hành động nhanh</h2>
                        <p>Những thao tác thường dùng để gửi hoặc kiểm tra yêu cầu.</p>
                    </div>
                </div>

                <div class="quick-actions">
                    <a class="action-card" href="{{ route('warranty.request') }}">
                        <strong>Tạo yêu cầu mới</strong>
                        <span>Gửi yêu cầu đổi trả hoặc bảo hành ngay.</span>
                    </a>
                    <a class="action-card" href="{{ route('warranty.index') }}">
                        <strong>Xem lịch sử</strong>
                        <span>Kiểm tra trạng thái chi tiết từng yêu cầu.</span>
                    </a>
                    <a class="action-card" href="#">
                        <strong>Hướng dẫn</strong>
                        <span>Xem cách thức gửi yêu cầu chính xác.</span>
                    </a>
                    <a class="action-card" href="#">
                        <strong>Liên hệ hỗ trợ</strong>
                        <span>Chat nhanh hoặc gửi mail tới đội ngũ Hasaki.</span>
                    </a>
                </div>
            </article>

            <article class="panel panel-recent">
                <div class="panel-header">
                    <div>
                        <h2>Yêu cầu gần nhất</h2>
                        <p>Danh sách 4 yêu cầu mới nhất của bạn.</p>
                    </div>
                    <a class="text-link" href="{{ route('warranty.index') }}">Xem tất cả</a>
                </div>

                @if(isset($recentRequests) && $recentRequests->count())
                    <div class="request-list">
                        @foreach($recentRequests as $request)
                            <a href="{{ route('warranty.show', $request) }}" class="request-item">
                                <div>
                                    <strong>{{ $request->order_number }}</strong>
                                    <span>{{ $request->order_status }}</span>
                                </div>
                                <div>
                                    <span class="request-status {{ $request->status === 'Chờ xử lý' ? 'status-pending' : ($request->status === 'Đã chấp nhận' ? 'status-approved' : 'status-rejected') }}">
                                        {{ $request->status }}
                                    </span>
                                    <small>{{ \Illuminate\Support\Carbon::parse($request->created_at)->diffForHumans() }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <p>Bạn chưa có yêu cầu nào. Hãy bắt đầu gửi yêu cầu bảo hành để nhận hỗ trợ nhanh chóng.</p>
                        <a class="button button-primary" href="{{ route('warranty.request') }}">Tạo yêu cầu ngay</a>
                    </div>
                @endif
            </article>

            <article class="panel panel-support">
                <div class="support-card">
                    <div class="support-header">
                        <span>Hỗ trợ 24/7</span>
                        <strong>Đội ngũ Hasaki luôn sẵn sàng.</strong>
                    </div>
                    <p>Gửi yêu cầu, theo dõi tiến trình và nhận phản hồi nhanh chóng. Mọi vấn đề đều được xử lý theo tiêu chuẩn uy tín Hasaki.</p>
                    <div class="support-actions">
                        <a class="button button-secondary" href="#">Chat ngay</a>
                        <a class="button button-outline" href="#">Email hỗ trợ</a>
                    </div>
                </div>
            </article>
        </section>

        <section class="insight-row">
            <article class="insight-card">
                <h3>Thương hiệu đáng tin cậy</h3>
                <p>Hasaki được tin dùng bởi hàng triệu khách hàng Việt với cam kết sản phẩm chính hãng và dịch vụ tận tâm.</p>
            </article>
            <article class="insight-card">
                <h3>Quy trình đơn giản</h3>
                <p>Bạn chỉ cần nêu lý do, chọn sản phẩm và gửi yêu cầu; mọi bước còn lại sẽ được đội ngũ xử lý nhanh chóng.</p>
            </article>
        </section>
    </div>
</body>
</html>
