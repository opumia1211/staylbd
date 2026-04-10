<div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content confirmation-modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">@lang('Confirmation Alert!')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="confirmationModalForm" action="" method="POST" class="confirmation-modal-form">
                @csrf
                <div class="modal-body">
                    <p class="question text-dark mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush
@push('script')
<script>
    (function ($) {
        "use strict";
        function ensureModalInBody() {
            var modal = document.getElementById('confirmationModal');
            if (modal && modal.parentNode && modal.parentNode !== document.body) {
                document.body.appendChild(modal);
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', ensureModalInBody);
        } else {
            ensureModalInBody();
        }
        $(document).on('click', '.confirmationBtn', function (e) {
            e.preventDefault();
            ensureModalInBody();
            var modal = document.getElementById('confirmationModal');
            var form = document.getElementById('confirmationModalForm');
            var questionEl = modal ? modal.querySelector('.question') : null;
            var action = $(this).data('action');
            var question = $(this).data('question');
            if (!action || !form) return;
            if (questionEl && question) questionEl.textContent = question;
            form.setAttribute('action', action);
            if (typeof bootstrap !== 'undefined' && modal) {
                var bsModal = bootstrap.Modal.getOrCreateInstance(modal);
                bsModal.show();
            } else if ($.fn.modal && $('#confirmationModal').length) {
                $('#confirmationModal').find('.question').text(question);
                $('#confirmationModal').find('form').attr('action', action);
                $('#confirmationModal').modal('show');
            }
        });
    })(jQuery);
</script>
@endpush
