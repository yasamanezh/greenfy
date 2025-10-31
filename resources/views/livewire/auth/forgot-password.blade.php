<div>
    

@php
$illustration = 'auth-two-steps-illustration.png';
@endphp
<div class="content-header mb-4">
<h3 class="mb-1">فراموشی رمز عبور</h3>
<p>لطفاً مراحل را طی کنید</p>
</div>

@if($message)
<div class="alert alert-{{ $messageType }}">{{ $message }}</div>
@endif

@if($step == 'phone')
<div class="row g-3">
    <div class="col-12">
        <label class="form-label" for="phone">شماره تلفن</label>
        <div class="input-group">
            <input wire:model="phone" type="text" class="form-control ltr" id="phone" placeholder="09123456789" />
            <span class="input-group-text">IR (+98)</span>
        </div>
        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    <div class="col-12">
        <button wire:click="sendCode" wire:loading.attr="disabled" class="btn btn-primary w-100">
            <span wire:loading.remove wire:target="sendCode">ارسال کد تایید</span>
            <span wire:loading wire:target="sendCode">در حال ارسال...</span>
        </button>
    </div>
    <div class="col-12 text-center mt-3">
        <a href="{{ route('login') }}" class="btn btn-link">بازگشت به ورود</a>
    </div>
</div>
@elseif($step == 'verify')
<div class="row g-3">
    <div class="col-12">
        <label class="form-label" for="verificationCode">کد تایید</label>
        <input wire:model="verificationCode" type="text" class="form-control text-center" id="verificationCode" placeholder="00000" maxlength="5" />
        @error('verificationCode') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    <div class="col-12">
        <button wire:click="verifyCode" wire:loading.attr="disabled" class="btn btn-primary w-100">
            <span wire:loading.remove wire:target="verifyCode">تایید</span>
            <span wire:loading wire:target="verifyCode">در حال تایید...</span>
        </button>
    </div>
</div>
@elseif($step == 'reset')
<div class="row g-3">
    <div class="col-12">
        <label class="form-label" for="newPassword">رمز عبور جدید</label>
        <div class="input-group input-group-merge">
            <input wire:model="newPassword" type="password" class="form-control" id="newPassword" />
            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
        </div>
        @error('newPassword') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="confirmPassword">تایید رمز عبور</label>
        <div class="input-group input-group-merge">
            <input wire:model="confirmPassword" type="password" class="form-control" id="confirmPassword" />
            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
        </div>
        @error('confirmPassword') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    <div class="col-12">
        <button wire:click="resetPassword" wire:loading.attr="disabled" class="btn btn-success w-100">
            <span wire:loading.remove wire:target="resetPassword">تغییر رمز عبور</span>
            <span wire:loading wire:target="resetPassword">در حال تغییر...</span>
        </button>
    </div>
</div>
@endif
</div>