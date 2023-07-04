                </div>
			</section>
            <?php global $awp_options; ?>
            <div class="clearfix"></div>

            <div class="modal fade" id="login_modal" data-backdrop="static" data-keyboard="true" tabindex="-1">
                <div class="vertical-alignment-helper">
                    <div class="modal-dialog vertical-align-center">
                        <div class="modal-content">
                            <div class="modal-body">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e("Close", "automotive"); ?></span></button>
                                
                                <h4><?php _e("Login to access different features", "automotive"); ?></h4>

                                <input type="text" placeholder="<?php _e("Username", "automotive"); ?>" class="username_input margin-right-10 margin-vertical-10">
                                <input type="password" placeholder="<?php _e("Password", "automotive"); ?>" class="password_input margin-right-10 margin-vertical-10">

                                <div class="clearfix"></div>

                                <input type="checkbox" name="remember_me" value="yes" id="remember_me"> <label for="remember_me" class="margin-bottom-10"><?php _e("Remember Me", "automotive"); ?></label><br>

                                <button class="ajax_login md-button" data-nonce="<?php echo wp_create_nonce("ajax_login_none"); ?>"><?php _e("Login", "automotive"); ?></button>

                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>
            </div><!-- /.modal -->
            
			<!--Footer Start-->
            <?php 
            wp_reset_postdata();

            global $post;

            $footer_area = (is_404() || is_search() || (function_exists("is_shop") && is_shop()) ? "" : get_post_meta( $post->ID, "footer_area", true ));
            $footer_area = (isset($footer_area) && !empty($footer_area) ? $footer_area : "default-footer");

            // footer text
            if(isset($awp_options['footer_text']) && !empty($awp_options['footer_text'])){ 
                $wp_link       = "<a href='http://www.wordpress.org'>WordPress</a>";
                $theme_link    = "<a href='http://www.themesuite.com'>Automotive</a>";
                $loginout_link = wp_loginout("", false);
                $blog_title    = get_bloginfo('name');
                $blog_link     = site_url();
                $the_year      = date("Y");
                                
                $search  = array("{wp-link}", "{theme-link}", "{loginout-link}", "{blog-title}", "{blog-link}", "{the-year}");
                $replace = array($wp_link, $theme_link, $loginout_link, $blog_title, $blog_link, $the_year);
                
                $footer_text = str_replace($search, $replace, $awp_options['footer_text']);
            } 

            if($footer_area != "no-footer" && $awp_options['footer_widgets']){ ?>
                <footer>
                    <div class="container">
                        <div class="row">
                            <?php dynamic_sidebar($footer_area); ?>
                        </div>
                    </div>
                </footer>
            <?php } ?>
            
            <div class="clearfix"></div>
            <section class="copyright-wrap <?php echo (isset($footer_area) && $footer_area == "no-footer" ? "no_footer" : "footer_area"); ?>">
                <div class="container">
                    <div class="row">
                        <?php if(isset($footer_area) && $footer_area == "no-footer"){ ?>
                        <div class="col-lg-12">
                            <div class="logo-footer margin-bottom-15 md-margin-bottom-15 sm-margin-bottom-10 xs-margin-bottom-15">        
                                <?php if($awp_options['footer_logo']){ ?>
                                    <?php if(isset($awp_options['footer_logo_image']['url']) && !empty($awp_options['footer_logo_image']['url'])){
                                        echo "<img src='" . $awp_options['footer_logo_image']['url'] . "' alt='logo'>";
                                    } else { ?>
                                        <?php if(isset($awp_options['logo_image']['url']) && !empty($awp_options['logo_image']['url'])){ ?>
                                        <img src='<?php echo $awp_options['logo_image']['url']; ?>' alt='logo'>
                                        <?php } else { ?>
                                        <div class="logo-footer"><a href="<?php echo home_url(); ?>">
                                            <h1><?php echo (isset($awp_options['logo_text']) && !empty($awp_options['logo_text']) ? $awp_options['logo_text'] : ""); ?></h1>
                                            <span><?php echo (isset($awp_options['logo_text_secondary']) && !empty($awp_options['logo_text_secondary']) ? $awp_options['logo_text_secondary'] : ""); ?></span></a>
                                        </div>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>

                                <?php if($awp_options['footer_copyright']){ ?>
                                    <p><?php echo $footer_text; ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } else { ?>

                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <?php if($awp_options['footer_logo']){ ?>
                                <?php if(isset($awp_options['footer_logo_image']['url']) && !empty($awp_options['footer_logo_image']['url'])){
                                        echo "<img src='" . $awp_options['footer_logo_image']['url'] . "' alt='logo'>";
                                    } else { ?>
                                        <?php if(isset($awp_options['logo_image']['url']) && !empty($awp_options['logo_image']['url'])){ ?>
                                        <img src='<?php echo $awp_options['logo_image']['url']; ?>' alt='logo'>
                                        <?php } else { ?>
                                        <div class="logo-footer"><a href="<?php echo home_url(); ?>">
                                            <h1><?php echo (isset($awp_options['logo_text']) && !empty($awp_options['logo_text']) ? $awp_options['logo_text'] : ""); ?></h1>
                                            <span><?php echo (isset($awp_options['logo_text_secondary']) && !empty($awp_options['logo_text_secondary']) ? $awp_options['logo_text_secondary'] : ""); ?></span></a>
                                        </div>
                                        <?php } ?>
                                <?php } ?>
                            <?php } ?>

                            <?php if($awp_options['footer_copyright']){ ?>
                                <p><?php echo $footer_text; ?></p>
                            <?php } ?>
                        </div>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                            <?php if($awp_options['footer_icons']) { ?>
                                <ul class="social clearfix">
                                    <?php
    								if(!empty($awp_options['social_network_links']['enabled'])){
    									unset($awp_options['social_network_links']['enabled']['placebo']);
    												
    									foreach($awp_options['social_network_links']['enabled'] as $index => $social){
    										$link = (isset($awp_options[ strtolower($social) . '_url']) && !empty($awp_options[ strtolower($social) . '_url']) ? $awp_options[ strtolower($social) . '_url'] : "");
    										echo '<li><a class="' . strtolower($social) . '" href="' . $link . '" target="_blank"></a></li>';
    									}
    								} ?>
                                </ul>
                            <?php } ?>
                        
                            <?php if($awp_options['footer_menu']) { ?>
                                <?php wp_nav_menu( array( 'theme_location' => 'footer-menu', 'menu_class' => 'f-nav', 'container_class' => 'col-lg-12' ) ); ?>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </section>
            
            <div class="back_to_top">
                <img src="<?php echo get_template_directory_uri(); ?>/images/arrow-up.png" alt="<?php _e('Back to top', 'automotive'); ?>" />
            </div>
			<?php
            if(isset($awp_options['body_layout']) && !empty($awp_options['body_layout']) && $awp_options['body_layout'] != 1){
                echo "</div>";
            } 

            if(isset($awp_options['custom_js']) && !empty($awp_options['custom_js'])){ ?>
            <script type="text/javascript">
                (function($) {
                    "use strict";
                    jQuery(document).ready( function($){
                        <?php echo $awp_options['custom_js']; ?>
                    });
                })(jQuery);
            </script>
            <?php } 
            
            automotive_google_analytics_code("body");
            wp_footer(); ?>
	</body>
</html>