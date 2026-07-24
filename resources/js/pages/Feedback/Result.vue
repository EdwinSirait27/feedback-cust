<!-- <script setup lang="ts">
import { useI18n } from 'vue-i18n'

interface SpinResult {
    prize_type: string
    voucher_amount: number | null
    voucher_code: string | null
}

const props = defineProps<{ spin: SpinResult }>()
const { t } = useI18n()

const isWinner = props.spin.prize_type !== 'no_prize'
</script>

<template>
    <div class="min-h-screen bg-[#FBF3E7] flex items-center justify-center px-6 py-10">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-sm border border-[#F0E4D0] p-8 text-center">
            <template v-if="isWinner">
                <h1 class="text-2xl font-bold text-[#3B2A20]">{{ t('result.congrats') }}</h1>
                <p class="text-sm text-[#8B5E3C] mt-1">{{ t('result.youWon') }}</p>
                <div class="mt-6 bg-[#B23B3B] text-white rounded-xl py-4 px-6 inline-block">
                    <p class="text-xs uppercase tracking-wide">{{ t('result.voucher') }}</p>
                    <p class="text-2xl font-bold">Rp {{ spin.voucher_amount?.toLocaleString('id-ID') }}</p>
                    <p class="text-xs mt-1 font-mono">{{ spin.voucher_code }}</p>
                </div>
                <p class="text-xs text-[#8B5E3C] mt-6">{{ t('result.sentNote') }}</p>
            </template>
            <template v-else>
                <h1 class="text-2xl font-bold text-[#3B2A20]">{{ t('result.thankYouTitle') }}</h1>
                <p class="text-sm text-[#8B5E3C] mt-2">{{ t('result.thankYouNote') }}</p>
            </template>

            <div class="mt-8 pt-6 border-t border-[#F0E4D0]">
                <p class="text-xs font-semibold text-[#8B5E3C] mb-3">{{ t('result.helpUs') }}</p>
                <a href="https://share.google/nIAj2AdDYWcU76oJM" target="_blank" rel="noopener"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-[#E8D9C3] text-sm font-medium text-[#3B2A20] hover:bg-[#F3E4CC]">
                    {{ t('result.reviewButton') }}
                </a>
            </div>
        </div>
    </div>
</template> -->
<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { done as doneAction } from '@/actions/App/Http/Controllers/FeedbackController'

interface SpinResult {
    prize_type: string
    voucher_amount: number | null
    voucher_code: string | null
}

const props = defineProps<{ spin: SpinResult }>()
const { t } = useI18n()

const isWinner = props.spin.prize_type !== 'no_prize'

function done() {
    router.post(doneAction().url, {}, {
        preserveState: false,
        preserveScroll: false,
    })
}
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-[#FBF3E7] px-6 py-10">
        <div class="w-full max-w-md rounded-2xl border border-[#F0E4D0] bg-white p-8 text-center shadow-sm">
            <template v-if="isWinner">
                <h1 class="text-2xl font-bold text-[#3B2A20]">{{ t('result.congrats') }}</h1>
                <p class="mt-1 text-sm text-[#8B5E3C]">{{ t('result.youWon') }}</p>
                <div class="mt-6 inline-block rounded-xl bg-[#B23B3B] px-6 py-4 text-white">
                    <p class="text-xs uppercase tracking-wide">{{ t('result.voucher') }}</p>
                    <p class="text-2xl font-bold">Rp {{ spin.voucher_amount?.toLocaleString('id-ID') }}</p>
                    <p class="mt-1 font-mono text-xs">{{ spin.voucher_code }}</p>
                </div>
                <p class="mt-6 text-xs text-[#8B5E3C]">{{ t('result.sentNote') }}</p>
            </template>
            <template v-else>
                <h1 class="text-2xl font-bold text-[#3B2A20]">{{ t('result.thankYouTitle') }}</h1>
                <p class="mt-2 text-sm text-[#8B5E3C]">{{ t('result.thankYouNote') }}</p>
            </template>

            <!-- Google Review -->
            <div class="mt-8 border-t border-[#F0E4D0] pt-6">
                <p class="mb-3 text-xs font-semibold text-[#8B5E3C]">{{ t('result.helpUs') }}</p>
                <a
                    href="https://share.google/nIAj2AdDYWcU76oJM"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center gap-2 rounded-lg border border-[#E8D9C3] px-4 py-2.5 text-sm font-medium text-[#3B2A20] hover:bg-[#F3E4CC]">
                    {{ t('result.reviewButton') }}
                </a>
            </div>

            <!-- Tombol Done -->
            <div class="mt-4">
                <button
                    type="button"
                    class="w-full rounded-lg bg-[#3B2A20] px-5 py-3 text-sm font-medium text-white hover:bg-[#2C1F17] transition-colors"
                    @click="done"
                >
                    {{ t('result.doneButton') }}
                </button>
                <p class="mt-2 text-[11px] text-[#C9B79C]">
                    {{ t('result.doneHint') }}
                </p>
            </div>
        </div>
    </div>
</template>