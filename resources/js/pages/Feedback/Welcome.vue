<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { selectLanguage } from '@/actions/App/Http/Controllers/FeedbackController';
const { t, locale } = useI18n();
const languages = [
    { code: 'id', label: 'Bahasa Indonesia', flag: '🇮🇩' },
    { code: 'en', label: 'English', flag: '🇬🇧' },
    { code: 'zh', label: '中文（简体）', flag: '🇨🇳' },
    { code: 'ja', label: '日本語 (Japanese)', flag: '🇯🇵' },
    { code: 'ko', label: '한국어 (Korean)', flag: '🇰🇷' },
    { code: 'ru', label: 'Русский (Russian)', flag: '🇷🇺' },
];
const form = useForm({ language: 'en' });

function choose(code: string) {
    form.language = code;
    locale.value = code;
    form.submit(selectLanguage());
}
</script>

<template>
    <div
        class="flex min-h-screen items-center justify-center bg-[#FBF3E7] px-6 py-10"
    >
        <div
            class="w-full max-w-md rounded-2xl border border-[#F0E4D0] bg-white p-8 text-center shadow-sm"
        >
            <h1 class="text-2xl font-bold whitespace-pre-line text-[#3B2A20]">
                {{ t('welcome.title') }}
            </h1>
            <p class="mt-3 text-sm text-[#8B5E3C]">
                {{ t('welcome.subtitle') }}
            </p>

            <p class="mt-8 mb-3 text-xs text-[#8B5E3C]">
                {{ t('welcome.chooseLanguage') }}
            </p>

            <div class="space-y-2">
                <button
                    v-for="lang in languages"
                    :key="lang.code"
                    type="button"
                    :disabled="form.processing"
                    class="flex w-full items-center gap-3 rounded-lg border border-[#E8D9C3] px-4 py-3 text-sm font-medium text-[#3B2A20] hover:bg-[#F3E4CC] disabled:opacity-50"
                    @click="choose(lang.code)"
                >
                    <span class="text-xl">{{ lang.flag }}</span>
                    {{ lang.label }}
                </button>
            </div>
        </div>
    </div>
</template>
