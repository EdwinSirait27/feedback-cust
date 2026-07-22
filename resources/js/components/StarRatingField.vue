<script setup lang="ts">
interface Props {
    modelValue: number | null
    label: string
}

defineProps<Props>()
defineEmits<{ 'update:modelValue': [value: number] }>()
</script>

<template>
    <div class="flex items-center justify-between py-3 border-b border-[#F0E4D0] last:border-0">
        <p class="text-sm font-medium text-[#3B2A20]">{{ label }}</p>
        <div class="flex items-center gap-1">
            <button
                v-for="n in 5"
                :key="n"
                type="button"
                class="text-2xl leading-none"
                :class="n <= (modelValue ?? 0) ? 'text-[#D4A017]' : 'text-[#E8D9C3]'"
                @click="$emit('update:modelValue', n)"
            >
                ★
            </button>
        </div>
    </div>
</template>