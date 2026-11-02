<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { selectLanguage } from '@/actions/App/Http/Controllers/FeedbackController';
const { t, locale } = useI18n();
const languages = [
    { code: 'id', label: 'Bahasa Indonesia' },
    { code: 'en', label: 'English' },
    { code: 'zh', label: '中文（简体）' },
    { code: 'ja', label: '日本語 (Japanese)' },
    { code: 'ko', label: '한국어 (Korean)' },
    { code: 'ru', label: 'Русский (Russian)' },
];
const form = useForm({ language: 'en' });

function choose(code: string) {
    form.language = code;
    locale.value = code;
    form.submit(selectLanguage());
}
</script>

<template>
    <div class="survey">
        <main class="survey-frame survey-welcome">
            <div>
                <img
                    src="/images/bali-in-bali-logo.png"
                    width="480"
                    height="431"
                    alt="Bali in Bali"
                    class="survey-welcome__logo"
                />
                <h1 class="survey-title survey-title--hero">
                    {{ t('welcome.title') }}
                </h1>
                <p class="survey-subtitle">
                    {{ t('welcome.subtitle') }}
                </p>
            </div>

            <div>
                <p class="survey-label">
                    {{ t('welcome.chooseLanguage') }}
                </p>
                <div class="survey-languages">
                    <button
                        v-for="lang in languages"
                        :key="lang.code"
                        type="button"
                        :lang="lang.code"
                        :disabled="form.processing"
                        class="survey-language"
                        @click="choose(lang.code)"
                    >
                        {{ lang.label }}
                    </button>
                </div>
            </div>
        </main>
    </div>
</template>
