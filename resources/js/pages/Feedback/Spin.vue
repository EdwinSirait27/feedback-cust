<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import BrandLogo from '@/components/BrandLogo.vue'
import { spin as spinAction, result as resultAction } from '@/actions/App/Http/Controllers/FeedbackController'


interface SpinResult {
    prize_type: string
    voucher_amount: number | null
    voucher_code: string | null
}

const props = defineProps<{
    alreadySpun: boolean
    prize: SpinResult | null
}>()

const { t } = useI18n()
const page = usePage()
const flashError = computed(() => (page.props.flash as any)?.error)

const canvasRef = ref<HTMLCanvasElement | null>(null)
const spinning = ref(false)
const btnLabel = ref(t('spin.button'))
const spinTitle = ref(t('spin.title'))
const spinSubtitle = ref(t('spin.subtitle'))
const showConfetti = ref(false)

const SEGMENTS = [
    { type: 'voucher_50k',  label: 'Rp 50.000',  color: '--color-gold', textColor: '--color-ink' },
    { type: 'voucher_100k', label: 'Rp 100.000', color: '--color-signal', textColor: '--color-signal-ink' },
    { type: 'no_prize',     label: t('spin.thankYou'), color: '--color-dusk-2', textColor: '--color-paper' },
    { type: 'voucher_25k',  label: 'Rp 25.000',  color: '--color-paper', textColor: '--color-ink' },
    { type: 'voucher_50k',  label: 'Rp 50.000',  color: '--color-gold', textColor: '--color-ink' },
    { type: 'voucher_100k', label: 'Rp 100.000', color: '--color-signal', textColor: '--color-signal-ink' },
    { type: 'no_prize',     label: t('spin.thankYou'), color: '--color-dusk-2', textColor: '--color-paper' },
    { type: 'voucher_25k',  label: 'Rp 25.000',  color: '--color-paper', textColor: '--color-ink' },
]

let currentAngle = 0
let rafId: number | null = null

function cssToken(name: string) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim()
}

function drawWheel(angle: number) {
    const canvas = canvasRef.value
    if (!canvas) return
    const ctx = canvas.getContext('2d')!
    const cx = 134, cy = 134, r = 130
    const segAngle = (Math.PI * 2) / SEGMENTS.length
    ctx.setTransform(3, 0, 0, 3, 0, 0)
    ctx.clearRect(0, 0, 268, 268)
    const labelFont = `600 13px ${cssToken('--font-outlier')}`

    SEGMENTS.forEach((seg, i) => {
        const start = angle + i * segAngle
        const end = start + segAngle
        ctx.beginPath()
        ctx.moveTo(cx, cy)
        ctx.arc(cx, cy, r, start, end)
        ctx.closePath()
        ctx.fillStyle = cssToken(seg.color)
        ctx.fill()
        ctx.strokeStyle = cssToken('--color-dusk')
        ctx.lineWidth = 1
        ctx.stroke()
        ctx.save()
        ctx.translate(cx, cy)
        ctx.rotate(start + segAngle / 2)
        ctx.textAlign = 'right'
        ctx.fillStyle = cssToken(seg.textColor)
        ctx.font = labelFont
        ctx.fillText(seg.label, r - 12, 4)
        ctx.restore()
    })

    // Center circle
    ctx.beginPath()
    ctx.arc(cx, cy, 32, 0, Math.PI * 2)
    ctx.fillStyle = cssToken('--color-dusk')
    ctx.fill()
}

function ease(t: number) {
    return 1 - Math.pow(1 - t, 3)
}

function animateSpin(totalRotation: number, duration: number, onDone?: () => void) {
    const startTime = performance.now()
    const startAngle = currentAngle

    function frame(now: number) {
        const elapsed = now - startTime
        const progress = Math.min(elapsed / duration, 1)
        currentAngle = startAngle + totalRotation * ease(progress)
        drawWheel(currentAngle)
        if (progress < 1) {
            rafId = requestAnimationFrame(frame)
        } else {
            onDone?.()
        }
    }
    rafId = requestAnimationFrame(frame)
}

function showResult() {
    const prize = props.prize
    if (!prize || prize.prize_type === 'no_prize') {
        spinTitle.value = t('spin.thankYou')
        spinSubtitle.value = t('spin.noLuck')
    } else {
        spinTitle.value = t('spin.youWon')
        spinSubtitle.value = `Rp ${prize.voucher_amount?.toLocaleString('id-ID')}`
        showConfetti.value = true
    }
    setTimeout(() => router.visit(resultAction()), 2500)
}

function doSpin() {
    if (spinning.value || props.alreadySpun) return
    spinning.value = true
    btnLabel.value = '...'

    router.post(spinAction().url, {}, {
        preserveState: false,
        preserveScroll: false,
        onError: () => {
            spinning.value = false
            btnLabel.value = t('spin.button')
        },
    })
}

onMounted(() => {
    drawWheel(0)
    document.fonts?.ready.then(() => drawWheel(currentAngle))
    if (props.alreadySpun && props.prize) {
        spinning.value = true
        const segAngle = (Math.PI * 2) / SEGMENTS.length
        const matchingIndices = SEGMENTS
            .map((s, i) => s.type === props.prize!.prize_type ? i : -1)
            .filter(i => i !== -1)
        const targetIdx = matchingIndices[Math.floor(Math.random() * matchingIndices.length)]
        const targetAngle = (Math.PI * 2 * 5) + (Math.PI * 2 - (targetIdx * segAngle + segAngle / 2)) + Math.PI * 1.5
        animateSpin(targetAngle, 4000, () => {
            spinning.value = false
            showResult()
        })
    }
})

onUnmounted(() => {
    if (rafId) cancelAnimationFrame(rafId)
})
</script>

<template>
    <div class="survey survey--dusk relative">
        <Transition name="fade">
            <div v-if="flashError" class="survey-alert" role="alert">
                {{ flashError }}
            </div>
        </Transition>

        <Transition name="confetti">
            <div v-if="showConfetti" class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
                <div
                    v-for="i in 40" :key="i"
                    class="survey-confetti"
                    :style="{
                        left: `${10 + Math.random() * 80}%`,
                        top: `${10 + Math.random() * 60}%`,
                        background: ['var(--color-gold)', 'var(--color-signal)', 'var(--color-paper)', 'var(--color-gold-2)', 'var(--color-paper-3)'][i % 5],
                        animationDelay: `${(i * 0.05).toFixed(2)}s`,
                    }"
                />
            </div>
        </Transition>

        <header class="survey-frame">
            <div class="survey-bar">
                <BrandLogo />
            </div>
        </header>

        <main class="survey-frame survey-spin">
            <div class="survey-spin__intro">
                <h1 class="survey-title" aria-live="polite">{{ spinTitle }}</h1>
                <p :class="showConfetti ? 'survey-figure mt-3' : 'survey-subtitle'">{{ spinSubtitle }}</p>
            </div>

            <div class="survey-spin__wheel">
                <div class="survey-wheel">
                    <svg class="survey-wheel__pointer" viewBox="0 0 28 36" aria-hidden="true">
                        <polygon points="14,34 26,2 2,2" />
                    </svg>
                    <div class="survey-wheel__ring">
                        <canvas
                            ref="canvasRef"
                            width="804"
                            height="804"
                            class="survey-wheel__canvas"
                            aria-hidden="true"
                        />
                    </div>
                    <button
                        type="button"
                        :disabled="spinning || alreadySpun"
                        class="survey-wheel__button"
                        @click="doSpin"
                    >
                        {{ btnLabel }}
                    </button>
                </div>
            </div>

            <div class="survey-spin__prizes">
                <ul class="survey-prizes">
                    <li>Rp 100.000</li>
                    <li>Rp 50.000</li>
                    <li>Rp 25.000</li>
                </ul>
                <p class="survey-small mt-3">{{ t('spin.footerNote') }}</p>
            </div>
        </main>
    </div>
</template>
