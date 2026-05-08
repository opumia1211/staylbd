@props(['id', 'title' => null, 'size' => 'max-w-2xl'])

<div id="{{ $id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-[1050] justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full {{ $size }} max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-100 transform transition-all duration-300 scale-95 opacity-0 modal-content-anim">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-5 border-b border-slate-100 rounded-t-2xl bg-slate-50/20">
                <h3 class="text-lg font-bold text-slate-800">
                    {{ $title }}
                </h3>
                <button type="button" class="text-slate-400 bg-transparent hover:bg-slate-100 hover:text-slate-900 rounded-xl text-sm w-9 h-9 ms-auto inline-flex justify-center items-center transition-colors" data-modal-hide="{{ $id }}">
                    <i class="las la-times text-xl"></i>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6 space-y-4">
                {{ $slot }}
            </div>
            <!-- Modal footer -->
            @if(isset($footer))
                <div class="flex items-center justify-end gap-3 p-5 border-t border-slate-100 rounded-b-2xl bg-slate-50/20">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

<div id="{{ $id }}-backdrop" class="admin-modal-overlay hidden opacity-0 transition-opacity duration-300"></div>

<style>
    .modal-open #{{ $id }} { display: flex; }
    .modal-open #{{ $id }} .modal-content-anim { scale: 1; opacity: 1; }
    .modal-open #{{ $id }}-backdrop { display: block; opacity: 1; }
</style>

