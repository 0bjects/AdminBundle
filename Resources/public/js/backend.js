function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            if ($(input).parent().find('img.imageThumb').length > 0) {
                $(input).parent().find('img.imageThumb').attr('src', e.target.result);
                if ($(input).parent().find('a.removeImage').length > 0) {
                    $(input).parent().find('a.removeImage').remove();
                }
            } else {
                $(input).parent().append('<img class="imageThumb" width="60" height="60" src="' + e.target.result + '"/>');
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function removeImage(anchor) {
    $.ajax({
        url: $(anchor).parent().find('input').attr('data-image-remove-url'),
        success: function() {
            $(anchor).parent().find('img').remove();
            $(anchor).remove();
        }
    });
}
$(document).ready(function() {
    $('textarea').livequery(function() {
        $(this).autosize();
    });
    CKEDITOR.replaceAll('ckeditor');
    $('input[data-image-url]').livequery(function() {
        $(this).parent().append('<img class="imageThumb" width="60" height="60" src="' + $(this).attr('data-image-url') + '"/>');
    });
    $('input[data-image-remove-url]').livequery(function() {
        $(this).parent().append('<a class="removeImage" style="margin-left: 7px;" href="javascript:void(0)" onclick="removeImage(this);"><i class="icon-remove"></i></a>');
    });
    $('input[data-class="datetime"]').livequery(function() {
        var self = $(this);
        self.prop('type', 'text');
        self.datetimepicker({
            changeMonth: true,
            changeYear: true,
            hourGrid: 4,
            minuteGrid: 10,
            timeFormat: 'hh:mm',
            dateFormat: 'yy-mm-dd',
            constrainInput: true
        });
        self.parent().addClass('input-append');
        self.parent().css('display', 'block');
        self.parent().append('<span class="add-on" onclick="$(this).prev().focus();"><i class="icon-calendar"></i></span>');
    });
    $('input[data-class="date"]').livequery(function() {
        var self = $(this);
        self.prop('type','text');
        self.datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            constrainInput: true
        });
        self.parent().addClass('input-append');
        self.parent().css('display', 'block');
        self.parent().append('<span class="add-on" onclick="$(this).prev().focus();"><i class="icon-calendar"></i></span>');
    });
    $('input[data-class="time"]').livequery(function() {
        var self = $(this);
        self.prop('type','text');
        self.timepicker();
        self.parent().addClass('input-append');
        self.parent().css('display', 'block');
        self.parent().append('<span class="add-on" onclick="$(this).prev().focus();"><i class="icon-globe"></i></span>');
    });
    $('.chzn-select').livequery(function() {
        $(this).chosen();
    });
    $('.chzn-select-deselect').livequery(function() {
        $(this).chosen({allow_single_deselect: true});
    });
});