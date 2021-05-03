<div class="row">
   <?php
   $settings = [
      'nopaging' => 1,
      'post_type' => 'page',
      'order' => 'DESC',
      'orderby' => 'ID',
      //'meta_key' => 'ok_golos_convocation',
      'post_parent' => get_the_ID()
   ];
   $query = new WP_Query($settings);
   if ($query->have_posts()) {
      while ($query->have_posts()) {
         $query->the_post();
         $metaPage = get_post_meta(get_the_ID());
         $minDate = $metaPage['ok_golos_convocation_date_min'][0];
         $maxDate = $metaPage['ok_golos_convocation_date_max'][0];
         if ($minDate == $maxDate) {
            $maxDate = '';
         }
   ?>
         <a href="<?php the_permalink(); ?>" class="ok-poimen-golos-convocation-main col-12 col-md-4">
            <div class="ok-poimen-golos-convocation-wrapper">
               <div class="ok-poimen-golos-convocation-main-bg"></div>
               <div class="ok-poimen-golos-convocation-bg">
                  <h2 class="ok-poimen-golos-convocation-title">
                     <?php the_title(); ?>
                     <div class="ok-poimen-golos-convocation-title__date">
                        (
                        <span class="ok-min-date"><?php echo $minDate ?></span>
                        <span <?php if (empty($maxDate)) echo 'style="display:none"'; ?> class="ok-divided-date">-</span>
                        <span <?php if (empty($maxDate)) echo 'style="display:none"'; ?> class="ok-max-date"><?php echo $maxDate ?></span>
                        )
                     </div>
                  </h2>
                  <ul class="ok-poimen-golos-convocation-body">
                     <li>
                        <span class="ok-poimen-golos-convocation-body__title">Кількість сесій</span>
                        <span class="ok-poimen-golos-convocation-body__meta"><?php echo $metaPage['ok_golos_convocation_all_session'][0] ?></span>
                     </li>
                     <li>
                        <span class="ok-poimen-golos-convocation-body__title">Кількість питань</span>
                        <span class="ok-poimen-golos-convocation-body__meta"><?php echo $metaPage['ok_golos_convocation_all_session_question'][0] ?></span>
                     </li>
                     <li>
                        <span class="ok-poimen-golos-convocation-body__title">Прийнятих питань</span>
                        <span class="ok-poimen-golos-convocation-body__meta"><?php echo $metaPage['ok_golos_convocation_accept_session_question'][0] ?></span>
                     </li>
                     <li>
                        <span class="ok-poimen-golos-convocation-body__title">Не прийнятих питань</span>
                        <span class="ok-poimen-golos-convocation-body__meta"><?php echo $metaPage['ok_golos_convocation_decline_session_question'][0] ?></span>
                     </li>
                     <li>
                        <span class="ok-poimen-golos-convocation-body__title">Середня пристутність</span>
                        <span class="ok-poimen-golos-convocation-body__meta"><?php echo $metaPage['ok_golos_convocation_average_deput_presence'][0] ?>%</span>
                     </li>
                  </ul>
               </div>
            </div>
         </a>
   <?php
      }
      wp_reset_postdata(); // сбрасываем переменную $post
   }
   ?>

</div>