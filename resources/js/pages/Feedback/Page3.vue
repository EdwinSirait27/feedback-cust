<!-- <script setup lang="ts">
// import { useForm } from '@inertiajs/vue3'
import { useForm, router } from '@inertiajs/vue3'

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
    if (!form.staff_friendliness) {
        form.setError('staff_friendliness', t('validation.ratingRequired'))
        return
    }
    if (!form.staff_helpfulness) {
        form.setError('staff_helpfulness', t('validation.ratingRequired'))
        return
    }
    if (!form.checkout_process) {
        form.setError('checkout_process', t('validation.ratingRequired'))
        return
    }
    if (!form.service_speed) {
        form.setError('service_speed', t('validation.ratingRequired'))
        return
    }

    form.finish_now = finishNow

    router.post(updateStep(3).url, form.data(), {
        preserveState: false,
        preserveScroll: false,
        replace: false,
    })
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
        <StarRatingField
            v-model="form.staff_friendliness"
            :label="t('page3.staffFriendliness')"
            :error="form.errors.staff_friendliness"
        />
        <StarRatingField
            v-model="form.staff_helpfulness"
            :label="t('page3.staffHelpfulness')"
            :error="form.errors.staff_helpfulness"
        />
        <StarRatingField
            v-model="form.checkout_process"
            :label="t('page3.checkoutProcess')"
            :error="form.errors.checkout_process"
        />
        <StarRatingField
            v-model="form.service_speed"
            :label="t('page3.serviceSpeed')"
            :error="form.errors.service_speed"
        />
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

interface Feedback {
    staff_friendliness: number | null
    staff_helpfulness: number | null
    checkout_process: number | null
    service_speed: number | null
}

const props = defineProps<{ feedback: Feedback }>()

const isSubmitting = ref(false)

const form = useForm({
    staff_friendliness: props.feedback.staff_friendliness ?? null,
    staff_helpfulness: props.feedback.staff_helpfulness ?? null,
    checkout_process: props.feedback.checkout_process ?? null,
    service_speed: props.feedback.service_speed ?? null,
    finish_now: false,
})

function submit(finishNow: boolean) {
    if (isSubmitting.value) return
    if (!form.staff_friendliness) { form.setError('staff_friendliness', t('validation.ratingRequired')); return }
    if (!form.staff_helpfulness) { form.setError('staff_helpfulness', t('validation.ratingRequired')); return }
    if (!form.checkout_process) { form.setError('checkout_process', t('validation.ratingRequired')); return }
    if (!form.service_speed) { form.setError('service_speed', t('validation.ratingRequired')); return }

    isSubmitting.value = true
    form.finish_now = finishNow
    router.post(updateStep(3).url, form.data(), {
        preserveState: false,
        preserveScroll: false,
        onFinish: () => { isSubmitting.value = false },
    })
}
</script>

<template>
    <FeedbackLayout
        :step="3"
        :title="t('page3.title')"
        :subtitle="t('page3.subtitle')"
        :back-href="showStep(2)"
        :loading="form.processing || isSubmitting"
        @continue="submit(false)"
        @finish="submit(true)"
    >
        <StarRatingField
            v-model="form.staff_friendliness"
            :label="t('page3.staffFriendliness')"
            :error="form.errors.staff_friendliness"
        />
        <StarRatingField
            v-model="form.staff_helpfulness"
            :label="t('page3.staffHelpfulness')"
            :error="form.errors.staff_helpfulness"
        />
        <StarRatingField
            v-model="form.checkout_process"
            :label="t('page3.checkoutProcess')"
            :error="form.errors.checkout_process"
        />
        <StarRatingField
            v-model="form.service_speed"
            :label="t('page3.serviceSpeed')"
            :error="form.errors.service_speed"
        />
    </FeedbackLayout>
</template>