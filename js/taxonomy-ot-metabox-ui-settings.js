(function ($) {
        
  $(document).on('click', '.option-tree-section-add', function() {
    hide_divs(100);
    hide_divs(500);
    hide_divs(1000);
   });
    
    function hide_divs(time){
         setTimeout(function(){
         $('.post_type_section_field').hide();
         $('.template_page_section_field').hide();
         
        
     },time)
    }


})(jQuery); 

