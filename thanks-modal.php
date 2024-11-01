<?php  
$modal_result    ='<div id="subscribeModalmesage" class="modal fade subscribeModal" tabindex="-1" role="dialog" aria-labelledby="subscribeModalmesage" aria-hidden="true">'
                .'<div class="modal-dialog">'
                    .'<div class="modal-content">'
                        .'<div class="close_button">CLOSE<a href="javascript:void(0);" class="MModalClose"><img src="'.plugins_url('images/close.png', __FILE__).'" width="42" height="42"></a></div>'
                        .'<div id="wp_subscribe-3" class="widget wp_vervemail"> '           
                            .'<div class="wp-vervemail-content">'
                                        
                                       .$message
                                         
                                    .'<form>'
                                           .'<input class="submit MModalClose" type="button" value="CLOSE">'
                                    .'</form>'
                                    .'<div class="clear"></div>'

                            .'</div><!--subscribe_widget-->.'
                            .'</div>'
                    .'</div>'
                .'</div>'
            .'</div>';

            ?>
 <script>
     jQuery( document ).ready(function (){
        jQuery("body").after('<?php echo $modal_result; ?>');
        jQuery('#subscribeModalmesage').addClass('in');
     } );
     
  </script>