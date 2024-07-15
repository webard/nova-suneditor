<template>
    <DefaultField :field="field" :errors="errors" :show-help-text="showHelpText" :full-width-content="fullWidthContent">
        <template #field>

            <a ref="emojianchor"></a>
            <textarea ref="editor" :id="field.attribute" :class="errorClasses" class="w-full" style="height:200px"
                :value="value" />

        </template>
    </DefaultField>
</template>

<script>
import { DependentFormField, HandlesValidationErrors } from 'laravel-nova'

import suneditor from 'suneditor'
import plugins from 'suneditor/src/plugins'
import { EmojiButton } from '@joeattardi/emoji-button';

export default {
    mixins: [DependentFormField, HandlesValidationErrors],
    emits: ['field-changed'],

    props: ['resourceName', 'resourceId', 'field'],
    data() {
        return {
            mounted: false,
            index: 0,
        }
    },

    created() {
        this.setInitialValue()
    },

    mounted() {
        Nova.$on(this.fieldAttributeValueEventName, this.listenToValueChanges)

        this.initEditor()

        this.editor.onPaste = function (e, cleanData, maxCharCount, core) { console.log('onPaste', e) }

        this.picker.on('emoji', (emoji) => { this.handleEmoji(emoji) });
    },

    beforeUnmount() {
        Nova.$off(this.fieldAttributeValueEventName, this.listenToValueChanges)
    },

    methods: {
        resetCursorPos() {
            const core = this.editor.core;
            const editorNode = core.context.element.wysiwyg;

            const lastChild = editorNode.lastChild;
            const range = document.createRange();
            const selection = window.getSelection();

            // delay setting curor until after html insert
            setTimeout(() => {
                range.setStartAfter(lastChild);
                range.collapse(true);
                selection.removeAllRanges();
                selection.addRange(range);

                this.editor.core.focus();
            }, 100);
        },

        handleEmoji(emoji) {
            this.editor.insertHTML(emoji.emoji, true, true);
            this.resetCursorPos();
        },

        initEmojiPickerPlugin() {
            this.picker = new EmojiButton({
                position: 'bottom-start',
                // rootElement: this.$refs.editor
            });

            // Define the custom plugin
            return {
                name: 'emoji',
                display: 'command',
                title: 'Emoji',
                buttonClass: '',
                innerHTML: '<div class="se-btn-module"><button type="button" class="se-btn se-btn-module" title="Insert Emoji"><span>😀</span></button></div>',
                add: function (core) { },
                action: () => {

                    this.picker.togglePicker(this.$refs.emojianchor);
                }
            };
        },

        initEditor() {
            this.editor = suneditor.create(this.$refs.editor, {
                plugins: {
                    emoji: this.initEmojiPickerPlugin(),
                    ...plugins
                },
                height: 'auto',
                minHeight: '200px',
                buttonList: [
                    [
                        'undo',
                        'redo',
                        'fontSize',
                        'formatBlock'
                    ],
                    [
                        'fontColor',
                        'hiliteColor',
                        'bold',
                        'underline',
                        'italic',
                        'strike',
                        'list',
                        'align',
                        'outdent',
                        'indent',
                        'removeFormat',
                    ],
                    [
                        'emoji',
                        'table',
                        'link',
                        'image',
                        'video',
                        'horizontalRule'
                    ],
                    [
                        'showBlocks',
                        'codeView',
                        'preview'
                    ]
                ]
                // lang: lang.ko
            });
        },
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || ''
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        handleChange(value) {
            this.editor.setContents(value)

            this.$emit('field-changed')
        },

        fill(formData) {
            this.fillIfVisible(formData, this.fieldAttribute, this.value || '')
        },

        onSyncedField() {
            this.handleChange(this.currentField.value ?? this.value)
            this.index++
        },
    },
}
</script>
