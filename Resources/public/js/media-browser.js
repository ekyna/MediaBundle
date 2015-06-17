(function(root, factory) {
    "use strict";

    // CommonJS module is defined
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = factory(
            require('jquery'),
            require('routing'),
            require('twig'),
            require('ekyna-modal'),
            require('ekyna-form'),
            require('fancytree')
        );
    }
    // AMD module is defined
    else if (typeof define === 'function' && define.amd) {
        define('ekyna-media-browser', ['jquery', 'routing', 'twig', 'ekyna-modal', 'ekyna-form', 'fancytree'], function($, routing, twig, modal, form) {
            return factory($, routing, twig, modal, form);
        });
    } else {
        // planted over the root!
        root.EkynaMediaBrowser = factory(
            root.jQuery,
            root.Routing,
            root.Twig,
            root.EkynaModal,
            root.EkynaForm
        );
    }

}(this, function($, routing, twig, modal, form) {
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
        path:    "",
        types: [],
        sortBy:  "filename",
        sortDir: "asc",
        search:  "",
        debug: true
    };

    var EkynaMediaBrowser = function(selector) {
        this.$browser = $(selector);
        this.config = $.extend({}, defaultConfig, this.$browser.data('config'));
        this.$tree = this.$browser.find('.media-browser-tree');
        this.$content = this.$browser.find('.media-browser-content');
        this.$controls = this.$browser.find('.media-browser-controls');
        this.modal = new modal();
        this.busy = false;
        this.folderId = null;
        this.browseXhr = null;
    };

    EkynaMediaBrowser.prototype = {
        constructor: EkynaMediaBrowser,
        init: function() {
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

            // Create button
            this.$controls.on('click', '[data-role="create"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                that.newMedia();
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
                    var icon = $input.val() == 'title' ? 'glyphicon-sort-by-alphabet' : 'glyphicon-sort-by-attributes';
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
                var $element = $(e.currentTarget).parents('.media-thumb');
                /* TODO if (parent.tinymce && parent.tinymce.activeEditor) {
                    parent.tinymce.activeEditor.windowManager.getParams().setUrl($element.data('web_path'));
                    parent.tinymce.activeEditor.windowManager.close();
                } else {*/
                    var event = jQuery.Event('ekyna.media-browser.selection');
                    event.media = $element.data();
                    $(that).trigger(event);
                //}
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
            this.$content.on('click', '.media-thumb [data-role="remove"]', function(e) {
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

            that.log('Browsing [' + this.folderId + ']');

            if (this.folderId) {
                that.setBusy(true);
                that.browseXhr = $.ajax({
                    url: Routing.generate('ekyna_media_browser_admin_list_media', {'id': this.folderId}),
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
                        $(d['medias']).each(function (index, media) {
                            $(Twig.render(media_browser_media, {media: media}))
                                .data(media)
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
                onShow: function() {},
                onHide: function() { that.setBusy(false); }
            }, options);

            if (!settings.url) {
                throw "Modal url is not defined";
            }

            var that = this;
            that.setBusy(true);

            var $form;
            that.modal = new modal();
            $(that.modal).on('ekyna.modal.content', function (e) {
                settings.onShow(e);
                if (e.contentType == 'form') {
                    $form = e.content;
                    form.init($form);
                } else if (e.contentType == 'data') {
                    that.modal.getDialog().close();
                    that.browse();
                } else {
                    settings.onData(e.content);
                }
            });

            $(that.modal).on('ekyna.modal.button_click', function (e) {
                if (e.buttonId == 'submit' && $form.size() == 1) {
                    $form.ajaxSubmit({
                        dataType: 'xml',
                        success: function(response) {
                            that.modal.handleResponse(response)
                        }
                    });
                }
            });

            $(that.modal).on('ekyna.modal.load_fail', function () {
                that.setBusy(false);
            });

            that.modal.getDialog().onHide(function() {
                settings.onHide();
            });

            that.modal.load({url: settings.url});
        },
        newMedia: function() {
            this.openModal({
                url: Routing.generate(
                    'ekyna_media_browser_admin_create_media',
                    {'id': this.folderId}
                )
            });
        },
        showMedia: function($media) {
            this.openModal({
                url: Routing.generate(
                    'ekyna_media_media_admin_show',
                    {'mediaId': $media.data('id')}
                )
            });
        },
        editMedia: function($media) {
            this.openModal({
                url: Routing.generate(
                    'ekyna_media_media_admin_edit',
                    {'mediaId': $media.data('id')}
                )
            });
        },
        removeMedia: function($media) {
            this.openModal({
                url: Routing.generate(
                    'ekyna_media_media_admin_remove',
                    {'mediaId': $media.data('id')}
                )
            });
        },
        downloadMedia: function($media) {
            var url = Routing.generate('ekyna_media_download', {'key': $media.data('path')});
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
                    var type = $element.data('type');
                    if (filters.length > 0) {
                        if (0 > $.inArray(type, filters)) {
                            $element.hide();
                        } else {
                            $element.show();
                        }
                    }
                    if (search.length > 0) {
                        if (searchRegex.test($element.data('title').removeDiatrics())) {
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
                return $(a).data(prop) > $(b).data(prop) ? dir : -dir;
            });
        },
        initTree: function() {
            var that = this;
            this.$tree.fancytree({
                source: {
                    url: Routing.generate('ekyna_media_browser_admin_list')
                },
                extensions: ["edit", "dnd"],
                dnd: {
                    preventVoidMoves: true,
                    preventRecursiveMoves: true,
                    autoExpandMS: 400,
                    dragStart: function(node) { // , data
                        return node.data.level > 0;
                    },
                    dragEnter: function(node, data) {
                        // return ["before", "after"];
                        if( !data.otherNode ) {
                            return ["over"];
                        }
                        if (node.data.level === 0) {
                            return ["over"];
                        }
                        return true;
                    },
                    dragDrop: function(refNode, data) {
                        that.setBusy(true);
                        if( !data.otherNode ) { // It's a non-tree draggable
                            var $draggable = $(data.draggable.element);
                            if ($draggable.hasClass('media-thumb')) {
                                $.ajax({
                                    url: Routing.generate('ekyna_media_browser_admin_move_media', {
                                        'id': refNode.key,
                                        'mediaId': $draggable.data('id')
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
                                url: Routing.generate('ekyna_media_browser_admin_move', {'id': node.key}),
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
                            url: Routing.generate('ekyna_media_browser_admin_rename', {'id': node.key}),
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
                },
                activate: function(event, data) {
                    that.browse(data.node.key);
                }
            });

            var createNode = function(node, mode) {
                that.setBusy(true);
                mode = mode || "child";
                $.ajax({
                    url: Routing.generate('ekyna_media_browser_admin_create', {'id': node.key}),
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
                .fail(function () {
                    that.setBusy(false);
                });
            };

            var removeNode = function(node) {
                that.setBusy(true);
                var message = 'Êtes-vous sûr de vouloir supprimer le dossier "' + node.title + '"';
                if (node.children.length) {
                    message = message + ' et tous ses sous-dossiers';
                }
                message = message + ' ?';
                if (confirm(message)) {
                    $.ajax({
                        url: Routing.generate('ekyna_media_browser_admin_delete', {'id': node.key}),
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
                    .fail(function () {
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
        },
        log: function(data) {
            if (!this.config.debug) return;
            if (typeof data === 'string') {
                console.log('[MediaBrowser] ' + data);
            } else {
                console.log(data);
            }
        }
    };

    return EkynaMediaBrowser;
}));