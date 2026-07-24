<!-- <script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import FeedbackLayout from '@/layouts/FeedbackLayout.vue'
import { showStep, updateStep } from '@/actions/App/Http/Controllers/FeedbackController'

const { t } = useI18n()

const form = useForm({
    may_contact: true,
    full_name: '',
    email: '',
    whatsapp: '',
    finish_now: true,
})

function submit() {
    form.submit(updateStep(7))
}
</script>

<template>
    <FeedbackLayout
        :step="7"
        :title="t('page7.title')"
        :subtitle="t('page7.subtitle')"
        :back-href="showStep(6)"
        :show-give-more="false"
        :loading="form.processing"
        @finish="submit"
    >
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-[#3B2A20] mb-2">{{ t('page7.mayContact') }}</label>
                <div class="flex gap-3">
                    <button type="button"
                        class="px-4 py-2 rounded-lg border text-sm font-medium"
                        :class="form.may_contact ? 'bg-[#3B2A20] text-white border-[#3B2A20]' : 'border-[#E8D9C3] text-[#8B5E3C]'"
                        @click="form.may_contact = true">{{ t('page7.yes') }}</button>
                    <button type="button"
                        class="px-4 py-2 rounded-lg border text-sm font-medium"
                        :class="!form.may_contact ? 'bg-[#3B2A20] text-white border-[#3B2A20]' : 'border-[#E8D9C3] text-[#8B5E3C]'"
                        @click="form.may_contact = false">{{ t('page7.no') }}</button>
                </div>
            </div>

            <template v-if="form.may_contact">
                <div>
                    <label class="block text-sm font-medium text-[#3B2A20] mb-1">{{ t('page7.fullName') }}</label>
                    <input v-model="form.full_name" type="text" class="w-full rounded-lg border border-[#E8D9C3] px-3 py-2.5 text-sm" />
                    <p v-if="form.errors.full_name" class="text-xs text-red-500 mt-1">{{ form.errors.full_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#3B2A20] mb-1">{{ t('page7.email') }}</label>
                    <input v-model="form.email" type="email" placeholder="email@contoh.com" class="w-full rounded-lg border border-[#E8D9C3] px-3 py-2.5 text-sm" />
                    <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#3B2A20] mb-1">{{ t('page7.whatsapp') }}</label>
                    <input v-model="form.whatsapp" type="text" placeholder="812 3456 7890" class="w-full rounded-lg border border-[#E8D9C3] px-3 py-2.5 text-sm" />
                </div>
            </template>
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
    may_contact: boolean | null
    full_name: string | null
    email: string | null
    whatsapp: string | null
}

const props = defineProps<{ feedback: Feedback }>()

const isSubmitting = ref(false)

const form = useForm({
    may_contact: props.feedback.may_contact ?? true,
    full_name: props.feedback.full_name ?? '',
    email: props.feedback.email ?? '',
    whatsapp: props.feedback.whatsapp ?? '',
    finish_now: true,
})

function submit() {
    if (isSubmitting.value) return
    isSubmitting.value = true
    router.post(updateStep(7).url, form.data(), {
        preserveState: false,
        preserveScroll: false,
        onFinish: () => { isSubmitting.value = false },
    })
}
</script>

<template>
    <FeedbackLayout
        :step="7"
        :title="t('page7.title')"
        :subtitle="t('page7.subtitle')"
        :back-href="showStep(6)"
        :show-give-more="false"
        :loading="form.processing || isSubmitting"
        @finish="submit"
    >
        <div class="space-y-5">
            <div>
                <label class="mb-2 block text-sm font-medium text-[#3B2A20]">
                    {{ t('page7.mayContact') }}
                </label>
                <div class="flex gap-3">
                    <button
                        type="button"
                        class="rounded-lg border px-4 py-2 text-sm font-medium"
                        :class="form.may_contact
                            ? 'border-[#3B2A20] bg-[#3B2A20] text-white'
                            : 'border-[#E8D9C3] text-[#8B5E3C] hover:bg-[#F3E4CC]'"
                        @click="form.may_contact = true"
                    >
                        {{ t('page7.yes') }}
                    </button>
                    <button
                        type="button"
                        class="rounded-lg border px-4 py-2 text-sm font-medium"
                        :class="!form.may_contact
                            ? 'border-[#3B2A20] bg-[#3B2A20] text-white'
                            : 'border-[#E8D9C3] text-[#8B5E3C] hover:bg-[#F3E4CC]'"
                        @click="form.may_contact = false"
                    >
                        {{ t('page7.no') }}
                    </button>
                </div>
            </div>

            <template v-if="form.may_contact">
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#3B2A20]">
                        {{ t('page7.fullName') }}
                    </label>
                    <input
                        v-model="form.full_name"
                        type="text"
                        class="w-full rounded-lg border border-[#E8D9C3] bg-white px-3 py-2.5 text-sm text-[#3B2A20] placeholder:text-[#C9B79C] focus:outline-none focus:ring-2 focus:ring-[#3B2A20]/20"
                    />
                    <p v-if="form.errors.full_name" class="mt-1 text-xs text-red-500">
                        {{ form.errors.full_name }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-[#3B2A20]">
                        {{ t('page7.email') }}
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="email@contoh.com"
                        class="w-full rounded-lg border border-[#E8D9C3] bg-white px-3 py-2.5 text-sm text-[#3B2A20] placeholder:text-[#C9B79C] focus:outline-none focus:ring-2 focus:ring-[#3B2A20]/20"
                    />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">
                        {{ form.errors.email }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-[#3B2A20]">
                        {{ t('page7.whatsapp') }}
                    </label>
                    <input
                        v-model="form.whatsapp"
                        type="text"
                        placeholder="812 3456 7890"
                        class="w-full rounded-lg border border-[#E8D9C3] bg-white px-3 py-2.5 text-sm text-[#3B2A20] placeholder:text-[#C9B79C] focus:outline-none focus:ring-2 focus:ring-[#3B2A20]/20"
                    />
                </div>
            </template>
        </div>
    </FeedbackLayout>
</template>