<!-- <script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import FeedbackLayout from '@/layouts/FeedbackLayout.vue'
import { showStep, updateStep } from '@/actions/App/Http/Controllers/FeedbackController'

const { t } = useI18n()

const form = useForm({
    like_most: '',
    improvement_suggestion: '',
    recommend_likelihood: null as number | null,
    finish_now: false,
})

function submit(finishNow: boolean) {
    form.finish_now = finishNow
    form.submit(updateStep(6))
}
</script>

<template>
    <FeedbackLayout
        :step="6"
        :title="t('page6.title')"
        :subtitle="t('page6.subtitle')"
        :back-href="showStep(5)"
        :loading="form.processing"
        @continue="submit(false)"
        @finish="submit(true)"
    >
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-[#3B2A20] mb-1">{{ t('page6.likeMost') }}</label>
                <textarea v-model="form.like_most" rows="3" :placeholder="t('page6.writeHere')"
                    class="w-full rounded-lg border border-[#E8D9C3] px-3 py-2.5 text-sm" />
            </div>
            <div>
                <label class="block text-sm font-medium text-[#3B2A20] mb-1">{{ t('page6.improve') }}</label>
                <textarea v-model="form.improvement_suggestion" rows="3" :placeholder="t('page6.writeHere')"
                    class="w-full rounded-lg border border-[#E8D9C3] px-3 py-2.5 text-sm" />
            </div>
            <div>
                <label class="block text-sm font-medium text-[#3B2A20] mb-2">{{ t('page6.recommend') }}</label>
                <div class="flex flex-wrap gap-1.5">
                    <button
                        v-for="n in 11" :key="n - 1" type="button"
                        class="w-8 h-8 rounded-md text-xs font-medium border"
                        :class="form.recommend_likelihood === n - 1
                            ? 'bg-[#3B2A20] text-white border-[#3B2A20]'
                            : 'border-[#E8D9C3] text-[#8B5E3C] hover:bg-[#F3E4CC]'"
                        @click="form.recommend_likelihood = n - 1"
                    >{{ n - 1 }}</button>
                </div>
                <div class="flex justify-between text-xs text-[#8B5E3C] mt-1">
                    <span>{{ t('page6.notLikely') }}</span>
                    <span>{{ t('page6.veryLikely') }}</span>
                </div>
            </div>
        </div>
    </FeedbackLayout>
</template> -->
<script setup lang="ts">
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import FeedbackLayout from '@/layouts/FeedbackLayout.vue'
import { showStep, updateStep } from '@/actions/App/Http/Controllers/FeedbackController'

const { t } = useI18n()

interface Feedback {
    like_most: string | null
    improvement_suggestion: string | null
    recommend_likelihood: number | null
}

const props = defineProps<{ feedback: Feedback }>()

const isSubmitting = ref(false)

const form = useForm({
    like_most: props.feedback.like_most ?? '',
    improvement_suggestion: props.feedback.improvement_suggestion ?? '',
    recommend_likelihood: props.feedback.recommend_likelihood ?? null,
    finish_now: false,
})

function submit(finishNow: boolean) {
    if (isSubmitting.value) return

    if (form.recommend_likelihood === null) {
        form.setError('recommend_likelihood', t('validation.ratingRequired'))
        return
    }

    isSubmitting.value = true
    form.finish_now = finishNow
    form.clearErrors()
    router.post(updateStep(6).url, form.data(), {
        preserveState: 'errors',
        preserveScroll: 'errors',
        onError: (errors) => form.setError(errors),
        onFinish: () => { isSubmitting.value = false },
    })
}
</script>

<template>
    <FeedbackLayout
        :step="6"
        :title="t('page6.title')"
        :subtitle="t('page6.subtitle')"
        :back-href="showStep(5)"
        :loading="form.processing || isSubmitting"
        @continue="submit(false)"
        @finish="submit(true)"
    >
        <div>
            <div class="survey-field">
                <label class="survey-label" for="like_most">
                    {{ t('page6.likeMost') }}
                </label>
                <textarea
                    id="like_most"
                    v-model="form.like_most"
                    name="like_most"
                    rows="3"
                    :placeholder="t('page6.writeHere')"
                    class="survey-input"
                />
                <p v-if="form.errors.like_most" class="survey-error" role="alert">
                    {{ form.errors.like_most }}
                </p>
            </div>

            <div class="survey-field">
                <label class="survey-label" for="improvement_suggestion">
                    {{ t('page6.improve') }}
                </label>
                <textarea
                    id="improvement_suggestion"
                    v-model="form.improvement_suggestion"
                    name="improvement_suggestion"
                    rows="3"
                    :placeholder="t('page6.writeHere')"
                    class="survey-input"
                />
                <p v-if="form.errors.improvement_suggestion" class="survey-error" role="alert">
                    {{ form.errors.improvement_suggestion }}
                </p>
            </div>

            <div class="survey-field">
                <p class="survey-label">
                    {{ t('page6.recommend') }}
                </p>
                <div class="survey-nps" role="group" :aria-label="t('page6.recommend')">
                    <button
                        v-for="n in 11"
                        :key="n - 1"
                        type="button"
                        class="survey-nps__cell"
                        :aria-pressed="form.recommend_likelihood === n - 1"
                        @click="form.recommend_likelihood = n - 1"
                    >
                        {{ n - 1 }}
                    </button>
                </div>
                <div class="survey-scale survey-small">
                    <span>{{ t('page6.notLikely') }}</span>
                    <span>{{ t('page6.veryLikely') }}</span>
                </div>
                <p v-if="form.errors.recommend_likelihood" class="survey-error" role="alert">
                    {{ form.errors.recommend_likelihood }}
                </p>
            </div>
        </div>
    </FeedbackLayout>
</template>