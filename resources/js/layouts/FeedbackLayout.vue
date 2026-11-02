<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import type { UrlMethodPair } from '@inertiajs/core';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import BrandLogo from '@/components/BrandLogo.vue';

interface Props {
    step: number;
    totalSteps?: number;
    title: string;
    subtitle?: string;
    backHref?: string | UrlMethodPair | null;
    backLabel?: string | null;
    showGiveMore?: boolean;
    loading?: boolean;
}
withDefaults(defineProps<Props>(), {
    totalSteps: 7,
    backHref: null,
    backLabel: null,
    showGiveMore: true,
    loading: false,
});

defineEmits<{ continue: []; finish: [] }>();

const { t } = useI18n();
const page = usePage();
const flashError = computed(() => (page.props.flash as any)?.error);
</script>

<template>
    <div class="survey">
        <div v-if="flashError" class="survey-alert" role="alert">
            {{ flashError }}
        </div>

        <header class="survey-frame">
            <div class="survey-bar">
                <BrandLogo />
                <div
                    class="survey-progress"
                    role="progressbar"
                    :aria-valuenow="step"
                    aria-valuemin="1"
                    :aria-valuemax="totalSteps"
                >
                    <span>{{ step }} / {{ totalSteps }}</span>
                    <span class="survey-progress__track" aria-hidden="true">
                        <span
                            v-for="n in totalSteps"
                            :key="n"
                            class="survey-progress__segment"
                            :data-state="
                                n === step
                                    ? 'current'
                                    : n < step
                                      ? 'done'
                                      : 'todo'
                            "
                        />
                    </span>
                </div>
            </div>
        </header>

        <div class="survey-frame survey-grid">
            <div class="survey-intro">
                <h1 class="survey-title">
                    {{ title }}
                </h1>
                <p v-if="subtitle" class="survey-subtitle">
                    {{ subtitle }}
                </p>
            </div>

            <main class="survey-main">
                <slot />

                <div class="survey-actions">
                    <button
                        type="button"
                        :disabled="loading"
                        class="survey-btn"
                        @click="$emit('finish')"
                    >
                        {{ t('common.submit') }}
                    </button>
                    <button
                        v-if="showGiveMore"
                        type="button"
                        :disabled="loading"
                        class="survey-plain"
                        @click="$emit('continue')"
                    >
                        {{ t('common.giveMore') }}
                    </button>
                </div>

                <div v-if="backHref" class="survey-back">
                    <p>
                        <Link :href="backHref" class="survey-link">
                            {{ backLabel ?? t('common.back') }}
                        </Link>
                    </p>
                    <p class="survey-small">
                        {{ t('common.backHint') }}
                    </p>
                </div>
            </main>
        </div>
    </div>
</template>
