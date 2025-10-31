<!DOCTYPE html>
<html class="light-style layout-navbar-fixed layout-wide" data-assets-path="assets/" data-template="front-pages"
    data-theme="theme-default" dir="rtl" lang="fa">

<head>
    <title>معرفی قالب داشبورد مدیریت ویکسی - VUEXY</title>

    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
        name="viewport" />
<style>
    .dropdown-toggle::after {
    display: none !important;

}
    </style>
    <meta content="" name="description" />
    @include('livewire.front.partials.head')
</head>

<body>

    @include('livewire.front.partials.header')

     {{ $slot }}
    @include('livewire.front.partials.footer')

    @include('livewire.front.partials.scripts')

</body>

</html>
