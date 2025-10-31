<div id="otp-section" class="row g-3" wire:poll.1s="decrementTimer">
    <div class="col-12 d-flex align-items-center justify-content-between">
        <label class="form-label mb-0">شماره تلفن: {{ $phone }}</label>
        <button type="button" class="btn btn-link btn-sm" wire:click="editPhone">ویرایش شماره</button>
    </div>
    <div class="col-12">
        <label class="form-label d-block mb-2">کد تایید 5 رقمی</label>
        <div class="auth-input-wrapper d-flex align-items-center justify-content-between ltr">
            @foreach ($digits as $index => $value)
                <input wire:model="digits.{{ $index }}" type="text"
                    class="form-control auth-input h-px-50 text-center mx-1"
                    maxlength="1" pattern="[0-9]" inputmode="numeric" />
            @endforeach
        </div>
        @error('code')
            <span class="text-danger d-block">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-12">
        <button wire:click="submit" wire:loading.attr="disabled" class="btn btn-primary w-100">
            <span wire:loading.remove wire:target="submit">بزن بریم!</span>
            <span wire:loading wire:target="submit">در حال تایید...</span>
        </button>
    </div>
    <div class="col-12 text-center">
        @if ($secondsLeft > 0)
            <span class="text-muted">
                ارسال مجدد کد بعد از {{ floor($secondsLeft / 60) }}:{{ str_pad($secondsLeft % 60, 2, '0', STR_PAD_LEFT) }}
            </span>
        @else
            <button type="button" wire:click="resend" wire:loading.attr="disabled" class="btn btn-link mt-2">
                <span wire:loading.remove wire:target="resend">ارسال مجدد کد</span>
                <span wire:loading wire:target="resend">در حال ارسال...</span>
            </button>
        @endif
    </div>
</div>
