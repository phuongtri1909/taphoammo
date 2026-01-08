@include('client.layouts.partials.header')

@include('components.sweetalert')
@include('components.toast-main')
@include('components.toast')

@yield('content')
@include('components.contact_widget')
@include('components.top_button')

@include('client.layouts.partials.footer')
