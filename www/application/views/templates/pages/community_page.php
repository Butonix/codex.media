<?= View::factory('templates/components/about', [
    'title' => $page->title,
    'description' => $page->description,
    'content_blocks' => $page->getTextContent(),
    'link_text' => 'Read more',
    'page_uri' => '/p/' . $page->id
])->render(); ?>

<?/** add form for new page */ ?>
<? if ($user->id): ?>

    <?= View::factory('templates/pages/form_wrapper', [
        'hideEditorToolbar' => true,
        'community_parent_id' => $page->id
    ]); ?>

<? endif ?>
<? /***/ ?>

<div class="island tabs island--margined">
    <a class="tabs__tab <?= $list == 'pages' || !$list ? 'tabs__tab--current' : '' ?>" href='<?= $page->url ?>'>
        General
    </a>
    <a class="tabs__tab <?= $list == 'events' ? 'tabs__tab--current' : '' ?>" href='<?= $page->url ?>/events'>
        Events
    </a>
</div>


<?= View::factory('templates/pages/list', [
    'pages' => $pages,
    'emptyListMessage' => 'Тут появятся статьи и заметки',
    'active_tab' => '',
    'events' => $events,
    'events_uri' => 'p/' . $page->id . '/' . $page->uri . '/events',
    'total_events' => $total_events,
    'hide_event_author' => 'true'
]); ?>
