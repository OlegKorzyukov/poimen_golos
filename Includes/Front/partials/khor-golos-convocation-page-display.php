<div class="row">
   <?php
   Inc\Khor_Golos_BaseController::okAddBreadcrumbInTitlePageGolos();
   $attsConstruct = $atts;
   $settings = [
      'nopaging' => 1,
      'post_type' => 'page',
      'order' => 'DESC',
      'orderby' => 'meta_value',
      'meta_key' => 'ok_poimen_golos_session',
      'post_parent' => get_the_ID()
   ];
   $query = new WP_Query($settings);
   if ($query->have_posts()) {
      while ($query->have_posts()) {
         $query->the_post();
         $metaPage = get_post_meta(get_the_ID());
         $metaSession = $metaPage['ok_poimen_golos_session'][0];
         $attsConstruct['session'] = $metaSession;
         $giveFromDB = self::okGiveFromDB($attsConstruct);
   ?>
         <a href="<?php the_permalink(); ?>" class="ok-poimen-golos-session-list-main col-12 col-md-6">
            <div class="ok-poimen-golos-session-list-wrapper">
               <div class="ok-poimen-golos-session-list-main-bg"></div>
               <div class="ok-poimen-golos-session-list-bg">
                  <div class="ok-poimen-golos-session-list__svg-top"></div>
                  <h2 class="ok-poimen-golos-session-list-title">
                     <?php the_title(); ?>
                  </h2>
                  <ul class="ok-poimen-golos-session-list-body">
                     <li>
                        <span class="ok-poimen-golos-session-list-body__title">Дата</span>
                        <span class="ok-poimen-golos-session-list-body__meta"><?php self::okGetTimeSession($attsConstruct) ?></span>
                     </li>
                     <li>
                        <span class="ok-poimen-golos-session-list-body__title">Всього питань</span>
                        <span class="ok-poimen-golos-session-list-body__meta"><?php echo self::okGetAllQuestion($giveFromDB) ?></span>
                     </li>
                     <li>
                        <span class="ok-poimen-golos-session-list-body__title">Прийнято питань</span>
                        <span class="ok-poimen-golos-session-list-body__meta"><?php echo self::okGetAcceptQuestion($giveFromDB) ?></span>
                     </li>
                     <li>
                        <span class="ok-poimen-golos-session-list-body__title">Не прийнято питань</span>
                        <span class="ok-poimen-golos-session-list-body__meta"><?php echo self::okGetDeclineQuestion($giveFromDB) ?></span>
                     </li>
                     <li>
                        <span class="ok-poimen-golos-session-list-body__title">Пристутність</span>
                        <span class="ok-poimen-golos-session-list-body__meta"><?php echo self::okGetTableHeaderDeputPresence($attsConstruct) ?></span>
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