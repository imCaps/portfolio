$(document).ready(function() {
    if($('.progress-tracking-list li#files').length) {
        $('.progress-tracking-list li#files').addClass('active');
        $('.progress-tracking-list li#files').prevAll().addClass('active');
    }
});
