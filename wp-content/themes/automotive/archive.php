<?php get_header(); ?>

    <div class="container">
        <div class="inner-page row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 page-content padding-left-none padding-right-none">
        
                <?php get_template_part('loop'); ?>        
                <?php get_template_part('pagination'); ?>
        
            </div>
        </div>
    </div>

<?php get_footer(); ?>