jQuery(document).ready(function($){
  /*
   * Fix all The Events Calendar translations.
   */
  $('#tribe-events h2.tribe-events-page-title').replaceWith('<h1>Prochains événements</h1>');
  $('#tribe-events .post-type-archive-tribe_events .tribe-events-list h2.tribe-events-page-title').replaceWith('<h1>Événements passés</h1>');
  $('#tribe-events p.tribe-events-back a').text(' « Tous les événements');
  $('#tribe-events .tribe-events-nav-left a').text('« Événements précédents');
  $('#tribe-events .tribe-events-nav-right a').text('Événements suivants »');
});
