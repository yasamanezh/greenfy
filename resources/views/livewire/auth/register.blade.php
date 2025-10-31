<div>
    @if ($currentStep == 'auth' || $currentStep == 'verify')
        <!-- فرم اولیه بدون stepper -->
        <div class="content-header mb-4">
            <h3 class="mb-1">ورود / ثبت نام</h3>
            <p>{{ $currentStep == 'auth' ? 'شماره تلفن خود را وارد کنید' : 'کد تایید را وارد کنید' }}</p>
        </div>

        @if ($message)
            <div class="alert alert-{{ $messageType }}">{{ $message }}</div>
        @endif

        @if ($currentStep == 'auth')
            <livewire:auth.layout.phone-section :phone="$phone" />
        @elseif($currentStep == 'verify')
            <livewire:auth.layout.verify-section :phone="$phone" />
        @endif
    @endif
    
    @if($currentStep == 'password-login')
        <livewire:auth.layout.password-login-section :phone="$phone" />
    @endif
    
    @if($currentStep == 'user-info' || $currentStep == 'website-info')
        <!-- Stepper برای مراحل بعدی -->
        @if ($message)
            <div class="alert alert-{{ $messageType }} mb-4">{{ $message }}</div>
        @endif

        <div class="bs-stepper shadow-none" id="multiStepsRegistration">
            <div class="bs-stepper-header border-bottom-0">
                <div class="step {{ $currentStep == 'user-info' ? 'active' : ($currentStep == 'website-info' ? 'complete' : '') }}"
                    data-target="#userInfoValidation">
                    <button class="step-trigger" type="button">
                        <span class="bs-stepper-circle">
                            <i class="ti ti-user ti-sm"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">اطلاعات کاربری</span>
                            <span class="bs-stepper-subtitle">نام و ایمیل</span>
                        </span>
                    </button>
                </div>
                <div class="line">
                    <i class="ti ti-chevron-right"></i>
                </div>
                <div class="step {{ $currentStep == 'website-info' ? 'active' : '' }}"
                    data-target="#websiteInfoValidation">
                    <button class="step-trigger" type="button">
                        <span class="bs-stepper-circle">
                            <i class="ti ti-world ti-sm"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">وبسایت</span>
                            <span class="bs-stepper-subtitle">اطلاعات سایت</span>
                        </span>
                    </button>
                </div>
            </div>

            <div class="bs-stepper-content">
                <form id="multiStepsForm" onSubmit="return false">

                    @if ($currentStep == 'user-info')
                        <livewire:auth.layout.user-info-section :phone="$phone" :data="$userInfo" />
                    @endif

                    @if ($currentStep == 'website-info')
                        <livewire:auth.layout.website-info-section :phone="$phone" :data="$websiteInfo" />
                    @endif
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const stepperElement = document.querySelector('#multiStepsRegistration');
                if (stepperElement && typeof Stepper !== 'undefined') {
                    const stepper = new Stepper(stepperElement);

                    // مقداردهی اولیه stepper بر اساس currentStep
                    @if ($currentStep == 'website-info')
                        stepper.to(2);
                    @else
                        stepper.to(1);
                    @endif
                }
            });
        </script>
    @endif

    @script
    <script>
        $wire.on('resendCode', () => {
            console.log('Resend code triggered');
            startResendTimer();
        });
        
        $wire.on('code-sent', () => {
            console.log('Code sent event');
            startResendTimer();
        });
        
        function startResendTimer() {
            if ($wire.currentStep !== 'verify') return;
            
            const resendTimerEl = document.getElementById('resend-timer');
            const resendBtn = document.getElementById('resend-btn');
            
            if (!resendTimerEl || !resendBtn) return;
            
            resendTimerEl.style.display = 'block';
            resendBtn.style.display = 'none';
            resendBtn.setAttribute('disabled', 'disabled');
            
            let resendTimeLeft = 120;
            
            const updateTimer = () => {
                const minutes = Math.floor(resendTimeLeft / 60);
                const seconds = resendTimeLeft % 60;
                resendTimerEl.textContent = `ارسال مجدد کد بعد از ${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                if (resendTimeLeft <= 0) {
                    resendTimerEl.style.display = 'none';
                    resendBtn.style.display = 'inline-block';
                    resendBtn.removeAttribute('disabled');
                    return false;
                }
                return true;
            };
            
            updateTimer();
            const interval = setInterval(() => {
                resendTimeLeft--;
                if (!updateTimer()) {
                    clearInterval(interval);
                }
            }, 1000);
        }
        
        function initOTPInputs() {
            if ($wire.currentStep !== 'verify') return;
            
            const inputs = Array.from(document.querySelectorAll('.auth-input'));
            if (inputs.length !== 5) return;
            
            inputs.forEach((input, index) => {
                if (input.dataset.otpInitialized === 'true') return;
                input.dataset.otpInitialized = 'true';
                
                input.addEventListener('input', function(e) {
                    const value = e.target.value.replace(/[^0-9]/g, '');
                    e.target.value = value;
                    
                    if (value && index < inputs.length - 1) {
                        setTimeout(() => {
                            inputs[index + 1].focus();
                            inputs[index + 1].select();
                        }, 0);
                    }
                });
                
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        setTimeout(() => {
                            inputs[index - 1].focus();
                            inputs[index - 1].select();
                        }, 0);
                    }
                });
            });
            
            startResendTimer();
        }
        
        initOTPInputs();
        
        Livewire.hook('morph.updated', () => {
            initOTPInputs();
        });
    </script>
  
    @endscript
</div>
