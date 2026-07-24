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
        <div class="space-y-5">
            <div>
                <label class="mb-2 block text-sm font-medium text-[#3B2A20]">
                    {{ t('page2.hasReceiptQuestion') }}
                    <span class="text-red-500">*</span>
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
                        @click="
                            hasReceipt = true;
                            hasReceiptError = null;
                        "
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
                      @click="hasReceipt = false; form.has_receipt = false; form.receipt_number = ''; form.total_purchase = null; hasReceiptError = null"
                    >
                        {{ t('page2.hasReceiptNo') }}
                    </button>
                </div>
                <!-- Pesan error kalau belum pilih -->
                <p v-if="hasReceiptError" class="mt-1 text-xs text-red-500">
                    {{ hasReceiptError }}
                </p>
            </div>

            <!-- Field receipt & total — hanya muncul kalau pilih Yes -->
            <template v-if="hasReceipt === true">
                <div>
                    <label
                        class="mb-1 block text-sm font-medium text-[#3B2A20]"
                    >
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

                <!-- <div>
                    <label
                        class="mb-1 block text-sm font-medium text-[#3B2A20]"
                    >
                        {{ t('page2.totalPurchase') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model.number="form.total_purchase"
                        type="number"
                        :placeholder="t('page2.totalPurchasePlaceholder')"
                        class="w-full rounded-lg border border-[#E8D9C3] bg-white px-3 py-2.5 text-sm text-[#3B2A20] focus:ring-2 focus:ring-[#3B2A20]/20 focus:outline-none"
                    />
                </div> -->
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
    <!-- Tambah ini -->
    <p v-if="form.errors.total_purchase" class="mt-1 text-xs text-red-500">
        {{ form.errors.total_purchase }}
    </p>
</div>
            </template>

            <!-- Kalau pilih No — tampilkan info kecil -->
            <div
                v-if="hasReceipt === false"
                class="rounded-lg border border-[#E8D9C3] bg-[#FBF3E7] px-4 py-3"
            >
                <p class="text-sm text-[#8B5E3C]">
                    {{ t('page2.noReceiptNote') }}
                </p>
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
                   :no-options-text="t('page2.noOptions')"
:no-results-text="t('page2.noResults')"
                />
                <p v-if="form.errors.country" class="mt-1 text-xs text-red-500">
                    {{ form.errors.country }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-[#3B2A20]">
                    {{ t('page2.overallRating') }}
                    <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <button
                        v-for="n in 5"
                        :key="n"
                        type="button"
                        class="text-3xl transition-transform hover:scale-110"
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
                <!-- Tambah ini -->
                <p
                    v-if="form.errors.overall_rating"
                    class="mt-1 text-xs text-red-500"
                >
                    {{ form.errors.overall_rating }}
                </p>
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
