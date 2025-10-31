<div>
    <div class="content-header mb-4">
        <h3 class="mb-1">اطلاعات وبسایت</h3>
        <p>اطلاعات وبسایت خود را وارد کنید</p>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <label class="form-label" for="websiteName">نام وبسایت</label>
            <input wire:model.live="websiteName" type="text" class="form-control" id="websiteName"
                placeholder="مثال: فروشگاه اینترنتی" />
            @if (isset($errorsState['websiteName']))
                <span class="text-danger">{{ $errorsState['websiteName'] }}</span>
            @endif
        </div>
        <div class="col-12">
            <label class="form-label" for="subdomain">زیردامنه</label>
            <div class="input-group">
                <input wire:model.live="subdomain" type="text" class="form-control" id="subdomain"
                    placeholder="my-store" />
                <span class="input-group-text">.domain.com</span>
            </div>
            @if (isset($errorsState['subdomain']))
                <span class="text-danger">{{ $errorsState['subdomain'] }}</span>
            @endif
            <small class="form-text text-muted">فقط حروف کوچک انگلیسی، اعداد و خط تیره</small>
        </div>
        <div class="col-12">
            <label class="form-label" for="description">توضیحات (اختیاری)</label>
            <textarea wire:model.live="description" class="form-control" id="description" rows="3"
                placeholder="توضیحات وبسایت"></textarea>
        </div>
        <div class="col-12">
            <button type="button" wire:click="submit" wire:loading.attr="disabled" class="btn btn-success w-100">
                <span wire:loading.remove wire:target="submit">ثبت نام و ورود به پنل</span>
                <span wire:loading wire:target="submit">در حال ثبت...</span>
            </button>
        </div>
    </div>
</div>
