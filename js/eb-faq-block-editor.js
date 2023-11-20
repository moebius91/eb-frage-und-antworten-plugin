(function (blocks, editor, element, components, data, blockEditor) {
    var el = element.createElement;
    var useSelect = data.useSelect;
    var SelectControl = components.SelectControl;
    var InspectorControls = blockEditor.InspectorControls;

    blocks.registerBlockType('eb/faq-block', {
        title: 'EB Frage/Antworten Block',
        icon: 'welcome-learn-more',
        category: 'common',

        attributes: {
            selectedPost: {
                type: 'number',
                default: 0
            },
            selectedPostTitle: {
                type: 'string',
                default: ''
            }
        },

        edit: function (props) {
            var setAttributes = props.setAttributes;
            var selectedPost = props.attributes.selectedPost;
            var selectedPostTitle = props.attributes.selectedPostTitle;

            var posts = useSelect(function (select) {
                return select('core').getEntityRecords('postType', 'eb_faq', { per_page: -1 });
            }, []);

            function onChangePost(postId) {
                var selected = posts.find(function (post) {
                    return post.id === parseInt(postId, 10);
                });

                setAttributes({
                    selectedPost: parseInt(postId, 10),
                    selectedPostTitle: selected ? selected.title.rendered : ''
                });
            }

            var options = [{ value: 0, label: 'Wähle einen F/A Post' }];
            if (posts) {
                posts.forEach(function (post) {
                    options.push({ value: post.id, label: post.title.rendered });
                });
            }

            function getEditPostLink(postId) {
                return postId ? '/wp-admin/post.php?post=' + postId + '&action=edit' : '#';
            }

            return el('div', {},
                el(InspectorControls, {},
                    el(components.PanelBody, { title: 'F/A Einstellungen', initialOpen: true },
                        el(SelectControl, {
                            label: 'Wähle F/A Post',
                            value: selectedPost,
                            options: options,
                            onChange: onChangePost
                        }),
                        selectedPost ? el('a', { href: getEditPostLink(selectedPost), target: '_blank' }, 'Bearbeite ausgewählten Beitrag') : ''
                    )
                ),
                el('div', { className: 'eb-faq-block-editor' },
                    'Ausgewählter F/A Post: ' + (selectedPostTitle ? selectedPostTitle : 'Keiner')
                )
            );
        },

        save: function () {
            return null; // Da serverseitiges Rendering verwendet wird
        }
    });
})(window.wp.blocks, window.wp.editor, window.wp.element, window.wp.components, window.wp.data, window.wp.blockEditor);
