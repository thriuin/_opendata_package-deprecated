<?php
/**
 * Hiding Content and Printing it Separately
 *
 * Use the hide() function to hide fields and other content, you can render it
 * later using the render() function. Install the Devel module and use
 * <?php print dsm($content); ?> to find variable names to hide() or render().
 */
hide($content['comments']);
hide($content['links']);
global $language;
?>

<?php
  /**
   * Load the CKAN dataset information from the od_package table.
   */
  $record = null;
  $result = db_select('od_package', 'c')
  ->fields('c', array('pkg_node_id', 'pkg_id', 'pkg_name', 'pkg_title_en', 
      'pkg_title_fr', 'pkg_description_en', 'pkg_description_fr'))
  ->condition('pkg_node_id', $node->nid, '=')
  ->execute();
  if ($result->rowCount() == 1) {
    $record = $result->fetchAssoc();
  }
?>

<article id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
  <?php print render($title_prefix); ?>
  
  <?php
  /**
   * Set the title from the CKAN dataset record - do not use the node title which is likely unilingual
   */
    $ckan_title = $title;
    if (($language->language == 'en') && ($record <> null)): 
      $ckan_title = $record['pkg_title_en'];
    elseif (($language->language == 'fr') && ($record <> null)): 
      $ckan_title = $record['pkg_title_fr'];
    endif;
    $title = $ckan_title;
  ?>
  <?php if ($title && !$page): ?>
    <header<?php print $header_attributes; ?>>
      <?php if ($title): ?>
        <h1<?php print $title_attributes; ?>>
          <a href="<?php print $node_url; ?>" rel="bookmark"><?php print $title; ?></a>
        </h1>
      <?php endif; ?>
    </header>
  <?php endif; ?>
  <?php if(!empty($user_picture) || $display_submitted): ?>
    <footer<?php print $footer_attributes; ?>>
      <?php print $user_picture; ?>
      <p class="author-datetime"><?php print $submitted; ?></p>
    </footer>
  <?php endif; ?>
  <div<?php print $content_attributes; ?>>

    <?php  // Dataset Description and link
      $link_prefix = variable_get('od_package_prefix', '/data/%s/dataset/');
      $link = '<a href="' . sprintf($link_prefix, $language->language) . $record['pkg_id'] . '">';
      if(($language->language == 'en') && ($result->rowCount() == 1)): 
        $desc = $record['pkg_description_en'];
        $descitem = array(
          'desc' => array(
           '#type' => 'markup',
           '#markup' => '<p>' . $desc . '</p><p>' . t('Dataset: ') . $link . $title . '</a></p>',
          )
        );
        print render($descitem);
      elseif (($language->language == 'fr') && ($result->rowCount() == 1)):
        $desc = $record['pkg_description_fr'];
        $descitem = array(
          'desc' => array(
           '#type' => 'markup',
           '#markup' => '<p>' . $desc . '</p><p>' . t('Jeu de données: ') . $link . $title . '</a></p>',
          )
        );
        print render($descitem);
      endif; ?>

    <?php print render($content); ?>
  </div>
  <?php if ($links = render($content['links'])): ?>
    <nav<?php print $links_attributes; ?>><?php print $links; ?></nav>
  <?php endif; ?>
  <?php print render($content['comments']); ?>
  <?php print render($title_suffix); ?>
  <dl id="gcwu-date-mod" role="contentinfo">
    <dt><?php print t('Date modified:'); ?></dt>
    <dd><span><time><?php print $date; ?></time></span></dd>
  </dl>
  <div class="clear"></div>
</article>