<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {!! app('seotools')->generate() !!}


    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/materialdesignicons.css') }}" />

    <!--    bootstrap------------------------------->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}" />
    <!--    owl.carousel---------------------------->
    <link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}" />
    <!--    fancybox-------------------------------->
    <link rel="stylesheet" href="{{ asset('assets/css/fancybox.min.css') }}">
    <!--    responsive------------------------------>
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/noUISlider.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-slider.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/custome.css')}}?{{time()}}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="{{ url('/manifest.json') }}">
    <meta name="theme-color" content="#fff" />
   
    <style>
        :root {
            --primary: {{ $design->primary_color }};
            --primary-light: {{ $design->primary_light }};
            --primary-dark: {{ $design->primary_dark }};
            --background: {{ $design->body_color }};
            --text: {{ $design->text_color }};
            --border-radius: {{ $design->border_radius }}px;
            --menu-bg: {{ $design->menu_bg }};
            --menu-color: {{ $design->menu_color }};
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryItems = document.querySelectorAll('.category-item');
            const submenuContents = document.querySelectorAll('.submenu-content');

            categoryItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    const targetId = this.dataset.category;

                    categoryItems.forEach(cat => cat.classList.remove('active'));
                    submenuContents.forEach(content => content.classList.remove('active'));

                    this.classList.add('active');
                    document.getElementById(targetId).classList.add('active');
                });
            });
        });
    </script>


    <livewire:front.layouts.head />
</head>

<body>
    <!--header------------------------------------->
    <livewire:front.layouts.header />

    {{ $slot }}

    <livewire:front.layouts.footer />

    <script src="{{ asset('assets/js/jquery-3.2.1.min.js') }}"></script>

    <!--    bootstrap-------------------------------->
    <script src="{{ asset('assets/js/bootstrap.js') }}"></script>
    <!--    owl.carousel----------------------------->
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    <!--fancybox------------------------------------->
    <script src="{{ asset('assets/js/jquery.fancybox.min.js') }}"></script>

    <script src="{{ asset('assets/js/jquery.countdown.min.js') }}"></script>
    <script src="{{ asset('assets/js/nouislider.min.js') }}"></script>
    <script src="{{ asset('assets/js/wNumb.js') }}"></script>

    <!--countdown------------------------------------>
    @stack('jsBeforMain')

    <!--main----------------------------------------->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'center',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        document.addEventListener('livewire:load', () => {
            Livewire.on('toast', (type, message) => {
                Toast.fire({
                    icon: type,
                    title: message
                })

            })
        })
        ClassicEditor
            .create(document.querySelector('.editor'), {
                language: {
                    ui: 'fa',
                    content: 'fa'
                }
            })

            .catch(error => {
                console.error(error);
            });
    </script>
    @stack('jsPanel')


    <livewire:scripts />

</body>

</html>
