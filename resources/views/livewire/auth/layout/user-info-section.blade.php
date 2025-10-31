<div>
    <div class="content-header mb-4">
        <h3 class="mb-1">اطلاعات شخصی</h3>
        <p>اطلاعات خود را وارد کنید</p>
    </div>

    <div class="row g-3">
    <div class="col-12">
        <label class="form-label" for="name">نام و نام خانوادگی</label>
        <input wire:model.live="data.name" type="text" class="form-control" id="name" placeholder="نام شما" />
        @error('name')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="email">ایمیل</label>
        <input wire:model.live="data.email" type="email" class="form-control" id="email" placeholder="your@email.com" />
        @error('email')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
        <div class="col-12">
        <label class="form-label" for="password">کلمه عبور</label>
            <div class="input-group input-group-merge form-password-toggle">
                <input wire:model.live="data.password" type="password" id="password" class="form-control" placeholder="••••••••" />
                <span class="input-group-text cursor-pointer" onclick="togglePassword('password', this)"><i class="ti ti-eye-off"></i></span>
        </div>
        @error('password')
            <span class="text-danger d-block mt-1">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="password_confirmation">تکرار کلمه عبور</label>
            <div class="input-group input-group-merge form-password-toggle">
                <input wire:model.live="data.password_confirmation" type="password" id="password_confirmation" class="form-control" placeholder="••••••••" />
                <span class="input-group-text cursor-pointer" onclick="togglePassword('password_confirmation', this)"><i class="ti ti-eye-off"></i></span>
        </div>
        @error('password_confirmation')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-12">
        <button type="button" wire:click="submit" wire:loading.attr="disabled" class="btn btn-primary w-100">
            <span wire:loading.remove wire:target="submit" >مرحله بعد</span>
            <span wire:loading wire:target="submit" >در حال ذخیره...</span>
        </button>
    </div>
    </div>
</div>


