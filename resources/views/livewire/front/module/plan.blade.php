<div>
    <section class="py-5 landing-demos bg-body lh-2" id="landingDemos">
        <div class="container-fluid container-lg">
            <h3 class="text-center mb-2 fw-semibold wow fadeInUp delay-0-2s">
                <span class="section-title fw-semibold">دمــوهای آمــاده</span>
                بــرای سـلیقه‌های مـتفاوت
            </h3>
            <div class="mb-3 mb-md-5 wow fadeInUp delay-0-4s">
                <p class="text-center fs-big fw-normal mb-0">
                    ویکسی دارای شخصی ساز آسان نیز می‌باشد و درلحظه می‌توانید قالب را مطابق میل خود تغییر دهید.
                </p>
                <p class="text-center fs-big fw-normal mobile-hidden">
                    درساخت این قالب از آخرین ترندهای طراحی وب استفاده شده و همواره بروزرسانی می‌شود.
                </p>
            </div>
            <div class="demo-wrapper mb-5 row gy-3 gx-0 g-sm-3 g-md-4">
                @foreach ($plans as $plan)
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="demo-panel w-c wow fadeInUp delay-0-2s">
                            <div class="demo-panel-header d-flex row">
                                <h5 class="title-demo-header fw-semibold w-c">{{ $plan->title }}</h5>
                                <div class="btn-group button-demo-header w-c me-0 pe-0" role="group">
                                    <a class="btn btn-label-primary waves-effect w-c end-0" 
                                       href="template/demo/transmitter/?template=vertical" 
                                       target="_blank" 
                                       type="button">RTL</a>
                                    <a class="btn btn-label-primary waves-effect w-c end-0" 
                                       href="template/demo/transmitter/?template=vertical&direction=LTR" 
                                       target="_blank" 
                                       type="button">LTR</a>
                                </div>
                            </div>
                            <div class="demo-panel-image-wrapper mt-3">
                                <a href="template/demo/transmitter/?template=vertical" target="_blank">
                                    <img alt="Vuexy VueJS Admin Template" 
                                         class="demo-panel-image" 
                                         decoding="async" 
                                         src="{{ asset('front/assets/img/shots/demo-vertical-layout.png') }}" 
                                         title="">
                                    <div class="backdrop">
                                        <div class="text">مشاهده دمو</div>
                                    </div>
                                </a>
                            </div>
                            
                            <!-- بخش قیمت‌ها و دکمه خرید -->
                            <div class="plan-prices mt-3 p-3 border-top">
                                <h6 class="text-center mb-3">پلن‌های قیمت‌گذاری:</h6>
                                
                                @foreach($plan->prices as $price)
                                    <div class="price-item d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                        <div>
                                            <span class="fw-bold">{{ $price->duration }} ماهه</span>
                                            <br>
                                            <small class="text-success fw-bold">{{ number_format($price->price) }} تومان</small>
                                        </div>
                                        <button 
                                            class="btn btn-primary btn-sm"
                                            wire:click="purchasePlan({{ $plan->id }}, {{ $price->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="purchasePlan({{ $plan->id }}, {{ $price->id }})"
                                        >
                                            <span wire:loading.remove wire:target="purchasePlan({{ $plan->id }}, {{ $price->id }})">
                                                خرید
                                            </span>
                                            <span wire:loading wire:target="purchasePlan({{ $plan->id }}, {{ $price->id }})">
                                                <span class="spinner-border spinner-border-sm me-1"></span>
                                                ...
                                            </span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- نمایش خطا -->
            @error('payment')
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @enderror
        </div>
    </section>
</div>