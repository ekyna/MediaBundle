define('ekyna-media-browser',
    ['jquery', 'routing', 'twig', 'ekyna-modal', 'ekyna-form', 'ekyna-media-player',
        'ekyna-string', 'ekyna-media-thumb', 'fancytree', 'fancybox'],
    function($, Router, Twig, Modal, Form, Player) {
    "use strict";

    // http://james.padolsey.com/javascript/sorting-elements-with-jquery/
    $.fn.sortElements = (function(){
        var sort = [].sort;
        return function(comparator, getSortable) {
            getSortable = getSortable || function(){return this;};
            var placements = this.map(function(){
                var sortElement = getSortable.call(this),
                    parentNode = sortElement.parentNode,
                    nextSibling = parentNode.insertBefore(
                        document.createTextNode(''),
                        sortElement.nextSibling
                    );
                return function() {
                    if (parentNode === this) {
                        throw new Error(
                            "You can't sort elements if any one is a descendant of another."
                        );
                    }
                    parentNode.insertBefore(this, nextSibling);
                    parentNode.removeChild(nextSibling);
                };
            });
            return sort.call(this, comparator).each(function(i){
                placements[i].call(getSortable.call(this));
            });
        };
    })();

    var defaultConfig = {
        mode:    "browse",
        //path:    "",
        folderId: null,
        types:   [],
        sortBy:  "filename",
        sortDir: "asc",
        search:  "",
        debug:   true
    };

    var EkynaMediaBrowser = function(selector) {
        this.$browser = $(selector);
        this.config = $.extend({}, defaultConfig, this.$browser.data('config'));
        this.$tree = this.$browser.find('.media-browser-tree');
        this.$content = this.$browser.find('.media-browser-content');
        this.$controls = this.$browser.find('.media-browser-controls');
        this.modal = new Modal();
        this.busy = false;
        this.folderId = null;
        this.browseXhr = null;
    };

    EkynaMediaBrowser.prototype = {
        constructor: EkynaMediaBrowser,
        init: function(config) {
            this.config = $.extend({}, this.config, config || {});
            if (-1 == $.inArray(this.config.mode, ['browse', 'single_selection', 'multiple_selection', 'tinymce'])) {
                throw 'Unexpected browser mode "' + this.config.mode + '".';
            }

            this.initTree();

            // Disable controls until the user select a folder
            this.$controls.find('button, label').addClass('disabled').prop('disabled', true);
            this.$controls.find('input').prop('disabled', true);
        },
        setBusy: function(busy) {
            if (busy && this.browseXhr) {
                this.browseXhr.abort();
            }
            if (this.busy == busy) {
                return;
            }
            this.busy = busy;
            if (this.busy) {
                this.clearHandlers();
                this.$controls.find('button, label').addClass('disabled').prop('disabled', true);
                this.$controls.find('input').prop('disabled', true);
                this.$controls.find('[data-role="refresh"] span.glyphicon').addClass('glyphicon-refresh-animate');
            } else {
                this.initHandlers();
                this.$controls.find('button, label').removeClass('disabled').prop('disabled', false);
                this.$controls.find('input').prop('disabled', false);
                this.$controls.find('[data-role="refresh"] span.glyphicon').removeClass('glyphicon-refresh-animate');
            }
        },
        initHandlers: function () {
            var that = this;

            // Create/import buttons
            this.$controls.on('click', '[data-role="create"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                that.newMedia();
            });
            this.$controls.on('click', '[data-role="import"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                that.importMedia();
            });

            // Refresh button
            this.$controls.on('click', '[data-role="refresh"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                that.browse();
            });

            // Filter button
            this.$controls.on('change', 'input[name="filters[]"]', function (e) {
                e.preventDefault();
                e.stopPropagation();
                that.filterList();
            });

            // Search input
            var searchTimeout = null;
            this.$controls.on('keyup', 'input[data-role="search"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() { that.filterList(); }, 1500);
            });
            this.$controls.on('click', 'button[data-role="clear-search"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                clearTimeout(searchTimeout);
                that.$controls.find('input[data-role="search"]').val('');
                that.filterList();
            });

            // Sort button
            this.$controls.on('click', '[data-role="sort"]', function(e) {
                e.preventDefault();
                var $input = $(e.currentTarget).find('input');
                var $icon = $(e.currentTarget).find('span.glyphicon');
                if ($input.is(':checked')) {
                    var icon = $input.val() == 'filename' ? 'glyphicon-sort-by-alphabet' : 'glyphicon-sort-by-attributes';
                    if ($input.data('dir') == 'asc') {
                        $icon.removeClass(icon).addClass(icon + '-alt');
                        $input.data('dir', 'desc');
                    } else {
                        $icon.removeClass(icon + '-alt').addClass(icon);
                        $input.data('dir', 'asc');
                    }
                    $input.trigger('change');
                }
            });
            this.$controls.on('change', 'input[name="sort"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                that.sortList();
            });

            // Media selection
            this.$content.on('click', '.media-thumb [data-role="select"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $element = $(e.currentTarget).parents('.media-thumb').eq(0);
                if (that.config.mode == 'tinymce' && parent.tinymce && parent.tinymce.activeEditor) {
                    parent.tinymce.activeEditor.windowManager.getParams().setUrl(
                        $element.data('media').front
                    );
                    parent.tinymce.activeEditor.windowManager.close();
                } else if (that.config.mode == 'single_selection') {
                    var event = jQuery.Event('ekyna.media-browser.selection');
                    event.media = $element.data('media');
                    $(that).trigger(event);
                } else if (that.config.mode == 'multiple_selection') {
                    var $checkbox = $element.find('input[type="checkbox"]');
                    $checkbox.prop('checked', !$checkbox.is(':checked'));
                }
            });

            // Media show
            this.$content.on('click', '.media-thumb [data-role="show"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $media = $(e.currentTarget).parents('.media-thumb');
                that.showMedia($media);
            });

            // Media download
            this.$content.on('click', '.media-thumb [data-role="download"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $media = $(e.currentTarget).parents('.media-thumb');
                that.downloadMedia($media);
            });

            // Media edit
            this.$content.on('click', '.media-thumb [data-role="edit"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $media = $(e.currentTarget).parents('.media-thumb');
                that.editMedia($media);
            });

            // Media remove
            this.$content.on('click', '.media-thumb [data-role="delete"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $media = $(e.currentTarget).parents('.media-thumb');
                that.removeMedia($media);
            });
        },
        clearHandlers: function () {
            this.$controls.off();
            this.$content.off();
        },
        browse: function(id) {
            var that = this;

            that.$content.empty();
            this.folderId = id || this.folderId;

            if (this.folderId) {
                that.setBusy(true);
                that.browseXhr = $.ajax({
                    url: Router.generate('ekyna_media_browser_admin_list_media', {'id': this.folderId}),
                    method: 'GET',
                    dataType: 'json',
                    data: {types: that.config.types}
                })
                .done(function (d) {
                    if (d.error) {
                        alert(d.message);
                        return;
                    }
                    if (d.hasOwnProperty('medias')) {
                        var selector = false;
                        if (that.config.mode == 'multiple_selection') {
                            selector = true;
                        }
                        var controls = [
                            {role: 'show', icon: 'play'},
                            {role: 'edit', icon: 'pencil'},
                            {role: 'delete', icon: 'trash'},
                            {role: 'download', icon: 'download'}
                        ];
                        $(d['medias']).each(function (index, media) {
                            $(Twig.render(EkynaMediaBundle.thumb.html, {media: media, controls: controls, selector: selector}))
                                .data('media', media)
                                .appendTo(that.$content)
                                .draggable({
                                    revert: "invalid",
                                    cursorAt: { top: -5, left: -5 },
                                    appendTo: that.$browser,
                                    delay: 200,
                                    containement: that.$browser,
                                    scroll: false,
                                    connectToFancytree: true,
                                    helper: function(e) {
                                        return $(e.currentTarget).clone();
                                    }
                                });
                        });
                        that.sortList();
                        that.filterList();
                    }
                    that.browseXhr = null;
                })
                .fail(function () {
                    // TODO ?
                })
                .always(function() {
                    that.setBusy(false);
                });
            }
        },
        openModal: function(options) {
            var settings = $.extend({}, {
                url: null,
                onData: function() {},
                onShow: function(data) {},
                onHide: function() {}
            }, options);

            if (!settings.url) {
                throw "Modal url is not defined";
            }

            var that = this;
            that.setBusy(true);

            that.modal = new Modal();
            $(that.modal).on('ekyna.modal.content', function (e) {
                settings.onShow(e);
                if (e.contentType == 'data') {
                    settings.onData(e.content);
                    that.browse();
                }
            });

            $(that.modal).on('ekyna.modal.load_fail', function () {
                that.setBusy(false);
            });

            $(that.modal).on('ekyna.modal.hide', function () {
                settings.onHide();
                that.setBusy(false);
            });

            that.modal.load({url: settings.url});
        },
        newMedia: function() {
            this.openModal({
                url: Router.generate(
                    'ekyna_media_browser_admin_create_media',
                    {'id': this.folderId}
                )
            });
        },
        importMedia: function() {
            this.openModal({
                url: Router.generate(
                    'ekyna_media_browser_admin_import_media',
                    {'id': this.folderId}
                )
            });
        },
        showMedia: function($media) {
            if ($media.data('media').type == 'file' || $media.data('media').type == 'file') {
                this.downloadMedia($media);
                return;
            }
            var params = {
                href        : $media.data('media').player,
                maxWidth    : 1200,
                //maxHeight   : 600,
                fitToView   : false,
                width       : '90%',
                height      : '90%',
                autoSize    : false,
                closeClick  : false,
                openEffect  : 'none',
                closeEffect : 'none',
                padding     : 0
            };
            if ($media.data('media').type == 'image') {
                params.type  = 'image';
            } else {
                params.type = 'ajax';
                params.beforeShow = function() {
                    Player.init($('.fancybox-inner'));
                };
                params.beforeClose = function() {
                    Player.destroy($('.fancybox-inner'));
                };
            }
            $.fancybox(params);
        },
        editMedia: function($media) {
            var that = this;
            this.openModal({
                url: Router.generate(
                    'ekyna_media_media_admin_edit',
                    {'mediaId': $media.data('media').id}
                )/*,
                TODO onData: function(data) {
                    if (data.hasOwnProperty('success') && data.success) {
                        var event = jQuery.Event('ekyna.media-browser.media_update');
                        event.media = $media.data('media');
                        $(that).trigger(event);
                    }
                }*/
            });
        },
        removeMedia: function($media) {
            var that = this;
            this.openModal({
                // TODO modal size
                url: Router.generate(
                    'ekyna_media_media_admin_remove',
                    {'mediaId': $media.data('media').id}
                ),
                onData: function(data) {
                    if (data.hasOwnProperty('success') && data.success) {
                        var event = jQuery.Event('ekyna.media-browser.media_delete');
                        event.media = $media.data('media');
                        $(that).trigger(event);
                    }
                }
            });
        },
        downloadMedia: function($media) {
            var url = Router.generate('ekyna_media_download', {'key': $media.data('media').path});
            window.open(url,'_blank');
        },
        filterList: function() {
            var filters = this.$controls.find('input[name="filters[]"]:checked').map(function(_, el) {
                return $(el).val()
            }).get();
            var search = this.$controls.find('input[name="search"]').val();
            if (filters.length > 0 || search.length > 0) {
                var searchRegex = new RegExp(search.removeDiatrics().escapeRegExp(), 'i');
                this.$content.find('.media-thumb').each(function(i, media) {
                    var $element = $(media);
                    var type = $element.data('media').type;
                    if (filters.length > 0) {
                        if (0 > $.inArray(type, filters)) {
                            $element.hide();
                        } else {
                            $element.show();
                        }
                    }
                    if (search.length > 0) {
                        if (// TODO searchRegex.test($element.data('media').title.removeDiatrics()) ||
                            searchRegex.test($element.data('media').filename.removeDiatrics())) {
                            $element.show();
                        } else {
                            $element.hide();
                        }
                    }
                });
            } else {
                this.$content.find('.media-thumb').show();
            }
        },
        sortList: function() {
            var $input = $(this.$controls.find('input[name="sort"]:checked'));
            var prop = $input.val();
            var dir = $input.data('dir') == 'asc' ? 1 : -1;
            this.$content.find('.media-thumb').sortElements(function(a, b) {
                return $(a).data('media')[prop] > $(b).data('media')[prop] ? dir : -dir;
            });
        },
        getSelection: function() {
            if (this.config.mode != 'multiple_selection') {
                return [];
            }
            var result = [];
            this.$content.find('input[name="thumb_selection[]"]:checked').each(function(i, input) {
                result.push($(input).closest('.media-thumb').data('media'));
            });
            return result;
        },
        initTree: function() {
            var that = this;
            this.$tree.addClass('fancytree-expander').fancytree({
                source: {
                    url: Router.generate('ekyna_media_browser_admin_list', that.config.folderId ? {folderId: that.config.folderId} : {})
                },
                activeVisible: true,
                minExpandLevel: 1,
                selectMode: 1,
                extensions: ["edit", "dnd", "wide", "glyph"],
                init: function(event, data) {
                    var node = data.tree.getActiveNode();
                    if (node)  {
                        node.setExpanded(true);
                        that.browse(node.key);
                    }
                },
                activate: function(event, data) {
                    that.browse(data.node.key);
                },
                glyph: {
                    map: {
                        doc: "glyphicon glyphicon-file",
                        docOpen: "glyphicon glyphicon-file",
                        checkbox: "glyphicon glyphicon-unchecked",
                        checkboxSelected: "glyphicon glyphicon-check",
                        checkboxUnknown: "glyphicon glyphicon-share",
                        dragHelper: "glyphicon glyphicon-play",
                        dropMarker: "glyphicon glyphicon-arrow-right",
                        error: "glyphicon glyphicon-warning-sign",
                        expanderClosed: "glyphicon glyphicon-menu-right",
                        expanderLazy: "glyphicon glyphicon-menu-right",  // glyphicon-plus-sign
                        expanderOpen: "glyphicon glyphicon-menu-down",  // glyphicon-collapse-down
                        folder: "glyphicon glyphicon-folder-close",
                        folderOpen: "glyphicon glyphicon-folder-open",
                        loading: "glyphicon glyphicon-refresh glyphicon-spin"
                    }
                },
                dnd: {
                    preventVoidMoves: true,
                    preventRecursiveMoves: true,
                    autoExpandMS: 400,
                    dragStart: function(node) { // , data
                        return node.data.level > 0;
                    },
                    dragEnter: function(node, data) {
                        // return ["before", "after"];
                        /*if( !data.otherNode ) {
                            return ["over"];
                        }
                        if (node.data.level === 0) {
                            return ["over"];
                        }
                        return true;*/
                        return ["over"];
                    },
                    dragDrop: function(refNode, data) {
                        that.setBusy(true);
                        if( !data.otherNode ) { // It's a non-tree draggable
                            var $draggable = $(data.draggable.element);
                            if ($draggable.hasClass('media-thumb')) {
                                $.ajax({
                                    url: Router.generate('ekyna_media_browser_admin_move_media', {
                                        'id': refNode.key,
                                        'mediaId': $draggable.data('media').id
                                    }),
                                    method: 'POST',
                                    dataType: 'json'
                                })
                                .done(function () {
                                    that.browse();
                                })
                                .fail(function () {
                                    that.setBusy(false);
                                });
                            }
                        } else {
                            var node = data.otherNode;
                            $.ajax({
                                url: Router.generate('ekyna_media_browser_admin_move', {'id': node.key}),
                                data: {
                                    'reference': refNode.key,
                                    'mode': data.hitMode
                                },
                                method: 'POST',
                                dataType: 'json'
                            })
                            .done(function (d) {
                                if (d.error) {
                                    alert(d.message);
                                    return;
                                }
                                node.moveTo(refNode, data.hitMode);
                            })
                            .fail(function () {
                                that.setBusy(false);
                            });
                        }
                    }
                },
                edit: {
                    triggerStart: ["f2", "dblclick", "shift+click", "mac+enter"],
                    adjustWidthOfs: 4,
                    beforeEdit: function(event, data){
                        return data.node.data.level > 0;
                    },
                    /*edit: function(event, data){
                     // Editor was opened (available as data.input)
                     },*/
                    beforeClose: function(event, data){
                        return data.input.val().length > 0;
                    },
                    save: function(event, data){
                        that.setBusy(true);
                        var node = data.node;
                        $.ajax({
                            url: Router.generate('ekyna_media_browser_admin_rename', {'id': node.key}),
                            data : { 'name' : data.input.val() },
                            method: 'POST',
                            dataType: 'json'
                        })
                        .done(function (d) {
                            if (d.error) {
                                node.setTitle(data.orgTitle);
                                alert(d.message);
                                return;
                            }
                            node.setTitle(d.name);
                            node.setActive();
                        })
                        .fail(function () {
                            node.setTitle(data.orgTitle);
                        })
                        .always(function(){
                            $(data.node.span).removeClass("pending");
                            that.setBusy(false);
                        });
                        return true;
                    },
                    close: function(event, data){
                        if( data.save ) {
                            $(data.node.span).addClass("pending");
                        }
                    }
                }
            });

            var createNode = function(node, mode) {
                that.setBusy(true);
                mode = mode || "child";
                $.ajax({
                    url: Router.generate('ekyna_media_browser_admin_create', {'id': node.key}),
                    data : {
                        'mode' : mode
                    },
                    method: 'POST',
                    dataType: 'json'
                })
                .done(function (d) {
                    if (d.error) {
                        alert(d.message);
                        return;
                    }
                    node.editCreateNode(mode, d.node);
                })
                .always(function () {
                    that.setBusy(false);
                });
            };

            var removeNode = function(node) {
                that.setBusy(true);
                var message = 'Êtes-vous sûr de vouloir supprimer le dossier "' + node.title + '"';
                if (node.children && node.children.length) {
                    message = message + ' et tous ses sous-dossiers';
                }
                message = message + ' ?';
                if (confirm(message)) {
                    $.ajax({
                        url: Router.generate('ekyna_media_browser_admin_delete', {'id': node.key}),
                        method: 'POST',
                        dataType: 'json'
                    })
                    .done(function (d) {
                        if (d.error) {
                            alert(d.message);
                            return;
                        }
                        var refNode = node.getNextSibling() || node.getPrevSibling() || node.getParent();
                        node.remove();
                        if (refNode) {
                            refNode.setActive();
                        }
                    })
                    .always(function () {
                        that.setBusy(false);
                    });
                }
            };

            this.$tree.on("nodeCommand", function(event, data){
                var node = $(this).fancytree("getTree").getActiveNode();

                switch( data.cmd ) {
                    case "rename":
                        node.editStart();
                        break;
                    case "remove":
                        removeNode(node);
                        break;
                    case "addChild":
                        createNode(node, "child");
                        break;
                    case "addSibling":
                        createNode(node, "after");
                        break;
                    default:
                        alert("Unhandled command: " + data.cmd);
                        return;
                }
            });

            /*
             * Context menu (https://github.com/mar10/jquery-ui-contextmenu)
             */
            this.$tree.contextmenu({
                delegate: "span.fancytree-node",
                menu: [
                    {title: "Modifier", cmd: "rename", uiIcon: "ui-icon-pencil" },
                    {title: "Supprimer", cmd: "remove", uiIcon: "ui-icon-trash" },
                    /*{title: "----"},*/
                    {title: "Ajouter suivant", cmd: "addSibling", uiIcon: "ui-icon-plus" },
                    {title: "Ajouter enfant", cmd: "addChild", uiIcon: "ui-icon-arrowreturn-1-e" }
                ],
                beforeOpen: function(event, ui) {
                    var node = $.ui.fancytree.getNode(ui.target);
                    node.setActive();
                    $(that.$tree)
                        .contextmenu("showEntry", "addSibling", node.data.level > 0)
                        .contextmenu("showEntry", "rename", node.data.level > 0)
                        .contextmenu("showEntry", "remove", node.data.level > 0)
                    ;
                },
                select: function(event, ui) {
                    var that = this;
                    // delay the event, so the menu can close and the click event does
                    // not interfere with the edit control
                    setTimeout(function(){
                        $(that).trigger("nodeCommand", {cmd: ui.cmd});
                    }, 100);
                }
            });
        }
    };

    return EkynaMediaBrowser;
});
