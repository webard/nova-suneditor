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

import { picmo }  from 'suneditor-picmo-emoji'

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
    },

    beforeUnmount() {
        Nova.$off(this.fieldAttributeValueEventName, this.listenToValueChanges)
    },

    methods: {
        initEditor() {
            const buttonListName = this.field.buttonListName ?? 'default';
            const buttonList = this.field.buttonList ?? Nova.config(`suneditor-buttonLists`)[buttonListName];
            const settings = this.field.settings ??  Nova.config(`suneditor-settings`);

            console.log(settings);

            this.editor = suneditor.create(this.$refs.editor, {
                plugins: {
                    picmo,
                    ...plugins
                },
                buttonList,
                ...settings
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
