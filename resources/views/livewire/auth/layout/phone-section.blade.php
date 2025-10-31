<div class="row g-3">
    <div class="col-12">
        <label class="form-label" for="phone">شماره تلفن</label>
        <div class="input-group">
            <input wire:model.live.debounce.300ms="phone" type="text" class="form-control ltr" id="phone"
                placeholder="09123456789" />
            <span class="input-group-text">IR (+98)</span>
        </div>
        @error('phone')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-12">
        <button wire:click="submit" wire:loading.attr="disabled" class="btn btn-primary w-100">
            <span wire:loading.remove wire:target="submit"  >بزن بریم!  </span>
            <span wire:loading wire:target="submit" >در حال ارسال...</span>
        </button>
    </div>
    <div class="col-12 text-center mt-3">
        <a href="{{ $forgotPasswordUrl }}" class="btn btn-link">رمز عبور را فراموش کرده‌اید؟</a>
    </div>
</div>
