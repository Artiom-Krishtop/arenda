$(function () {
   $('.js_link_form_review').click(function () {
       var elementClick = $('.form_services');
       var destination = $(elementClick).offset().top;

       $('html').animate({ scrollTop: destination }, 600);
       return false;
   });
});