<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import FeedbackLayout from '@/layouts/FeedbackLayout.vue';
import { ref } from 'vue';
// import { updateStep } from '@/actions/App/Http/Controllers/FeedbackController'
import {
    updateStep,
    welcome,
} from '@/actions/App/Http/Controllers/FeedbackController';

import Multiselect from '@vueform/multiselect';
import '@vueform/multiselect/themes/default.css';
import { computed } from 'vue'


interface Feedback {
        has_receipt: boolean | null
    receipt_number: string | null
    total_purchase: number | null
    country: string | null
    overall_rating: number | null
    language: string
}
const props = defineProps<{
    countries: Country[]
    feedback: Feedback
}>()

// const hasReceipt = ref<boolean | null>(
//     props.feedback.receipt_number !== null
//         ? true
//         : props.feedback.total_purchase !== null
//             ? false
//             : null
// )
const hasReceipt = ref<boolean | null>(
    props.feedback.has_receipt !== null && props.feedback.has_receipt !== undefined
        ? props.feedback.has_receipt
        : null
)

const hasReceiptError = ref<string | null>(null);

const { t } = useI18n();

interface Country {
    code: string;
    name: string;
}



// const form = useForm({
//     receipt_number: '',
//     total_purchase: null as number | null,
//     country: '',
//     overall_rating: null as number | null,
//     finish_now: false,
// });
const form = useForm({
           has_receipt: props.feedback.has_receipt ?? null,
    receipt_number: props.feedback.receipt_number ?? '',
    total_purchase: props.feedback.total_purchase ?? null,
    country: props.feedback.country ?? '',
    overall_rating: props.feedback.overall_rating ?? null,
    finish_now: false,
})


function submit(finishNow: boolean) {
    // Validasi hasReceipt wajib dipilih dulu
    if (hasReceipt.value === null) {
        hasReceiptError.value = t('page2.hasReceiptRequired');
        return;
    }

    hasReceiptError.value = null;

    if (hasReceipt.value === true && !form.receipt_number) {
        form.setError('receipt_number', t('page2.receiptNumberRequired'))
        return
    }

    // Tambah ini
    if (hasReceipt.value === true && !form.total_purchase) {
        form.setError('total_purchase', t('page2.totalPurchaseRequired'))
        return
    }

    if (form.overall_rating === null) {
        form.setError('overall_rating', t('page2.overallRatingRequired'));
        return;
    }

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
        <div>
            <div class="survey-field">
                <p class="survey-label">
                    {{ t('page2.hasReceiptQuestion') }}
                    <span class="survey-required">*</span>
                </p>
                <div class="survey-choices">
                    <button
                        type="button"
                        class="survey-choice"
                        :aria-pressed="hasReceipt === true"
                        @click="
                            hasReceipt = true;
                            form.has_receipt = true;
                            hasReceiptError = null;
                        "
                    >
                        {{ t('page2.hasReceiptYes') }}
                    </button>
                    <button
                        type="button"
                        class="survey-choice"
                        :aria-pressed="hasReceipt === false"
                        @click="hasReceipt = false; form.has_receipt = false; form.receipt_number = ''; form.total_purchase = null; hasReceiptError = null"
                    >
                        {{ t('page2.hasReceiptNo') }}
                    </button>
                </div>
                <p v-if="hasReceiptError" class="survey-error" role="alert">
                    {{ hasReceiptError }}
                </p>
                <p v-if="hasReceipt === false" class="survey-note">
                    {{ t('page2.noReceiptNote') }}
                </p>
            </div>

            <template v-if="hasReceipt === true">
                <div class="survey-field">
                    <label class="survey-label" for="receipt_number">
                        {{ t('page2.receiptNumber') }}
                        <span class="survey-required">*</span>
                    </label>
                    <input
                        id="receipt_number"
                        v-model="form.receipt_number"
                        name="receipt_number"
                        type="text"
                        autocomplete="off"
                        :placeholder="t('page2.receiptPlaceholder')"
                        class="survey-input"
                    />
                    <p v-if="form.errors.receipt_number" class="survey-error" role="alert">
                        {{ form.errors.receipt_number }}
                    </p>
                </div>

                <div class="survey-field">
                    <label class="survey-label" for="total_purchase">
                        {{ t('page2.totalPurchase') }}
                        <span class="survey-required">*</span>
                    </label>
                    <input
                        id="total_purchase"
                        v-model.number="form.total_purchase"
                        name="total_purchase"
                        type="number"
                        inputmode="numeric"
                        :placeholder="t('page2.totalPurchasePlaceholder')"
                        class="survey-input"
                    />
                    <p v-if="form.errors.total_purchase" class="survey-error" role="alert">
                        {{ form.errors.total_purchase }}
                    </p>
                </div>
            </template>

            <div class="survey-field">
                <p class="survey-label">
                    {{ t('page2.country') }} <span class="survey-required">*</span>
                </p>
                <Multiselect
                    v-model="form.country"
                    :options="countries"
                    label="name"
                    value-prop="code"
                    :placeholder="t('page2.countryPlaceholder')"
                    :searchable="true"
                    :can-clear="false"
                    :no-options-text="t('page2.noOptions')"
                    :no-results-text="t('page2.noResults')"
                />
                <p v-if="form.errors.country" class="survey-error" role="alert">
                    {{ form.errors.country }}
                </p>
            </div>

            <div class="survey-field">
                <p class="survey-label">
                    {{ t('page2.overallRating') }}
                    <span class="survey-required">*</span>
                </p>
                <div class="survey-flowers survey-flowers--large" role="group" :aria-label="t('page2.overallRating')">
                    <button
                        v-for="n in 5"
                        :key="n"
                        type="button"
                        class="survey-flower"
                        :data-on="n <= (form.overall_rating ?? 0)"
                        :aria-label="`${n} / 5`"
                        :aria-pressed="n === form.overall_rating"
                        @click="form.overall_rating = n"
                    />
                </div>
                <div class="survey-scale survey-small">
                    <span>{{ t('page2.veryPoor') }}</span>
                    <span>{{ t('page2.excellent') }}</span>
                </div>
                <p v-if="form.errors.overall_rating" class="survey-error" role="alert">
                    {{ form.errors.overall_rating }}
                </p>
            </div>
        </div>
    </FeedbackLayout>
</template>

<style>
.multiselect-option { color: var(--color-ink) !important; }
.multiselect-option.is-pointed { background: var(--color-ink) !important; color: var(--color-paper) !important; }
.multiselect-option.is-selected { background: var(--color-paper-3) !important; color: var(--color-ink) !important; }
.multiselect-option.is-selected.is-pointed { background: var(--color-ink) !important; color: var(--color-paper) !important; }
.multiselect-wrapper { min-height: 3rem; background: var(--color-paper-2) !important; }
.multiselect { min-height: 3rem; background: var(--color-paper-2) !important; border-color: var(--color-ink) !important; border-radius: 0 !important; font-size: 1rem; }
.multiselect.is-open, .multiselect.is-active { border-color: var(--color-ink) !important; box-shadow: none !important; outline: 2px solid var(--color-focus); outline-offset: -3px; }
.multiselect-single-label { color: var(--color-ink) !important; }
.multiselect-placeholder { color: var(--color-ink-3) !important; }
.multiselect-search { background: var(--color-paper-2) !important; color: var(--color-ink) !important; border-radius: 0 !important; }
.multiselect-search::placeholder { color: var(--color-ink-3) !important; }
.multiselect-dropdown { background: var(--color-paper-2) !important; border-color: var(--color-ink) !important; border-radius: 0 !important; }
.multiselect-caret { background-color: var(--color-ink-2) !important; }
</style>
<!-- Pertanyaan struk — tampil selalu -->
<!-- <div>
                <label class="mb-2 block text-sm font-medium text-[#3B2A20]">
                    {{ t('page2.hasReceiptQuestion') }}
                </label>
                <div class="flex gap-3">
                    <button
                        type="button"
                        class="flex-1 rounded-lg border py-3 text-sm font-medium transition-colors"
                        :class="
                            hasReceipt === true
                                ? 'border-[#3B2A20] bg-[#3B2A20] text-white'
                                : 'border-[#E8D9C3] text-[#8B5E3C] hover:bg-[#F3E4CC]'
                        "
                        @click="hasReceipt = true"
                    >
                        {{ t('page2.hasReceiptYes') }}
                    </button>
                    <button
                        type="button"
                        class="flex-1 rounded-lg border py-3 text-sm font-medium transition-colors"
                        :class="
                            hasReceipt === false
                                ? 'border-[#3B2A20] bg-[#3B2A20] text-white'
                                : 'border-[#E8D9C3] text-[#8B5E3C] hover:bg-[#F3E4CC]'
                        "
                        @click="
                            hasReceipt = false;
                            form.receipt_number = '';
                            form.total_purchase = null;
                        "
                    >
                        {{ t('page2.hasReceiptNo') }}
                    </button>
                </div>
            </div> -->
