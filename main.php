<?php
/*
Plugin Name: Vervemail Subscriber

Description: Vervemail Subscriber is a simple  subscription plugin which supports Vervemail.
Author: Marvin Chacon and Mariela Gonzalez, ActRight
Version: 2.5

 * 
 */

if (!class_exists('XMLVervemail'))
    require_once dirname(__FILE__) . '/XMLVervemail.php';

function wp_vervemail_register_widget() {
    register_widget( 'wp_vervemail' );
}
add_action('widgets_init', 'wp_vervemail_register_widget');



class wp_vervemail extends WP_Widget {
    
    function wp_vervemail(){
        
        add_action('wp_enqueue_scripts', array(&$this, 'register_scripts'));
        $widget_ops = array( 
            'classname' => 'wp_vervemail',
            'description' => 'This widget will subscribe people to Vervemail',
        );
        
        $this->WP_Widget( 'wp_vervemail','Vervemail Subscriber' ,$widget_ops );
        
    }//end constructor
   
    function register_scripts() { 
        // JS    
        wp_register_script('wp-vervemail-parsley-js', plugins_url('js/parsley.min.js', __FILE__), array('jquery'),null); 
        wp_enqueue_script('wp-vervemail-parsley-js');
        wp_register_script('wp-vervemail-main-js', plugins_url('js/main.js', __FILE__), array('jquery'),null); 
        wp_enqueue_script('wp-vervemail-main-js');
        // CSS     
        wp_register_style('wp-vervemail-main-css', plugins_url('css/main.css', __FILE__),array(),null);
        wp_enqueue_style('wp-vervemail-main-css');
    }  
    
    public function get_defaults() {
        return array(
            'api_key' => '', 
            'shared_key'=> '', 
            'segment_id' => '',

            'title' => 'Get more stuff like this<br/> <span>in your inbox</span>',
            'text' => 'Subscribe to our mailing list and get interesting stuff and updates to your email inbox.',
            'email_placeholder' => 'Enter your email here.',
            'button_text' => 'Sign Up Now', 'wp-vervemail',
            'success_message' => 'Thank you for subscribing.', 'wp-vervemail',
            'error_message' => 'Something went wrong.', 
            'already_subscribed_message' =>'This email is already subscribed.',
            'footer_text' => 'we respect your privacy and take protecting it seriously',
            'firstname'=>'no'
        );
    }
    
    public function output_text_field($setting_name, $setting_label, $setting_value) {
        ?>

        <p class="wp-subscribe-<?php echo $setting_name; ?>-field">
            <label for="<?php echo $this->get_field_id($setting_name) ?>">
                <?php echo $setting_label ?>
            </label>

            <input class="widefat" 
                   id="<?php echo $this->get_field_id($setting_name) ?>" 
                   name="<?php echo $this->get_field_name($setting_name) ?>" 
                   type="text" 
                   value="<?php echo esc_attr($setting_value) ?>" />
        </p>

        <?php
    }
    
        public function output_textarea_field($setting_name, $setting_label, $setting_value) {
        ?>

        <p class="wp-subscribe-<?php echo $setting_name; ?>-field">
            <label for="<?php echo $this->get_field_id($setting_name) ?>">
                <?php echo $setting_label ?>
            </label>

            <textarea class="widefat" id="<?php echo $this->get_field_id($setting_name) ?>" name="<?php echo $this->get_field_name($setting_name) ?>"><?php echo esc_attr($setting_value); ?></textarea>
        </p>

        <?php
    }
    
    public function output_checkbox_field( $setting_name, $setting_label , $setting_value ){
        ?>
        <p  class="wp-subscribe-<?php echo $setting_name; ?>-field" >
            <label for="<?php echo $this->get_field_id($setting_name) ?>">
                <?php echo $setting_label ?>
            </label>
            <input type="checkbox" name="<?php echo $this->get_field_name($setting_name); ?>" value="yes" <?php if($setting_value =="yes") echo "checked"; ?> >
            
    <?php }
    
    function form($instance) {
            // Valores por defecto
            $defaults = $this->get_defaults();
            // Se hace un merge, en $instance quedan los valores actualizados
            $instance = wp_parse_args((array)$instance, $defaults);
            // Cogemos los valores
            $api_key = $instance['api_key'];
            $shared_key = $instance['shared_key'];
            $segment_id= $instance['segment_id'];
            
            // Mostramos el formulario
            ?>
            <p>
                API Key
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('api_key');?>"
                       value="<?php echo esc_attr($api_key) ;?>"/>
            </p>
            <p>
                Shared Key
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('shared_key');?>"
                       value="<?php echo esc_attr($shared_key);?>"/>
            </p>
            <p>
                Segment ID
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('segment_id');?>"
                       value="<?php echo esc_attr($segment_id);?>"/>
            </p>

        <div class="wp_subscribe_labels" style="">    
            <?php 
            $this->output_textarea_field('title', 'Title',  $instance['title']);
            $this->output_text_field('text', 'Text',  $instance['text']);
            $this->output_text_field('email_placeholder', 'Email Placeholder', $instance['email_placeholder']);
            $this->output_text_field('button_text','Button Text', $instance['button_text']);
            $this->output_text_field('success_message', 'Success Message', $instance['success_message']);
            //$this->output_text_field('error_message', 'Error Message',  $instance['error_message']);
            $this->output_text_field('already_subscribed_message', 'Error: Already Subscribed', $instance['already_subscribed_message']);
            $this->output_text_field('footer_text', 'Footer Text', $instance['footer_text']);
            echo "<h3>Optional Fields<h3>";
            $this->output_checkbox_field('firstname',"First Name",$instance['firstname'] );
            $this->output_checkbox_field('lastname',"Last Name",$instance['lastname'] );
            $this->output_checkbox_field('zipcode',"Zipcode",$instance['zipcode'] );
            $this->output_checkbox_field('city',"City",$instance['city'] );
            $this->output_checkbox_field('state',"State",$instance['state'] );
            $this->output_checkbox_field('address',"Address",$instance['address'] );
            
         
            ?>
        </div><!-- .wp_subscribe_labels -->
            
        <?php
    }//end form
    
    function update($new_instance, $old_instance) {
        


        return $new_instance;

    }//end update
  
    function vervemail_subscribe($api_key , $shared_key , $segment){
        $vervemail= new XMLVervemail( $api_key ,$shared_key );
        $vervemail->setEmailSuscriber( $_POST["vervemail_email"] );
        require_once 'wp-config.php';
        $DB = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
        $DB->query("CREATE TABLE IF NOT EXISTS `vervemail_plugin_subcribers` (
                    `firstname` text NOT NULL,
                    `lastname` text NOT NULL,
                    `address` text NOT NULL,
                    `city` text NOT NULL,
                    `state` text NOT NULL,
                    `zipcode` text NOT NULL,
                    `email` text NOT NULL,
                    `result` text NOT NULL,
                    `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
                  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
        $query ="INSERT INTO vervemail_plugin_subcribers(";
        
       // die();
        if( isset($_POST["vervemail_firstname"])){
            $vervemail->updateSuscriberFirstName( $_POST["vervemail_firstname"] );
            $query = $query ."`firstname`";
            $values= "'" .$_POST["vervemail_firstname"]."'"; 
        }
        if( isset($_POST["vervemail_lastname"])){
            $vervemail->updateSuscriberLastName( $_POST["vervemail_lastname"] );
            $query = $query .",`lastname`";
            $values= $values.",'".$_POST["vervemail_lastname"]."'"; 
        }
        if( isset($_POST["vervemail_address"])){
            $vervemail->updateSuscriberAddress( $_POST["vervemail_address"] );
            $query = $query .",`address`";
            $values= $values.",'".$_POST["vervemail_address"]."'";             
        }
        if( isset($_POST["vervemail_city"])){
            $vervemail->updateSuscriberCity( $_POST["vervemail_city"] );
            $query = $query .",`city`";
            $values= $values.",'" .$_POST["vervemail_city"]."'"; 
        }if( isset($_POST["vervemail_state"] )){
            $vervemail->updateSuscriberState( $_POST["vervemail_state"] );
            $query = $query .",`state`";
            $values= $values.",'" .$_POST["vervemail_state"]."'"; 
        }
        if( isset($_POST["vervemail_zipcode"] )){
            $vervemail->updateSuscriberZipCode( $_POST["vervemail_zipcode"] );
            $query = $query .",`zipcode`";
            $values= $values.",'" .$_POST["vervemail_zipcode"]."'"; 
        }
       


        
        $vervemail->addSuscribertToSegment( $segment );
        $responseVervemail =  $vervemail->sendToVerveMail();

        if(strpos( $responseVervemail, "<responseCode><![CDATA[201]]>")!== false){
            $values = "VALUES(".$values.",'".$_POST["vervemail_email"]."','successful')";
            $query =$query.",`email`,`result`) $values"; 
            $DB->query($query);
            $DB->close();
            return true;
        }else if( strpos( $responseVervemail, "<responseCode><![CDATA[200]]>")!== false ) {
            $values = "VALUES(".$values.",'".$_POST["vervemail_email"]."','email already subscribed')";
            $query = $query.",`email`,`result`) $values"; 
            $DB->query($query);
            $DB->close();
            return 200; 
        }
        else if( "pure message vervemail"){// Errors returned from vervemail
            $values = "VALUES(".$values.",'".$_POST["vervemail_email"]."','$responseVervemail')";
            $query = $query.",`email`,`result`) $values"; 
            $DB->query($query);
            $DB->close();
            $response = simplexml_load_string(  $responseVervemail ,'SimpleXMLElement',LIBXML_NOCDATA );
            if( isset( $response->item->responseText ) ){
              return $response->item->responseText;
            }
            if( isset ($response->item->responseData->message) ){
                return $response->item->responseData->message;
            }
             if( isset ($response->item->responseData->error) ){
                 return $response->item->responseData->error;
             }
        }
    

}
    
    function widget($args, $instance) {
        extract( $args );
        $defaults = $this->get_defaults();
        $instance = wp_parse_args( (array) $instance, $defaults ); 
        if(  $_POST["widget-vervemail-id"]  == $this->id   ){
            $response_verve = $this->vervemail_subscribe( $instance['api_key'] ,$instance['shared_key'] ,$instance['segment_id'] );
            if( $response_verve === true){ //Added the new subscriber
                if( $instance["success_message"] != "")
                    $success_message = $instance["success_message"];
                else
                    $success_message ="";
                $message = '<h4 class="title">Thank You For Joining</h4><p class="text">'.$success_message.'</p>';
             }else if( $response_verve === 200){ //Already subscribed user
                if( $instance["already_subscribed_message"] != ""){
                    $error_message = $instance["already_subscribed_message"];
                    $message='<h3 class="title">Something went wrong</h3><p class="text">'. $error_message.'</p>';
                }else{ //In case already subscribed is missing
                    if( $instance["success_message"] != ""){
                        $success_message = $instance["success_message"];
                    }else{
                        $success_message ="";
                    }
                    $message = '<h4 class="title">Thank You For Joining</h4><p class="text">'.$success_message.'</p>';

                }//
             }
             else {// Errors returned by vervemail
                 $message='<h3 class="title">Something went wrong</h3><p class="text">'. $response_verve.'</p>';
             }
              
            require_once dirname(__FILE__) . '/thanks-modal.php';
        }//
        // Before widget (defined by themes). 
        echo $before_widget;
       
        // Display Widget 
        
            ?>
            <div class="wp-vervemail-content">
            <h4 class="title"><?php echo $instance['title'];?></h4>
            <p class="text"><?php echo $instance['text'];?></p>
            <form action="" method="post" data-parsley-validate>
                <div>
                <?php $this->input_checkbox($instance,"firstname","First Name"); 
                      $this->input_checkbox($instance,"lastname","Last Name");
                ?>
                <input class="vervemail-field email-field" type="text" value="" placeholder="<?php echo $instance['email_placeholder']; ?>" name="vervemail_email" data-parsley-required="true" data-parsley-type="email" data-parsley-error-message="Please enter a valid email">
                <?php 
                      $this->input_checkbox($instance,"address","Address");
                      $this->input_checkbox($instance,"city","City");
                      $this->input_checkbox($instance,"state","State");
                      $this->input_checkbox($instance,"zipcode","Zip Code");
                ?>
                </div>
                <input class="submit" name="submit" type="submit" value="<?php echo $instance['button_text']; ?>">
                <input type="hidden" name="widget-vervemail-id" value="<?php echo $this->id; ?>" />
            </form>
            <div class="clear"></div>
                
            <p class="footer-text"><?php echo $instance['footer_text'];?></p>
                
            </div><!--subscribe_widget-->
            
            

            
            <?php 

              
              /* After widget (defined by themes). */
               echo $after_widget;

   
    
    
}

function input_checkbox($instance,$name_field , $place_holder){
    if ( $instance[$name_field] =="yes" ){ ?>
        <input class="vervemail-field <?php echo $name_field; ?>-field" type="text"  placeholder="<?php echo $place_holder; ?>" name="vervemail_<?php echo $name_field; ?>" data-parsley-required="true" data-parsley-error-message="Please enter a valid <?php echo $name_field; ?>">
    <?php }    
}



}


?>