// Setup modal values - use live reference so it works when modal is in DOM
function getCuModalForm() {
    var cuModal = $("#cuModal");
    return cuModal.length ? cuModal.find("form").first() : $();
}

function getBaseAction(formEl) {
    if (!formEl || !formEl.length) return null;
    var base = formEl.attr("data-base-action") || (formEl[0] && formEl[0].action);
    if (base && typeof base === 'string') return base.replace(/\/\d+\s*$/, '').replace(/\/$/, '');
    return base;
}

$(document).on("click", ".cuModalBtn", function (e) {
    e.preventDefault();
    e.stopPropagation();
    var cuModal = $("#cuModal");
    if (!cuModal.length) return;
    // Ensure modal is in body so it appears above backdrop (gray overlay)
    var modalEl = cuModal[0];
    if (modalEl.parentNode && modalEl.parentNode !== document.body) {
        document.body.appendChild(modalEl);
    }
    var form = cuModal.find("form").first();
    if (!form.length) return;
    var baseAction = getBaseAction(form) || (form[0] && form[0].action);
    var data = $(this).data();
    var resource = null;
    try {
        resource = data.resource != null ? data.resource : null;
        if (typeof resource === 'string') resource = JSON.parse(resource);
    } catch (err) { resource = null; }

    if (!resource) {
        form.trigger("reset");
        if (baseAction) form.attr("action", baseAction);
        cuModal.find('textarea').text('');
        cuModal.find(".status").empty();
    }
    cuModal.find(".modal-title").text(data.modal_title || "");
    if (resource) {
        var id = resource.id;
        if (baseAction && id != null) form.attr("action", baseAction + "/" + id);
        if (resource.image_with_path) {
            cuModal.find(".profilePicPreview")
                .css("background-image", "url(" + resource.image_with_path + ")")
                .addClass("has-image");
        }
        if (data.has_status) {
            cuModal.find(".status").html(
                '<div class="form-group"><label class="fw-bold">Status</label>' +
                '<input type="checkbox" data-width="100%" data-height="50" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="Enabled" data-off="Disabled" name="status">' +
                '</div>'
            );
            if (typeof cuModal.find("[name=status]").bootstrapToggle === 'function') {
                cuModal.find("[name=status]").bootstrapToggle();
            }
        }
        var fields = cuModal.find("input, select, textarea");
        fields.each(function () {
            var el = this;
            var $el = $(el);
            var fieldName = el.name;
            if (!$el.hasClass('profilePicUpload')) {
                if (fieldName === "_token") return;
                var key = fieldName;
                if (key && key.length >= 2 && key.substring(key.length - 2) === "[]") key = key.substring(0, key.length - 2);
                if (!key || resource[key] === undefined) return;
                if (el.tagName === "TEXTAREA") {
                    if ($el.hasClass("nicEdit")) $(".nicEdit-main").html(resource[key]);
                    else $el.val(resource[key]);
                } else if ($el.data("toggle") === "toggle") {
                    if (resource[key] != 0) $el.bootstrapToggle("on"); else $el.bootstrapToggle("off");
                } else if (el.type !== "file") {
                    var val = resource[key];
                    $el.val($.isNumeric(val) ? val * 1 : (val || ""));
                }
            } else {
                $el.removeAttr("required");
            }
        });
    }
    if (cuModal.length && cuModal[0]) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modalInstance = bootstrap.Modal.getOrCreateInstance(cuModal[0]);
            modalInstance.show();
        } else {
            cuModal.modal("show");
        }
    }
});