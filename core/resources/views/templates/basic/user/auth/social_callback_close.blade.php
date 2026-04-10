<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@lang('Login')</title>
    <link rel="stylesheet" href="{{ storefront_compiled_stylesheet_url('critical-storefront') }}" crossorigin="anonymous">
    <script>
        (function(){
            var redirectUrl = {!! json_encode($redirectUrl ?? '') !!};
            if (window.opener && window.opener.location) {
                if (redirectUrl && redirectUrl.length > 0) {
                    try { window.opener.location.href = redirectUrl; } catch (e) { window.opener.location.reload(); }
                } else {
                    window.opener.location.reload();
                }
            }
            try { window.close(); } catch (e) {}
            setTimeout(function(){ window.location = redirectUrl && redirectUrl.length ? redirectUrl : '/'; }, 800);
        })();
    </script>

</head>
<body class="st-social-callback"></body>
</html>


