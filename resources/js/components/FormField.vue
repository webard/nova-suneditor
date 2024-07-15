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
import { FormField, HandlesValidationErrors } from 'laravel-nova'

import suneditor from 'suneditor'
import plugins from 'suneditor/src/plugins'
import image from 'suneditor/src/plugins/dialog/link'
import list from 'suneditor/src/plugins/submenu/list'
import { EmojiButton } from '@joeattardi/emoji-button';

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],
    data() {
        return {
            mounted: false
        }
    },

    created() {

        this.setInitialValue()


    },

    mounted() {
        this.initEditor()

        this.editor.onPaste = function (e, cleanData, maxCharCount, core) { console.log('onPaste', e) }

        this.picker.on('emoji', (emoji) => { this.handleEmoji(emoji) });
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
            this.picker = new EmojiButton();

            // Define the custom plugin
            return {
                name: 'emoji',
                display: 'command',
                title: 'Emoji',
                buttonClass: '',
                innerHTML: '<div class="se-btn-module"><button type="button" class="se-btn se-btn-module" title="Insert HTML"><span>😀</span></button></div>',
                add: function (core) { },
                action: () => {

                    this.picker.togglePicker(this.$refs.emojianchor);
                }
            };
        },

        initEditor() {
            this.editor = suneditor.create(this.$refs.editor, {
                plugins: [
                    this.initEmojiPickerPlugin(),
                    list,
                    image,
                    plugins.fontColor,
                    plugins.fontSize,
                    plugins.align,
                    plugins.horizontalRule,
                    plugins.table],
                height: 'auto',
                buttonList: [
                    ['fontColor', 'fontSize', 'emoji', 'list', 'bold', 'underline', 'italic', 'table', 'link', 'image', 'align', 'horizontalRule']
                ],
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
        fill(formData) {
            formData.append(this.fieldAttribute, this.value || '')
        },


    },
}
</script>
