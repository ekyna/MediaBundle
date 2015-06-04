(function ($, router) {
    /*$(window).resize(function () {
        var h = Math.max($(window).height() - 0, 420);
        $('#container, #data, #tree, #data .content').height(h).filter('.default').css('lineHeight', h + 'px');
    }).resize();*/

    $(document).on('fos_js_routing_loaded', function() {
        var $manager = $('#media-manager'),
            $tree = $('#media-manager-tree'),
            $list = $('#media-manager-list'),
            root = $manager.data('root');

        $tree.jstree({
            'core' : {
                'data' : {
                    'url' : router.generate('ekyna_media_manager_admin_list', {'root': root}),
                    method: 'GET',
                    dataType: 'json'
                },
                'check_callback' : function(o, n, p, i, m) {
                    if (m && m.dnd && m.pos !== 'i') { return false; }
                    if (o === "move_node" || o === "copy_node") {
                        if (this.get_node(n).parent === this.get_node(p).id) { return false; }
                    }
                    return true;
                },
                'themes' : {
                    'responsive' : false,
                    'variant' : 'small',
                    'stripes' : true
                }
            },
            'contextmenu' : {
                'items' : function(node) {
                    var tmp = $.jstree.defaults.contextmenu.items();
                    delete tmp.ccp;
                    return tmp;
                }
            },
            'types' : {
                'default' : { 'icon' : 'folder' }
                //'file' : { 'valid_children' : [], 'icon' : 'file' }
            },
            'unique' : {
                'duplicate' : function (name, counter) {
                    return name + ' ' + counter;
                }
            },
            'plugins' : ['state','dnd','types','contextmenu','unique']
        })
        .on('delete_node.jstree', function (e, data) {
            $.ajax({
                url: router.generate('ekyna_media_manager_admin_delete', {'root': root, 'id': data.node.id}),
                method: 'POST',
                dataType: 'json'
            })
            .done(function (d) {
                if (d.error) {
                    alert(d.message);
                    data.instance.refresh();
                }
            })
            .fail(function () {
                data.instance.refresh();
            });
        })
        .on('create_node.jstree', function (e, data) {
            $.ajax({
                url: router.generate('ekyna_media_manager_admin_create', {'root': root, 'id': data.node.parent}),
                data : { 'type' : data.node.type, 'id' : data.node.parent, 'name' : data.node.text },
                method: 'POST',
                dataType: 'json'
            })
            .done(function (d) {
                if (d.error) {
                    alert(d.message);
                    data.instance.refresh();
                    return;
                }
                data.instance.set_id(data.node, d.id);
            })
            .fail(function () {
                data.instance.refresh();
            });
        })
        .on('rename_node.jstree', function (e, data) {
            $.ajax({
                url: router.generate('ekyna_media_manager_admin_rename', {'root': root, 'id': data.node.id}),
                data : { 'name' : data.text },
                method: 'POST',
                dataType: 'json'
            })
            .done(function (d) {
                if (d.error) {
                    alert(d.message);
                    data.instance.refresh();
                    return;
                }
                data.instance.set_id(data.node, d.id);
            })
            .fail(function () {
                data.instance.refresh();
            });
        })
        .on('move_node.jstree', function (e, data) {
            $.ajax({
                url: router.generate('ekyna_media_manager_admin_move', {'root': root, 'id': data.node.id}),
                data : { 'parent' : data.parent, 'position': data.position },
                method: 'POST',
                dataType: 'json'
            })
            .done(function (d) {
                if (d.error) {
                    alert(d.message);
                }
                data.instance.refresh();
            })
            .fail(function () {
                data.instance.refresh();
            });
        })
        .on('copy_node.jstree', function (e, data) {
            /*$.get('?operation=copy_node', { 'id' : data.original.id, 'parent' : data.parent })
                .done(function (d) {
                    //data.instance.load_node(data.parent);
                    data.instance.refresh();
                })
                .fail(function () {
                    data.instance.refresh();
                });*/
        })
        .on('changed.jstree', function (e, data) {
            /*if(data && data.selected && data.selected.length) {
                $.get('?operation=get_content&id=' + data.selected.join(':'), function (d) {
                    if(d && typeof d.type !== 'undefined') {
                        $('#data .content').hide();
                        switch(d.type) {
                            case 'text':
                            case 'txt':
                            case 'md':
                            case 'htaccess':
                            case 'log':
                            case 'sql':
                            case 'php':
                            case 'js':
                            case 'json':
                            case 'css':
                            case 'html':
                                $('#data .code').show();
                                $('#code').val(d.content);
                                break;
                            case 'png':
                            case 'jpg':
                            case 'jpeg':
                            case 'bmp':
                            case 'gif':
                                $('#data .image img').one('load', function () { $(this).css({'marginTop':'-' + $(this).height()/2 + 'px','marginLeft':'-' + $(this).width()/2 + 'px'}); }).attr('src',d.content);
                                $('#data .image').show();
                                break;
                            default:
                                $('#data .default').html(d.content).show();
                                break;
                        }
                    }
                });
            }
            else {
                $('#data .content').hide();
                $('#data .default').html('Select a file from the tree.').show();
            }*/
        });
    });
})(jQuery, Routing);
