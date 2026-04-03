<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@lang('Login')</title>
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
    <style>html,body{background:#fff;margin:0}</style>
</head>
<body></body>
</html>


