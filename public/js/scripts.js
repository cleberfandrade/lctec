$(document).on('blur', ".maiusculo", function () {
    $(this).val(function (_, val) {
        return val.toUpperCase();
    });
});
