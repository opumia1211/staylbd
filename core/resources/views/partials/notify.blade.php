<link rel="stylesheet" href="{{ asset('assets/global/css/iziToast.min.css') }}?v={{ $assetVersion ?? config('app.version') }}">
<script src="{{ asset('assets/global/js/iziToast.min.js') }}?v={{ $assetVersion ?? config('app.version') }}"></script>
@if(session()->has('notify'))
    @foreach(session('notify') as $msg)
        <script>
            "use strict";
            iziToast.{{ $msg[0] }}({message:"{{ __($msg[1]) }}", position: "topRight", timeout: 1200, transitionIn: 'fadeInDown', transitionOut: 'fadeOutUp', displayMode: 'replace'});
        </script>
    @endforeach
@endif

@if (isset($errors) && $errors->any())
    @php
        $collection = collect($errors->all());
        $errors = $collection->unique();
    @endphp

    <script>
        "use strict";
        @foreach ($errors as $error)
        iziToast.error({
            message: '{{ __($error) }}',
            position: "topRight",
            timeout: 2500
        });
        @endforeach
    </script>

@endif
<script>
    "use strict";
    function notify(status, message) {
        var opts = {
            message: typeof message == 'string' ? message : (message && message[0]) ? message[0] : '',
            position: 'topRight',
            timeout: 1200,
            transitionIn: 'fadeInDown',
            transitionOut: 'fadeOutUp',
            displayMode: 'replace',
            closeOnClick: true
        };
        if (typeof message == 'string') {
            iziToast[status](opts);
        } else if (message && message.length) {
            message.forEach(function(val) {
                iziToast[status]({ message: val, position: opts.position, timeout: opts.timeout, transitionIn: opts.transitionIn, transitionOut: opts.transitionOut, displayMode: opts.displayMode, closeOnClick: opts.closeOnClick });
            });
        }
    }
</script>

