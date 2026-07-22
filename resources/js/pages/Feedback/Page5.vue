<!-- Page3.vue -->
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
</template>