<div class="row g-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <label class="form-label mb-0">شماره تلفن: {{ $phone }}</label>
            <button type="button" class="btn btn-link btn-sm" wire:click="$dispatch('password-edit-phone')">ویرایش شماره</button>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label" for="loginPassword">کلمه عبور</label>
        <div class="input-group input-group-merge form-password-toggle">
            <input wire:model.live="password" type="password" id="loginPassword" class="form-control"
                placeholder="••••••••" />
            <span class="input-group-text cursor-pointer" onclick="togglePassword('loginPassword', this)"><i class="ti ti-eye-off"></i></span>
        </div>
        @error('password')
            <span class="text-danger d-block mt-1">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-12">
        <button type="button" wire:click="submit" wire:loading.attr="disabled" class="btn btn-primary w-100">
            <span wire:loading.remove wire:target="submit">ورود</span>
            <span wire:loading wire:target="submit">در حال ورود...</span>
        </button>
    </div>
    <div class="col-12 d-flex justify-content-between">
        <a href="{{ route('password.request') }}" class="btn btn-link p-0">فراموشی کلمه عبور</a>
        <button type="button" class="btn btn-link p-0" wire:click="switch" wire:loading.attr="disabled" wire:target="switch">ورود با کد یکبار مصرف</button>
    </div>
</div>
