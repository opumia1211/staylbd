<div class="overflow-x-auto w-full">
    <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse']) }}>
        @if(isset($thead))
            <thead class="bg-slate-50/50 text-slate-500 uppercase text-[0.7rem] font-bold tracking-wider">
                <tr>
                    {{ $thead }}
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
            {{ $slot }}
        </tbody>
        @if(isset($tfoot))
            <tfoot class="bg-slate-50/30">
                <tr>
                    {{ $tfoot }}
                </tr>
            </tfoot>
        @endif
    </table>
</div>
