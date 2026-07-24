<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { UrlMethodPair } from '@inertiajs/core';
import { useI18n } from 'vue-i18n';

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
</script>

<template>
    <div class="flex min-h-screen flex-col bg-[#FBF3E7]">
        <header class="border-b border-[#E8D9C3] bg-white/60 backdrop-blur-sm">
            <div
                class="mx-auto flex max-w-3xl items-center justify-between px-6 py-4"
            >
                <span
                    class="rounded-full bg-[#F3E4CC] px-3 py-1 text-xs font-semibold tracking-wide text-[#8B5E3C]"
                >
                    PAGE {{ step }}
                </span>
                <div class="flex items-center gap-2">
                    <div
                        v-for="n in totalSteps"
                        :key="n"
                        class="flex h-7 w-7 items-center justify-center rounded-full border text-xs font-semibold transition-colors"
                        :class="
                            n === step
                                ? 'border-[#3B2A20] bg-[#3B2A20] text-white'
                                : n < step
                                  ? 'border-[#E8D9C3] bg-[#E8D9C3] text-[#8B5E3C]'
                                  : 'border-[#E8D9C3] bg-transparent text-[#C9B79C]'
                        "
                    >
                        {{ n }}
                    </div>
                </div>
            </div>
        </header>

        <main
            class="mx-auto w-full max-w-3xl flex-1 px-4 pt-6 pb-4 sm:px-6 sm:py-10"
        >
            <div class="mb-6 px-2 text-center sm:mb-8">
                <h1 class="text-xl font-bold text-[#3B2A20] sm:text-2xl">
                    {{ title }}
                </h1>
                <p v-if="subtitle" class="mt-1 text-sm text-[#8B5E3C]">
                    {{ subtitle }}
                </p>
            </div>
            <div
                class="rounded-2xl border border-[#F0E4D0] bg-white p-4 shadow-sm sm:p-6"
            >
                <slot />
            </div>
        </main>

        <!-- <footer
            class="mx-auto mt-4 w-full max-w-3xl px-4 pb-8 sm:px-6 sm:pb-10"
        >
         
            <div class="hidden items-center justify-between gap-3 sm:flex">
    <div class="flex flex-col gap-1">
        <Link
            v-if="backHref"
            :href="backHref"
            class="rounded-lg border border-[#D9C6A8] px-4 py-2.5 text-sm font-medium text-[#8B5E3C] hover:bg-[#F3E4CC]"
        >
            {{ backLabel ?? t('common.back') }}
        </Link>
        <p v-if="backHref" class="px-1 text-[11px] text-[#C9B79C]">
            {{ t('common.backHint') }}
        </p>
        <div v-if="!backHref" />
    </div>

    <div class="flex items-center gap-3">
        <button
            v-if="showGiveMore"
            type="button"
            :disabled="loading"
            class="rounded-lg bg-[#F3E4CC] px-4 py-2.5 text-sm font-medium text-[#8B5E3C] hover:bg-[#EBD6B3] disabled:opacity-50"
            @click="$emit('continue')"
        >
            {{ t('common.giveMore') }}
        </button>
        <button
            type="button"
            :disabled="loading"
            class="rounded-lg bg-[#3B2A20] px-5 py-2.5 text-sm font-medium text-white hover:bg-[#2C1F17] disabled:opacity-50"
            @click="$emit('finish')"
        >
            {{ t('common.submit') }}
        </button>
    </div>
</div>

            <div class="flex flex-col gap-2 sm:hidden">
                <button
                    v-if="showGiveMore"
                    type="button"
                    :disabled="loading"
                    class="w-full rounded-lg bg-[#F3E4CC] py-3 text-sm font-medium text-[#8B5E3C] hover:bg-[#EBD6B3] disabled:opacity-50"
                    @click="$emit('continue')"
                >
                    {{ t('common.giveMore') }}
                </button>
                <button
                    type="button"
                    :disabled="loading"
                    class="w-full rounded-lg bg-[#3B2A20] py-3 text-sm font-medium text-white hover:bg-[#2C1F17] disabled:opacity-50"
                    @click="$emit('finish')"
                >
                    {{ t('common.submit') }}
                </button>
                <Link
                    v-if="backHref"
                    :href="backHref"
                    class="w-full rounded-lg border border-[#D9C6A8] py-2.5 text-center text-sm font-medium text-[#8B5E3C] hover:bg-[#F3E4CC]"
                >
                    {{ backLabel ?? t('common.back') }}
                </Link>
            </div>
        </footer> -->
        <footer class="mx-auto mt-4 w-full max-w-3xl px-4 pb-8 sm:px-6 sm:pb-10">
    <!-- Satu struktur, responsif -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-col gap-1 order-last sm:order-first">
            <Link
                v-if="backHref"
                :href="backHref"
                class="rounded-lg border border-[#D9C6A8] px-4 py-2.5 text-center text-sm font-medium text-[#8B5E3C] hover:bg-[#F3E4CC] sm:text-left"
            >
                {{ backLabel ?? t('common.back') }}
            </Link>
            <p v-if="backHref" class="px-1 text-[11px] text-[#C9B79C]">
                {{ t('common.backHint') }}
            </p>
            <div v-if="!backHref" />
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
            <button
                v-if="showGiveMore"
                type="button"
                :disabled="loading"
                class="w-full rounded-lg bg-[#F3E4CC] py-3 text-sm font-medium text-[#8B5E3C] hover:bg-[#EBD6B3] disabled:opacity-50 sm:w-auto sm:px-4 sm:py-2.5"
                @click="$emit('continue')"
            >
                {{ t('common.giveMore') }}
            </button>
            <button
                type="button"
                :disabled="loading"
                class="w-full rounded-lg bg-[#3B2A20] py-3 text-sm font-medium text-white hover:bg-[#2C1F17] disabled:opacity-50 sm:w-auto sm:px-5 sm:py-2.5"
                @click="$emit('finish')"
            >
                {{ t('common.submit') }}
            </button>
        </div>
    </div>
</footer>
    </div>
</template>
