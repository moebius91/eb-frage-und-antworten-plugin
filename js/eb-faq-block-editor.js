(function(blocks, editor, element, components, data) {
    var el = element.createElement;
    var withSelect = data.withSelect;
    var SelectControl = components.SelectControl;

    blocks.registerBlockType('eb/faq-block', {
        title: 'EB FAQ Block',
        icon: 'welcome-learn-more',
        category: 'common',

        edit: withSelect(function(select) {
            var posts = select('core').getEntityRecords('postType', 'eb_faq', { per_page: -1 });

            return function(props) {
                var setAttributes = props.setAttributes;
                var attributes = props.attributes;

                function onChangePost(postId) {
                    setAttributes({ selectedPost: postId });
                }

                var options = [{ value: 0, label: 'Wähle einen FAQ Post' }];
                if(posts) {
                    posts.forEach(function(post) {
                        options.push({ value: post.id, label: post.title.rendered });
                    });
                }

                return el('div', { className: 'eb-faq-block-editor' },
                    el(SelectControl, {
                        label: 'Wähle FAQ Post',
                        value: attributes.selectedPost,
                        options: options,
                        onChange: onChangePost
                    })
                );
            }
        }),

        save: function() {
            // Speicherfunktion wird nicht benötigt, da serverseitiges Rendering verwendet wird
            return null;
        }
    });
}(window.wp.blocks, window.wp.editor, window.wp.element, window.wp.components, window.wp.data));
