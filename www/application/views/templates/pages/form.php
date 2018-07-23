<form class="writing island island--bottom-rounded" action="/p/writing" id="atlasForm" method="post" name="atlas" data-module="writing">

    <module-settings hidden>
        [
            {
                "selector" : ".js-comment-settings",
                "items" : [{
                    "title" : "Удалить",
                    "handler" : {
                        "module" : "comments",
                        "method" : "remove"
                    }
                }]
            }
        ]
    </module-settings>

    <?
        /** if there is no information about page */
        if (!isset($page)) {
            $page = new Model_Page();
        }

        /** Name of object's type in genitive declension */
        // $object_name = $page->is_news_page ? 'новости' : 'страницы';

        $fromIndexPage = !empty(Request::$current) && Request::$current->controller() == 'Index';
        $fromNewsTab = Request::$current->param('feed_key', Model_Feed_Pages::MAIN) == Model_Feed_Pages::MAIN;
        $fromEventsTab = Request::$current->param('feed_key', Model_Feed_Pages::EVENTS) == Model_Feed_Pages::EVENTS;
        $fromUserProfile = Request::$current->controller() == 'User_Index';

        $isNews = $page->isPageOnMain;

        $vkPost = $page->isPostedInVK;

        if ($user->isAdmin() && $fromIndexPage && $fromNewsTab) {
            $selectedPageType = Model_Page::NEWS;
        } elseif ($user->isAdmin() && $fromIndexPage && $fromEventsTab) {
            $selectedPageType = Model_Page::EVENT;
        } elseif (!$user->isAdmin() && $fromIndexPage || $fromUserProfile) {
            $selectedPageType = Model_Page::BLOG;
        } else {
            $selectedPageType = 0;
        }
    ?>

    <?= Form::hidden('csrf', Security::token()); ?>
    <?= Form::hidden('id', $page->id); ?>
    <?= Form::hidden('id_parent', $page->id_parent); ?>
    <?= Form::hidden('content', !empty($page->content) ? $page->content : ''); ?>

    <?= View::factory('templates/pages/form_type_selector', [
        'page' => $page,
        'selectedPageType' => $selectedPageType
    ]); ?>

    <div class="writing__title-wrapper">
        <textarea class="writing__title js-autoresizable" rows="1" name="title" placeholder="Заголовок" id="editorWritingTitle"><?= $page->title ?></textarea>
    </div>

    <div class="editor-wrapper" id="placeForEditor"></div>

    <div class="writing__actions">

        <div class="writing__actions-content">

            <span class="button master" onclick="codex.writing.submit(this)">
                <? if ($page->id): ?>
                    Сохранить
                <? else: ?>
                    Опубликовать
                <? endif; ?>
            </span>

            <? if ($user->isAdmin() && !$fromUserProfile): ?>
                <span name="cdx-custom-checkbox" class="writing__vk-post" data-name="vkPost" data-checked="<?= $vkPost ?>" title="Опубликовать на стене сообщества">
                    <i class="icon-vkontakte"></i>
                </span>
            <? endif; ?>

            <? if (!empty($hideEditorToolbar) && $hideEditorToolbar): ?>
                <span class="writing-fullscreen__button" onclick="codex.writing.openEditorFullscreen()">
                    <? include(DOCROOT . 'public/app/svg/zoom.svg') ?>
                    <span class="writing-fullscreen__text">На весь экран</span>
                </span>
            <? endif ?>

        </div>

    </div>

    <? if (($user->isAdmin() && $fromUserProfile) || isset($isPersonalBlog)): ?>
        <?= Form::hidden('isPersonalBlog', isset($isPersonalBlog) ? $isPersonalBlog : '1'); ?>
    <? endif; ?>

    <? if (!empty($community_parent_id) && $community_parent_id != 0): ?>
        <?= Form::hidden('id_parent', $community_parent_id); ?>
    <? endif; ?>

</form>


<?
    $hideEditorToolbar = !empty($hideEditorToolbar) && $hideEditorToolbar;
?>
<script>

    /** Document is ready */
    codex.docReady(function() {

      return;

        var plugins = ['header'],
            // plugins = ['paragraph', 'header', 'image', 'attaches', 'list', 'raw'],
            // scriptPath = 'https://cdn.ifmo.su/editor/v1.6/',
            scriptPath = '/public/extensions/codex.editor/';
            settings = {
                holderId          : 'placeForEditor',
                pageId            : <?= $page->id ?>,
                parentId          : <?= $page->id_parent ?>,
                hideEditorToolbar : <?= $hideEditorToolbar ? 'true' : 'false' ?>,
                data              : <?= $page->content ?: '[]' ?>,
                resources         : []
            };

        settings.resources.push({
            name : 'codex-editor',
            path : {
                script : scriptPath + 'build/codex-editor.js',
                // style  : scriptPath + 'codex-editor.css'
            }
        });

        for (var i = 0, plugin; !!(plugin = plugins[i]); i++) {
            settings.resources.push({
                name : plugin,
                path : {
                    script : scriptPath + 'example/plugins/' + plugin + '/' + plugin + '.js',
                    style  : scriptPath + 'example/plugins/' + plugin + '/' + plugin + '.css',
                }
            });
        }

        var editorReady = codex.writing.prepare(settings);

        <? if (!$hideEditorToolbar): ?>
            editorReady.then(codex.writing.init);
        <? endif ?>
    });
</script>
