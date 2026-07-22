<script setup lang="ts">
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
                <a href="https://g.page/r/GANTI-DENGAN-LINK-REVIEW-KAMU/review" target="_blank" rel="noopener"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-[#E8D9C3] text-sm font-medium text-[#3B2A20] hover:bg-[#F3E4CC]">
                    {{ t('result.reviewButton') }}
                </a>
            </div>
        </div>
    </div>
</template>