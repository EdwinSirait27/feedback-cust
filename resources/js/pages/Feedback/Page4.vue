<script setup lang="ts">
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import FeedbackLayout from '@/layouts/FeedbackLayout.vue'
import StarRatingField from '@/components/StarRatingField.vue'
import { showStep, updateStep } from '@/actions/App/Http/Controllers/FeedbackController'

const { t } = useI18n()
const isSubmitting = ref(false)
interface Feedback {
    store_cleanliness: number | null
    store_ambience: number | null
    product_display: number | null
    product_variety: number | null
}

const props = defineProps<{ feedback: Feedback }>()


// const form = useForm({
//     store_cleanliness: null as number | null,
//     store_ambience: null as number | null,
//     product_display: null as number | null,
//     product_variety: null as number | null,
//     finish_now: false,
// })

const form = useForm({
    store_cleanliness: props.feedback.store_cleanliness ?? null,
    store_ambience: props.feedback.store_ambience ?? null,
    product_display: props.feedback.product_display ?? null,
    product_variety: props.feedback.product_variety ?? null,
    finish_now: false,
})

function submit(finishNow: boolean) {
    if (isSubmitting.value) return
    if (!form.store_cleanliness) { form.setError('store_cleanliness', t('validation.ratingRequired')); return }
    if (!form.store_ambience) { form.setError('store_ambience', t('validation.ratingRequired')); return }
    if (!form.product_display) { form.setError('product_display', t('validation.ratingRequired')); return }
    if (!form.product_variety) { form.setError('product_variety', t('validation.ratingRequired')); return }

    isSubmitting.value = true
    form.finish_now = finishNow
    router.post(updateStep(4).url, form.data(), {
        preserveState: false,
        preserveScroll: false,
        onFinish: () => { isSubmitting.value = false },
    })
}
</script>

<template>
    <FeedbackLayout
        :step="4"
        :title="t('page4.title')"
        :subtitle="t('page4.subtitle')"
        :back-href="showStep(3)"
        :loading="form.processing || isSubmitting"
        @continue="submit(false)"
        @finish="submit(true)"
    >
        <StarRatingField v-model="form.store_cleanliness" :label="t('page4.storeCleanliness')" :error="form.errors.store_cleanliness" />
        <StarRatingField v-model="form.store_ambience" :label="t('page4.storeAmbience')" :error="form.errors.store_ambience" />
        <StarRatingField v-model="form.product_display" :label="t('page4.productDisplay')" :error="form.errors.product_display" />
        <StarRatingField v-model="form.product_variety" :label="t('page4.productVariety')" :error="form.errors.product_variety" />
    </FeedbackLayout>
</template>