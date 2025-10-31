<div>
    @section('page-title', 'وبسایت‌های من')
    <div class="scrollspy-example bg-body" data-bs-spy="scroll">
        <!-- Hero: Start -->
        <section id="hero-animation">
            <div class="section-py landing-hero position-relative" id="landingHero">
                <div class="container">
                    <div class="row g-4 mt-2">
                        @forelse($websites as $website)
                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="card-title mb-1">{{ $website->name }}</h5>
                                                <div class="text-muted small ltr">
                                                    {{ $website->domain ?? ($website->subdomain ?: '—') }}
                                                </div>
                                            </div>
                                            <span class="badge bg-label-{{ $website->status_color ?? 'primary' }}">
                                                {{ $website->status_label ?? 'نامشخص' }}
                                            </span>
                                        </div>

                                        <ul class="list-unstyled small mb-0 text-muted">
                                            <li class="d-flex justify-content-between align-items-center mb-1">
                                                <span>تاریخ ایجاد</span>
                                                <span
                                                    class="fw-semibold">{{ optional($website->created_at)->format('Y/m/d') }}</span>
                                            </li>
                                            @php($subscription = $website->activeSubscription)
                                            <li class="d-flex justify-content-between align-items-center mb-1">
                                                <span>اعتبار اشتراک</span>
                                                <span class="fw-semibold">
                                                    @if ($subscription && optional($subscription->end_date)->isFuture())
                                                        {{ optional($subscription->end_date)->format('Y/m/d') }}
                                                        <span class="badge bg-label-success ms-2">
                                                            {{ now()->diffInDays($subscription->end_date, false) }} روز
                                                            باقی‌مانده
                                                        </span>
                                                    @elseif($subscription)
                                                        <span class="badge bg-label-danger">منقضی شده</span>
                                                    @else
                                                        <span class="badge bg-label-secondary">بدون اشتراک فعال</span>
                                                    @endif
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="card border-dashed h-100">
                                    <div class="card-body text-center py-5">
                                        <div class="avatar avatar-lg bg-label-primary mb-3">
                                            <span class="avatar-initial rounded-circle"><i
                                                    class="ti ti-world"></i></span>
                                        </div>
                                        <h5 class="mb-2">هنوز وبسایتی ایجاد نکرده‌اید</h5>
                                        <p class="text-muted mb-0">بعد از ساخت وبسایت، لیست آن‌ها اینجا نمایش داده
                                            می‌شود.</p>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
            <div class="landing-hero-blank mobile-show"></div>
        </section>
        <br>
        <br>
        <br>
        <br>
        <br> 
        <br>

        <!-- Hero: End -->
    </div>

</div>
