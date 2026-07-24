<!-- Page3.vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import FeedbackLayout from '@/layouts/FeedbackLayout.vue'
import StarRatingField from '@/components/StarRatingField.vue'
import { showStep, updateStep } from '@/actions/App/Http/Controllers/FeedbackController'

const { t } = useI18n()

const form = useForm({
    staff_friendliness: null as number | null,
    staff_helpfulness: null as number | null,
    checkout_process: null as number | null,
    service_speed: null as number | null,
    finish_now: false,
})

function submit(finishNow: boolean) {
    form.finish_now = finishNow
    form.submit(updateStep(3))
}
</script>

<template>
    <FeedbackLayout
        :step="3"
        :title="t('page3.title')"
        :subtitle="t('page3.subtitle')"
        :back-href="showStep(2)"
        :loading="form.processing"
        @continue="submit(false)"
        @finish="submit(true)"
    >
        <StarRatingField v-model="form.staff_friendliness" :label="t('page3.staffFriendliness')" />
        <StarRatingField v-model="form.staff_helpfulness" :label="t('page3.staffHelpfulness')" />
        <StarRatingField v-model="form.checkout_process" :label="t('page3.checkoutProcess')" />
        <StarRatingField v-model="form.service_speed" :label="t('page3.serviceSpeed')" />
    </FeedbackLayout>
</template> -->
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
    product_quality: number | null
    product_design: number | null
    price_appropriateness: number | null
    value_for_money: number | null
}
const props = defineProps<{ feedback: Feedback }>()


// const form = useForm({
//     product_quality: null as number | null,
//     product_design: null as number | null,
//     price_appropriateness: null as number | null,
//     value_for_money: null as number | null,
//     finish_now: false,
// })

const form = useForm({
    product_quality: props.feedback.product_quality ?? null,
    product_design: props.feedback.product_design ?? null,
    price_appropriateness: props.feedback.price_appropriateness ?? null,
    value_for_money: props.feedback.value_for_money ?? null,
    finish_now: false,
})

function submit(finishNow: boolean) {
    if (isSubmitting.value) return
    if (!form.product_quality) { form.setError('product_quality', t('validation.ratingRequired')); return }
    if (!form.product_design) { form.setError('product_design', t('validation.ratingRequired')); return }
    if (!form.price_appropriateness) { form.setError('price_appropriateness', t('validation.ratingRequired')); return }
    if (!form.value_for_money) { form.setError('value_for_money', t('validation.ratingRequired')); return }

    isSubmitting.value = true
    form.finish_now = finishNow
    router.post(updateStep(5).url, form.data(), {
        preserveState: false,
        preserveScroll: false,
        onFinish: () => { isSubmitting.value = false },
    })
}
</script>

<template>
    <FeedbackLayout
        :step="5"
        :title="t('page5.title')"
        :subtitle="t('page5.subtitle')"
        :back-href="showStep(4)"
        :loading="form.processing || isSubmitting"
        @continue="submit(false)"
        @finish="submit(true)"
    >
        <StarRatingField v-model="form.product_quality" :label="t('page5.productQuality')" :error="form.errors.product_quality" />
        <StarRatingField v-model="form.product_design" :label="t('page5.productDesign')" :error="form.errors.product_design" />
        <StarRatingField v-model="form.price_appropriateness" :label="t('page5.priceAppropriateness')" :error="form.errors.price_appropriateness" />
        <StarRatingField v-model="form.value_for_money" :label="t('page5.valueForMoney')" :error="form.errors.value_for_money" />
    </FeedbackLayout>
</template>