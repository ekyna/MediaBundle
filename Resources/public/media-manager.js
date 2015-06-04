(function($, router) {

    $(document).on('fos_js_routing_loaded', function() {

        var $manager = $('#media-manager'),
            $tree = $('#media-manager-tree'),
            $list = $('#media-manager-list'),
            root = $manager.data('root');

        $tree.fancytree({
            source: {
                url: router.generate('ekyna_media_manager_admin_list', {'root': root})
            },
            extensions: ["edit", "dnd"],
            dnd: {
                preventVoidMoves: true,
                preventRecursiveMoves: true,
                autoExpandMS: 400,
                dragStart: function(node, data) {
                    return node.data.level > 0;
                },
                dragEnter: function(node, data) {
                    // return ["before", "after"];
                    if (node.data.level === 0) {
                        return ["over"];
                    }
                    return true;
                },
                dragDrop: function(refNode, data) {
                    var node = data.otherNode;
                    $.ajax({
                        url: router.generate('ekyna_media_manager_admin_move', {
                            'root': root,
                            'id': node.key
                        }),
                        data : {
                            'reference' : refNode.key,
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
                        // TODO ?
                    });
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
                    var node = data.node;

                    $.ajax({
                        url: router.generate('ekyna_media_manager_admin_rename', {'root': root, 'id': node.key}),
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
            mode = mode || "child";
            $.ajax({
                url: router.generate('ekyna_media_manager_admin_create', {
                    'root': root,
                    'id': node.key
                }),
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
                // TODO ?
            });
        };

        var removeNode = function(node) {
            var message = 'Êtes-vous sûr de vouloir supprimer le dossier "' + node.title + '"';
            if (node.children.length) {
                message = message + ' et tous ses sous-dossiers';
            }
            message = message + ' ?';
            if (confirm(message)) {
                $.ajax({
                    url: router.generate('ekyna_media_manager_admin_delete', {
                        'root': root,
                        'id': node.key
                    }),
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
                    // TODO ?
                });
            }
        };

        $tree.on("nodeCommand", function(event, data){
            var refNode,
                tree = $(this).fancytree("getTree"),
                node = tree.getActiveNode();

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
        $tree.contextmenu({
            delegate: "span.fancytree-node",
            menu: [
                {title: "Modifier", cmd: "rename", uiIcon: "ui-icon-pencil" },
                {title: "Supprimer", cmd: "remove", uiIcon: "ui-icon-trash" },
                {title: "----"},
                {title: "Ajouter suivant", cmd: "addSibling", uiIcon: "ui-icon-plus" },
                {title: "Ajouter enfant", cmd: "addChild", uiIcon: "ui-icon-arrowreturn-1-e" }
            ],
            beforeOpen: function(event, ui) {
                var node = $.ui.fancytree.getNode(ui.target);
                node.setActive();
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

    });

})(jQuery, Routing);