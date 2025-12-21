<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { EditorView, basicSetup } from 'codemirror';
import { EditorState } from '@codemirror/state';
import { python } from '@codemirror/lang-python';
import { java } from '@codemirror/lang-java';
import { javascript } from '@codemirror/lang-javascript';

interface Props {
    language?: 'python' | 'java' | 'javascript';
    initialCode?: string;
    height?: string;
    minHeight?: string;
}

const props = withDefaults(defineProps<Props>(), {
    language: 'python',
    initialCode: '',
    height: '400px',
    minHeight: '300px',
});

const editorContainer = ref<HTMLDivElement | null>(null);
let editor: EditorView | null = null;

const getLanguageSupport = () => {
    switch (props.language) {
        case 'python':
            return python();
        case 'java':
            return java();
        case 'javascript':
            return javascript();
        default:
            return python();
    }
};

const getCode = () => {
    return editor?.state.doc.toString() || '';
};

const setCode = (code: string) => {
    if (editor) {
        editor.dispatch({
            changes: {
                from: 0,
                to: editor.state.doc.length,
                insert: code,
            },
        });
    }
};

const clearCode = () => {
    setCode('');
};

onMounted(() => {
    if (editorContainer.value) {
        const startState = EditorState.create({
            doc: props.initialCode,
            extensions: [basicSetup, getLanguageSupport()],
        });

        editor = new EditorView({
            state: startState,
            parent: editorContainer.value,
        });
    }
});

defineExpose({
    getCode,
    setCode,
    clearCode,
});
</script>

<template>
    <div
        ref="editorContainer"
        class="code-editor rounded-lg border border-sidebar-border/50 overflow-hidden"
        :style="{ height: height, minHeight: minHeight }"
    ></div>
</template>

<style scoped>
:deep(.cm-editor) {
    height: 100% !important;
    font-family: 'Fira Code', monospace;
}

:deep(.cm-content) {
    padding: 1rem;
}

:deep(.cm-gutters) {
    background-color: #1e1e1e;
    border-right: 1px solid #3e3e3e;
}

:deep(.cm-activeLineGutter) {
    background-color: #2d2d2d;
}

:deep(.cm-cursor) {
    border-left-color: #d4d4d4;
}
</style>
