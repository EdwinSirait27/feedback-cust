<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import FeedbackLayout from '@/layouts/FeedbackLayout.vue';
// import { updateStep } from '@/actions/App/Http/Controllers/FeedbackController'
import {
    updateStep,
    welcome,
} from '@/actions/App/Http/Controllers/FeedbackController';

import Multiselect from '@vueform/multiselect';
import '@vueform/multiselect/themes/default.css';

const { t } = useI18n();

interface Country {
    code: string;
    name: string;
}

defineProps<{
    countries: Country[];
}>();

const form = useForm({
    receipt_number: '',
    total_purchase: null as number | null,
    country: '',
    overall_rating: null as number | null,
    finish_now: false,
});

function submit(finishNow: boolean) {
    form.finish_now = finishNow;
    form.submit(updateStep(2));
}
</script>

<template>
    <FeedbackLayout
        :step="2"
        :title="t('page2.title')"
        :subtitle="t('page2.subtitle')"
        :back-href="welcome()"
        :back-label="t('page2.backToLanguage')"
        :loading="form.processing"
        @continue="submit(false)"
        @finish="submit(true)"
    >
        <div class="space-y-5">
            <div>
                <label class="mb-1 block text-sm font-medium text-[#3B2A20]">
                    {{ t('page2.receiptNumber') }}
                    <span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.receipt_number"
                    type="text"
                    :placeholder="t('page2.receiptPlaceholder')"
                    class="w-full rounded-lg border border-[#E8D9C3] bg-white px-3 py-2.5 text-sm text-[#3B2A20] placeholder:text-[#C9B79C] focus:ring-2 focus:ring-[#3B2A20]/20 focus:outline-none"
                />
                <p
                    v-if="form.errors.receipt_number"
                    class="mt-1 text-xs text-red-500"
                >
                    {{ form.errors.receipt_number }}
                </p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-[#3B2A20]">
                    {{ t('page2.totalPurchase') }}
                    <span class="text-red-500">*</span>
                </label>
                <input
                    v-model.number="form.total_purchase"
                    type="number"
                    :placeholder="t('page2.totalPurchasePlaceholder')"
                    class="w-full rounded-lg border border-[#E8D9C3] bg-white px-3 py-2.5 text-sm text-[#3B2A20] focus:ring-2 focus:ring-[#3B2A20]/20 focus:outline-none"
                />
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-[#3B2A20]">
                    {{ t('page2.country') }} <span class="text-red-500">*</span>
                </label>
                <Multiselect
                    v-model="form.country"
                    :options="countries"
                    label="name"
                    value-prop="code"
                    :placeholder="t('page2.countryPlaceholder')"
                    :searchable="true"
                    :can-clear="false"
                    :no-options-text="t('No Options')"
                    :no-results-text="t('No Results')"
                />
                <p v-if="form.errors.country" class="mt-1 text-xs text-red-500">
                    {{ form.errors.country }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-[#3B2A20]">
                    {{ t('page2.overallRating') }}
                </label>
                <div class="flex items-center gap-2">
                    <button
                        v-for="n in 5"
                        :key="n"
                        type="button"
                        class="text-3xl"
                        :class="
                            n <= (form.overall_rating ?? 0)
                                ? 'text-[#D4A017]'
                                : 'text-[#E8D9C3]'
                        "
                        @click="form.overall_rating = n"
                    >
                        ★
                    </button>
                </div>
                <div class="mt-1 flex justify-between text-xs text-[#8B5E3C]">
                    <span>{{ t('page2.veryPoor') }}</span>
                    <span>{{ t('page2.excellent') }}</span>
                </div>
            </div>
        </div>
    </FeedbackLayout>
</template>

<style>
/* yang sudah ada sebelumnya */
.multiselect-option {
    color: #3b2a20 !important;
}
.multiselect-option.is-pointed {
    background: #3b2a20 !important;
    color: white !important;
}
.multiselect-option.is-selected {
    background: #e8d9c3 !important;
    color: #3b2a20 !important;
}
.multiselect-option.is-selected.is-pointed {
    background: #3b2a20 !important;
    color: white !important;
}

/* tambahan baru — fix warna teks input & background */
.multiselect-wrapper {
    background: white !important;
}
.multiselect {
    background: white !important;
    border-color: #e8d9c3 !important;
    border-radius: 0.5rem !important;
}
.multiselect.is-open {
    border-color: #3b2a20 !important;
    box-shadow: 0 0 0 2px rgba(59, 42, 32, 0.2) !important;
}
.multiselect-single-label {
    color: #3b2a20 !important;
}
.multiselect-placeholder {
    color: #c9b79c !important;
}
.multiselect-search {
    background: white !important;
    color: #3b2a20 !important;
}
.multiselect-search::placeholder {
    color: #c9b79c !important;
}
.multiselect-dropdown {
    background: white !important;
    border-color: #e8d9c3 !important;
    border-radius: 0.5rem !important;
}
.multiselect-caret {
    color: #8b5e3c !important;
}
</style>
